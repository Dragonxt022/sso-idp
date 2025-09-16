<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Events\UserActionEvent;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            $redirect = $request->query('redirect_uri');
            if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
                $token = Auth::user()->createToken('SSO Client')->accessToken;
                return redirect($redirect . '?token=' . $token);
            }
            return redirect('/dashboard');
        }

        $redirect = $request->query('redirect_uri', null);
        return view('auth.login', compact('redirect'));
    }

    /**
     * Login local + SSO
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        $redirect = $request->input('redirect_uri', '/dashboard');

        if (!Auth::attempt($credentials)) {
            return back()->with('error', 'Credenciais inválidas!');
        }

        $user = Auth::user();

        // ✅ Verifica se o usuário pode acessar
        if (!in_array($user->status, ['ativo', 'ferias'])) {
            $statusMap = [
                'ferias' => 'de férias',
                'demitido' => 'demitido',
            ];
            $statusMsg = $statusMap[$user->status] ?? $user->status;

            Auth::logout();
            return back()->with('error', "Este usuário está $statusMsg.");
        }

        // Dispara log de auditoria
        event(new UserActionEvent(
            $user,
            'login',
            'Usuário realizou login',
            $request->ip(),
            $request->userAgent()
        ));

        // Cria token via Passport
        $tokenResult = $user->createToken('SSO Client');
        $token = $tokenResult->accessToken;

        session(['user_token' => $token]);

        if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
            return redirect($redirect . '?token=' . $token);
        }

        return redirect('/dashboard');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Dispara log de auditoria
            event(new UserActionEvent(
                $user,
                'logout',
                'Usuário realizou logout',
                $request->ip(),
                $request->userAgent()
            ));

            $user->tokens()->delete();
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Callback do IdP (SSO)
     */
    public function callback(Request $request)
    {
        session()->start();

        $token = $request->query('token');
        $redirect = $request->query('redirect_uri');

        if (!$token) {
            return 'Token não recebido!';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('https://login.taiksu.com.br/api/user');

        if ($response->failed()) {
            return 'Token inválido ou expirado!';
        }

        $userData = $response->json();

        $user = User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['name'],
                'unidade_id' => $userData['unidade']['id'] ?? null,
                'permissions' => $userData['permissions'] ?? null
            ]
        );

        if (!in_array($user->status, ['ativo', 'ferias'])) {
            $statusMap = [
                'ferias' => 'de férias',
                'demitido' => 'demitido',
            ];
            $statusMsg = $statusMap[$user->status] ?? $user->status;
            return redirect('/login')->with('error', "Este usuário está $statusMsg.");
        }

        Auth::login($user);

        // Dispara log de auditoria
        event(new UserActionEvent(
            $user,
            'sso_login',
            'Usuário realizou login via SSO',
            $request->ip(),
            $request->userAgent()
        ));

        if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
            $tokenLocal = $user->createToken('SSO Client')->accessToken;
            return redirect($redirect . '?token=' . $tokenLocal);
        }

        return redirect('/dashboard');
    }

    /**
     * Alterar senha
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'nova-senha' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->input('nova-senha'));
        $user->save();

        // Dispara log de auditoria
        event(new UserActionEvent(
            $user,
            'change_password',
            'Usuário alterou sua senha',
            $request->ip(),
            $request->userAgent()
        ));

        return back()->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Enviar link de recuperação de senha
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link de recuperação enviado para seu e-mail!')
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Resetar senha do usuário
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                // Dispara log de auditoria
                event(new UserActionEvent(
                    $user,
                    'reset_password',
                    'Usuário resetou a senha via link',
                    request()->ip(),
                    request()->userAgent()
                ));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Senha redefinida com sucesso!')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
