<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\ImageService;

class FornecedorController extends Controller
{
    // Lista todos os fornecedores
    public function index()
    {
        // Lista todos usuários com role Fornecedor
        $fornecedores = User::role('Fornecedor')->paginate(15);
        return response()->json($fornecedores, 200);
    }

    // Mostrar detalhes de um fornecedor específico
    public function show($id)
    {
        $fornecedor = User::role('Fornecedor')->findOrFail($id);
        return response()->json($fornecedor, 200);
    }

    // Criar novo fornecedor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'empresa_fornecedora_id' => 'required|exists:empresas_fornecedoras,id', // novo campo
            'profile_photo_path' => 'nullable|file|image',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['cpf'] = preg_replace('/[\.\-\,]/', '', $validated['cpf']);
        $photoPath = ImageService::handleUpload($request->file('profile_photo_path'), null, 'frontend/profiles');
        $photoName = $photoPath ? basename($photoPath) : null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'unidade_id' => auth()->user()->unidade_id ?? null,
            'empresa_fornecedora_id' => $validated['empresa_fornecedora_id'], // salva relacionamento
            'profile_photo_path' => $photoName,
            'password' => Hash::make($validated['password']),
            'pin' => $this->generateUniquePin(),
        ]);

        $role = Role::where('name', 'Fornecedor')->where('guard_name', 'api')->first();
        if ($role) {
            $user->syncRoles([$role]);
        }

        if ($request->filled('permission_ids')) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->pluck('name')->toArray();
            $user->syncPermissions($permissions);
        }

        return response()->json([
            'message' => 'Fornecedor criado com sucesso!',
            'data' => $user
        ], 201);
    }

    // Atualizar fornecedor
    public function update(Request $request, $id)
    {
        $fornecedor = User::role('Fornecedor')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $fornecedor->id,
            'cpf' => 'sometimes|required|string|max:14|unique:users,cpf,' . $fornecedor->id,
            'profile_photo_path' => 'nullable',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
            'password' => 'nullable|string|min:8|confirmed',
            'empresa_fornecedora_id' => 'sometimes|required|exists:empresas_fornecedoras,id',

        ]);

        if ($request->filled('cpf')) {
            $validated['cpf'] = preg_replace('/[\.\-\,]/', '', $validated['cpf']);
        }

        if ($request->hasFile('profile_photo_path')) {
            $photoPath = ImageService::handleUpload($request->file('profile_photo_path'), null, 'frontend/profiles');
            $validated['profile_photo_path'] = $photoPath ? basename($photoPath) : $fornecedor->profile_photo_path;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $fornecedor->update($validated);

        if ($request->filled('permission_ids')) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->pluck('name')->toArray();
            $fornecedor->syncPermissions($permissions);
        }

        return response()->json([
            'message' => 'Fornecedor atualizado com sucesso!',
            'data' => $fornecedor
        ], 200);
    }

    // Atualizar status do fornecedor
    public function updateStatus(Request $request, $id)
    {
        $fornecedor = User::role('Fornecedor')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:ativo,inativo',
        ]);

        $fornecedor->status = $request->status;
        $fornecedor->save();

        return response()->json([
            'message' => 'Status do fornecedor atualizado com sucesso!',
            'data' => $fornecedor
        ], 200);
    }

    // Excluir fornecedor
    public function destroy($id)
    {
        $fornecedor = User::role('Fornecedor')->findOrFail($id);
        $fornecedor->delete();

        return response()->json(['message' => 'Fornecedor excluído com sucesso!'], 200);
    }

    // Método para gerar um PIN único
    protected function generateUniquePin($length = 4)
    {
        do {
            $pin = mt_rand(1000, 9999);
        } while (User::where('pin', $pin)->exists());

        return $pin;
    }
}
