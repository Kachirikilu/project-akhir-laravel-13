<x-layouts::app :title="__('OBE Management')">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:staff.rps-management :switch-table="request()->route('switchTable') ?? 'rps'" />
        </div>
    </div>
</x-layouts::app>
