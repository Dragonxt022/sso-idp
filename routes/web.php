<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas de autenticação
|--------------------------------------------------------------------------
*/

// Formulário de login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');

// Processa login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Callback SSO
Route::get('/callback', [AuthController::class, 'callback'])->name('sso.callback');


/*
|--------------------------------------------------------------------------
| Rotas protegidas (usuário autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil do usuário
    Route::get('/perfil', [DashboardController::class, 'perfil'])->name('perfil');

    // Alterar senha
    Route::post('/user/change-password', [AuthController::class, 'changePassword'])->name('user.change-password');


    /*
    |--------------------------------------------------------------------------
    | Rotas protegidas para FRANQUEADORA
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Franqueadora')->group(function () {
        // Menu de Configurações
        Route::get('/config', [ConfigController::class, 'index'])->name('config.index');

        // CRUD de Aplicações
        Route::resource('config/applications', ApplicationController::class);
    });
});
