@php
    $pageTitle = __('Capaian Mahasiswa Management');
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:admin.prodi-management.capaian-management :isProdiDsn="1" :kode_pr="Auth::user()->dosen->pr_rel->kode" :switch-table="request()->route('switchTable') ?? 'cpl'" />
        </div>
    </div>

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

    <livewire:admin.user-management.list-rps-user-management lazy />
</x-layouts::app>
