<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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

        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'foto'        => $foto,
            'unidade_id'  => $user->unidade_id ?? null,
            'cpf'         => $user->cpf ?? null,
            // pega o primeiro role (se existir) e expõe só o id como grupo_id
            'grupo_id'    => $user->roles->first()->id ?? null,
            'permissions' => $user->getAllPermissions()->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }),
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

    /**
     * Atribui um ou vários roles ao usuário
     */
    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles($request->roles); // Remove antigos e adiciona os novos

        return response()->json([
            'message' => 'Roles atribuídos com sucesso!',
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
