@extends('layouts.app')
@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-5 opacity-0 translate-y-4 transition-all duration-700">

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
                        <div class="flex items-center gap-4">

                            <!-- Upload de imagem -->
                            <label for="profile_photo_input" class="block font-semibold text-gray-700 mb-2 cursor-pointer p-8">
                                <img id="preview" src="{{ asset('frontend/img/upload_img.png') }}" class="h-24 w-24 rounded-full object-cover" />
                            </label>
                            <input type="file" name="profile_photo_path" id="profile_photo_input" accept="image/*" class="hidden" />
                        </div>
                    </div>
            </div>

            <div class="flex flex-col gap-4 items-center justify-between bg-white rounded-xl py-4 px-8 border border-gray-200">
                <div class="w-full flex flex-row items-center justify-between">
                    <div class="flex flex-row items-center gap-2">
                        <img src="{{ asset('frontend/img/cargo.png') }}" class="w-6 h-6">
                        <h3 class="text-md font-semibold text-gray-600">Cargo</h3>
                    </div>
                    <img src="{{ asset('frontend/img/breve_tag.png') }}" class="h-6">
                </div>
                <hr class="border-t border-gray-200 w-full">
                <div class="w-full flex flex-row items-center justify-between">
                    <div class="flex flex-row items-center gap-2">
                        <img src="{{ asset('frontend/img/money.png') }}" class="w-6 h-6">
                        <h3 class="text-md font-semibold text-gray-600">Salário</h3>
                    </div>
                    <img src="{{ asset('frontend/img/breve_tag.png') }}" class="h-6">
                </div>
            </div>

            <div class="flex flex-col bg-white rounded-xl p-8 gap-4">
                    <!-- Nome Completo -->
                    <div class="w-full">
                        <label class="text-sm font-medium text-gray-500">Nome Completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="w-full">
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <input type="email" name="name" value="{{ old('email') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>






            </div>

            <div class="flex flex-col gap-2 mt-2">
                <div class="text-sm font-medium text-gray-500 px-2">Permissões</div>

                <div id="permissoes"
                    class="flex flex-col items-center justify-between bg-white rounded-xl py-4 px-8 border border-gray-200 text-gray-600">

                        <div
                            class="flex flex-row items-center justify-between w-full pt-4 pb-4 hover:text-green-700 transition-all duration-300 cursor-pointer">
                            <span>Nome permissão</span>
                            <button type="button"
                                class="toggle-active w-11 h-6 rounded-full flex items-center p-0.5 cursor-pointer">
                                <div
                                    class="bg-white w-5 h-5 rounded-full transform">
                                </div>
                            </button>
                        </div>
                </div>
            </div>

            <div id="alterar-senha"
                class="flex flex-row items-center justify-between bg-white rounded-xl py-6 px-8 border border-gray-200 text-gray-600 hover:text-green-700 cursor-pointer">
                <div class="flex flex-row items-center gap-2">
                    <img src="{{ asset('frontend/img/senha.png') }}" class="w-6 h-6">
                    <h3 class="text-md font-semibold">Alterar senha</h3>
                </div>
                <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
            </div>

    </section>

    <!-- Cabeçalho -->
    <section class="max-w-2xl mx-auto mb-8">


            <!-- Formulário -->
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-6 border border-gray-200 ">

                    <!-- CPF -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">CPF *</label>
                        <input type="number" name="cpf" value="{{ old('cpf') }}"
                            class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                        @error('cpf')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Campo senha -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Senha *</label>
                        <input type="password" id="password" name="password"
                            class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                            required>
                        <small class="text-gray-500">A senha deve ter entre 8 e 12 caracteres, incluindo maiúsculas,
                            minúsculas, números e caracteres especiais.</small>
                    </div>

                    <!-- Confirmar Senha -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Confirmar Senha *</label>
                        <input type="password" name="password_confirmation"
                            class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                        @error('password_confirmation')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>



                    <!-- Permissões extras (chavinhas) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Permissões Adicionais</label>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($permissions as $permission)
                                <div class="flex items-center justify-between w-full mb-2">
                                    <span class="text-gray-700">{{ $permission->name }}</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}"
                                            class="sr-only peer"
                                            {{ in_array($permission->id, old('permission_ids', [])) ? 'checked' : '' }}>
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-green-500
                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                after:bg-white after:border-gray-300 after:border after:rounded-full
                after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                                        </div>
                                    </label>
                                </div>
                            @endforeach

                        </div>
                        <small class="text-gray-500">Estas permissões adicionais podem ser atribuídas ao
                            colaborador.</small>
                    </div>


                    <!-- Botão de Envio -->
                    <div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition cursor-pointer">
                            Criar Colaborador
                        </button>
                    </div>
                </form>
            </div>
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

                    reader.readAsDataURL(file);
                } else {
                    preview.src = '{{ asset('frontend/img/user.png') }}';
                }
            });
        });
    </script>
@endsection
