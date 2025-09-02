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

<body class="flex flex-row min-h-screen flex items-center justify-center">
    <div>
        imagem
    </div>

    <div id="loginContainer"
        class="opacity-0 w-full max-w-sm p-8 rounded-xl bg-white border transition-opacity duration-700 ease-in-out">

        <div class="flex justify-center mb-6">
            <img src="{{ asset('frontend/img/logo.png') }}" alt="Logo Taiksu" class="h-6" />
        </div>

        @if (session('error'))
            <div class="text-red-600 text-sm text-center mt-4">
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
                    class="w-full px-4 py-3 border rounded-md focus:outline-none hover:ring-2 focus:ring-2 ring-green-400 text-m transition duration-300" />
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm text-gray-600 mb-1">Senha</label>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                    placeholder="● ● ● ● ● ● ● ●"
                    class="w-full px-4 py-3 border rounded-md focus:outline-none hover:ring-2 focus:ring-2 ring-green-400 text-m transition duration-300 mb-2" />
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lembrar-me + Esqueci minha senha -->
            <div class="flex items-center justify-between mb-2">
                {{-- <a href="{{ route('password.request') }}" class="text-green-600 hover:underline text-sm">
                    Esqueci minha senha
                </a> --}}
            </div>

            <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-3 rounded-md transition">
                Entrar
            </button>
        </form>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginContainer = document.getElementById('loginContainer');

            setTimeout(() => {
                loginContainer.classList.remove('opacity-0');
                loginContainer.classList.add('opacity-100');
            }, 200); // Tempo pra animação de fade-in
        });
    </script>
</body>

</html>
