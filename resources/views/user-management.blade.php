<x-layouts::app :title="__('User Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 mb-96 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:admin.user-management :switch-table="request()->route('switchTable') ?? ''" />
        </div>
    </div>

    <livewire:admin.user-management.excel-user-management />
    <livewire:admin.user-management.modal-user-management />
    <livewire:admin.user-management.delete-user-management />
</x-layouts::app>
