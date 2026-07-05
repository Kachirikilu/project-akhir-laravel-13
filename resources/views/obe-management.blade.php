@php
    $pageTitle = __('OBE Management');

    // if (request()->route('switchTable') == 'rps') {
    //     $pageTitle = __('RPS Management');
    // } elseif (request()->route('switchTable') == 'cpl') {
    //     $pageTitle = __('CPL Management');
    // } elseif (request()->route('switchTable') == 'cpmk') {
    //     $pageTitle = __('CPMK Management');
    // } elseif (request()->route('switchTable') == 'sub-cpmk') {
    //     $pageTitle = __('Sub-CPMK Management');
    // } elseif (request()->route('switchTable') == 'refrensi') {
    //     $pageTitle = __('Referensi Management');
    // } elseif (request()->route('switchTable') == 'dosen') {
    //     $pageTitle = __('Dosen Management');
    // }
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:staff.obe-management :switch-table="request()->route('switchTable') ?? 'rps'" />
        </div>
    </div>

    <livewire:staff.obe-management.rps-management.modal-rps-management />
    <livewire:staff.obe-management.rps-management.show-rps-management />
    <livewire:staff.obe-management.rps-management.delete-rps-management />

    <livewire:staff.obe-management.cpl-management.modal-cpl-management />
    <livewire:staff.obe-management.cpl-management.list-rps-cpl-management />
    <livewire:staff.obe-management.cpl-management.delete-cpl-management />

    <livewire:staff.obe-management.cpmk-management.modal-cpmk-management />
    <livewire:staff.obe-management.cpmk-management.delete-cpmk-management />

    <livewire:staff.obe-management.cpmk-management.modal-sub-cpmk-management />
    <livewire:staff.obe-management.cpmk-management.delete-sub-cpmk-management />

    <livewire:staff.obe-management.referensi-management.modal-referensi-management />
    <livewire:staff.obe-management.referensi-management.delete-referensi-management />

    <livewire:staff.obe-management.tim-dosen-management.modal-tim-dosen-management />
    <livewire:staff.obe-management.tim-dosen-management.list-rps-tim-dosen-management />
    <livewire:staff.obe-management.tim-dosen-management.delete-tim-dosen-management />

    <livewire:admin.user-management.modal-user-management />
    <livewire:admin.user-management.list-rps-user-management />
    <livewire:admin.user-management.delete-user-management />
</x-layouts::app>
