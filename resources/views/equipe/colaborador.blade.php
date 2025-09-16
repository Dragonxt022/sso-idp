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
                    <p class="capitalize text-gray-500 text-lg font-semibold">{{ $user->name }}</p>
                </div>

                <div class="w-full md:w-auto flex flex-row gap-3">
                    <div class="w-full md:w-auto justify-center group rounded-lg border border-gray-200 px-4 py-2 bg-white flex flex-row items-center gap-2 hover:cursor-pointer transition-all duration-300"
                        onclick="abrirModalPin('{{ $user->id }}', '{{ $user->pin }}')">
                        <img src="{{ asset('frontend/img/pin.png') }}" class="h-5">
                        <span
                            class="text-gray-500 text-sm leading-tight font-medium group-hover:text-gray-600 transition duration-200 ease-in">Visualizar
                            PIN</span>
                    </div>

                    <!-- Demitir - Modal: tem certeza que deseja demitir Fulano? o colaborador perderá acesso ao sistema imediatamente. esta ação é irreversível -->
                    <div id="btn-status"
                        class="w-full md:w-auto justify-center group rounded-lg border border-gray-200 px-4 py-2 bg-white flex flex-row items-center gap-2 hover:cursor-pointer transition-all duration-300"
                        style="background-color: {{ $user->status === 'demitido' ? '#FFFFFF' : '#FFFFFF' }};"
                        onclick="abrirModalStatus('{{ $user->id }}', '{{ $user->status }}')">

                        {!! $user->status === 'demitido'
                            ? '<img src="' . asset('frontend/img/replay.png') . '" class="h-5">'
                            : '<img src="' . asset('frontend/img/negative.png') . '" class="h-5">' !!}

                        <span id="btn-status-text"
                            class="text-gray-500 text-sm leading-tight font-medium group-hover:text-gray-600 transition duration-200 ease-in">
                            {{ $user->status === 'demitido' ? 'Reativar' : 'Demitir' }}
                        </span>
                    </div>


                </div>

            </div>

            <div
                class="flex flex-row justify-between items-center bg-white rounded-xl border border-gray-200 py-6 px-8 hover:shadow-xl transition-all duration-300 cursor-pointer">
                <div class="flex flex-row items-center gap-6">
                    <img src="{{ $user->profile_photo_path ? asset('frontend/profiles/' . $user->profile_photo_path) : asset('frontend/img/user.png') }}"
                        onerror="this.onerror=null;this.src='{{ asset('frontend/img/user.png') }}';"
                        class="w-12 h-12 rounded-full group-hover:scale-105 group-hover:shadow-md transition-all duration-700" />
                    <div>
                        <h2 class="text-2xl font-semibold text-green-800">{{ $user->name }}</h2>
                        <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                    </div>
                </div>

                <!-- Botão Editar Perfil -->
                <button id="editar-perfil">
                    <img src="{{ asset('frontend/img/edit.png') }}" class="w-8 h-8 cursor-pointer">
                </button>
            </div>


            <div
                class="flex flex-col gap-4 items-center justify-between bg-white rounded-xl py-4 px-8 border border-gray-200">
                <div class="w-full flex flex-row items-center justify-between">
                    <div class="flex flex-row items-center gap-2">
                        <img src="{{ asset('frontend/img/cargo.png') }}" class="w-6 h-6">
                        <h3 class="text-md font-semibold text-gray-600">Cargo</h3>
                    </div>
                    <!-- Select dinâmico de roles -->
                    <select name="role_id" onchange="atualizarRole(this.value)">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
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

            <div class="hidden flex flex-col gap-2 mt-2">
                <div class="text-sm font-medium text-gray-500 px-2">Permissões</div>

                <div id="permissoes"
                    class="flex flex-col items-center justify-between bg-white rounded-xl py-4 px-8 border border-gray-200 text-gray-600">

                    @foreach ($permissions as $permission)
                        <div
                            class="flex flex-row items-center justify-between w-full pt-4 pb-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <span>{{ $permission->name }}</span>
                            <button type="button" onclick="togglePermissao('{{ $permission->name }}')"
                                class="toggle-active w-11 h-6 rounded-full flex items-center p-0.5 group cursor-pointer {{ $userPermissions->contains($permission->name) ? 'bg-green-500' : 'bg-gray-200' }}">
                                <div
                                    class="bg-white w-5 h-5 rounded-full transition-all duration-700 p-0.5 border border-gray-300 {{ $userPermissions->contains($permission->name) ? 'translate-x-5' : '' }}">
                                </div>
                            </button>
                        </div>
                    @endforeach
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

    <div id="modal"
        class="fixed inset-0 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">

        <!-- Fundo escuro -->
        <div id="modal-fundo" class="absolute inset-0 bg-white opacity-0 transition-opacity duration-300">
        </div>

        <!-- Conteúdo -->
        <div id="modal-content"
            class="bg-white rounded-xl border border-gray-300 p-8 w-[400px] mx-4 transform scale-90 transition-transform duration-300 relative z-10">
            <h2 class="text-gray-500 text-xl font-semibold mb-8">Alterar senha de <span
                    class="text-gray-600">{{ $user->name }}</span></h2>

            <form action="{{ route('user.change-password') }}" method="POST">
                @csrf

                {{-- Nova senha --}}
                <div class="mb-4 relative">
                    <label for="nova-senha" class="block text-sm font-medium text-gray-700">Nova senha</label>
                    <input type="password" id="nova-senha" name="nova-senha" required
                        class="p-2 mt-1 w-full border border-gray-500 rounded pr-10 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    <button type="button" class="absolute top-9 right-2 text-gray-400 hover:text-gray-700"
                        onclick="togglePassword('nova-senha', this)">
                        <img src="{{ asset('frontend/img/olho-aberto.svg') }}" class="w-6 h-6" alt="Mostrar Senha">
                    </button>
                </div>

                {{-- Confirmar nova senha --}}
                <div class="mb-4 relative">
                    <label for="nova-senha_confirmation" class="block text-sm font-medium text-gray-700">Confirmar nova
                        senha</label>
                    <input type="password" id="nova-senha_confirmation" name="nova-senha_confirmation" required
                        class="p-2 mt-1 w-full border border-gray-500 rounded pr-10 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    <button type="button" class="absolute top-9 right-2 text-gray-400 hover:text-gray-700"
                        onclick="togglePassword('nova-senha_confirmation', this)">
                        <img src="{{ asset('frontend/img/olho-aberto.svg') }}" class="w-6 h-6" alt="Mostrar Senha">
                    </button>
                </div>
            </form>


            <div class="flex flex-row gap-4 mt-8">
                <div onclick="fecharModal()" id="cancelar-popup"
                    class="w-full px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-center rounded-full mt-4 transition-all duration-300 cursor-pointer">
                    Cancelar
                </div>
                <button type="submit"
                    class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-full mt-4  transition-all duration-300 cursor-pointer">
                    Salvar
                </button>
            </div>
            </form>
        </div>
    </div>

    <!-- Modal PIN -->
    <div id="modal-pin" class="fixed inset-0 flex items-center justify-center pointer-events-none">
        <!-- Fundo preto -->
        <div id="modal-pin-fundo"
            class="absolute inset-0 bg-white bg-opacity-50 opacity-0 transition-opacity duration-300">
        </div>

        <!-- Conteúdo do modal -->
        <div id="modal-pin-conteudo"
            class="relative bg-white border border-gray-300 rounded-xl p-8 w-[400px] transform scale-90 opacity-0 transition-all duration-300">
            <h2 class="text-gray-500 text-center text-xl font-semibold mb-6">PIN de <span
                    class="text-gray-600">{{ $user->name }}</span></h2>
            <div class="mb-4 text-center">
                <span id="pin-valor" class="text-8xl font-bold text-green-700 tracking-wide">******</span>
            </div>
            <div class="flex justify-between gap-4 mt-8">
                <button onclick="fecharModalPin()"
                    class="w-full px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-center rounded-full mt-4 transition-all duration-300 cursor-pointer">Fechar</button>
                <button onclick="regenerarPin()"
                    class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-full mt-4  transition-all duration-300 cursor-pointer">Regenerar</button>
            </div>
        </div>
    </div>

    {{-- Modal de confirmação --}}

    <div id="modal-status"
        class="fixed inset-0 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div id="modal-status-fundo"
            class="absolute inset-0 bg-white bg-opacity-50 opacity-0 transition-opacity duration-300"></div>
        <div id="modal-status-conteudo"
            class="bg-white rounded-xl border border-gray-300 p-6 w-[400px] transform scale-90 opacity-0 transition-all duration-300 relative z-10 text-center">
            <h2 id="modal-status-titulo" class="text-gray-500 text-center text-xl font-semibold mb-6"></h2>
            <p id="modal-status-msg" class="mb-6"></p>
            <div class="flex gap-4 justify-center">
                <button onclick="fecharModalStatus()"
                    class="w-full px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-center rounded-full mt-4 transition-all duration-300 cursor-pointer">Cancelar</button>
                <button id="confirmar-status"
                    class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-full mt-4 transition-all duration-300 cursor-pointer">Confirmar</button>
            </div>
        </div>
    </div>

    {{-- Script do PIN --}}
    <script>
        let userId = {{ $user->id }};

        function abrirModalPin(id, pin) {
            userId = id;
            document.getElementById('pin-valor').textContent = pin;

            const fundo = document.getElementById('modal-pin-fundo');
            const conteudo = document.getElementById('modal-pin-conteudo');

            fundo.classList.remove('opacity-0');
            fundo.classList.add('opacity-100');

            conteudo.classList.remove('opacity-0', 'scale-90');
            conteudo.classList.add('opacity-100', 'scale-100');

            document.getElementById('modal-pin').classList.remove('pointer-events-none');
        }

        function fecharModalPin() {
            const fundo = document.getElementById('modal-pin-fundo');
            const conteudo = document.getElementById('modal-pin-conteudo');

            fundo.classList.remove('opacity-100');
            fundo.classList.add('opacity-0');

            conteudo.classList.remove('opacity-100', 'scale-100');
            conteudo.classList.add('opacity-0', 'scale-90');

            document.getElementById('modal-pin').classList.add('pointer-events-none');
        }


        // Regenerar PIN
        function regenerarPin() {
            fetch(`/user/${userId}/regenerate-pin`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('pin-valor').textContent = data.pin;
                });
        }

        // Toggle permissão
        function togglePermissao(permission) {
            fetch(`/user/${userId}/toggle-permission`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        permission
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const btn = Array.from(document.querySelectorAll('#permissoes button'))
                        .find(b => b.getAttribute('onclick').includes(permission));
                    const inner = btn.querySelector('div');
                    if (data.status) {
                        btn.classList.remove('bg-gray-200');
                        btn.classList.add('bg-green-500');
                        inner.classList.add('translate-x-5');
                    } else {
                        btn.classList.remove('bg-green-500');
                        btn.classList.add('bg-gray-200');
                        inner.classList.remove('translate-x-5');
                    }
                });
        }
    </script>

    {{-- Sscript de atualziar senha --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alterarSenhaBtn = document.getElementById('alterar-senha');
            const modal = document.getElementById('modal');
            const modalContent = document.getElementById('modal-content');
            const modalFundo = document.getElementById('modal-fundo');

            // Abre modal com animação
            alterarSenhaBtn.addEventListener('click', function() {
                modal.classList.remove('pointer-events-none');
                modal.classList.add('opacity-100');
                modal.classList.remove('opacity-0');

                modalFundo.classList.remove('opacity-0');
                modalFundo.classList.add('opacity-100');

                modalContent.classList.remove('scale-90');
                modalContent.classList.add('scale-100');
            });

            // Fecha modal com animação
            function fecharModal() {
                modal.classList.add('opacity-0');
                modal.classList.remove('opacity-100');

                modalFundo.classList.remove('opacity-100');
                modalFundo.classList.add('opacity-0');

                modalContent.classList.add('scale-90');
                modalContent.classList.remove('scale-100');

                setTimeout(() => {
                    modal.classList.add('pointer-events-none');
                }, 300);
            }

            window.fecharModal = fecharModal;
        });


        // Função para alternar visibilidade da senha
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const img = btn.querySelector('img');

            if (input.type === "password") {
                input.type = "text";
                img.src = "{{ asset('frontend/img/olho-fechado.svg') }}"; // altera para olho fechado
                img.alt = "Ocultar senha";
            } else {
                input.type = "password";
                img.src = "{{ asset('frontend/img/olho-aberto.svg') }}"; // altera para olho aberto
                img.alt = "Mostrar senha";
            }
        }
    </script>

    {{-- Mudar status   --}}
    <script>
        let currentUserId = null;
        let currentStatus = null;

        function abrirModalStatus(userId, status) {
            currentUserId = userId;
            currentStatus = status;

            const titulo = document.getElementById('modal-status-titulo');
            const msg = document.getElementById('modal-status-msg');
            const confirmarBtn = document.getElementById('confirmar-status');

            if (status === 'demitido') {
                titulo.textContent = 'Reativar usuário';
                msg.textContent = 'Deseja reativar este colaborador?';
            } else {
                titulo.textContent = 'Demitir colaborador';
                msg.textContent = 'Este colaborador perderá acesso ao sistema imediatamente.';
            }

            confirmarBtn.onclick = () => toggleStatus();

            const modal = document.getElementById('modal-status');
            const fundo = document.getElementById('modal-status-fundo');
            const conteudo = document.getElementById('modal-status-conteudo');

            modal.classList.remove('pointer-events-none', 'opacity-0');
            modal.classList.add('opacity-100');
            fundo.classList.remove('opacity-0');
            fundo.classList.add('opacity-100');
            conteudo.classList.remove('scale-90', 'opacity-0');
            conteudo.classList.add('scale-100', 'opacity-100');
        }

        function fecharModalStatus() {
            const modal = document.getElementById('modal-status');
            const fundo = document.getElementById('modal-status-fundo');
            const conteudo = document.getElementById('modal-status-conteudo');

            modal.classList.add('opacity-0');
            modal.classList.remove('opacity-100');
            fundo.classList.add('opacity-0');
            fundo.classList.remove('opacity-100');
            conteudo.classList.add('scale-90', 'opacity-0');
            conteudo.classList.remove('scale-100', 'opacity-100');

            setTimeout(() => modal.classList.add('pointer-events-none'), 300);
        }

        function toggleStatus() {
            fetch(`/user/${currentUserId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    // Atualiza botão
                    const btn = document.getElementById('btn-status');
                    const texto = document.getElementById('btn-status-text');

                    if (data.status === 'demitido') {
                        btn.style.backgroundColor = '#F59E0B';
                        texto.textContent = 'Reativar';
                    } else {
                        btn.style.backgroundColor = '#FFFFFF';
                        texto.textContent = 'Demitir';
                    }

                    fecharModalStatus();
                    window.location.reload();
                });
        }
    </script>

    {{-- Update de cargo --}}
    <script>
        function atualizarRole(roleId) {
            fetch(`/user/${userId}/update-role`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        role_id: roleId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Usando toast
                        toastSuccess(`Cargo alterado para: ${data.role}`);
                    } else {
                        toastError('Erro ao atualizar cargo.');
                    }
                })
                .catch(() => toastError('Erro na requisição.'));
        }
    </script>
@endsection
