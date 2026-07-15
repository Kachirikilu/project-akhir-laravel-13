<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="px-3 sm:px-5 md:px-8 lg:px-12 xl:px-15 2xl:px-21">
        {{ $slot }}
    </flux:main>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- <script>
        const savedTheme = localStorage.getItem('app-theme') || 'blue';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script> --}}
</x-layouts::app.sidebar>
