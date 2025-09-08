@extends('layouts.app')

@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-5">

            <!-- Cabeçalho -->
            <div class="flex flex-row justify-between items-center">
                <!-- Breadcrumb -->
                <div class="hidden md:flex flex-row justify-start items-center gap-4">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/">Dashboard</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold">Equipe</p>
                </div>

                <!-- Botão Novo Aplicativo -->
                <a class="w-full md:w-auto" href="/equipe/create">
                    <div class="w-full md:w-auto justify-center group rounded-lg border border-gray-200 px-4 py-2 bg-white flex flex-row items-end gap-2 hover:cursor-pointer transition-all duration-300">
                        <img src="{{ asset('frontend/img/add.png') }}" class="h-5">
                        <p class="text-gray-500 text-sm leading-tight font-medium group-hover:text-green-700 transition duration-200 ease-in">
                            Cadastrar colaborador
                        </p>
                    </div>
                </a>
            </div>

            <!-- Cards de Usuários card inteiro deve ser clicável pra abrir tela de info -->
            <div class="flex flex-col gap-4 mb-16">
                @if ($users->isEmpty())
                    <div class="text-center text-gray-500 py-10">
                        Nenhum colaborador encontrado.
                    </div>
                @else
                    @foreach ($users as $user)
                        <div id="user-{{ $user->id }}"
                            class="bg-white border border-gray-200 rounded-xl p-6 hover:border-gray-100 hover:shadow-xl transition duration-300 flex flex-row items-center justify-between cursor-pointer group">

                            <div class="flex flex-row items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center">
                                    <img src="{{ $user->profile_photo_path ? asset('frontend/profiles/' . $user->profile_photo_path) : asset('frontend/img/user.png') }}"
                                        onerror="this.onerror=null;this.src='{{ asset('frontend/img/user.png') }}';"
                                        class="w-12 h-12 rounded-full group-hover:scale-105 group-hover:shadow-md transition-all duration-700" />
                                </div>
                                <div class="text-left">
                                    <h3 class="text-md font-medium text-gray-700">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>

                            <img src="{{ asset('frontend/img/seta.png') }}" class="w-6 h-6">
                        </div>
                    @endforeach
                @endif
            </div>



        </div>
    </section>
@endsection

<script>
    // Pega o id de cada card gerado pelo forEach e adiciona o clique para redirecionar para página do colaborador
    document.addEventListener('DOMContentLoaded', function() {
        const userCard = document.querySelectorAll('[id^="user-"]');
        userCard.forEach(card => {
            card.addEventListener('click', function() {
                window.location.href = `/equipe/colaborador/${card.id.split('-')[1]}`;
            });
        });
    });
</script>
