@php
    $pageTitle = __('Kelas Management');

    if (request()->routeIs('jadwal-management')) {
        $pageTitle = __('Jadwal Kelas Management');
    } elseif (request()->routeIs('sesi-management')) {
        $pageTitle = __('Sesi Kelas Management');
    }
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if(request()->routeIs('kelas-management'))
                <livewire:all-role.kelas-management :switchTable="request()->route('switchTable') ?? ''" />
            @elseif (request()->routeIs('jadwal-management'))
                <livewire:all-role.kelas-management.jadwal-management :kode="request()->route('kode')" :switchTable="request()->route('switchTable') ?? 'jadwal-card'" />
            @elseif(request()->routeIs('sesi-management'))
                <livewire:all-role.kelas-management.jadwal-management.sesi-management :kode="request()->route('kode')"
                    :kode_jadwal_url="request()->route('kode_jadwal_url')" :switchTable="request()->route('switchTable') ?? 'sesi-card'" />
            @endif
        </div>
    </div>
</x-layouts::app>
