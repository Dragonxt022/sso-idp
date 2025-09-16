<?php

use App\Http\Controllers\Api\EmpresaFornecedoraController;
use App\Http\Controllers\Api\FornecedorController;
use App\Http\Controllers\Api\FranqueadoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\InforUnidadeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Aqui são registradas as rotas para a sua API. Essas rotas são
| carregadas pelo RouteServiceProvider e, por padrão, recebem o
| prefixo "api". Todas as rotas definidas aqui estão protegidas
| pelo middleware 'auth:api', garantindo que apenas usuários
| autenticados possam acessá-las.
|
*/

// Grupo de rotas que exigem autenticação via token da API.
Route::middleware(['auth:api', 'checkUserStatus'])->group(function () {

    /**
     * =======================================================================
     * ROTAS DE USUÁRIO E PERMISSÕES
     * =======================================================================
     * Rotas para obter informações do usuário, listar e atribuir papéis e
     * permissões.
     */

    // Retorna os dados do usuário atualmente autenticado.
    Route::get('/user/me', [UserController::class, 'me'])->name('api.user.me');

    // Lista todos os papéis (roles) e permissões disponíveis no sistema.
    Route::get('/roles-permissions', [UserController::class, 'listRolesPermissions'])->name('api.roles_permissions.list');

    // Atribui um ou mais papéis (roles) a um usuário específico.
    Route::post('/users/{id}/assign-role', [UserController::class, 'assignRole'])->name('api.users.assign_role');

    // Atribui uma ou mais permissões diretas a um usuário específico.
    Route::post('/users/{id}/assign-permission', [UserController::class, 'assignPermission'])->name('api.users.assign_permission');


    /**
     * =====================================
     * Api de cadastros de fornecedores
     * =====================================
     */

    // Rotas de API Resource para o modelo User (index, store, show, update, destroy).
    Route::apiResource('fornecedores', FornecedorController::class);


    /**
     * =======================
     *  Rotas de criação de Empresa Fornecedores
     * =======================
     */
    // Rotas de API Resource para o modelo EmpresaFornecedora (index, store, show, update, destroy).
    Route::apiResource('empresas-fornecedoras', EmpresaFornecedoraController::class);


    /**
     * =======================================================================
     * ROTAS DE INFORMAÇÕES DA UNIDADE (CRUD)
     * =======================================================================
     */

    // Rotas de API Resource para o modelo InforUnidade (index, store, show, update, destroy).
    Route::apiResource('infor-unidades', InforUnidadeController::class);


    /**
     * =======================================================================
     * ROTAS DE CONTROLLER BACKOFFICE - FRANQUEADOS
     * =======================================================================
     *
     */

    // Visualiza lista de franqueados
    Route::get('/franqueados', [FranqueadoController::class, 'index'])->name('api.franqueados.index');

    // Cadastro de franqueado
    Route::post('/franqueados', [FranqueadoController::class, 'store'])->name('api.franqueados.store');

    // Busque de um franqueado especifico
    Route::get('/franqueados/{id}', [FranqueadoController::class, 'show'])->name('api.franqueados.show');


});
