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

    @if (request()->routeIs('program-studi-management'))
    <livewire:admin.prodi-management.modal-prodi-management lazy />
    <livewire:admin.prodi-management.delete-prodi-management lazy />
    @endif

    @if (request()->routeIs('capaian-management'))
        <livewire:staff.obe-management.rps-management.modal-rps-management lazy />
        <livewire:staff.obe-management.rps-management.show-rps-management lazy />
        <livewire:staff.obe-management.rps-management.delete-rps-management lazy />

        <livewire:staff.obe-management.cpl-management.modal-cpl-management lazy />
        <livewire:staff.obe-management.cpl-management.list-rps-cpl-management lazy />
        <livewire:staff.obe-management.cpl-management.delete-cpl-management lazy />

        <livewire:staff.obe-management.cpmk-management.modal-cpmk-management lazy />
        <livewire:staff.obe-management.cpmk-management.delete-cpmk-management lazy />

        <livewire:staff.obe-management.cpmk-management.modal-sub-cpmk-management lazy />
        <livewire:staff.obe-management.cpmk-management.delete-sub-cpmk-management lazy />

        <livewire:staff.obe-management.referensi-management.modal-referensi-management lazy />

        <livewire:staff.obe-management.tim-dosen-management.modal-tim-dosen-management lazy />
        <livewire:staff.obe-management.tim-dosen-management.list-rps-tim-dosen-management lazy />

        <livewire:admin.user-management.modal-user-management lazy />
        <livewire:admin.user-management.list-rps-user-management lazy />
        <livewire:admin.user-management.delete-user-management lazy />
    @endif
</x-layouts::app>
