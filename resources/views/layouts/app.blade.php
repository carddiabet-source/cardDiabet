<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Cardioprenatal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 text-gray-800 flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">

            <h1 class="text-lg font-semibold text-blue-600">
                Cardioprenatal
            </h1>

            <!-- Botão mobile -->
            <button id="menuButton" class="md:hidden text-gray-600 focus:outline-none">
                ☰
            </button>

            <!-- Menu desktop -->
            <nav class="hidden md:flex space-x-6 text-sm items-center">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <a href="{{ route('gestantes.index') }}" class="hover:text-blue-600">Gestantes</a>
                <a href="{{ route('consultas.import') }}" class="hover:text-blue-600">Importar CSV</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-md
               bg-red-50 text-red-600 hover:bg-red-100
               transition text-sm font-medium">
                        Sair
                    </button>
                </form>
            </nav>
        </div>

        <!-- Menu mobile -->
        <div id="mobileMenu" class="hidden md:hidden border-t bg-white">
            <nav class="flex flex-col px-4 py-3 space-y-2 text-sm items-center">
                <a href="{{ route('dashboard') }}" class="py-2">Dashboard</a>
                <a href="{{ route('gestantes.index') }}" class="py-2">Gestantes</a>
                <a href="{{ route('consultas.import') }}" class="py-2">Importar CSV</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-md
               bg-red-50 text-red-600 hover:bg-red-100
               transition text-sm font-medium">
                        Sair
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Conteúdo -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 py-6">
            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t">
        <div class="max-w-7xl mx-auto px-4 py-4 text-sm text-center text-gray-500">
            © {{ date('Y') }} Cardioprenatal
        </div>
    </footer>

    <script>
        document.getElementById('menuButton').addEventListener('click', () => {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });
    </script>

</body>

</html>
