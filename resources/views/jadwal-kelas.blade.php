@php
    $role = Auth::user()->role;
    $prefix = request()->routeIs('sesi-jadwal-kelas') ? 'Sesi Kelas' : 'Jadwal Kelas';
    
    $pageTitle = __("$prefix $role");
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full max-w-[4600px] flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-96 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if (request()->routeIs('jadwal-kelas'))
                <livewire:all-role.kelas-management.jadwal-management :isJadwalOnly="true" :switchTable="request()->route('switchTable') ?? 'jadwal-card'" />
            @elseif(request()->routeIs('sesi-jadwal-kelas'))
                <livewire:all-role.kelas-management.jadwal-management.sesi-management :kode_kelas="request()->route('kode_kelas')"
                    :kode_jadwal_short="request()->route('kode_jadwal_short')" :isJadwalOnly="true" :switchTable="request()->route('switchTable') ?? 'sesi-hari-ini'" />
            @endif
        </div>
    </div>

    @if (Auth::user()->admin || Auth::user()->dosen)
        @if (request()->routeIs('jadwal-kelas'))
            <livewire:all-role.kelas-management.jadwal-management.modal-jadwal-management />
            <livewire:all-role.kelas-management.jadwal-management.delete-jadwal-management />
        @endif
        @if (request()->routeIs('jadwal-kelas') || request()->routeIs('sesi-jadwal-kelas'))
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.excel-nilai-sesi-management />
        @endif
        @if (request()->routeIs('sesi-jadwal-kelas'))
            <livewire:all-role.kelas-management.jadwal-management.modal-jadwal-management :isSesi="1" />
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.modal-sesi-management />
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.nilai-absensi-sesi-management />
        @endif
    @endif

    @if (Auth::user()->mahasiswa)
        @if (request()->routeIs('sesi-jadwal-kelas'))
            <livewire:all-role.kelas-management.jadwal-management.left-jadwal-management />
            <livewire:all-role.kelas-management.jadwal-management.sesi-management.absensi-sesi-management />
        @endif
    @endif
</x-layouts::app>
