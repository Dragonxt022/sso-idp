<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Taiksu Office' }}</title>

    <meta name="theme-color" content="#2c8516ff" />
    <link rel="shortcut icon" href="https://login.taiksu.com.br/frontend/img/favicone.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Scripts -->
    <script src="{{ asset('frontend/js/index.js?v1') }}" defer></script>
    <link href="{{ asset('frontend/css/global.css') }}" rel="stylesheet">

</head>

<body class="max-w-4xl min-h-screen mx-auto mt-28 px-8 overflow-auto">

    {{-- Header --}}
    <header id="header"
        class="fixed top-0 left-0 w-full z-30 flex items-center justify-between py-6 px-8 bg-white shadow-sm  opacity-0 -translate-y-6 transition-all duration-[1200ms]">
        <a href="/"><img src="{{ asset('frontend/img/logo.png') }}" class="h-6"></a>
        <div class="relative">
            <form class="h-6 w-6" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" id="logout">
                    <img src="{{ asset('frontend/img/logout.png') }}"class="w-6 h-6 cursor-pointer">
                </button>
            </form>

        </div>
    </header>


    {{-- Conteúdo principal --}}
    <main class="p-2 mb-6 mx-auto">
        @yield('content')
    </main>

    {{-- Footer simples --}}
    <footer
        class="hidden md:flex flex-row items-center justify-between fixed bottom-0 left-0 w-full bg-green-100 text-gray-600 text-center text-md py-2-12 px-8 font-semibold">

        <div> </div>

        <div>
            Taiksu <span class="font-normal text-green-600">Office</span>
        </div>

        <div></div>
    </footer>


</body>

</html>
