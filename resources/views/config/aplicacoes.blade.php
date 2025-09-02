@extends('layouts.app')

@section('content')
    <section class="max-w-6xl mx-auto">
        <div id="cards" class="flex flex-col gap-5 opacity-0 translate-y-4 transition-all duration-700">

            <!-- Cabeçalho -->
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
                    <p class="capitalize text-gray-500 text-lg font-semibold"> Gerenciar Aplicativos</p>
                </div>
            </div>

            <!-- Subtítulo -->
            <h2 class="text-sm font-medium text-gray-500 px-1 -mb-4">Aplicativos Instalados</h2>

            <!-- Tabela de Aplicações -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">

                <!-- Botão Novo Aplicativo -->
                <div class="flex justify-end mb-4">
                    <a href="{{ route('applications.create') }}"
                        class="w-full flex items-center justify-center gap-2 text-white text-sm font-semibold bg-green-500 hover:bg-green-600 py-2 px-4 rounded-lg shadow-sm hover:shadow-none transition-all duration-300">
                        + Novo Aplicativo
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-600 text-sm font-semibold border-b">
                                <th class="px-4 py-2">Ícone</th>
                                <th class="px-4 py-2">Nome</th>
                                <th class="px-4 py-2">Descrição</th>
                                <th class="px-4 py-2">Link</th>
                                <th class="px-4 py-2 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 border-b">
                                        @if ($app->icon)
                                            <img src="{{ asset($app->icon) }}" class="w-8 h-8 rounded">
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b font-medium">{{ $app->name }}</td>
                                    <td class="px-4 py-3 border-b text-gray-600">{{ $app->description ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">
                                        <a href="{{ $app->link_redirect }}" target="_blank"
                                            class="text-green-600 hover:underline">
                                            Acessar
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 border-b">
                                        <div class="flex justify-center gap-2">
                                            {{-- <a href="{{ route('applications.edit', $app->id) }}"
                                                class="px-3 py-1 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600">
                                                Editar
                                            </a> --}}
                                            <form method="POST" action="{{ route('applications.destroy', $app->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600"
                                                    onclick="return confirm('Tem certeza que deseja remover este aplicativo?')">
                                                    Remover
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-6">
                                        Nenhum aplicativo instalado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
