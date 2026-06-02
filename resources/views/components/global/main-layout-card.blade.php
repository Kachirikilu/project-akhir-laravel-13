@props([
    'paginator' => null,
    'onlyAdmin' => false,
    'targetLoading' => "
                filterByStatus, filterByStrata, filterByMK,
                filterByRPS, filterByCPMK, filterBySCPMK, filterByCPL, filterByRef, filterByDosen,
                filterByKelas,
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
                searchBobotRPS, resetInputBobotRPS,
                searchBobotCPMK, resetInputBobotCPMK,
                perPage, loadingTable, sortBy
                {{-- gotoPage, previousPage, nextPage, page --}}
            ",
])

<div class="space-y-6">

    @if (isset($leftHead) || isset($rightHead))
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 border-b border-[var(--border-table-color)] pb-2">
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
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 border-b border-[var(--border-table-color)] pb-2">
            <div class="scrollbar-thin overflow-x-auto flex flex-row items-center gap-2 w-full lg:w-auto">
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

    <div class="max-w-[4500px] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-3 3xl:grid-cols-4 4xl:grid-cols-5 5xl:grid-cols-6 gap-4 items-start"
        wire:loading.class="opacity-50 pointer-events-none transition-opacity" wire:target="{{ $targetLoading }}">

        {{ $slot }}

    </div>

    @if (isset($footer))
        <div class="mt-4 pt-4 border-t border-[var(--border-table-color)]">
            {{ $footer }}
        </div>
    @elseif($paginator)
        <div class="mt-4 pt-4 border-t border-[var(--border-table-color)]">
            @include('livewire.global.table.footer-table', [
                'typeXString' => $paginator,
                'onlyAdmin' => $onlyAdmin,
            ])
        </div>
    @endif
</div>
