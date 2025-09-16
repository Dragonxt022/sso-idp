<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2c8516ff" />
    <title>{{ config('app.name', 'Laravel') }} - Redefinir Senha</title>
    <link rel="icon" href="{{ asset('frontend/img/favicon.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('frontend/css/global.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-[#F3F8F3]">

    <div class="w-full max-w-sm flex flex-col">
        <h1 class="text-left text-gray-500 mb-6 text-xl font-medium hidden md:block">
            Redefinir <span class="text-green-700 font-semibold">minha senha</span>
        </h1>
        <div class="bg-white p-8 rounded-xl border  border-gray-200">

            {{-- Mensagem de sucesso --}}
            @if (session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 text-center">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Mensagem de erros --}}
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                {{-- Token oculto --}}
                <input type="hidden" name="token" value="{{ $token }}">
                {{-- E-mail oculto --}}
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">


                {{-- Nova senha --}}
                <div>
                    <label for="password" class="block text-sm text-gray-600 mb-1">Nova Senha</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>

                {{-- Confirmação da nova senha --}}
                <div>
                    <label for="password_confirmation" class="block text-sm text-gray-600 mb-1">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>

                <button type="submit"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-md transition">
                    Redefinir Senha
                </button>
            </form>
        </div>
    </div>

</body>

</html>
