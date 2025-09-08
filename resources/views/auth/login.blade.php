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

    <!-- Coluna da Imagem (oculta em telas pequenas) -->
    <div id="background"
        class="hidden md:flex w-2/5 bg-cover bg-no-repeat bg-left opacity-0 transition-opacity duration-700 ease-in-out"
        style="background-image: url('{{ daily_background() }}')">
        <!-- Logo no canto -->
        <div class="absolute top-6 left-6">
            <img src="{{ asset('frontend/img/logo-white.png') }}" alt="Logo Taiksu" class="h-6">
        </div>
    </div>

    <!-- Coluna do Formulário -->
    <div class="flex w-full md:w-3/5 flex-col items-center justify-center h-screen mx-auto">
        <div class="w-full max-w-sm">
            <h1 class="text-left text-gray-500 mb-6 text-xl font-medium hidden md:block">
                Entrar no <span class="text-green-700 font-semibold">Taiksu Office</span>
            </h1>

            <div id="loginContainer"
                class="opacity-0 w-full p-8 rounded-xl bg-white border transition-opacity duration-700 ease-in-out">

                <div class="flex justify-center mb-6 md:hidden">
                    <!-- Mostra logo somente no mobile -->
                    <img src="{{ asset('frontend/img/logo.png') }}" alt="Logo Taiksu" class="h-6" />
                </div>

                @if (session('error'))
                    <div class="px-4 py-2 rounded-full bg-red-100 text-red-700 font-semibold text-sm text-center mt-2">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="/login" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm text-gray-600 mb-1">Email</label>
                        <input type="hidden" name="redirect_uri" value="{{ $redirect }}">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" placeholder="marina@taiksu.com.br"
                            class="w-full px-4 py-3 border rounded-md focus:outline-none hover:ring-2 focus:ring-2 ring-green-400 transition duration-300" />
                        @error('email')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm text-gray-600 mb-1">Senha</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="● ● ● ● ● ● ● ●"
                            class="w-full px-4 py-3 border rounded-md focus:outline-none hover:ring-2 focus:ring-2 ring-green-400 transition duration-300 mb-2" />
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-3 rounded-md transition">
                        Entrar
                    </button>
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

        setTimeout(() => {
            backgroundContainer.classList.remove('opacity-0');
            backgroundContainer.classList.add('opacity-100');
        }, 400); // Tempo pra animação de fade-in

        setTimeout(() => {
            loginContainer.classList.remove('opacity-0');
            loginContainer.classList.add('opacity-100');
        }, 600); // Tempo pra animação de fade-in
    });
</script>

</html>
