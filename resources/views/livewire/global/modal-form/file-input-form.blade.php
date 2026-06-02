<div wire:key="file-input-{{ $modelString }}-{{ $alpine ?? 'config' }}">
    @include('livewire.global.modal-form.partial.label')

    <div class="mt-1" >
        <div class="relative w-full mt-1">
            <input wire:model="{{ $modelString }}" type="file" id="{{ $modelString }}" accept=".xlsx, .xls"
                wire:key="{{ $wireKeyString }}"
                class="
                    bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]
                    placeholder-[var(--contrast-third-text)]
                    
                    w-full border rounded-lg px-3 py-2 text-gray-800 dark:text-gray-200 font-medium
                    file:mr-4 file:py-1 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold file:text-white
                    transition-all cursor-pointer"
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
    </div>

    @if (!empty($message))
        <span class="text-red-500 text-sm mt-1 block">
            {{ $message }}
        </span>
    @endif
</div>
