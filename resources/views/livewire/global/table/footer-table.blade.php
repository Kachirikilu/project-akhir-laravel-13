<div>
    {{-- Loading indicator --}}
    {{-- <div wire:loading.flex
                wire:target="
                filterByUser, filterByStrata, filterByMK, filterByRPS, filterByCPMK, filterBySCPMK, filterByCPL, filterByRef,
                showDeleted,
                saveUserExcel, procesImportUserExcel,
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

    @include('livewire.global.table.trash-delete')
</div>
