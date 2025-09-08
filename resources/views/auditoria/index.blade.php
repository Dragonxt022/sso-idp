@extends('layouts.app')

@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-5 opacity-0 translate-y-4 transition-all duration-700">

            <div class="flex flex-row justify-between items-center">
                <!-- Breadcrumb -->
                <div class="flex flex-row justify-start items-center gap-4">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/">Dashboard</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold">Auditoria</p>
                </div>

                <img id="sso-icon" class="h-6 opacity-0 hover:opacity-30 transition-all duration-700 cursor-pointer"
                    src="{{ asset('frontend/img/key.png') }}">
            </div>

            <!-- Gerenciar Aplicativos -->
            <a href="{{ route('applications.index') }}">
                <div id="alterar-senha"
                    class="flex flex-row items-center justify-between bg-white rounded-xl py-8 px-8 border border-gray-200 text-gray-600 hover:text-green-700 transition-all duration-300 group cursor-pointer">
                    <div class="flex flex-row items-center gap-2">
                        <img src="{{ asset('frontend/img/grid_view.png') }}"
                            class="w-6 h-6 group-hover:scale-110 transition-all duration-300">
                        <h3 class="text-md font-semibold">Gerenciar aplicativos</h3>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                </div>
            </a>

            <!-- Gerenciar Aplicativos -->
            <a href="{{ route('applications.index') }}">
                <div id="alterar-senha"
                    class="flex flex-row items-center justify-between bg-white rounded-xl py-8 px-8 border border-gray-200 text-gray-600 hover:text-green-700 transition-all duration-300 group cursor-pointer">
                    <div class="flex flex-row items-center gap-2">
                        <img src="{{ asset('frontend/img/apps/security.png') }}"
                            class="w-6 h-6 group-hover:scale-110 transition-all duration-300">
                        <h3 class="text-md font-semibold">Auditoria</h3>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                </div>
            </a>

        </div>
    </section>
@endsection
