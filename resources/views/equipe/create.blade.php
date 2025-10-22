@extends('layouts.app')
@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-5 mb-8 opacity-0 translate-y-4 transition-all duration-700">

            <!-- Cabeçalho -->
            <div class="flex flex-row justify-between items-center">
                <!-- Breadcrumb -->
                <div class="hidden md:flex flex-row justify-start items-center gap-4">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="{{ route('equipe.index') }}">Equipe</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold">Cadastrar colaborador</p>
                </div>
            </div>

            <div class="flex flex-row justify-center items-center py-2 px-8">
                @if ($errors->any())
                    <div class="mb-4 p-4 rounded bg-red-100 text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('equipe.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Foto do Colaborador -->
                    <div>
                        <div class="flex items-center py-4">

                            <!-- Upload de imagem -->
                            <label for="profile_photo_input" class="block font-semibold text-gray-700 mb-2">
                                <img class="h-32  object-cover rounded-full cursor-pointer hover:scale-105 transition-all duration-300"
                                    id="preview" src="{{ asset('frontend/img/upload_img.png') }}" />
                            </label>
                            <input type="file" name="profile_photo_path" id="profile_photo_input" accept="image/*"
                                class="hidden" />
                        </div>

                    </div>
            </div>

            <div class="flex flex-col gap-8 items-left  bg-white rounded-xl py-6 px-8 border border-gray-200">
                <div class="w-full flex flex-row items-center justify-between">
                    <div class="flex flex-row items-center gap-2">
                        <img src="{{ asset('frontend/img/cargo.png') }}" class="w-6 h-6">
                        <h3 class="text-md font-semibold text-gray-600">Cargo</h3>
                    </div>
                    <!-- Select dinâmico de roles -->
                    <select name="role_id">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <hr class="border-t border-gray-200 w-full">

                <div class="w-full flex flex-row items-center justify-between">
                    <div class="flex flex-row items-center gap-2">
                        <img src="{{ asset('frontend/img/money.png') }}" class="w-6 h-6">
                        <h3 class="text-md font-semibold text-gray-600">Salário Bruto</h3>
                    </div>
                    <input type="text" name="salario_base" class="bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                </div>

                <p class="text-xs text-gray-400 text-left">Adicione o salário bruto recebido, aquele registrado em carteira sem contar os benefícios.</p>
            </div>



            <div class="flex flex-col bg-white rounded-xl border border-gray-200 p-8 gap-4">
                <!-- Nome Completo -->
                <div class="w-full">
                    <label class="text-sm font-medium text-gray-500">Nome Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="w-full">
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Telefone -->
                <div class="w-full">
                    <label class="text-sm font-medium text-gray-500">Telefone</label>
                    <input type="text" name="telefone" class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                </div>

                <!-- CPF -->
                <div>
                    <label class="text-sm font-medium text-gray-500">CPF *</label>
                    <input type="text" name="cpf" value="{{ old('cpf') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                    @error('cpf')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo senha -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Senha</label>
                    <input type="password" id="password" name="password"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none"
                        required>
                </div>

                <!-- Confirmar Senha -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Confirmar Senha</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                    @error('password_confirmation')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Botão de Envio -->
            <div>
                <button type="submit"
                    class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition cursor-pointer">
                    Criar Colaborador
                </button>
            </div>
        </form>
    </section>

    <!-- Script de pré-visualização -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('profile_photo_input');
            const preview = document.getElementById('preview');

            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result; // ✅ Atualiza a imagem
                    };

                    reader.readAsDataURL(file);
                } else {
                    preview.src = '{{ asset('frontend/img/user.png') }}'; // Imagem padrão
                }
            });
        });
    </script>

@endsection
