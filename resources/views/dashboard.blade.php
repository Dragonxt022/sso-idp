@extends('layouts.app')

@section('content')
    <!--Foto, nome e notificações-->
    <div id="user-bar" class="opacity-0 translate-y-4 transition-all duration-700">
        <div class="w-full flex flex-row justify-between items-center">
            <div id="perfil" class="flex flex-row justify-start items-center gap-5 mb-4 cursor-pointer group">

                <div class="w-12 h-12 rounded-full overflow-hidden shadow-xl">
                    <img src="{{ $user->profile_photo_path ? asset('frontend/profiles/' . $user->profile_photo_path) : asset('frontend/img/user.png') }}"
                        onerror="this.onerror=null;this.src='{{ asset('frontend/img/user.png') }}';"
                        class="w-12 h-12 rounded-full group-hover:scale-105 group-hover:shadow-md transition-all duration-700" />
                </div>

                <div class="text-green-700 text-xl font-semibold group-hover:text-green-600 transition-colors duration-300">
                    Olá, {{ $user->name }}

                </div>
            </div>
            <div>
                @if ($user->hasRole('Desenvolvedor'))
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

        @forelse($applications as $app)
            <a href="{{ $app->active ? $app->link_redirect ?? '#' : '#' }}"
                class="card-link {{ $app->active ? '' : 'opacity-40 pointer-events-none' }}">
                <div
                    class="bg-white rounded-xl px-6 py-8 group shadow-sm hover:shadow-xl transition duration-300 justify-center text-center">

                    @if ($app->icon)
                        <img src="{{ asset($app->icon) }}"
                            class="w-12 h-12 mx-auto mb-4 {{ $app->active ? '' : 'opacity-40' }}"
                            alt="{{ $app->name }}">
                    @else
                        <div
                            class="w-12 h-12 mx-auto mb-4 bg-gray-100 rounded flex items-center justify-center {{ $app->active ? '' : 'opacity-40' }}">
                            <span class="text-gray-400">{{ strtoupper(substr($app->name, 0, 1)) }}</span>
                        </div>
                    @endif

                    <p class="nome text-lg text-gray-600 text-center {{ $app->active ? '' : 'opacity-40' }}">
                        {{ $app->name }}
                    </p>
                </div>
            </a>
        @empty
            <p class="text-gray-500">Nenhum aplicativo disponível para o seu perfil.</p>
        @endforelse
    </div>
@endsection
