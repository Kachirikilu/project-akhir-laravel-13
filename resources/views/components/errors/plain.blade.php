<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kesalahan Sistem</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        /* Menggunakan font sistem yang bersih seperti preferensi VS Code / Laragon Anda */
        body {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
        }
    </style>
</head>

<body
    class="flex min-h-full flex-col bg-zinc-50 text-zinc-900 transition-colors duration-200 dark:bg-zinc-900 dark:text-zinc-100">

    <main class="mx-auto flex w-full max-w-xl flex-1 flex-col justify-center px-6 py-16 sm:py-24">
        <div class="text-center">

            {{-- IKON PERINGATAN (Dinamis & Halus) --}}
            <div
                class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-500 ring-1 ring-amber-500/20 mb-6 dark:bg-amber-500/5">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>

            {{-- HEADER STATUS --}}
            <p class="text-xs font-bold uppercase tracking-widest text-amber-500">Aplikasi Mengalami Kendala</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">Internal
                Server Error</h1>

            {{-- PESAN ERROR UTAMA --}}
            <div
                class="mt-4 rounded-xl border border-zinc-200/80 bg-white p-4 shadow-xs dark:border-zinc-800 dark:bg-zinc-950">
                <p class="text-sm font-medium leading-relaxed text-zinc-600 dark:text-zinc-400">
                    {{-- Jika pesan kosong / default dari internal server, beri teks fallback ramah pengguna --}}
                    {{ empty(trim($message)) ? 'Permintaan tidak dapat diproses oleh server karena gangguan internal sementara.' : $message }}
                </p>
            </div>

            {{-- TOMBOL AKSI INTERAKTIF --}}
            <div class="mt-8 flex items-center justify-center gap-x-3">
                {{-- Tombol Kembali (Memicu History Browser Kembali Semula) --}}
                <button type="button" onclick="window.history.back()"
                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-xs hover:bg-zinc-50 transition dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700/80">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </button>

                {{-- Tombol Muat Ulang Halaman --}}
                <button type="button" onclick="window.location.reload()"
                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-zinc-800 transition dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Segarkan
                </button>
            </div>

        </div>
    </main>

    {{-- FOOTER IDENTITAS --}}
    <footer class="w-full shrink-0 border-t border-zinc-200/60 py-4 text-center dark:border-zinc-800/50">
        <p class="text-xs text-zinc-400 dark:text-zinc-500">&copy; {{ date('Y') }} Tugas Akhir - Universitas
            Sriwijaya. All role core system.</p>
    </footer>

</body>

</html>
