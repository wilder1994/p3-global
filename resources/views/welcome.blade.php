<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>P3 Seguridad Privada LTDA</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-gray-900 via-black to-gray-800 text-white">

    <div class="relative flex flex-col items-center justify-center min-h-screen px-6">

        <!-- Navbar -->
        @if (Route::has('login'))
            <livewire:welcome.navigation />
        @endif

        <!-- Hero -->
        <div class="text-center max-w-3xl">
            <!-- Logo -->
            <img src="{{ asset('images/logo.png') }}" alt="P3 Seguridad Privada LTDA" class="h-28 mx-auto mb-6">

            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Bienvenido a <span class="text-blue-400">P3 Seguridad Privada LTDA</span>
            </h1>
            <p class="text-lg text-gray-300">
                Protegiendo lo que más importa con soluciones de seguridad privada confiables.
            </p>
        </div>

        <!-- Opcional: Botón de acceso -->
        <div class="mt-10">
            <a href="{{ route('login') }}" 
               class="px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-lg shadow-lg text-white font-semibold transition">
                Ingresar al Sistema
            </a>
        </div>

        <!-- Footer -->
        <footer class="mt-20 text-gray-500 text-sm text-center">
            <!-- Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})-->
        </footer>
    </div>
</body>
</html>
