<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Retorna dados do usuário autenticado com roles e permissões
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $foto = $user->profile_photo_path
            ? asset('frontend/profiles/' . $user->profile_photo_path)
            : null;

        $primeiroRole = $user->roles->first();

        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'foto'        => $foto,
            'cpf'         => $user->cpf ?? null,
            'grupo_id'    => $user->roles->first()->id ?? null,
            'grupo_nome'  => $primeiroRole->name ?? null,
            'permissions' => $user->getAllPermissions()->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }),
            'unidade'     => $user->unidade ? $user->unidade : null,
        ]);
    }


    /**
     * Lista todas as roles e permissões
     */
    public function listRolesPermissions()
    {
        return response()->json([
            'roles' => Role::pluck('name'),
            'permissions' => Permission::pluck('name'),
        ]);
    }


    public function assignRole(Request $request, $id)
    {
        // Verifica se o usuário logado tem o role Franqueadora
        if (!Auth::user()->hasRole('Franqueadora')) {
            return response()->json([
                'message' => 'Você não tem autorização para atribuir grupos.'
            ], 403);
        }

        // Validação dos roles recebidos
        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::findOrFail($id);

        // Sincroniza os roles
        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Grupos atribuídos com sucesso!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }


    /**
     * Atribui uma ou várias permissões ao usuário
     */
    public function assignPermission(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::findOrFail($id);
        $user->syncPermissions($request->permissions); // Remove antigas e adiciona as novas

        return response()->json([
            'message' => 'Permissões atribuídas com sucesso!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }
}
