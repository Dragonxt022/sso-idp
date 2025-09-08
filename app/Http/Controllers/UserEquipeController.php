<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserEquipeController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('Franqueadora')) {
            // Franqueadora só vê usuários que são Franqueados
            $users = User::role('Franqueado') // método do spatie
                ->with('unidade')
                ->get();
        } elseif ($authUser->hasRole('Franqueado')) {
            // Franqueado só vê colaboradores da sua unidade
            $users = User::role('Colaborador')
                ->with('unidade')
                ->where('unidade_id', $authUser->unidade_id)
                ->get();
        } else {
            // Nenhum acesso
            $users = collect();
        }

        return view('equipe.index', compact('users'));
    }

    public function create()
    {
        $permissions = Permission::where('name', '!=', 'Beckoffice')->get();
        return view('equipe.create', compact('permissions'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'profile_photo_path' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'O campo Nome Completo é obrigatório.',
            'name.max' => 'O Nome Completo não pode ter mais de 255 caracteres.',

            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',

            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'cpf.max' => 'O CPF não pode ultrapassar 14 caracteres.',

            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter entre 8 e 12 caracteres, incluindo maiúsculas, minúsculas, números e caracteres especiais',
            'password.confirmed' => 'A confirmação da senha não confere.',

            'permission_ids.*.exists' => 'Uma ou mais permissões selecionadas não são válidas.',
        ]);


        // 🔹 Limpa pontos, traços e vírgulas do CPF
        $validated['cpf'] = preg_replace('/[\.\-\,]/', '', $validated['cpf']);

        // Upload da foto
        $photoPath = ImageService::handleUpload($request->file('profile_photo_path'), null, 'frontend/profiles');

        // Extrai só o nome do arquivo
        $photoName = $photoPath ? basename($photoPath) : null;
        // Cria o usuário
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'unidade_id' => auth()->user()->unidade_id,
            'profile_photo_path' => $photoName, // salva somente o nome
            'password' => bcrypt($validated['password']), // senha digitada pelo usuário
            'pin' => $this->generateUniquePin(), // gera um PIN aleatório
        ]);

        // Atribui role fixo Colaborador
        $user->assignRole('Colaborador');

        // Atribui permissões extras, se tiver
        if ($request->filled('permission_ids')) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->pluck('name')->toArray();
            $user->syncPermissions($permissions);
        }

        return redirect()->route('equipe.index')->with('success', 'Colaborador criado com sucesso!');
    }

    protected function generateUniquePin($length = 4)
    {
        do {
            // Gera um PIN numérico aleatório
            $pin = mt_rand(1000, 9999);
        } while (\App\Models\User::where('pin', $pin)->exists()); // Verifica duplicado

        return $pin;
    }


    public function show(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('Franqueadora')) {
            return response()->json($user->load('unidade'));
        } elseif ($authUser->hasRole('Franqueado') && $authUser->unidade_id == $user->unidade_id) {
            return response()->json($user->load('unidade'));
        }

        return response()->json(['error' => 'Acesso negado'], 403);
    }

    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();

        // Regra de edição
        if (
            $authUser->hasRole('Franqueadora') ||
            ($authUser->hasRole('Franqueado') && $authUser->unidade_id == $user->unidade_id)
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
            $authUser->hasRole('Franqueadora') ||
            ($authUser->hasRole('Franqueado') && $authUser->unidade_id == $user->unidade_id)
        ) {

            $user->delete();
            return response()->json(['message' => 'Usuário deletado']);
        }

        return response()->json(['error' => 'Acesso negado'], 403);
    }

    public function equipe(Request $request)
    {
        $user = $request->user();

        // Recupera todas as permissões do usuário
        $permissions = Permission::where('name', '!=', 'Beckoffice')->get();


        return view('equipe.index', compact('user', 'permissions'));
    }

    public function colaborador(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $foto = $user->profile_photo_path
            ? asset('frontend/profiles/' . $user->profile_photo_path)
            : asset('frontend/img/user.png');

        // Pegando todas as permissões
        $permissions = Permission::where('name', '!=', 'Beckoffice')->get();


        // Permissões do usuário
        $userPermissions = $user->getPermissionNames();

        return view('equipe.colaborador', compact('user', 'foto', 'permissions', 'userPermissions'));
    }

    // Gerar novo PIN via AJAX
    public function regeneratePin(Request $request, User $user)
    {
        // Gera PIN único
        do {
            $pin = mt_rand(1000, 9999);
        } while (\App\Models\User::where('pin', $pin)->exists());

        $user->pin = $pin;
        $user->save();

        return response()->json(['pin' => $pin]);
    }

    public function togglePermission(Request $request, User $user)
    {
        $permissionName = $request->input('permission');

        // Limpa cache do Spatie para evitar inconsistências
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Busca a permissão com guard 'web'
        $permission = Permission::where('name', $permissionName)
            ->where('guard_name', 'web')
            ->first();

        if (!$permission) {
            return response()->json(['error' => "Permissão não encontrada"], 404);
        }

        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            $status = false;
        } else {
            $user->givePermissionTo($permission);
            $status = true;
        }

        return response()->json(['status' => $status]);
    }
}
