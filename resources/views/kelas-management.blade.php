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
            @if (request()->routeIs('kelas-management'))
                <livewire:all-role.kelas-management lazy :switchTable="request()->route('switchTable') ?? ''" :switchTable2="request()->route('switchTable2') ?? 'kelas-card'" />
            @elseif (request()->routeIs('jadwal-management'))
                <livewire:all-role.kelas-management.jadwal-management :kode_kelas="request()->route('kode_kelas')" :switchTable="request()->route('switchTable') ?? 'jadwal-card'" />
            @elseif(request()->routeIs('sesi-management'))
                <livewire:all-role.kelas-management.jadwal-management.sesi-management :kode_kelas="request()->route('kode_kelas')"
                    :kode_jadwal_short="request()->route('kode_jadwal_short')" :switchTable="request()->route('switchTable') ?? 'sesi-card'" />
            @endif
        </div>
    </div>
    <livewire:staff.obe-management.rps-management.show-rps-management lazy />
    @if (Auth::user()->admin || Auth::user()->dosen)
        @if (request()->routeIs('kelas-management'))
            <livewire:all-role.kelas-management.modal-kelas-management lazy />
            <livewire:all-role.kelas-management.delete-kelas-management lazy />
        @elseif (request()->routeIs('jadwal-management'))
            <livewire:all-role.kelas-management.modal-kelas-management :isJadwal="1" />
            <livewire:all-role.kelas-management.jadwal-management.modal-jadwal-management />
            <livewire:all-role.kelas-management.jadwal-management.delete-jadwal-management />
        @endif
        @if (request()->routeIs('jadwal-management') || request()->routeIs('sesi-management'))
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.excel-nilai-sesi-management />
        @endif
        @if (request()->routeIs('sesi-management'))
            <livewire:all-role.kelas-management.jadwal-management.modal-jadwal-management :isSesi="1" />
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.modal-sesi-management />
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.nilai-absensi-sesi-management />
        @endif
    @endif

    @if (Auth::user()->mahasiswa)
        @if (request()->routeIs('jadwal-management'))
            <livewire:all-role.kelas-management.jadwal-management.join-jadwal-management />
        @elseif (request()->routeIs('sesi-management'))
            <livewire:all-role.kelas-management.jadwal-management.left-jadwal-management />
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.absensi-sesi-management />
        @endif
    @endif
</x-layouts::app>
