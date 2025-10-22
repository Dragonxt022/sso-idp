@extends('layouts.app')
@section('content')
    <section class="max-w-2xl mx-auto">
        <div id="cards" class="flex flex-col gap-5 mb-8 opacity-0 translate-y-4 transition-all duration-700">

            <!-- Cabeçalho -->
            <div class="flex flex-row justify-between items-center">
                <div class="hidden md:flex flex-row justify-start items-center gap-4">
                    <div class="text-gray-700 text-lg font-semibold hover:text-green-700 transition-all duration-200">
                        <a href="{{ route('equipe.index') }}">Equipe</a>
                    </div>
                    <img class="h-6" src="{{ asset('frontend/img/seta.png') }}">
                    <p class="capitalize text-gray-500 text-lg font-semibold">Editar perfil</p>
                </div>
            </div>

            <div class="flex flex-row justify-center items-center py-2 px-8">
                @if ($errors->any())
                    <div class="mb-4 p-4 rounded bg-red-100 text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div id="success-alert"
                        class="fixed top-6 right-6 bg-green-500 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition-opacity duration-700 opacity-100 z-50">
                        {{ session('success') }}
                    </div>

                    <script>
                        setTimeout(() => {
                            const alert = document.getElementById('success-alert');
                            if (alert) {
                                alert.classList.add('opacity-0');
                                setTimeout(() => alert.remove(), 700);
                            }
                        }, 3500);
                    </script>
                @endif

                <form action="{{ route('equipe.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                    class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Foto -->
                    <div>
                        <div class="flex items-center py-4">
                            <label for="profile_photo_input" class="block font-semibold text-gray-700 mb-2">
                                <img class="h-32 object-cover rounded-full cursor-pointer hover:scale-105 transition-all duration-300"
                                    id="preview" src="{{ $foto }}" />
                            </label>
                            <input type="file" name="profile_photo_path" id="profile_photo_input" accept="image/*"
                                class="hidden" />
                        </div>
                    </div>
            </div>

            @if (in_array($authRole, ['Desenvolvedor', 'Franqueadora', 'Franqueado']))
                <div
                    class="flex flex-col gap-4 items-center justify-between bg-white rounded-xl py-6 px-8 border border-gray-200">
                    <div class="w-full flex flex-row items-center justify-between">
                        <div class="flex flex-row items-center gap-2">
                            <img src="{{ asset('frontend/img/cargo.png') }}" class="w-6 h-6">
                            <h3 class="text-md font-semibold text-gray-600">Cargo</h3>
                        </div>
                        <select name="role_id">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ $user->roles->first()->id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <input type="hidden" name="id" value="{{ $user->id }}">

            <div class="flex flex-col bg-white rounded-xl border border-gray-200 p-8 gap-4">

                <!-- Nome -->
                <div class="w-full">
                    <label class="text-sm font-medium text-gray-500">Nome Completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                </div>

                <!-- Email -->
                <div class="w-full">
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="new-email"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                </div>

                <!-- Telefone -->
                <div class="w-full">
                    <label class="text-sm font-medium text-gray-500">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $user->telefone) }}"
                        autocomplete="new-telefone"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none"
                        placeholder="(XX) XXXX-XXXX">
                </div>

                <!-- CPF -->
                <div>
                    <label class="text-sm font-medium text-gray-500">CPF</label>
                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $user->cpf) }}"
                        autocomplete="new-cpf"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none"
                        placeholder="000.000.000-00">
                </div>

                <!-- Senha -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Nova Senha (opcional)</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                </div>
            </div>

            <!-- Salário -->
            @if (in_array($authRole, ['Desenvolvedor', 'Franqueadora', 'Franqueado']))
                <div
                    class="flex flex-col gap-4 items-center justify-between bg-white rounded-xl py-4 px-8 border border-gray-200">
                    <div class="w-full flex flex-row items-center justify-between">
                        <div class="flex flex-row items-center gap-2">
                            <img src="{{ asset('frontend/img/money.png') }}" class="w-6 h-6">
                            <h3 class="text-md font-semibold text-gray-600">Salário Base</h3>
                        </div>
                        <input type="text" id="salario_base" name="salario_base"
                            class="bg-gray-50 border border-gray-200 rounded-md p-2 focus:ring-2 focus:bg-white focus:ring-green-500 focus:outline-none">
                    </div>
                </div>
            @endif


            <div>
                <button type="submit"
                    class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition cursor-pointer">
                    Atualizar Colaborador
                </button>
            </div>
            </form>
        </div>
    </section>
    {{-- Atualizador de foto --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('profile_photo_input');
            const preview = document.getElementById('preview');

            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

    {{-- Formatação de salário --}}
    <script>
        function formatarReal(valor) {
            // Remove tudo que não for número
            valor = valor.replace(/\D/g, "");

            if (!valor) return "";

            // Converte para número e divide por 100 para centavos
            let numero = (Number(valor) / 100).toFixed(2);

            // Separa centavos e parte inteira
            let partes = numero.split('.');
            let inteiro = partes[0];
            let decimal = partes[1];

            // Adiciona ponto de milhar na parte inteira
            inteiro = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            return "R$ " + inteiro + "," + decimal;
        }

        const inputSalario = document.getElementById('salario_base');

        inputSalario.addEventListener('input', (e) => {
            let input = e.target;
            let valorOriginal = input.value;
            let cursorPos = input.selectionStart;

            // Conta quantos números existiam antes do cursor
            let numerosAntesCursor = valorOriginal.slice(0, cursorPos).replace(/\D/g, "").length;

            // Formata o valor
            input.value = formatarReal(valorOriginal);

            // Reposiciona o cursor com base nos números
            let numerosContados = 0;
            for (let i = 0; i < input.value.length; i++) {
                if (/\d/.test(input.value[i])) numerosContados++;
                if (numerosContados >= numerosAntesCursor) {
                    input.setSelectionRange(i + 1, i + 1);
                    break;
                }
            }
        });
    </script>

    {{-- Mascara de telefone --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const telefoneInput = document.querySelector('input[name="telefone"]');

            telefoneInput.addEventListener('input', (e) => {
                let valor = e.target.value.replace(/\D/g, ''); // remove tudo que não for número

                if (valor.length > 11) valor = valor.slice(0, 11); // limita a 11 dígitos

                if (valor.length > 10) {
                    // formato (99) 99999-9999
                    valor = valor.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
                } else if (valor.length > 5) {
                    // formato (99) 9999-9999
                    valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
                } else if (valor.length > 2) {
                    valor = valor.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
                } else if (valor.length > 0) {
                    valor = valor.replace(/^(\d*)$/, '($1');
                }

                e.target.value = valor;
            });
        });
    </script>

    {{-- Mascara de CPF --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cpfInput = document.getElementById('cpf');

            cpfInput.addEventListener('input', (e) => {
                let valor = e.target.value.replace(/\D/g, ''); // remove tudo que não é número
                if (valor.length > 11) valor = valor.slice(0, 11); // limita a 11 dígitos

                // Formata CPF: 000.000.000-00
                if (valor.length > 9) {
                    valor = valor.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2})$/, '$1.$2.$3-$4');
                } else if (valor.length > 6) {
                    valor = valor.replace(/^(\d{3})(\d{3})(\d{0,3})$/, '$1.$2.$3');
                } else if (valor.length > 3) {
                    valor = valor.replace(/^(\d{3})(\d{0,3})$/, '$1.$2');
                }

                e.target.value = valor;
            });
        });
        </script>

@endsection
