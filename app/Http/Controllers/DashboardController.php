<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Token;
use App\Models\User;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return view('dashboard', compact('user'));
    }

    public function perfil(Request $request)
    {
        $user = $request->user();

        // Recupera o token JWT que foi criado no login
        // Passport cria um "accessToken" ao gerar o token
        $tokenResult = $user->tokens()->where('revoked', false)->latest()->first();

        $token = $tokenResult ? $tokenResult->id : null;

        // IMPORTANTE: se você quiser o plainTextToken, precisa salvar no login em session
        // no login, depois de criar o token:
        // session(['user_token' => $tokenResult->accessToken]);

        // Aqui você pega da session
        $token = session('user_token');

        return view('perfil', compact('user', 'token'));
    }
}
