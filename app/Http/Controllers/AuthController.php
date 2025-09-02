<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLoginForm(Request $request)
    {
        // Se o usuário já estiver logado
        if (Auth::check()) {
            $redirect = $request->query('redirect_uri');
            if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
                $token = Auth::user()->createToken('SSO Client')->accessToken;
                return redirect($redirect . '?token=' . $token);
            }
            return redirect('/dashboard');
        }

        // Pega redirect_uri da query string, se houver
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

        // Cria token via Passport
        $tokenResult = $user->createToken('SSO Client');
        $token = $tokenResult->accessToken;

        session(['user_token' => $token]);

        // Redireciona para cliente externo se redirect_uri for válido
        if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
            return redirect($redirect . '?token=' . $token);
        }

        // Senão, leva para dashboard
        return redirect('/dashboard');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Revoga todos os tokens do usuário
            $user->tokens()->delete(); // Passport: acessToken ou token pessoal
        }

        Auth::logout();
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

        // Consulta o IdP
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('https://login.taiksu.com.br/api/user');

        if ($response->failed()) {
            return 'Token inválido ou expirado!';
        }

        $userData = $response->json();

        // Cria ou atualiza usuário local
        $user = User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['name'],
                'unidade_id' => $userData['unidade']['id'] ?? null,
                'permissions' => $userData['permissions'] ?? null
            ]
        );

        Auth::login($user);

        // Se houver redirect_uri, envia token para o cliente
        if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
            $tokenLocal = $user->createToken('SSO Client')->accessToken;
            return redirect($redirect . '?token=' . $tokenLocal);
        }

        // Senão, leva para dashboard
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

        return back()->with('success', 'Senha alterada com sucesso!');
    }
}
