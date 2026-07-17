
@php
    $justifyClass = $isLeft ?? false ? 'sm:justify-start' : 'sm:justify-end';
@endphp
<div class="flex flex-col-reverse sm:flex-row {{ $justifyClass }} sm:items-start gap-2 w-full sm:w-auto mt-auto">
    <div class="flex flex-col sm:flex-row gap-2 {{ $mt ?? 'mt-2' }}">

        {{-- Button Simpan Excel (Green) --}}
        <template x-if="$store.{{ $alpine ?? 'config' }}?.typeModal == 'excel'" x-cloak>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                wire:target="{{ $wireLoading ?? null }}, {{ $wireLoading2 ?? null }}"
                class="text-xs sm:text-sm cursor-pointer w-full sm:w-auto bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 active:hover:bg-green-700 text-white border-none transition-all duration-200 shadow-sm dark:shadow-green-500/20">

                <span wire:loading.remove wire:target="{{ $wireLoading2 ?? $wireLoading ?? null }}" class="text-white">
                    Simpan Semua Data
                    {{ isset($parsedUserRows) && count($parsedUserRows) > 0 ? '(' . count($parsedUserRows) . ' Baris)' : null }}
                </span>
                <span wire:loading wire:target="{{ $wireLoading2 ?? $wireLoading ?? null }}" class="text-white">
                    Menyimpan...
                </span>
            </flux:button>
        </template>

        {{-- Button Simpan/Update Biasa (Indigo) --}}
        <template x-if="$store.{{ $alpine ?? 'config' }}?.typeModal !== 'excel'" x-cloak>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="{{ $targetX }}"
                class="text-xs sm:text-sm cursor-pointer w-full sm:w-auto
                bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90
                shadow-sm text-white border-none transition-all duration-200">

                <span x-text="$store.{{ $alpine ?? 'config' }}?.isEdit ? 'Perbarui Data' : 'Simpan Data'"
                    wire:loading.remove wire:target="{{ $targetX }}" class="text-white">
                </span>
                <span wire:loading wire:target="{{ $targetX }}" class="text-white">
                    Memproses...
                </span>
            </flux:button>
        </template>

        {{-- Button Batal (Gray) --}}
        <flux:modal.close>
            <flux:button variant="primary"
                class="text-xs sm:text-sm cursor-pointer w-full sm:w-auto 
                {{-- bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 --}}
                bg-[var(--sub-table-color)] hover:bg-[var(--main-table-color)] active:bg-[var(--main-table-color)]/90
                text-[var(--contrast-second-text)]
                
                border-none transition-colors duration-200">
                Batal
            </flux:button>
        </flux:modal.close>

    </div>
</div>
