<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const savedTheme = localStorage.getItem('app-theme') || 'blue';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</x-layouts::app.sidebar>
