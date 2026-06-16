@php
    $pageTitle = __('Nilai Mahasiswa');

    if (request()->routeIs('nilai-rps-mahasiswa')) {
        $pageTitle = __('Nilai RPS Mahasiswa');
    }
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if(request()->routeIs('nilai-mahasiswa'))
                <livewire:mahasiswa.nilai-mahasiswa />
            @elseif (request()->routeIs('nilai-rps-mahasiswa'))
                <livewire:mahasiswa.nilai-mahasiswa.nilai-rps-mahasiswa
                    :ganjil_genap="request()->route('ganjil_genap')" :akademik="request()->route('akademik')" />
            @endif
        </div>
    </div>
</x-layouts::app>
