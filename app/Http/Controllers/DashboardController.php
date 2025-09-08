<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Token;
use App\Models\User;
use App\Models\Application; // no topo do controller


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ids das roles do usuário
        $roleIds = $user->roles->pluck('id')->toArray();

        $applications = Application::with('roles')
            // ->whereDoesntHave('roles') // apps públicos
            ->orWhereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('roles.id', $roleIds);
            })
            ->orderBy('order')
            ->get();

        return view('dashboard', compact('user', 'applications'));
    }

    public function perfil(Request $request)
    {
        $user = $request->user();

        // Recupera o token JWT que foi criado no login
        // Passport cria um "accessToken" ao gerar o token
        $tokenResult = $user->tokens()->where('revoked', false)->latest()->first();

        $token = $tokenResult ? $tokenResult->id : null;

        // Pega o grupo e nome da cidade do user
        $grupo = $user->getRoleNames()->first();
        $cidade = $user->unidade->cidade;


        // IMPORTANTE: se você quiser o plainTextToken, precisa salvar no login em session
        // no login, depois de criar o token:
        // session(['user_token' => $tokenResult->accessToken]);

        // Aqui você pega da session
        $token = session('user_token');

        return view('perfil', compact('user', 'token', 'grupo', 'cidade'));
    }
}
