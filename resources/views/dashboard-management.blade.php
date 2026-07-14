@php
    $pageTitle = __('Dashboard ' . Auth::user()->role);
@endphp

<x-layouts::app :title="$pageTitle">

    <div class="flex h-full max-w-[4600px] flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-96 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:all-role.dashboard-management />
        </div>
    </div>

    <livewire:all-role.dashboard-management.modal-wa-dashboard-management />

</x-layouts::app>
