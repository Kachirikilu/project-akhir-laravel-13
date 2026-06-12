@php
    $pageTitle = __('Jadwal Kelas');

    if (request()->routeIs('sesi-mahasiswa')) {
        $pageTitle = __('Sesi Kelas Mahasiswa');
    }
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if (request()->routeIs('jadwal-mahasiswa'))
                <livewire:all-role.kelas-management.jadwal-management :isJadwalMhs="true" :switchTable="request()->route('switchTable') ?? 'jadwal-card'" />
            @elseif(request()->routeIs('sesi-mahasiswa'))
                <livewire:all-role.kelas-management.jadwal-management.sesi-management :kode_kelas="request()->route('kode_kelas')"
                    :kode_jadwal="request()->route('kode_jadwal')" :isJadwalMhs="true" :switchTable="request()->route('switchTable') ?? 'sesi-card'" />
            @endif
        </div>
    </div>
</x-layouts::app>