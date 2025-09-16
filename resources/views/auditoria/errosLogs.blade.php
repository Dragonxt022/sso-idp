@extends('layouts.app')

@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-4 opacity-0 translate-y-4 transition-all duration-700">

            <div class="hidden md:flex flex-row justify-between items-center">
                <div class="flex flex-row justify-start items-center gap-2">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/config">Configurações</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold">Auditoria de Erros</p>
                </div>
                <img id="sso-icon" class="h-6 opacity-0 hover:opacity-100 transition-all duration-600 cursor-pointer"
                    src="{{ asset('frontend/img/key.png') }}">
            </div>

            {{-- Cards de estatísticas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="bg-white rounded-lg shadow p-3 flex flex-col">
                    <span class="text-gray-500 capitalize text-sm">Total de Erros</span>
                    <span class="text-2xl font-bold">{{ $stats['total_erros'] ?? 0 }}</span>
                </div>
                <div class="bg-white rounded-lg shadow p-3 flex flex-col">
                    <span class="text-gray-500 capitalize text-sm">Erros nas Últimas 24h</span>
                    <span class="text-2xl font-bold">{{ $stats['erros_hoje'] ?? 0 }}</span>
                </div>
            </div>

            <canvas id="exceptionsChart" class="mt-2 bg-white rounded-lg shadow p-3"></canvas>

            <div class="flex justify-between items-center mt-4">
                <span id="results-count" class="text-sm text-gray-600">Carregando...</span>
                <button id="toggle-filters" class="flex items-center gap-1 text-gray-600 hover:text-green-700 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M3 4a1 1 0 011-1h12a1 1 0 01.8 1.6l-4.8 6V15a1 1 0 01-1.447.894l-2-1A1 1 0 017 14V10.6L2.2 5.6A1 1 0 013 4z" />
                    </svg>
                    <span class="hidden sm:inline">Filtros</span>
                </button>
            </div>

            <div id="filters-panel" class="flex flex-wrap gap-2 mt-2 hidden">
                <input type="text" id="filter-message" placeholder="Pesquisar por mensagem"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md flex-1 text-sm">
                <select id="filter-type"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md text-sm">
                    <option value="">Todos os tipos</option>
                    @foreach ($exceptionsByType as $type)
                        <option value="{{ $type->exception_type }}">{{ $type->exception_type }} ({{ $type->total }})
                        </option>
                    @endforeach
                </select>
                <input type="date" id="filter-start"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md text-sm">
                <input type="date" id="filter-end"
                    class="bg-white p-2 outline-none ring-1 ring-gray-200 focus:ring-green-500 rounded-md text-sm">
                <button id="btn-filter"
                    class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Filtrar</button>

                <button id="btn-clear-logs"
                    class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Limpar Todos os
                    Logs</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow text-sm" id="logs-table">
                    <thead class="bg-red-100 text-red-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-2 text-left">Usuário</th>
                            <th class="px-4 py-2 text-left">Mensagem</th>
                            <th class="px-4 py-2 text-left">Tipo</th>
                            <th class="px-4 py-2 text-left">URL</th>
                            <th class="px-4 py-2 text-left">Arquivo:Linha</th>
                            <th class="px-4 py-2 text-left">IP</th>
                            <th class="px-4 py-2 text-center">Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody id="logs-cards"></tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="log-modal" class="fixed inset-0 bg-white bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow p-6 max-w-4xl w-full h-5/6 relative flex flex-col">
            <button id="close-modal" class="absolute top-2 right-2 text-gray-600 text-xl font-bold">&times;</button>
            <h2 class="text-xl font-bold mb-4">Detalhes do Erro</h2>

            <button id="copy-error-btn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors duration-200 mb-4">
                Copiar Erro Completo
            </button>

            <div id="modal-content" class="flex-1 overflow-y-auto"></div>
        </div>
    </div>

    {{-- Script para a listagem e filtros --}}
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

            function buildRow(log) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 cursor-pointer';

                tr.innerHTML = `
                    <td class="px-4 py-2 font-medium">${log.user?.name ?? 'N/A'}</td>
                    <td class="px-4 py-2 font-medium truncate max-w-xs">${log.message}</td>
                    <td class="px-4 py-2 text-xs">${log.exception_type}</td>
                    <td class="px-4 py-2 text-xs truncate max-w-[100px]">${log.url}</td>
                    <td class="px-4 py-2 text-xs truncate max-w-[100px]">${log.file}:${log.line}</td>
                    <td class="px-4 py-2 text-xs">${log.ip ?? '-'}</td>
                    <td class="px-4 py-2 text-xs">${new Date(log.created_at).toLocaleString()}</td>
                `;

                tr.addEventListener('click', () => openModal(log));
                return tr;
            }

            async function loadLogs(reset = false) {
                if (loading || lastPage) return;
                loading = true;

                if (reset) {
                    logsContainer.innerHTML = '';
                    page = 1;
                    lastPage = false;
                }

                const params = new URLSearchParams({
                    ...filters,
                    page
                });
                const url = "{{ route('auditoria.erros.fetch') }}?" + params.toString();
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

            // Modal
            function openModal(log) {
                // Conteúdo original
                const modalContentHTML = `
                    <p><strong>Usuário:</strong> ${log.user?.name ?? 'N/A'}</p>
                    <p><strong>Tipo:</strong> ${log.exception_type}</p>
                    <p><strong>URL:</strong> ${log.url ?? 'N/A'}</p>
                    <p><strong>Método:</strong> ${log.method ?? 'N/A'}</p>
                    <p><strong>Arquivo:</strong> ${log.file}</p>
                    <p><strong>Linha:</strong> ${log.line}</p>
                    <p><strong>IP:</strong> ${log.ip ?? 'N/A'}</p>
                    <p><strong>Navegador:</strong> ${log.user_agent ?? 'N/A'}</p>
                    <p><strong>Data/Hora:</strong> ${new Date(log.created_at).toLocaleString()}</p>
                    <div class="mt-4 p-2 bg-gray-100 rounded-md overflow-x-auto text-sm">
                        <h3 class="font-semibold mb-2">Mensagem:</h3>
                        <pre class="whitespace-pre-wrap">${log.message}</pre>
                    </div>
                    <div class="mt-4 bg-gray-100 rounded-md text-sm">
                        <button onclick="toggleStack('stack-content')"
                                class="w-full text-left px-2 py-1 font-semibold hover:bg-gray-200 rounded flex justify-between items-center">
                            Stack Trace
                            <svg id="stack-icon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="stack-content" class="hidden p-2 overflow-y-auto max-h-64">
                            <pre class="whitespace-pre-wrap">${log.stack_trace}</pre>
                        </div>
                    </div>


                `;

                // Atualiza o conteúdo do modal
                modalContent.innerHTML = modalContentHTML;

                // Adiciona o evento de clique ao novo botão de copiar
                const copyBtn = document.getElementById('copy-error-btn');
                if (copyBtn) {
                    copyBtn.onclick = () => {
                        // Cria um texto formatado para ser copiado
                        const textToCopy = `
            Detalhes do Erro:
            Usuário: ${log.user?.name ?? 'N/A'}
            Tipo: ${log.exception_type}
            URL: ${log.url ?? 'N/A'}
            Método: ${log.method ?? 'N/A'}
            Arquivo: ${log.file}
            Linha: ${log.line}
            IP: ${log.ip ?? 'N/A'}
            Navegador: ${log.user_agent ?? 'N/A'}
            Data/Hora: ${new Date(log.created_at).toLocaleString()}

            Mensagem:
            ${log.message}

            Stack Trace:
            ${log.stack_trace}
                        `.trim();

                        // Usa a API Clipboard para copiar o texto
                        navigator.clipboard.writeText(textToCopy)
                            .then(() => {
                                alert('Erro copiado para a área de transferência!');
                            })
                            .catch(err => {
                                console.error('Falha ao copiar o texto: ', err);
                                alert('Não foi possível copiar o erro. Por favor, tente manualmente.');
                            });
                    };
                }

                // Mostra o modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            // Fecha o modal
            closeModal.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            // Também fecha se clicar fora do conteúdo do modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });

            //  fim



            // Lógica para o novo botão de limpar logs
            document.getElementById('btn-clear-logs').addEventListener('click', async () => {
                if (!confirm(
                        'Tem certeza que deseja limpar TODOS os logs de erro? Esta ação é irreversível.'
                        )) {
                    return;
                }

                try {
                    const url = "{{ route('auditoria.erros.clear') }}";
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Necessário para proteção CSRF
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        alert(data.message);
                        loadLogs(true); // Recarrega a tabela após a limpeza
                    } else {
                        alert('Erro: ' + (data.message || 'Falha na requisição.'));
                    }
                } catch (error) {
                    alert('Erro na conexão: ' + error.message);
                }
            });

            // Filtros
            document.getElementById('btn-filter').addEventListener('click', () => {
                filters = {
                    message: document.getElementById('filter-message').value,
                    type: document.getElementById('filter-type').value,
                    start_date: document.getElementById('filter-start').value,
                    end_date: document.getElementById('filter-end').value,
                };
                loadLogs(true);
            });

            // Scroll infinito
            window.addEventListener('scroll', () => {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
                    loadLogs();
                }
            });

            // Carga inicial
            loadLogs();
        });
    </script>

    {{-- ative o acorden --}}
    <script>
        function toggleStack(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('stack-icon');
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>

    {{-- Script do gráfico --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const data = @json($exceptionsByType);
            const labels = data.map(item => item.exception_type);
            const values = data.map(item => item.total);

            new Chart(document.getElementById('exceptionsChart'), {
                type: 'pie', // Alterado para gráfico de pizza, mais adequado para mostrar proporções
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Erros por Tipo',
                        data: values,
                        backgroundColor: [
                            '#F87171', // Red 400
                            '#FB923C', // Orange 400
                            '#FBBF24', // Yellow 400
                            '#A3E635', // Lime 400
                            '#34D399', // Emerald 400
                            '#60A5FA', // Blue 400
                            '#A78BFA', // Violet 400
                            '#F472B6', // Pink 400
                            '#94A3B8', // Slate 400
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: 'Distribuição de Erros por Tipo'
                        }
                    }
                }
            });
        });
    </script>
@endsection
