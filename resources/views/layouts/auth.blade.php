<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Acesso | Cardioprenatal')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noty/lib/noty.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noty/lib/themes/mint.css">

    <script src="https://cdn.jsdelivr.net/npm/noty/lib/noty.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js'])
</head>


<body class="min-h-screen bg-gray-100">

    <main class="min-h-screen flex items-center justify-center px-4 sm:px-6">

        <div class="w-full max-w-md sm:max-w-lg md:max-w-lg lg:max-w-lg">

            <!-- Card -->
            <div class="bg-white rounded-xl shadow p-5 sm:p-6 md:p-8">

                <!-- Logo / Título -->
                <div class="mb-6 text-center">
                    <h1 class="text-xl sm:text-2xl font-bold text-blue-600">
                        Cardioprenatal
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        @yield('subtitle')
                    </p>
                </div>

                <!-- Conteúdo -->
                @yield('content')

            </div>
        </div>

    </main>
    </div>

</body>

</html>
