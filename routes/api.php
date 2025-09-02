<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Grupo protegido por autenticação
Route::middleware('auth:api')->group(function () {
    // Retorna dados do usuário autenticado
    Route::get('/user/me', [UserController::class, 'me']);

    // Lista todas as roles e permissões
    Route::get('/roles-permissions', [UserController::class, 'listRolesPermissions']);

    // Atribuir roles e permissões a usuários
    Route::post('/users/{id}/assign-role', [UserController::class, 'assignRole']);
    Route::post('/users/{id}/assign-permission', [UserController::class, 'assignPermission']);
});
