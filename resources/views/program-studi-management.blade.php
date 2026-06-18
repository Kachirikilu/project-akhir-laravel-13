@php
    $pageTitle = __('Study Program Management');

    if (request()->routeIs('capaian-management')) {
        $pageTitle = __('Capaian Mahasiswa Management');
    } elseif (request()->routeIs('rps-capaian-management')) {
        $pageTitle = __('RPS Capaian Management');
    }
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if(request()->routeIs('program-studi-management'))
                <livewire:admin.program-studi-management :switch-table="request()->route('switchTable') ?? ''" />
            @elseif (request()->routeIs('capaian-management'))
                <livewire:admin.prodi-management.capaian-management :kode_pr="request()->route('kode_pr')" :switch-table="request()->route('switchTable') ?? 'cpl'" />
            @elseif (request()->routeIs('rps-capaian-management'))
                <livewire:admin.prodi-management.capaian-management.rps-capaian-management :kode_cpl="request()->route('kode_cpl')" :kode_pr="request()->route('kode_pr')" />
            @endif
        </div>
    </div>
</x-layouts::app>
