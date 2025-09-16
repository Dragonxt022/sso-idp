<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ErrorLogController;
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

Route::get('/test-error', function () {
    // Força uma exceção para o Laravel capturar
    throw new \Exception('Este é um erro de teste para o Handler.');

    // O código abaixo não será executado
    // $a = null;
    // $a->doSomething();
});


/*
|--------------------------------------------------------------------------
| Rotas de recuperação de senha
|--------------------------------------------------------------------------
*/
Route::get('forgot-password', function () {
    return view('auth.passwordReset');
})->name('password.request');

Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email');

Route::get('reset-password/{token}', function ($token) {
    return view('auth.passwordUpdade', [
        'token' => $token,
        'email' => request('email')
    ]);
})->name('password.reset');


Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');


/*
|--------------------------------------------------------------------------
| Rotas protegidas (usuário autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'checkUserStatus'])->group(function () {

    // Gestão de equipe
    Route::get('/equipe', [UserEquipeController::class, 'index'])->name('equipe.index');
    Route::get('/equipe/create', [UserEquipeController::class, 'create'])->name('equipe.create');
    Route::post('/equipe', [UserEquipeController::class, 'store'])->name('equipe.store');
    Route::get('/equipe/{id}', [UserEquipeController::class, 'show'])->name('equipe.show');

    Route::get('/equipe/colaborador/{id}', [UserEquipeController::class, 'colaborador'])->name('equipe.colaborador');
    Route::post('/user/{user}/regenerate-pin', [UserEquipeController::class, 'regeneratePin'])->name('user.regenerate-pin');
    Route::post('/user/{user}/toggle-permission', [UserEquipeController::class, 'togglePermission'])->name('user.toggle-permission');
    Route::post('/user/{id}/update-role', [UserEquipeController::class, 'updateRole'])->name('user.update-role');

    // rota para demissão
    Route::post('/user/{user}/toggle-status', [UserEquipeController::class, 'toggleStatus'])->name('user.toggle-status');


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

        Route::get('/auditoria', [AuditController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/fetch', [AuditController::class, 'fetch'])->name('auditoria.fetch');
        Route::get('/auditoria/export', [AuditController::class, 'export'])->name('auditoria.export');

        // Erros
        // Rota para a página de visualização dos logs de erro
        Route::get('/auditoria/erros', [ErrorLogController::class, 'index'])->name('auditoria.erros');

        // Rota API para buscar os logs de erro de forma assíncrona
        Route::get('/auditoria/erros/fetch', [ErrorLogController::class, 'fetch'])->name('auditoria.erros.fetch');
        Route::delete('/auditoria/erros/clear', [ErrorLogController::class, 'clearLogs'])->name('auditoria.erros.clear');

    });
});
