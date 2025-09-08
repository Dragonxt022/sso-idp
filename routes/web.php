<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserEquipeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas de autenticação
|--------------------------------------------------------------------------
*/

// Formulário de login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');

// Processa login
Route::post('/login', [AuthController::class, 'login'])->name('login.entrar');

// Login local
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

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

    // Gestão de equipe
    Route::get('/equipe', [UserEquipeController::class, 'index'])->name('equipe.index');
    Route::get('/equipe/create', [UserEquipeController::class, 'create'])->name('equipe.create');
    Route::post('/equipe', [UserEquipeController::class, 'store'])->name('equipe.store');
    Route::get('/equipe/{id}', [UserEquipeController::class, 'show'])->name('equipe.show');

    Route::get('/equipe/colaborador/{id}', [UserEquipeController::class, 'colaborador'])->name('equipe.colaborador');
    Route::post('/user/{user}/regenerate-pin', [UserEquipeController::class, 'regeneratePin'])->name('user.regenerate-pin');
    Route::post('/user/{user}/toggle-permission', [UserEquipeController::class, 'togglePermission'])->name('user.toggle-permission');



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
    Route::middleware('role:Desenvolvedor')->group(function () {
        // Menu de Configurações
        Route::get('/config', [ConfigController::class, 'index'])->name('config.index');

        // CRUD de Aplicações
        Route::resource('config/applications', ApplicationController::class);

        Route::patch('/applications/{application}/toggle-active', [ApplicationController::class, 'toggleActive'])->name('applications.toggleActive');
        Route::post('/config/applications/order', [ApplicationController::class, 'updateOrder'])->name('applications.updateOrder');
    });
});
