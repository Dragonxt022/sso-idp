<?php

namespace App\Http\Controllers;

use App\Models\InforUnidade;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserEquipeController extends Controller
{
    /**
     * Exibe a lista de usuários da equipe com base na role do usuário autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('Franqueadora', 'api')) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'Franqueado')->where('guard_name', 'api');
            })->get();
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

    /**
     * Exibe o formulário para criar um novo colaborador.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Exibe o formulário para editar um colaborador existente.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $excludedRoles = ['Franqueadora', 'Desenvolvedor', 'Fornecedor', 'Franqueado'];

        $roles = Role::where('guard_name', 'api')
            ->whereNotIn('name', $excludedRoles)
            ->get();

        $foto = $user->profile_photo_path
            ? asset('frontend/profiles/' . $user->profile_photo_path)
            : asset('frontend/img/user.png');

        $authUser = Auth::user();
        $authRole = $authUser->roles->pluck('name')->first();

        return view('equipe.edit', compact('user', 'roles', 'foto', 'authRole'));
    }

    /**
     * Armazena um novo colaborador no banco de dados e notifica a API de RH.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

            // Campos adicionais para API de RH
            'salario_base' => 'required|string|min:0',
            'adicional_insalubridade' => 'nullable|numeric|min:0',
            'gratificacao_gerente' => 'nullable|numeric|min:0',
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
            $user->syncPermissions($permissions);
        }

        // Envia os dados para a API de RH
        try {
            $response = Http::post('https://rh.taiksu.com.br/salario', [
                'user_id' => $user->id,
                'salario_base' => $validated['salario_base'],
                'adicional_insalubridade' => $validated['adicional_insalubridade'] ?? 0,
                'adicional_noturno' => $validated['adicional_noturno'] ?? 0,
                'token_rh' => '7d6e3402-1ad7-42c0-ab2a-6d3a70395a71', // Token fixo do RH para autenticação
            ]);

            if (!$response->successful()) {
                Log::warning('Falha ao criar colaborador no RH', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao enviar colaborador para API de RH: ' . $e->getMessage());
        }

        return redirect()->route('equipe.index')->with('success', 'Colaborador criado com sucesso!');
    }


    /**
     * Gera um PIN numérico único de 4 dígitos.
     *
     * @param  int  $length
     * @return int
     */
    protected function generateUniquePin($length = 4)
    {
        do {
            $pin = mt_rand(1000, 9999);
        } while (User::where('pin', $pin)->exists());

        return $pin;
    }

    /**
     * Exibe os detalhes de um usuário específico em formato JSON.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Atualiza os dados de um colaborador existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        Log::info('Dados recebidos no update:', $request->all());

        $authUser = Auth::user();

        $validated = $request->validate([
            'id' => 'required|numeric|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'cpf' => 'required|string|max:14',
            'telefone' => 'required|string|max:15',
            'profile_photo_path' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'role_id' => 'nullable|exists:roles,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
            'password' => 'nullable|string|min:8|confirmed',
            'salario_base' => 'nullable|string|min:0',
        ]);

        $user = User::findOrFail($validated['id']);

        // Upload de imagem
        $photoPath = ImageService::handleUpload(
            $request->file('profile_photo_path'),
            $user->profile_photo_path,
            'frontend/profiles'
        );
        $photoName = $photoPath ? basename($photoPath) : $user->profile_photo_path;

        // Atualiza dados do usuário
        $validated['telefone'] = $validated['telefone'] ?? $user->telefone;

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'telefone' => $validated['telefone'],
            'profile_photo_path' => $photoName,
            'password' => $request->filled('password') ? bcrypt($validated['password']) : $user->password,
        ]);

        // Atualiza papel e permissões
        if (!empty($validated['role_id'])) {
            // Se um novo cargo foi enviado, atualiza
            $role = Role::where('id', $validated['role_id'])
                ->where('guard_name', 'api')
                ->first();

            if ($role) {
                $user->syncRoles([$role]);
            }
        } else {
            // Se não enviou role_id, mantém o papel atual
            $currentRole = $user->roles()->first();
            if ($currentRole) {
                $user->syncRoles([$currentRole]);
            }
        }


        if ($request->filled('permission_ids')) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])
                ->pluck('name')
                ->toArray();
            $user->syncPermissions($permissions);
        }

        // Atualizador salário na API de RH
        $salario = $user->salario_base ?? 0;

        if (!empty($validated['salario_base'])) {
            $salario = str_replace(['R$', ' ', '.'], '', $validated['salario_base']);
            $salario = str_replace(',', '.', $salario);
        }

        // Busca token do usuário logado
        $token = $authUser->tokens()->where('revoked', false)->latest()->first()?->plain_text_token;

        if (!$token) {
            return redirect()->back()->with('error', 'Não foi possível obter token de autenticação.');
        }
        // Atualizar o salário na API de RH
        try {
            $response = Http::withToken($token)
                ->post("https://rh.taiksu.com.br/salario/{$user->id}", [
                    'salario_base' => $salario,
                ]);

            if (!$response->successful()) {
                // API retornou erro, avisa usuário e não continua a atualização do salário
                return redirect()->back()->with('error', 'Erro ao atualizar salário na API externa: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao conectar com API externa: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Colaborador atualizado com sucesso!');
    }

    /**
     * Remove um usuário do banco de dados.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Exibe a página de perfil de um colaborador para gerenciamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\View\View
     */
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

    /**
     * Atualiza a role de um usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Adiciona ou remove uma permissão de um usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Alterna o status de um usuário entre 'ativo' e 'demitido'.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'demitido' ? 'ativo' : 'demitido';
        $user->save();

        return response()->json([
            'status' => $user->status,
            'message' => $user->status === 'ativo' ? 'Usuário reativado!' : 'Usuário demitido!'
        ]);
    }

    /**
     * Gera um novo PIN para o usuário.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function regeneratePin(User $user)
    {
        // 🔹 Se você ainda quiser alguma checagem (por exemplo, só permitir que o próprio usuário regenere o próprio PIN),
        // pode colocar algo como:
        // if (Auth::id() !== $user->id) {
        //     return response()->json(['error' => 'Acesso negado'], 403);
        // }

        // Gera e salva novo PIN sem restrição de role
        $user->pin = $this->generateUniquePin();
        $user->save();

        return response()->json([
            'message' => 'PIN regenerado com sucesso!',
            'pin'     => $user->pin,
        ]);
    }

    // Atualizar unidade do usuário
    public function updateUnidade(Request $request, $id)
    {
        $request->validate([
            'unidade_id' => 'required|exists:infor_unidade,id',
        ]);

        $user = User::findOrFail($id);
        $user->unidade_id = $request->unidade_id;
        $user->save();

        return response()->json([
            'message' => 'Unidade atualizada com sucesso!',
            'unidade_nome' => $user->unidade->cidade ?? 'Sem unidade'
        ]);
    }
}
