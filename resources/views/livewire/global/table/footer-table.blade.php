<div>
    {{-- Loading indicator --}}
    {{-- <div wire:loading.flex
                wire:target="
                filterByUser, filterByStrata, filterByMK, filterByRPS, filterByCPMK, filterBySCPMK, filterByCPL, filterByRef,
                showDeleted,
                saveAllRows, processImport,
                saveUser, updateUser, destroyUser, restoreUser,
                saveProdi, updateProdi, destroyProdi, restoreProdi,
                saveMK, updateMK, destroyMK, restoreMK,
                saveRPS, updateRPS, destroyRPS, restoreRPS,
                saveCPMK, updateCPMK, destroyCPMK, restoreCPMK,
                saveSCPMK, updateSCPMK, destroySCPMK, restoreSCPMK,
                saveCPL, updateCPL, destroyCPL, restoreCPL,
                saveRef, updateRef, destroyRef, restoreRef,
                search,
                selectPrForFilter, resetPrFilter,
                selectDpForFilter, resetDpFilter,
                selectFkForFilter, resetFkFilter,
                selectMKForFilter, resetMKFilter,
                selectRPSForFilter, resetRPSFilter,
                selectCPMKForFilter, resetCPMKFilter,
                selectSCPMKForFilter, resetSCPMKFilter,
                selectCPLForFilter, resetCPLFilter,
                selectDosenForFilter, resetDosenFilter,
                resetInputFilter, searchAngkatan, resetInputAngkatan,
                sortBy, perPage, gotoPage, previousPage, nextPage, page"
        class="justify-center items-center py-4">
        <div class="flex items-center space-x-3 text-[var(--focus-color)]">
            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="block text-center italic">
                Memuat data...
            </span>
        </div>
    </div> --}}
    {{-- Pagination --}}
    @if ($typeXString->hasPages())
        <div class="p-4" id="pagination-links-container" wire:target="{{ $typeXString->getPageName() }}">
            {{ $typeXString->links('vendor.pagination.tailwind') }}
        </div>
    @endif


    <div class="flex flex-col">

        <div class="m-3 flex items-center gap-3 p-2 bg-[var(--second-pop-up-color)] border border-[var(--border-table-color)] rounded-xl shadow-sm"
            x-data="{ localShowDeleted: @entangle('showDeleted').live }"> {{-- Tambahkan .live agar Livewire langsung merespon --}}

            <flux:icon name="check-circle" class="h-4 w-4 transition-colors duration-200"
                ::class="!localShowDeleted ? 'text-[var(--focus-color)]' : 'text-gray-400'" />

            <button type="button" role="switch" {{-- Hapus wire:click, gunakan @click Alpine saja --}} @click="localShowDeleted = !localShowDeleted"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none"
                :class="localShowDeleted ? 'bg-red-500' : 'bg-[var(--focus-color)]'">

                <span aria-hidden="true"
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-300 ease-in-out"
                    :class="localShowDeleted ? 'translate-x-5' : 'translate-x-0'">
                </span>
            </button>

            <flux:icon name="trash" class="h-4 w-4 transition-colors duration-200"
                ::class="localShowDeleted ? 'text-red-500' : 'text-gray-400'" />

            <span class="text-sm font-medium text-[var(--contrast-main-text)] min-w-[90px]">
                <span x-text="localShowDeleted ? 'Mode Sampah' : 'Data Aktif'"></span>
            </span>
        </div>
    </div>
</div>
