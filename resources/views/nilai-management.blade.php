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
                <livewire:staff.nilai-management :switch-table="request()->route('switchTable') ?? 'mahasiswa'" />
            @elseif (request()->routeIs('nilai-mahasiswa-management'))
                <livewire:staff.nilai-management.nilai-mahasiswa-management :nim="request()->route('nim')" />
            @elseif (request()->routeIs('rps-mahasiswa-management'))
                <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management :nim="request()->route('nim')" :ganjil_genap="request()->route('ganjil_genap')" :akademik="request()->route('akademik')" />
            @elseif (request()->routeIs('rps-capaian-mahasiswa-management'))
                <livewire:staff.nilai-management.rps-capaian-mahasiswa-management :kode_rps="request()->route('kode_rps')" />
            @endif

        </div>
    </div>

    @if(request()->routeIs('nilai-management') && Auth::user()->admin)
        <livewire:admin.user-management.modal-user-management />
        <livewire:admin.user-management.delete-user-management />
    @endif
    @if (request()->routeIs('nilai-management') || request()->routeIs('rps-mahasiswa-management'))
        <livewire:staff.obe-management.rps-management.show-rps-management />
        <livewire:admin.user-management.list-rps-user-management :noModalRPS="1" />
    @endif
    @if (request()->routeIs('rps-mahasiswa-management'))
        <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.delete-rps-mahasiswa-management />
    @endif

    @if (request()->routeIs('rps-mahasiswa-management') || request()->routeIs('rps-capaian-mahasiswa-management'))
        <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.modal-rps-mahasiswa-management />
    @endif
</x-layouts::app>
