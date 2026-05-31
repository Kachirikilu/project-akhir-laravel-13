<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Records Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glow-effect {
            box-shadow: 0 0 80px -10px rgba(16, 185, 129, 0.15);
        }
    </style>
</head>

<body class="h-full flex items-center justify-center p-6 antialiased selection:bg-emerald-500/30">

    <div
        class="absolute inset-0 bg-[radial-gradient(circle_at_50%_-20%,#1e293b,rgba(255,255,255,0))] pointer-events-none">
    </div>

    <div
        class="relative bg-slate-900/50 border border-slate-800/80 rounded-3xl py-10 px-28 text-center backdrop-blur-xl glow-effect overflow-hidden">

        {{-- <div
            class="inline-flex items-center gap-2 px-3 p-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold tracking-wider uppercase mb-8">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Live Database Counter
        </div> --}}

        <h1
            class="text-7xl md:text-8xl font-extrabold text-transparent bg-clip-text bg-gradient-to-b from-white via-slate-100 to-slate-400 tracking-tighter mb-4 tabular-nums drop-shadow-sm">
            {{ number_format($totalRows, 0, ',', '.') }}
        </h1>

        <p class="text-sm md:text-base font-medium text-slate-400 tracking-wide">
            Total Jumlah Data di Seluruh Database
        </p>

        <div
            class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-emerald-500/40 to-transparent">
        </div>
    </div>

</body>

</html>
