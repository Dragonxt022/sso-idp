<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

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

    /**
     * Atribui uma ou várias roles ao usuário
     */
    public function assignRole(Request $request, $id)
    {
        auth()->shouldUse('web'); // garante guard web para esta request

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

        // 🔹 Carrega apenas os roles do guard web
        $roles = Role::whereIn('name', $request->roles)
            ->where('guard_name', 'web')
            ->get();

        // Sincroniza com o guard correto
        $user->syncRoles($roles);

        return response()->json([
            'message' => 'Grupos atribuídos com sucesso!',
            'user' => [
                'id'   => $user->id,
                'name' => $user->name,
                'roles'=> $user->getRoleNames(),
            ]
        ]);
    }


    /**
     * Atribui uma ou várias permissões ao usuário
     */
    public function assignPermission(Request $request, $id)
    {
        auth()->shouldUse('web'); // autentica com guard web

        $request->validate([
            'permissions'   => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::findOrFail($id);

        // carrega as permissões pelo guard correto
        $permissions = \Spatie\Permission\Models\Permission::whereIn('name', $request->permissions)
            ->where('guard_name', 'web')
            ->get();

        // sincroniza no guard web
        $user->syncPermissions($permissions);

        return response()->json([
            'message' => 'Permissões atribuídas com sucesso!',
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    // Retorna colaboradores por unidade
    public function getByUnidade(Request $request, $unidade_id)
    {
        if (!is_numeric($unidade_id)) {
            return response()->json(['error' => 'ID da unidade inválido'], 400);
        }

        $colaboradorId = $request->query('id');

        $query = User::where('unidade_id', $unidade_id);

        if ($colaboradorId) {
            $query->where('id', $colaboradorId);
        }

        $colaboradores = $query->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'pin' => $user->pin,
                'status' => $user->status,
                'unidade_id' => $user->unidade_id,
                'grupo_id' => $user->roles->first()->id ?? null,
                'cpf' => $user->cpf,
                'profile_photo_path' => $user->profile_photo_path,
                'empresa_fornecedora_id' => $user->empresa_fornecedora_id,
                'criado_em' => $user->created_at
                    ? $user->created_at->format('d/m/Y H:i')
                    : null,
            ];
        });

        if ($colaboradores->isEmpty()) {
            return response()->json(['message' => 'Nenhum colaborador encontrado.'], 200);
        }

        return response()->json([
            'total_usuarios_unidade_' . $unidade_id => $colaboradores->count(),
            'data' => $colaboradores
        ], 200);
    }

    // Buscar informações de um usuario por ID
    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'pin' => $user->pin,
            'status' => $user->status,
            'unidade_id' => $user->unidade_id,
            'grupo_id' => $user->roles->first()->id ?? null,
            'cpf' => $user->cpf,
            'profile_photo_path' => $user->profile_photo_path,
            'empresa_fornecedora_id' => $user->empresa_fornecedora_id,
            'criado_em' => $user->created_at
                ? $user->created_at->format('d/m/Y H:i')
                : null,
        ];

        return response()->json($data, 200);
    }




}
