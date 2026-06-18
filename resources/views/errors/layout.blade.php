<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - @yield('code')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-zinc-950 flex items-center justify-center min-h-screen transition-colors duration-200">
    <div class="text-center p-6 max-w-md mx-auto">
        <h1 class="text-7xl font-black text-gray-300 dark:text-zinc-800 tracking-tight animate-pulse">
            @yield('code')
        </h1>
        
        <p class="text-xl font-bold text-gray-800 dark:text-zinc-200 mt-2">
            @yield('headline')
        </p>
        
        <div class="mt-4 bg-white dark:bg-zinc-900 px-5 py-3.5 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm leading-relaxed text-xs text-gray-500 dark:text-zinc-400">
            @yield('message')
        </div>

        <div class="mt-6 flex items-center justify-center gap-4">
            <a href="{{ url()->previous() }}" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center gap-1">
                &larr; Kembali
            </a>
            <span class="text-gray-300 dark:text-zinc-700">|</span>
            <a href="{{ url('/') }}" class="text-xs font-bold text-gray-600 dark:text-zinc-400 hover:underline">
                Ke Beranda
            </a>
        </div>
    </div>
</body>
</html>