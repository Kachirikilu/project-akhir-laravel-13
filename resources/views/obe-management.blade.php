@php
    $pageTitle = __('OBE Management');
    
    if (request()->route('switchTable') == 'rps') {
        $pageTitle = __('RPS Management');
    } elseif (request()->route('switchTable') == 'cpl') {
        $pageTitle = __('CPL Management');
    } elseif (request()->route('switchTable') == 'cpmk') {
        $pageTitle = __('CPMK Management');
    } elseif (request()->route('switchTable') == 'sub-cpmk') {
        $pageTitle = __('Sub-CPMK Management');
    } elseif (request()->route('switchTable') == 'refrensi') {
        $pageTitle = __('Referensi Management');
    } elseif (request()->route('switchTable') == 'dosen') {
        $pageTitle = __('Dosen Management');
    }
@endphp

<x-layouts::app :title="$pageTitle">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:staff.obe-management :switch-table="request()->route('switchTable') ?? 'rps'" />
        </div>
    </div>
</x-layouts::app>
