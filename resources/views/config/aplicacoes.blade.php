@extends('layouts.app')

@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-5 opacity-0 translate-y-4 transition-all duration-700">

            <!-- Cabeçalho -->
            <div class="flex flex-row justify-between items-center">
                <!-- Breadcrumb -->
                <div class="flex flex-row justify-start items-center gap-4">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/config">Configurações</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold"> Gerenciar Aplicativos</p>
                </div>

                <!-- Botão Novo Aplicativo -->
                <div class="group rounded-lg border border-gray-200 px-[10px] py-2 bg-white flex flex-row items-end gap-2 hover:cursor-pointer transition-all duration-300">
                    <img src="{{ asset('frontend/img/add.png') }}" class="h-5">
                    <a href="{{ route('applications.create') }}" class="text-gray-500 text-sm leading-tight font-medium group-hover:text-green-700 transition duration-200 ease-in">
                        Adicionar aplicativo
                    </a>
                </div>
            </div>

            <!-- Cards de Aplicações -->
            <div>

                @if ($applications->isEmpty())
                    <p class="text-center text-gray-500 py-6">Nenhum aplicativo instalado.</p>
                @else
                    <div id="apps-grid" class="flex flex-col gap-6 mb-16">
                        @foreach ($applications as $app)
                            <div class="relative bg-white border border-gray-200 rounded-xl p-8 hover:border-gray-300 transition duration-300 flex flex-row items-center justify-between text-center cursor-move group {{ $app->active ? '' : 'opacity-40' }}"
                                data-id="{{ $app->id }}">

                                <div class="flex flex-row items-center gap-2">
                                    <!-- Ícone do aplicativo -->
                                    @if ($app->icon)
                                        <img src="{{ asset($app->icon) }}" class="w-8 h-8 rounded {{ $app->active ? '' : 'opacity-40' }}">
                                    @else
                                        <div class="w-12 h-12 flex items-center justify-center bg-gray-200 rounded mb-3 {{ $app->active ? '' : 'opacity-40' }}">
                                            <span class="text-gray-400 text-lg">—</span>
                                        </div>
                                    @endif
                                    <!-- Nome do aplicativo -->
                                    <h3 class="text-md font-medium text-gray-600 {{ $app->active ? '' : 'opacity-40' }}">{{ $app->name }}</h3>
                                </div>

                                <!-- Ativar e editar -->
                                <div class="flex flex-row justify-between items-center gap-4">
                                    <button type="button"
                                        class="toggle-active cursor-pointer w-11 h-6 rounded-full flex items-center p-0.5 {{ $app->active ? 'bg-green-500' : 'bg-gray-300' }}"
                                        data-id="{{ $app->id }}" title="{{ $app->active ? 'Desativar' : 'Ativar' }}">
                                        <div class="bg-white w-5 h-5 rounded-full transform {{ $app->active ? 'translate-x-5' : '' }} transition"></div>
                                    </button>

                                    <a href="{{ route('applications.edit', $app->id) }}"
                                        class="text-blue-500 hover:text-blue-600 transition">
                                        <img src="{{ asset('frontend/img/seta.png') }}" class="w-6 h-6">
                                    </a>
                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif
            </div>

        </div>
    </section>

    {{-- Sistema de mover o card --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const grid = document.getElementById("apps-grid");

        new Sortable(grid, {
            animation: 150,
            ghostClass: "bg-gray-100",
            onEnd: function() {
                let order = [];
                document.querySelectorAll("#apps-grid > div").forEach((el) => {
                    order.push(el.dataset.id);
                });

                fetch("{{ route('applications.updateOrder') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        order
                    })
                }).then(res => res.json()).then(data => {
                    console.log("Ordem salva:", data);
                });
            }
        });
    </script>

    {{-- Ativar e desativar --}}
    <script>
        document.querySelectorAll('.toggle-active').forEach(button => {
            button.addEventListener('click', function() {
                const appId = this.dataset.id;
                const toggleButton = this;
                const card = toggleButton.closest('.group');

                fetch(`/applications/${appId}/toggle-active`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Alterna visualmente o toggle
                        toggleButton.classList.toggle('bg-green-500', data.active);
                        toggleButton.classList.toggle('bg-gray-300', !data.active);
                        const ball = toggleButton.querySelector('div');
                        ball.classList.toggle('translate-x-5', data.active);

                        // Atualiza apenas a opacidade do card
                        card.classList.toggle('opacity-40', !data.active);
                    } else {
                        alert('Erro ao atualizar status');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erro ao atualizar status');
                });
            });
        });


    </script>


@endsection
