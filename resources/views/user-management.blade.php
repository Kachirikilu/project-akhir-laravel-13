<x-layouts::app :title="__('User Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:admin.user-management lazy :switch-table="request()->route('switchTable') ?? ''" />
        </div>
    </div>
</x-layouts::app>
