<x-layouts::app :title="__('Mata Kuliah Management')">
    <div class="flex h-full max-w-[4600px] flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-96 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:staff.mata-kuliah-management :switch-table="request()->route('switchTable') ?? ''" />
        </div>
    </div>

    <livewire:staff.mk-management.modal-mk-management />
    <livewire:staff.mk-management.delete-mk-management />
</x-layouts::app>
