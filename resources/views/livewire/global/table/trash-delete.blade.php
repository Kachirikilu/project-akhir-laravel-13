@if (Auth::user()->admin || Auth::user()->dosen)
    @if (!($onlyAdmin ?? false))
        <div class="flex flex-col w-full">
            <div class="{{ $mx ?? 'mx-3' }} my-3 flex items-center justify-end gap-3 p-2 bg-[var(--second-pop-up-color)] border table-border rounded-xl shadow-sm"
                x-data="{ localShowDeleted: @entangle('showDeleted').live }">
                <span class="text-xs sm:text-sm font-medium text-[var(--contrast-main-text)]">
                    <span x-text="localShowDeleted ? 'Mode Sampah' : 'Data Aktif'"></span>
                </span>
                <flux:icon name="check-circle" class="h-4 w-4 transition-colors duration-200"
                    ::class="!localShowDeleted ? 'text-[var(--focus-color)]' : 'text-gray-400'" />

                <button type="button" role="switch" @click="localShowDeleted = !localShowDeleted"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none"
                    :class="localShowDeleted ? 'bg-red-500' : 'bg-[var(--focus-color)]'">

                    <span aria-hidden="true"
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-300 ease-in-out"
                        :class="localShowDeleted ? 'translate-x-5' : 'translate-x-0'">
                    </span>
                </button>

                <flux:icon name="trash" class="h-4 w-4 transition-colors duration-200"
                    ::class="localShowDeleted ? 'text-red-500' : 'text-gray-400'" />

            </div>
        </div>
    @endif
@endif