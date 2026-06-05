<x-layouts::app :title="__('Nilai Kuliah')">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            <livewire:mahasiswa.nilai-mahasiswa 
            {{-- :switch-table="request()->route('switchTable') ?? 'nilai'" --}}
            />
        </div>
    </div>
</x-layouts::app>
