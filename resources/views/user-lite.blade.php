<x-layouts::app :title="__('User Lite')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl border border-neutral-200 dark:border-neutral-700">
            <livewire:admin.user-lite />
        </div>
    </div>
</x-layouts::app>
