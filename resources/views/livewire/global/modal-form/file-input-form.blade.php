<div wire:key="file-input-{{ $modelString }}-{{ $alpine ?? 'config' }}">
    @include('livewire.global.modal-form.partial.label')

    <div class="mt-1">
        <div class="relative w-full mt-1 flex items-center gap-2">
            
            <div class="relative flex-1">
                <input wire:model="{{ $modelString }}" type="file" id="{{ $modelString }}"
                    accept="{{ $typeFile ?? '.xlsx, .xls' }}"
                    wire:key="{{ $wireKeyString }}"
                    {{  $multiFile ?? false ? 'multiple' : '' }}
                    class="
                        bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]
                        placeholder-[var(--contrast-third-text)]
                        
                        w-full border rounded-lg px-3 py-2 text-gray-800 dark:text-gray-200 font-medium
                        file:mr-4 file:py-1 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold file:text-white
                        transition-all cursor-pointer pr-24"
                        x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIconBg">

                {{-- Status Loading --}}
                <div wire:loading.flex wire:target="{{ $modelString }}, {{ $wireLoading ?? null }}"
                    class="absolute inset-y-0 right-3 items-center">
                    <div class="text-[var(--focus-color)] flex items-center space-x-2 text-xs pl-2 rounded-r-lg">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="hidden sm:inline">Memuat...</span>
                    </div>
                </div>
            </div>

            {{-- TOMBOL HAPUS FILE (Muncul jika file sudah terunggah di temporary Livewire) --}}
            @if ($this->{$modelString})
                <button type="button" 
                    wire:click="{{ $fileDelete ?? 'clearExcelFile' }}"
                    wire:loading.attr="disabled"
                    class="px-3 py-3 bg-red-50 hover:bg-red-100 dark:bg-red-950/30 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/50 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1 shrink-0 cursor-pointer"
                    title="Hapus file yang dipilih">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <span class="hidden md:inline">Hapus</span>
                </button>
            @endif

        </div>
    </div>

    @if (!empty($message))
        <span class="text-red-500 text-sm mt-1 block">
            {{ $message }}
        </span>
    @endif
</div>