@props([
    'paginator' => null,
    'onlyAdmin' => false,
    'targetLoading' => "
                filterByStatus, filterByAngkatan, filterByStrata, filterByMK,
                filterByRPS, filterByCPMK, filterBySCPMK, filterByCPL, filterByRef, filterByDosen,
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
                selectDosenForFilter, resetDosenFilter,
                resetInputFilter, searchAngkatan, resetInputAngkatan,
                searchBobotRPS, resetInputBobotRPS,
                searchBobotCPMK, resetInputBobotCPMK,
                searchBobotSCPMK, resetInputBobotSCPMK,
                perPage, loadingTable, sortBy
                {{-- gotoPage, previousPage, nextPage, page --}}
            ",
    'layoutGrid' => true,
])

<div class="space-y-6">

    @if (isset($leftHead) || isset($rightHead))
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 border-b table-border pb-2">
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
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 border-b table-border pb-2">
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

    @php
        if ($layoutGrid) {
            $layout_grid = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-3 3xl:grid-cols-4 4xl:grid-cols-5 5xl:grid-cols-6 gap-4 items-start';
        } else {
            $layout_grid = 'space-y-4';
        }
    @endphp
    <div class="max-w-[4500px] {{ $layout_grid ?? null }}"
        wire:loading.class="opacity-50 pointer-events-none transition-opacity" wire:target="{{ $targetLoading }}">
        {{ $slot }}
    </div>

    @if (isset($emptys))
    <div class="max-w-[4500px]"
        wire:loading.class="opacity-50 pointer-events-none transition-opacity" wire:target="{{ $targetLoading }}">
        {{ $emptys }}
    </div>
    @endif

    @if (isset($footer))
        <div class="mt-4 pt-4 border-t table-border">
            {{ $footer }}
        </div>
    @elseif($paginator)
        <div class="mt-4 pt-4 border-t table-border">
            @include('livewire.global.table.footer-table', [
                'typeXString' => $paginator,
                'onlyAdmin' => $onlyAdmin,
            ])
        </div>
    @endif
</div>
