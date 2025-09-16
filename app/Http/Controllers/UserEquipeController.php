<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserEquipeController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('Franqueadora', 'api')) {
            $users = User::role('Franqueado', 'api')->with('unidade')->get();
        } elseif ($authUser->hasAnyRole(['Franqueado', 'Gerente'], 'api')) {
            $users = User::role(['Colaborador', 'Gerente', 'Recepcionista'], 'api')
                ->with('unidade')
                ->where('unidade_id', $authUser->unidade_id)
                ->get();
        } else {
            $users = collect();
        }

        return view('equipe.index', compact('users'));
    }

    public function create()
    {
        $excludedRoles = [
            'Franqueadora',
            'Desenvolvedor',
            'Fornecedor',
            'Franqueado',
        ];

        $roles = Role::where('guard_name', 'api')
            ->whereNotIn('name', $excludedRoles)
            ->get();

        $permissions = Permission::where('guard_name', 'api')
            ->where('name', '!=', 'Beckoffice')
            ->get();

        return view('equipe.create', compact('permissions', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'profile_photo_path' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'role_id' => 'required|exists:roles,id',
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
            'unidade_id' => auth()->user()->unidade_id,
            'profile_photo_path' => $photoName,
            'password' => bcrypt($validated['password']),
            'pin' => $this->generateUniquePin(),
        ]);

        $role = Role::where('id', $validated['role_id'])
            ->where('guard_name', 'api')
            ->first();

        if ($role) {
            $user->syncRoles([$role]);
        }

        if ($request->filled('permission_ids')) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])
                ->pluck('name')
                ->toArray();
            $user->syncPermissions($permissions); // guard_name = api
        }

        return redirect()->route('equipe.index')->with('success', 'Colaborador criado com sucesso!');
    }


    protected function generateUniquePin($length = 4)
    {
        do {
            $pin = mt_rand(1000, 9999);
        } while (User::where('pin', $pin)->exists());

        return $pin;
    }

    public function show(User $user)
    {
        $authUser = Auth::user();

        if (
            $authUser->hasRole('Franqueadora', 'api') ||
            ($authUser->hasRole('Franqueado', 'api') && $authUser->unidade_id == $user->unidade_id)
        ) {
            return response()->json($user->load('unidade'));
        }

        return response()->json(['error' => 'Acesso negado'], 403);
    }

    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();

        if (
            $authUser->hasRole('Franqueadora', 'api') ||
            ($authUser->hasRole('Franqueado', 'api') && $authUser->unidade_id == $user->unidade_id)
        ) {

            $data = $request->only(['name', 'email', 'cpf', 'unidade_id']);
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            $user->update($data);
            return response()->json($user);
        }

        return response()->json(['error' => 'Acesso negado'], 403);
    }

    public function destroy(User $user)
    {
        $authUser = Auth::user();

        if (
            $authUser->hasRole('Franqueadora', 'api') ||
            ($authUser->hasRole('Franqueado', 'api') && $authUser->unidade_id == $user->unidade_id)
        ) {

            $user->delete();
            return response()->json(['message' => 'Usuário deletado']);
        }

        return response()->json(['error' => 'Acesso negado'], 403);
    }

    public function colaborador(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $foto = $user->profile_photo_path
            ? asset('frontend/profiles/' . $user->profile_photo_path)
            : asset('frontend/img/user.png');

        $excludedRoles = ['Franqueadora', 'Desenvolvedor', 'Fornecedor', 'Franqueado'];

        $roles = Role::where('guard_name', 'api')
            ->whereNotIn('name', $excludedRoles)
            ->get();

        $permissions = Permission::where('guard_name', 'api')
            ->where('name', '!=', 'Beckoffice')
            ->get();

        $userPermissions = $user->getPermissionNames();

        return view('equipe.colaborador', compact('user', 'foto', 'roles', 'permissions', 'userPermissions'));
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validação básica
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        // 🔹 Busca a role garantindo que seja do guard api
        $role = Role::where('id', $request->role_id)
            ->where('guard_name', 'api')
            ->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role não encontrada no guard API'
            ], 404);
        }

        // 🔹 Aplica a role no guard api
        $user->syncRoles(Role::where('name', $role->name)->where('guard_name', 'api')->first());


        return response()->json([
            'status' => 'success',
            'role' => $role->name,
            'message' => 'Cargo atualizado com sucesso!'
        ]);
    }



    public function togglePermission(Request $request, User $user)
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::where('name', $request->input('permission'))
            ->where('guard_name', 'api')
            ->first();

        if (!$permission) {
            return response()->json(['error' => 'Permissão não encontrada'], 404);
        }

        if ($user->hasPermissionTo($permission, 'api')) {
            $user->revokePermissionTo($permission);
            $status = false;
        } else {
            $user->givePermissionTo($permission);
            $status = true;
        }

        return response()->json(['status' => $status]);
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'demitido' ? 'ativo' : 'demitido';
        $user->save();

        return response()->json([
            'status' => $user->status,
            'message' => $user->status === 'ativo' ? 'Usuário reativado!' : 'Usuário demitido!'
        ]);
    }
}
