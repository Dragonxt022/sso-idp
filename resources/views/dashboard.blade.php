@extends('layouts.app')

@section('content')
    <!--Foto, nome e notificações-->
    <div id="user-bar" class="opacity-0 translate-y-4 transition-all duration-700">
        <div class="w-full flex flex-row justify-between items-center">
            <div id="perfil" class="flex flex-row justify-start items-center gap-5 mb-4 cursor-pointer group">

                <div class="w-12 h-12 rounded-full overflow-hidden shadow-xl">
                    <img src="{{ asset('frontend/profiles/' . $user->profile_photo_path) }}"
                        class="w-full h-full object-cover group-hover:scale-115 transition-transform duration-700"
                        alt="Foto de {{ $user->name }}">
                </div>

                <div class="text-green-700 text-xl font-semibold group-hover:text-green-600 transition-colors duration-300">
                    Olá, {{ $user->name }}
                </div>
            </div>
            <div>
                @if ($user->hasRole('Franqueadora'))
                    <a href="{{ route('config.index') }}">
                        <img src="{{ asset('frontend/img/settings.png') }}"
                            class="h-6 w-6 hover:cursor-pointer hover:scale-110 hover:rotate-180 transition-transform duration-300" />
                    </a>
                @endif
            </div>
        </div>

        <!--Barra de pesquisa-->
        <div class="relative mt-4 mb-6">
            <img src="{{ asset('frontend/img/pesquisa.png') }}"
                class="w-5 h-5 absolute inset-y-0 left-3 my-auto pointer-events-none">
            <input type="search" placeholder="Pesquisar aplicativo"
                class="w-full bg-white pl-10 p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md hover:ring-green-200 transition-all duration-300"
                id="search" onkeyup="filtraApps()">
        </div>
    </div>

    <div id="cards"
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 opacity-0 translate-y-4 transition-all duration-700">

        <a href="https://login.taiksu.com.br/?redirect_uri=https://admin.taiksu.com.br/callback">
            <div
                class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/erp.png') }}" class="w-12 h-12 mx-auto mb-4">
                <p class="nome text-lg text-gray-600 text-center">ERP Taiksu</p>
            </div>
        </a>

        <!-- Painel da franqueadora -->
        @if ($user->hasRole('Franqueadora'))
            <a href="https://login.taiksu.com.br/?redirect_uri=https://franqueadora.taiksu.com.br/callback">
                <div
                    class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center">
                    <img src="{{ asset('frontend/img/apps/backoffice.png') }}" class="w-12 h-12 mx-auto mb-4">
                    <p class="nome text-lg text-gray-600 text-center">Backoffice</p>
                </div>
            </a>
        @endif

        <!-- Atualmente manda pro ERP, daqui um tempo vai ser loja dedicada-->
        <a href="https://admin.taiksu.com.br/franqueado/pedidos">
            <div
                class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/loja.png') }}" class="w-12 h-12 mx-auto mb-4">
                <p class="nome text-lg text-gray-600 text-center">Loja</p>
            </div>
        </a>


        <!-- Esta aplicação está em deenvolvimento-->
        <a>
            <div class="bg-white rounded-xl px-6 py-8 group transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/curriculos.png') }}" class=" w-12 h-12 mx-auto mb-4 opacity-50">
                <p class="nome text-lg text-gray-600 text-center">Currículos</p>
            </div>
        </a>

        <!-- Aqui tá top-->
        <a href="https://login.taiksu.com.br/?redirect_uri=https://vistoria.taiksu.com.br/callback">
            <div
                class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/vistorias.png') }}" class="w-12 h-12 mx-auto mb-4">
                <p class="nome text-lg text-gray-600 text-center">Vistoria</p>
            </div>
        </a>

        @if ($user->hasRole('Franqueadora'))
        <a href="https://mail.hostinger.com">
            <div
                class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/mail.png') }}" class="w-12 h-12 mx-auto mb-4">
                <p class="nome text-lg text-gray-600 text-center">E-mail</p>
            </div>
        </a>
        @endif

        <a href="https://arquivos.taiksu.com.br/s/P5nypCADKccgcib">
            <div
                class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/midia.png') }}" class="w-12 h-12 mx-auto mb-4">
                <p class="nome text-lg text-gray-600 text-center">Mídias</p>
            </div>
        </a>

        <a href="#">
            <div
                class="bg-white rounded-xl px-6 py-8 group transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/consumo.png') }}" class="w-12 h-12 mx-auto mb-4 opacity-50">
                <p class="nome text-lg text-gray-600 text-center">Consumo</p>
            </div>
        </a>

        <a href="#">
            <div
                class="bg-white rounded-xl px-6 py-8 group transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/equipe.png') }}" class="w-12 h-12 mx-auto mb-4 opacity-50">
                <p class="nome text-lg text-gray-600 text-center">Equipe</p>
            </div>
        </a>

        <a href="https://admin.taiksu.com.br/franqueado/supervisao-residos">
            <div
                class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center">
                <img src="{{ asset('frontend/img/apps/lixo.png') }}" class="w-12 h-12 mx-auto mb-4">
                <p class="nome text-lg text-gray-600 text-center">Resíduos</p>
            </div>
        </a>

    </div>
@endsection
