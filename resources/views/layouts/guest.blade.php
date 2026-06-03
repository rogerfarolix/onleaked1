<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Onleaked') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900|jetbrains-mono:400,500,600&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink text-text min-h-screen antialiased flex flex-col justify-center items-center py-12 px-6">
    <div class="grid-backdrop fixed inset-0 -z-10 pointer-events-none"></div>

    <div class="mb-8">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-md bg-brand flex items-center justify-center font-bold text-lg text-white ring-1 ring-brand/40">O</div>
            <span class="font-bold text-2xl tracking-tight">On<span class="text-brand-bright">leaked</span></span>
        </a>
    </div>

    <div class="w-full sm:max-w-md p-8 glass-card shadow-2xl">
        {{ $slot }}
    </div>
</body>
</html>
