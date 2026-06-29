@props([
    'paginator' => null,
    'onlyAdmin' => false,
    'targetLoading' => "
        filterByStatus, filterByAngkatan, filterByStrata, filterByMK,
        filterByRPS, filterByCPMK, filterBySCPMK, filterByCPL, filterByRef, filterByTimDosen, filterByDosen,
        filterByKelas, filterByMKgg, filterByRPSgg, filterByKelasgg,
        showDeleted, searchMode,
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
        generateRekapCapaian, generateRekapRPSProdi,
        generateRekapCPLProdi, generateRekapCPMKProdi, generateRekapSCPMKProdi,
        generateRekapCPLMahasiswa, generateRekapCPMKMahasiswa, generateRekapSCPMKMahasiswa,
        selectPrForFilter, resetPrFilter,
        selectDpForFilter, resetDpFilter,
        selectFkForFilter, resetFkFilter,
        selectMKForFilter, resetMKFilter,
        selectRPSForFilter, resetRPSFilter,
        selectCPMKForFilter, resetCPMKFilter,
        selectSCPMKForFilter, resetSCPMKFilter,
        selectCPLForFilter, resetCPLFilter,
        selectTimDosenForFilter, resetTimDosenFilter,
        selectDosenForFilter, resetDosenFilter,
        resetInputFilter, searchAngkatan, resetInputAngkatan,
        searchBobotRPS, resetInputBobotRPS,
        searchBobotCPMK, resetInputBobotCPMK,
        searchBobotSCPMK, resetInputBobotSCPMK,
        perPage, loadingTable, sortBy
        {{-- gotoPage, previousPage, nextPage, page --}}
    ",
])

@if (isset($leftHead) || isset($rightHead))
    <div
        class="pb-3 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 border-b table-border pb-1 mb-4">
        <div class="flex flex-row items-center gap-2">
            @if (isset($leftHead))
                {{ $leftHead }}
            @endif
        </div>
        <div class="flex items-center w-full md:w-auto justify-between md:justify-end">
            @if (isset($rightHead))
                {{ $rightHead }}
            @endif
        </div>
    </div>
@endif

@if (isset($sortir) || isset($search))
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 border-b table-border pb-2">
        <div class="scrollbar-tiny overflow-x-auto flex flex-row items-center gap-2 w-full lg:w-auto">
            @if (isset($sortir))
                {{ $sortir }}
            @endif
        </div>
        <div class="flex items-center w-full lg:w-auto justify-end lg:justify-end">
            @if (isset($search))
                {{ $search }}
            @endif
        </div>

    </div>
@endif

<div class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] shadow-lg rounded-lg overflow-hidden"
    id="table-results-container">
    <div class="scrollbar-x-large overflow-auto max-h-[1000px]">
        <table class="min-w-full divide-y">
            <thead class="sticky top-0 z-30 bg-[var(--main-table-color)] table-border">
                {{ $header }}
            </thead>
            <tbody wire:loading.class="opacity-50 pointer-events-none transition-opacity"
                wire:target="{{ $targetLoading }}" class="bg-[var(--second-table-color)] table-border divide-y">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if (isset($footer))
        {{ $footer }}
    @elseif($paginator)
        @include('livewire.global.table.footer-table', [
            'typeXString' => $paginator,
            'onlyAdmin' => $onlyAdmin,
        ])
    @endif
</div>
