@extends('layouts.app')

@section('content')
    <!-- Cabeçalho -->
    <section class="max-w-2xl mx-auto mb-8">
        <div id="cards" class="flex flex-col gap-5 opacity-0 translate-y-4 transition-all duration-700 mb-6">
            <div class="flex flex-row justify-between items-center">
                <!-- Breadcrumb -->
                <div class="flex flex-row justify-start items-center gap-4">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/">Dashboard</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">

                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/config">Configurações</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/config/applications">Gerenciar Apl..</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold"> Criar aplicativo</p>
                </div>
            </div>
        </div>

        <h2 class="text-sm font-medium text-gray-500 px-1 opacity-0 translate-y-4 transition-all duration-700">Criar
            Aplicativo</h2>


        <!-- Formulário -->
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-6 border border-gray-200 ">

            @if ($errors->any())
                <div class="mb-4 p-4 rounded bg-red-100 text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Nome -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nome *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                        required>
                </div>

                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Descrição</label>
                    <textarea name="description" rows="3"
                        class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <!-- Ícone -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Ícone (opcional)</label>
                    <input type="file" name="icon"
                        class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>

                <!-- Link de Redirecionamento -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Link de Redirecionamento *</label>
                    <input type="url" name="link_redirect" value="{{ old('link_redirect') }}"
                        class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                        required>

                    <small class="text-gray-500 mb-2">Ex: https://aplicativo.taiksu.com.br/callback </small>
                </div>

                <!-- Permissões -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Permissões</label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($roles as $role)
                            <div class="flex items-center justify-between w-full mb-2">
                                <span class="text-gray-700">{{ $role->name }}</span>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                        class="sr-only peer"
                                        {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}>
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
                    <small class="text-gray-500">Selecione as permissões que esse aplicativo precisa para ser exibido no
                        painel.</small>
                </div>



                <!-- Botões -->
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition cursor-pointer">
                        Cria aplicação
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
