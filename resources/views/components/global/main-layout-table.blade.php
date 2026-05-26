<div class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] shadow-lg rounded-lg overflow-hidden" id="table-results-container">

    <div class="scrollbar-medium overflow-x-auto">
        <table class="min-w-full divide-y">
            {{-- Head Table --}}
            <thead
            class="bg-[var(--main-table-color)] border-[var(--border-table-color)]"
            {{-- class="bg-gray-50 dark:bg-neutral-700/50 border-gray-400 dark:border-neutral-500" --}}
            >
                    {{ $header }}
                    {{-- Body Table --}}
            <tbody 
            {{-- wire:loading.class="opacity-50"  --}}
                wire:loading.class="opacity-50 pointer-events-none transition-opacity"
                wire:target="
                filterByStatus, filterByStrata, filterByMK,
                filterByRPS, filterByCPMK, filterBySCPMK, filterByCPL, filterByRef, filterByDosen,
                filterByKelas,
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
                searchBobotRPS, resetInputBobotRPS,
                searchBobotCPMK, resetInputBobotCPMK,
                perPage, loadingTable, sortBy
                {{-- gotoPage, previousPage, nextPage, page --}}
                "
                class="bg-[var(--second-table-color)] border-[var(--border-table-color)] divide-y">
                {{ $slot }}
            </tbody>
        </table>


    </div>
    {{-- FOOTER --}}
    {{ $footer }}
</div>
