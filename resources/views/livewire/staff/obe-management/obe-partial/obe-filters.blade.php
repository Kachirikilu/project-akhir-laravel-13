<div x-show="activeTab == 'rps'" x-transition:enter="transition ease-out duration-1000"
    x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
    class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
    <div class="min-w-0 flex-1 overflow-hidden">
        @if (Auth::user()->dosen)
            @include('livewire.global.search-and-filters.filter-mode', [
                'filterByFunc' => 'filterByRPS',
                'filterString' => 'filterRPS',
                'totalTab' => $stats['rps-saya'],
                'totalTab1' => $stats['rps-prodi'],
                'totalTab2' => $stats['rps'],
                'totalTab3' => $stats['rps-akademik'],
                'totalTab4' => $stats['rps-rev-new'],
                'totalTab5' => $stats['rps-aktif'],
                'totalTab6' => $stats['rps-draf'],
                'totalTab7' => $stats['rps-older-5'],
                'tab1String' => 'rps-prodi',
                'tab2String' => 'rps-all',
                'tab3String' => 'rps-akademik',
                'tab4String' => 'rps-rev-new',
                'tab5String' => 'rps-aktif',
                'tab6String' => 'rps-draf',
                'tab7String' => 'rps-older-5',
                'tabName' => 'RPS Saya',
                'tab1Name' => Auth::user()->prodi,
                'tab2Name' => 'Semua RPS',
                'tab3Name' => 'Terbaru',
                'tab4Name' => 'Baru Direvisi',
                'tab5Name' => 'Aktif',
                'tab6Name' => 'Draf',
                'tab7Name' => '>5 Tahun Lalu',
            ])
        @else
            @include('livewire.global.search-and-filters.filter-mode', [
                'filterByFunc' => 'filterByRPS',
                'filterString' => 'filterRPS',
                'totalTab' => $stats['rps-prodi'],
                'totalTab1' => $stats['rps'],
                'totalTab2' => $stats['rps-akademik'],
                'totalTab3' => $stats['rps-rev-new'],
                'totalTab4' => $stats['rps-aktif'],
                'totalTab5' => $stats['rps-draf'],
                'totalTab6' => $stats['rps-older-5'],
                'tabHiddenString' => 'rps-prodi',
                'tab1String' => 'rps-all',
                'tab2String' => 'rps-akademik',
                'tab3String' => 'rps-rev-new',
                'tab4String' => 'rps-aktif',
                'tab5String' => 'rps-draf',
                'tab6String' => 'rps-older-5',
                'tabName' => Auth::user()->prodi,
                'tab1Name' => 'Semua RPS',
                'tab2Name' => 'Terbaru',
                'tab3Name' => 'Baru Direvisi',
                'tab4Name' => 'Aktif',
                'tab5Name' => 'Draf',
                'tab6Name' => '>5 Tahun Lalu',
            ])
        @endif
    </div>
    <div class="shrink-0">
        @include('livewire.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
            'key' => 'page-control-rps',
            'autoSmall' => 'lg',
        ])
    </div>
</div>

<div x-show="activeTab == 'cpl'" x-transition:enter="transition ease-out duration-1000"
    x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
    class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
    <div class="min-w-0 flex-1 overflow-hidden">
        @include('livewire.global.search-and-filters.filter-mode', [
            'filterByFunc' => 'filterByCPL',
            'filterString' => 'filterCPL',
            'totalTab' => $stats['cpl'],
            'totalTab1' => $stats['cpl-month'],
            'totalTab2' => $stats['cpl-6-months'],
            'totalTab3' => $stats['cpl-year'],
            'totalTab4' => $stats['cpl-older-5'],
            'tab1String' => 'cpl-month',
            'tab2String' => 'cpl-6-months',
            'tab3String' => 'cpl-year',
            'tab4String' => 'cpl-older-5',
            'tabName' => 'Semua CPL',
            'tab1Name' => 'Terbaru',
            'tab2Name' => 'Semester Ini',
            'tab3Name' => 'Tahun Ini',
            'tab4Name' => '>5 Tahun Lalu',
        ])
    </div>
    <div class="shrink-0">
        @include('livewire.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100],
            'key' => 'page-control-cpl',
            'autoSmall' => 'lg',
        ])
    </div>
</div>

<div x-show="activeTab == 'cpmk'" x-transition:enter="transition ease-out duration-1000"
    x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
    class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
    <div class="min-w-0 flex-1 overflow-hidden">
        @include('livewire.global.search-and-filters.filter-mode', [
            'filterByFunc' => 'filterByCPMK',
            'filterString' => 'filterCPMK',
            'totalTab' => $stats['cpmk'],
            'totalTab1' => $stats['cpmk-month'],
            'totalTab2' => $stats['cpmk-6-months'],
            'totalTab3' => $stats['cpmk-year'],
            'totalTab4' => $stats['cpmk-older-5'],
            'tab1String' => 'cpmk-month',
            'tab2String' => 'cpmk-6-months',
            'tab3String' => 'cpmk-year',
            'tab4String' => 'cpmk-older-5',
            'tabName' => 'Semua CPMK',
            'tab1Name' => 'Terbaru',
            'tab2Name' => 'Semester Ini',
            'tab3Name' => 'Tahun Ini',
            'tab4Name' => '>5 Tahun Lalu',
        ])
    </div>
    <div class="shrink-0">
        @include('livewire.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300],
            'key' => 'page-control-cpmk',
            'autoSmall' => 'lg',
        ])
    </div>
</div>

<div x-show="activeTab == 'sub-cpmk'" x-transition:enter="transition ease-out duration-1000"
    x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
    class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
    <div class="min-w-0 flex-1 overflow-hidden">
        @include('livewire.global.search-and-filters.filter-mode', [
            'filterByFunc' => 'filterBySCPMK',
            'filterString' => 'filterSCPMK',
            'totalTab' => $stats['scpmk'],
            'totalTab1' => $stats['scpmk-month'],
            'totalTab2' => $stats['scpmk-6-months'],
            'totalTab3' => $stats['scpmk-year'],
            'totalTab4' => $stats['scpmk-older-5'],
            'tab1String' => 'scpmk-month',
            'tab2String' => 'scpmk-6-months',
            'tab3String' => 'scpmk-year',
            'tab4String' => 'scpmk-older-5',
            'tabName' => 'Semua Sub-CPMK',
            'tab1Name' => 'Terbaru',
            'tab2Name' => 'Semester Ini',
            'tab3Name' => 'Tahun Ini',
            'tab4Name' => '>5 Tahun Lalu',
        ])
    </div>
    <div class="shrink-0">
        @include('livewire.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300, 500],
            'key' => 'page-control-sub-cpmk',
            'autoSmall' => 'lg',
        ])
    </div>
</div>
