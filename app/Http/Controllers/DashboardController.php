<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Token;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Application; // no topo do controller
use App\Models\InforUnidade;

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

        // Pega o token ativo
        $tokenResult = $user->tokens()->where('revoked', false)->latest()->first();
        $token = $tokenResult ? $tokenResult->id : null;

        $grupo = $user->getRoleNames()->first();
        $cidade = $user->unidade->cidade ?? 'Sem unidade';

        // Recupera todas as roles do guard 'api'
        $roles = Role::where('guard_name', 'api')->get();

        // Recupera todas as unidades para o seletor
        $unidades = InforUnidade::orderBy('cidade')->get(['id', 'cidade']);

        // Retorna a view com todas as variáveis
        return view('perfil', compact('user', 'token', 'grupo', 'cidade', 'roles', 'unidades'));
    }


    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::where('id', $request->role_id)
            ->where('guard_name', 'api')
            ->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role não encontrada no guard API'
            ], 404);
        }

        $user->syncRoles([$role]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cargo atualizado com sucesso!',
            'role' => $role->name
        ]);
    }
}
