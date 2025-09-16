<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2c8516ff" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('frontend/img/favicon.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('frontend/css/global.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col md:flex-row">

    <!-- Coluna da Imagem -->
    <div id="background"
        class="hidden md:flex w-2/5 bg-cover bg-no-repeat bg-left opacity-0 transition-opacity duration-700 ease-in-out"
        style="background-image: url('{{ daily_background() }}')">
        <!-- Logo -->
        <div class="absolute top-6 left-6">
            <img src="{{ asset('frontend/img/logo-white.png') }}" alt="Logo Taiksu" class="h-6">
        </div>
    </div>

    <!-- Coluna do Formulário -->
    <div class="flex w-full md:w-3/5 flex-col items-center justify-center h-screen mx-auto">
        <div class="w-full max-w-sm">
            <h1 class="text-left text-gray-500 mb-6 text-xl font-medium hidden md:block">
                Recuperar <span class="text-green-700 font-semibold">minha senha</span>
            </h1>

            <div id="loginContainer"
                class="opacity-0 w-full p-8 rounded-xl bg-white border transition-opacity duration-700 ease-in-out">

                <div class="flex justify-center mb-6 md:hidden">
                    <img src="{{ asset('frontend/img/logo.png') }}" alt="Logo Taiksu" class="h-6" />
                </div>

                {{-- Mensagem de sucesso --}}
                @if (session('status'))
                    <div id="resetMessage"
                        class="flex items-center gap-2 px-4 py-3 rounded-md bg-green-100 text-green-700 font-medium text-sm text-center justify-center mt-4 opacity-0 transform scale-95 transition duration-700 ease-out">

                        <!-- Ícone check animado -->
                        <svg id="checkIcon" xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-green-600 opacity-0 transform scale-50 transition duration-500 ease-out"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>

                        <span>
                            Se o seu e-mail existir em nosso banco de dados, em alguns segundos você receberá o token de
                            recuperação de senha.
                        </span>
                    </div>
                @endif

                {{-- Formulário de reset --}}
                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm text-gray-600 mb-1">E-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" placeholder="marina@taiksu.com.br"
                            class="w-full px-4 py-3 border rounded-md focus:outline-none hover:ring-2 focus:ring-2 ring-green-400 transition duration-300" />
                        @error('email')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-md transition-all duration-300">
                        Recuperar senha
                    </button>

                    {{-- Link voltar ao login --}}
                    <div class="flex justify-center mt-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-gray-600">
                            Voltar ao login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

{{-- Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginContainer = document.getElementById('loginContainer');
        const backgroundContainer = document.getElementById('background');
        const msg = document.getElementById('resetMessage');
        const icon = document.getElementById('checkIcon');

        setTimeout(() => {
            backgroundContainer.classList.remove('opacity-0');
            backgroundContainer.classList.add('opacity-100');
        }, 400);

        setTimeout(() => {
            loginContainer.classList.remove('opacity-0');
            loginContainer.classList.add('opacity-100');
        }, 600);

        // Animação da mensagem de reset
        if (msg) {
            setTimeout(() => {
                msg.classList.remove('opacity-0', 'scale-95');
                msg.classList.add('opacity-100', 'scale-100');
            }, 400);

            setTimeout(() => {
                icon.classList.remove('opacity-0', 'scale-50');
                icon.classList.add('opacity-100', 'scale-100');
            }, 800);
        }
    });
</script>

</html>
