@extends('layouts.app')
@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-5 opacity-0 translate-y-4 transition-all duration-700">

            <div class="hidden md:flex flex-row justify-between items-center">
                <!-- Breadcrumb -->
                <div class="flex flex-row justify-start items-center gap-4">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="/">Dashboard</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold">Meu perfil</p>
                </div>

                <img id="sso-icon" class="h-6 opacity-0 hover:opacity-100 transition-all duration-600 cursor-pointer" src="{{ asset('frontend/img/key.png') }}">
            </div>

            <div
                class="flex flex-row justify-between items-center bg-white rounded-xl border border-gray-200 py-6 px-8 hover:shadow-xl transition-all duration-300 cursor-pointer">
                <div class="flex flex-row items-center gap-6">
                    <img src="{{ asset('frontend/profiles/' . $user->profile_photo_path) }}" class="w-16 h-16 rounded-full">
                    <div>
                        <h2 class="text-2xl font-semibold text-green-800">{{ $user->name }}</h2>
                        <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                    </div>
                </div>

                <img src="{{ asset('frontend/img/edit.png') }}" class="w-8 h-8">
            </div>

            <div
                class="flex flex-row items-center justify-between bg-white rounded-xl py-6 px-8 border border-gray-200 border-gray-200">
                <div class="flex flex-row items-center gap-2">
                    <img src="{{ asset('frontend/img/unidade.png') }}" class="h-6">
                    <h3 class="text-md font-medium font-semibold text-gray-600">Unidade</h3>
                </div>
                <span class="text-gray-600 text-sm">{{ $cidade }}</span>
            </div>

            <div class="flex flex-row items-center justify-between bg-white rounded-xl py-6 px-8 border border-gray-200">
                <div class="flex flex-row items-center gap-2">
                    <img src="{{ asset('frontend/img/cargo.png') }}" class="w-6 h-6">
                    <h3 class="text-md font-medium font-semibold text-gray-600">Cargo</h3>
                </div>
                <span class="text-gray-600 text-sm">{{ $grupo }}</span>
            </div>

            <div class="flex flex-row items-center justify-between bg-white rounded-xl py-6 px-8 border border-gray-200">
                <div class="flex flex-row items-center gap-2">
                    <img src="{{ asset('frontend/img/money.png') }}" class="w-6 h-6">
                    <h3 class="text-md font-semibold text-gray-600">Salário</h3>
                </div>
                <img src="{{ asset('frontend/img/breve_tag.png') }}" class="h-6">
            </div>

            <div id="ver-pin"
                class="flex flex-row items-center justify-between bg-white rounded-xl py-6 px-8 border border-gray-200 text-gray-600 hover:text-green-700 cursor-pointer"
                onclick="abrirModalPin('{{ $user->id }}', '{{ $user->pin }}')">
                <div class="flex flex-row items-center gap-2">
                    <img src="{{ asset('frontend/img/pin.png') }}" class="w-6 h-6">
                    <h3 class="text-md font-semibold">Visualizar meu PIN</h3>
                </div>
                <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
            </div>

            <div id="alterar-senha"
                class="flex flex-row items-center justify-between bg-white rounded-xl py-6 px-8 border border-gray-200 text-gray-600 hover:text-green-700 cursor-pointer">
                <div class="flex flex-row items-center gap-2">
                    <img src="{{ asset('frontend/img/senha.png') }}" class="w-6 h-6">
                    <h3 class="text-md font-semibold">Alterar senha</h3>
                </div>
                <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
            </div>
        </div>
    </section>

    <div id="modal"
        class="fixed inset-0 flex bg-white items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div id="modal-content"
            class="bg-white rounded-xl border border-gray-300 p-8 w-[400px] mx-4 transform scale-90 transition-transform duration-300">
            <h2 class="text-gray-500 text-xl font-semibold mb-8">Alterar minha senha</h2>

            <form action="{{ route('user.change-password') }}" method="POST">
                @csrf

                {{-- Nova senha --}}
                <div class="mb-4 relative">
                    <label for="nova-senha" class="block text-sm font-medium text-gray-700">Nova senha</label>
                    <input type="password" id="nova-senha" name="nova-senha" required
                        class="p-2 mt-1 w-full border border-gray-500 rounded pr-10 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    <button type="button" class="absolute top-9 right-2 text-gray-400 hover:text-gray-700"
                        onclick="togglePassword('nova-senha', this)">
                        <img src="{{ asset('frontend/img/olho-aberto.svg') }}" class="w-6 h-6"
                            alt="<img src="{{ asset('frontend/img/olho-aberto.svg') }}" class="w-6 h-6"
                            alt="Mostrar Senha">
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
                        <img src="{{ asset('frontend/img/olho-aberto.svg') }}" class="w-6 h-6"
                            alt="<img src="{{ asset('frontend/img/olho-aberto.svg') }}" class="w-6 h-6"
                            alt="Mostrar Senha">
                    </button>
                </div>

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
        <div id="modal-pin-fundo" class="absolute inset-0 bg-white bg-opacity-50 opacity-0 transition-opacity duration-300">
        </div>

        <!-- Conteúdo do modal -->
        <div id="modal-pin-conteudo"
            class="relative bg-white border border-gray-300 rounded-xl p-8 w-[400px] transform scale-90 opacity-0 transition-all duration-300">
            <h2 class="text-gray-500 text-center text-xl font-semibold mb-6">Meu PIN</h2>
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alterarSenhaBtn = document.getElementById('alterar-senha');
        const modal = document.getElementById('modal');
        const modalContent = document.getElementById('modal-content');

        // Abre modal com animação
        alterarSenhaBtn.addEventListener('click', function() {
            modal.classList.remove('pointer-events-none');
            modal.classList.add('opacity-100');
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-90');
            modalContent.classList.add('scale-100');
        });

        // Fecha modal com animação
        function fecharModal() {
            modal.classList.add('opacity-0');
            modal.classList.remove('opacity-100');
            modalContent.classList.add('scale-90');
            modalContent.classList.remove('scale-100');
            setTimeout(() => {
                modal.classList.add('pointer-events-none');
            }, 300);
        }

        window.fecharModal = fecharModal;

        // Copia o token SSO para a área de transferência
        const ssoIcon = document.getElementById("sso-icon");
        const userToken = @json($user_token); // injeta como string segura

        ssoIcon.addEventListener("click", function () {
            navigator.clipboard.writeText(userToken)
                .then(() => {
                    alert("Token copiado com sucesso!");
                })
                .catch(err => {
                    console.error("Erro ao copiar token:", err);
                });
        });
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
</script>
