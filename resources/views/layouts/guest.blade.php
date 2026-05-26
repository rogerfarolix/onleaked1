<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Onleaked') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(120, 119, 198, 0.15), transparent),
                        #09090b;
        }
        .glass-card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.06);
        }
    </style>
</head>
<body class="gradient-bg text-white min-h-screen antialiased flex flex-col justify-center items-center py-12 px-6">
    <div class="mb-8">
        <a href="/" class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-rose-500 flex items-center justify-center font-bold text-lg shadow-[0_0_20px_rgba(139,92,246,0.3)]">O</div>
            <span class="font-bold text-2xl tracking-tight">Onleaked</span>
        </a>
    </div>

    <div class="w-full sm:max-w-md p-8 glass-card rounded-2xl shadow-2xl relative overflow-hidden">
        <!-- Decorative glow -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-violet-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
