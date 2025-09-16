<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InforUnidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Services\ImageService;
use App\Mail\FranqueadoBemVindo;
use Illuminate\Support\Facades\Mail;

class FranqueadoController extends Controller
{

    public function index()
    {
        $franqueados = User::whereHas('roles', function ($query) {
            $query->where('name', 'Franqueado');
        })
        ->with('unidade')
        ->paginate(10);

        return response()->json([
            'status' => 'sucesso',
            'data' => $franqueados
        ], 200);
    }

    public function show($id)
    {
        // Busca usuário que tenha o role "Franqueado"
        $franqueado = User::where('id', $id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Franqueado');
            })
            ->with('unidade')
            ->first();

        if (!$franqueado) {
            return response()->json([
                'status' => 'erro',
                'message' => 'Franqueado não encontrado.'
            ], 404);
        }

        return response()->json([
            'status' => 'sucesso',
            'data' => $franqueado
        ], 200);
    }


    public function store(Request $request)
    {
        // 🔹 Validação dos dados recebidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'password' => 'required|string|min:8|confirmed',
            'unidade_id' => 'required|exists:infor_unidade,id',
            'profile_photo_path' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ], [
            // mensagens personalizadas
            'name.required' => 'O campo Nome Completo é obrigatório.',
            'name.max' => 'O Nome Completo não pode ter mais de 255 caracteres.',

            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',

            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'cpf.max' => 'O CPF não pode ultrapassar 14 caracteres.',

            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',

            'unidade_id.required' => 'O ID da unidade é obrigatório.',
            'unidade_id.exists' => 'A unidade informada não existe.',

            'profile_photo_path.image' => 'O arquivo deve ser uma imagem.',
            'profile_photo_path.mimes' => 'A imagem deve estar nos formatos png, jpg ou jpeg.',
            'profile_photo_path.max' => 'A imagem não pode ultrapassar 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'erro',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // 🔹 Limpa CPF
        $validated['cpf'] = preg_replace('/[\.\-\,]/', '', $validated['cpf']);

        // 🔹 Upload da foto
        $photoPath = ImageService::handleUpload($request->file('profile_photo_path'), null, 'frontend/profiles');
        $photoName = $photoPath ? basename($photoPath) : null;

        // 🔹 Cria o usuário
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'unidade_id' => $validated['unidade_id'],
            'profile_photo_path' => $photoName,
            'password' => Hash::make($validated['password']),
            'pin' => $this->generateUniquePin(),
            'status' => 'ativo',
        ]);

        // 🔹 Atribui role fixa "Franqueado"
        $user->assignRole(['Franqueado' => 'web']);

        // 🔹 Atribui permissões extras (se existirem)
        if (!empty($validated['permission_ids'])) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->pluck('name')->toArray();
            $user->syncPermissions($permissions);
        }

        // 🔹 Envia e-mail de boas-vindas
        Mail::to($user->email)->send(new FranqueadoBemVindo($user, $validated['password']));

        return response()->json([
            'status' => 'sucesso',
            'message' => 'Franqueado cadastrado com sucesso!',
            'data' => $user
        ], 201);
    }

    private function generateUniquePin()
    {
        do {
            $pin = rand(1000, 9999);
        } while (User::where('pin', $pin)->exists());

        return $pin;
    }
}
