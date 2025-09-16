@extends('layouts.app')

@section('content')
    <section class="max-w-2xl mx-auto">

        <div id="cards" class="flex flex-col gap-4 opacity-0 translate-y-4 transition-all duration-700">

            <!-- Título + Botões alinhados -->
            <div class="hidden md:flex flex-row justify-between items-center">
                <!-- Breadcrumb -->
                <div class="flex flex-row justify-start items-center gap-2">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/config">Configurações</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold">Auditoria de Usuário</p>
                </div>
                <img id="sso-icon" class="h-6 opacity-0 hover:opacity-100 transition-all duration-600 cursor-pointer"
                    src="{{ asset('frontend/img/key.png') }}">
            </div>

            {{-- Cards resumo --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                @foreach ($stats as $action => $total)
                    <div class="bg-white rounded-lg shadow p-3 flex flex-col">
                        <span class="text-gray-500 capitalize text-sm">{{ str_replace('_', ' ', $action) }}</span>
                        <span class="text-2xl font-bold">{{ $total }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Card comparação mês --}}
            <div class="bg-white rounded-lg shadow p-3 mt-2 flex flex-col items-center">
                <span class="text-gray-500 text-sm">Acessos este mês</span>
                <span class="text-2xl font-bold">{{ $compare['this'] }}</span>
                <p class="text-xs text-gray-600">
                    Mês passado: {{ $compare['last'] }}
                    (<span class="{{ $compare['percent'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $compare['percent'] }}%
                    </span>)
                </p>
            </div>

            <canvas id="accessChart" class="mt-2 bg-white rounded-lg shadow p-3"></canvas>

            <!-- Contador de resultados + ícone filtro -->
            <div class="flex justify-between items-center mt-4">
                <span id="results-count" class="text-sm text-gray-600">Carregando...</span>

                <!-- Ícone filtro -->
                <button id="toggle-filters" class="flex items-center gap-1 text-gray-600 hover:text-green-700 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M3 4a1 1 0 011-1h12a1 1 0 01.8 1.6l-4.8 6V15a1 1 0 01-1.447.894l-2-1A1 1 0 017 14V10.6L2.2 5.6A1 1 0 013 4z" />
                    </svg>
                    <span class="hidden sm:inline">Filtros</span>
                </button>
            </div>

            <!-- Filtros -->
            <div id="filters-panel" class="flex flex-wrap gap-2 mt-2 hidden">
                <input type="text" id="filter-name" placeholder="Pesquisar por nome"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md flex-1 text-sm">
                <select id="filter-action"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md text-sm">
                    <option value="">Todas ações</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="change_password">Alteração de senha</option>
                    <option value="reset_password">Reset de senha</option>
                </select>
                {{-- <input type="text" id="filter-ip" placeholder="Pesquisar por IP"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md text-sm"> --}}
                <input type="date" id="filter-start"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md text-sm">
                <input type="date" id="filter-end"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md text-sm">
                <button id="btn-filter"
                    class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Filtrar</button>
                <button id="btn-export"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Exportar CSV</button>
            </div>



            <!-- Cards de logs -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow text-sm" id="logs-table">
                    <thead class="bg-green-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-2 text-left">Usuário</th>
                            <th class="px-4 py-2 text-left">Cidade</th>
                            <th class="px-4 py-2 text-left">Ação</th>
                            <th class="px-4 py-2 text-left">Descrição</th>
                            <th class="px-4 py-2 text-left">IP</th>
                            <th class="px-4 py-2 text-center">Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody id="logs-cards"></tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Modal detalhado -->
    <div id="log-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow p-6 max-w-md w-full relative">
            <button id="close-modal" class="absolute top-2 right-2 text-gray-600">&times;</button>
            <h2 class="text-xl font-bold mb-4">Detalhes do Log</h2>
            <div id="modal-content"></div>
        </div>
    </div>

    {{-- Controle de listagem de log --}}
    <script>
        const toggleFilters = document.getElementById('toggle-filters');
        const filtersPanel = document.getElementById('filters-panel');

        toggleFilters.addEventListener('click', () => {
            filtersPanel.classList.toggle('hidden');
        });


        document.addEventListener('DOMContentLoaded', () => {
            const logsContainer = document.getElementById('logs-cards');
            const modal = document.getElementById('log-modal');
            const modalContent = document.getElementById('modal-content');
            const closeModal = document.getElementById('close-modal');

            let page = 1;
            let loading = false;
            let lastPage = false;
            let filters = {};

            // Listener do botão export
            document.getElementById('btn-export').addEventListener('click', () => {
                const params = new URLSearchParams(filters); // agora filters existe
                window.location.href = "{{ route('auditoria.export') }}?" + params.toString();
            });

            function buildRow(log) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 cursor-pointer';

                tr.innerHTML = `
                    <td class="px-4 py-2 font-medium">${log.user?.name ?? 'N/A'}</td>
                    <td class="px-4 py-2">${log.user?.unidade?.cidade ?? 'N/A'}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 rounded font-semibold text-xs
                            ${log.action === 'login' ? 'bg-green-100 text-green-700' : ''}
                            ${log.action === 'logout' ? 'bg-red-100 text-red-700' : ''}
                            ${log.action === 'change_password' ? 'bg-blue-100 text-blue-700' : ''}
                            ${log.action === 'reset_password' ? 'bg-yellow-100 text-yellow-700' : ''}">
                            ${log.action}
                        </span>
                    </td>
                    <td class="px-4 py-2 truncate max-w-xs">${log.description ?? '-'}</td>
                    <td class="px-4 py-2">${log.ip_address ?? '-'}</td>
                    <td class="px-4 py-2">${new Date(log.created_at).toLocaleString()}</td>
                `;

                tr.addEventListener('click', () => openModal(log));
                return tr;
            }


            async function loadLogs(reset = false) {
                if (loading || lastPage) return;
                loading = true;

                if (reset) {
                    logsContainer.innerHTML = ''; // tbody vazio
                    page = 1;
                    lastPage = false;
                }

                const params = new URLSearchParams({
                    ...filters,
                    page
                });
                const url = "{{ route('auditoria.fetch') }}?" + params.toString();
                const res = await fetch(url);
                const data = await res.json();

                if (page === 1 && data.total !== undefined) {
                    document.getElementById('results-count').textContent =
                        `${data.total} resultado${data.total !== 1 ? 's' : ''} encontrado${data.total !== 1 ? 's' : ''}`;
                }

                data.data.forEach(log => logsContainer.appendChild(buildRow(log)));

                if (!data.next_page_url) lastPage = true;
                page++;
                loading = false;
            }


            function openModal(log) {
                modalContent.innerHTML = `
            <p><strong>Usuário:</strong> ${log.user?.name ?? 'N/A'}</p>
            <p><strong>Cidade:</strong> ${log.user?.unidade?.cidade ?? 'N/A'}</p>
            <p><strong>Ação:</strong> ${log.action}</p>
            <p><strong>Descrição:</strong> ${log.description ?? '-'}</p>
            <p><strong>IP:</strong> ${log.ip_address ?? '-'}</p>
            <p><strong>Navegador:</strong> ${log.user_agent ?? '-'}</p>
            <p><strong>Data/Hora:</strong> ${new Date(log.created_at).toLocaleString()}</p>`;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            closeModal.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            // filtros
            document.getElementById('btn-filter').addEventListener('click', () => {
                filters = {
                    name: document.getElementById('filter-name').value,
                    action: document.getElementById('filter-action').value,
                    ip: document.getElementById('filter-ip') ? document.getElementById('filter-ip')
                        .value : '',
                    start_date: document.getElementById('filter-start').value,
                    end_date: document.getElementById('filter-end').value
                };
                loadLogs(true);
            });

            // scroll infinito
            window.addEventListener('scroll', () => {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
                    loadLogs();
                }
            });

            loadLogs();
        });
    </script>

    {{-- Grafico de acessos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hourly = @json($hourly);
            const actions = [...new Set(hourly.map(h => h.action))];
            const hours = [...Array(24).keys()];
            const datasets = actions.map(action => ({
                label: action,
                data: hours.map(h => {
                    const row = hourly.find(r => r.hour === h && r.action === action);
                    return row ? row.total : 0;
                }),
                fill: false,
                borderWidth: 2
            }));

            new Chart(document.getElementById('accessChart'), {
                type: 'line',
                data: {
                    labels: hours.map(h => h + ":00"),
                    datasets
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
