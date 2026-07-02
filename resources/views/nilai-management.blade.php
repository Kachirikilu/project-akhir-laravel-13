@php
    $pageTitle = __('Nilai Management');
    
    if (request()->routeIs('nilai-mahasiswa-management')) {
        $pageTitle = __('Manajemen Periode Nilai Mahasiswa');
    } elseif (request()->routeIs('rps-mahasiswa-management')) {
        $pageTitle = __('Manajemen Nilai Mahasiswa');
    }
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if(request()->routeIs('nilai-management'))
                <livewire:staff.nilai-management lazy />
            @elseif (request()->routeIs('nilai-mahasiswa-management'))
                <livewire:staff.nilai-management.nilai-mahasiswa-management :nim="request()->route('nim')" />
            @elseif (request()->routeIs('rps-mahasiswa-management'))
                <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management :nim="request()->route('nim')" :ganjil_genap="request()->route('ganjil_genap')" :akademik="request()->route('akademik')" />
            @endif
        </div>
    </div>
</x-layouts::app>
