<div x-data="{ activeTab: @entangle($filterString) }" class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1">

    {{-- Tab Semua --}}
    @include('livewire.global.search-and-filters.partial.tab-filter', [
        'xString' => $filterByFunc,
        'xFilter' => $filterString,
        'tabFilter' => $totalTab ?? null,
        'tabString' => $tabString ?? null,
        'tabHiddenString' => $tabHiddenString ?? null,
        'tabNameString' => $tabName ?? ucfirst($tabString),
    ])

    {{-- Tab 1 --}}
    @include('livewire.global.search-and-filters.partial.tab-filter', [
        'xString' => $filterByFunc,
        'xFilter' => $filterString,
        'tabFilter' => $totalTab1 ?? null,
        'tabString' => $tab1String ?? null,
        'tabHiddenString' => $tabHiddenString1 ?? null,
        'tabNameString' => $tab1Name ?? ucfirst($tab1String),
    ])

    {{-- Tab 2 --}}
    @include('livewire.global.search-and-filters.partial.tab-filter', [
        'xString' => $filterByFunc,
        'xFilter' => $filterString,
        'tabFilter' => $totalTab2 ?? null,
        'tabString' => $tab2String ?? null,
        'tabHiddenString' => $tabHiddenString2 ?? null,
        'tabNameString' => $tab2Name ?? ucfirst($tab2String),
    ])

    @if ($tab3String ?? null)
        {{-- Tab 3 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
            'xString' => $filterByFunc,
            'xFilter' => $filterString,
            'tabFilter' => $totalTab3 ?? null,
            'tabString' => $tab3String ?? null,
            'tabHiddenString' => $tabHiddenString3 ?? null,
            'tabNameString' => $tab3Name ?? ucfirst($tab3String ?? null),
        ])
    @endif

    @if ($tab4String ?? null)
        {{-- Tab 4 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
            'xString' => $filterByFunc,
            'xFilter' => $filterString,
            'tabFilter' => $totalTab4 ?? null,
            'tabString' => $tab4String ?? null,
            'tabHiddenString' => $tabHiddenString4 ?? null,
            'tabNameString' => $tab4Name ?? ucfirst($tab4String ?? null),
        ])
    @endif

    @if ($tab5String ?? null)
        {{-- Tab 5 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
            'xString' => $filterByFunc,
            'xFilter' => $filterString,
            'tabFilter' => $totalTab5 ?? null,
            'tabString' => $tab5String ?? null,
            'tabHiddenString' => $tabHiddenString5 ?? null,
            'tabNameString' => $tab5Name ?? ucfirst($tab5String ?? null),
        ])
    @endif

    @if ($tab6String ?? null)
        {{-- Tab 6 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
            'xString' => $filterByFunc,
            'xFilter' => $filterString,
            'tabFilter' => $totalTab6 ?? null,
            'tabString' => $tab6String ?? null,
            'tabHiddenString' => $tabHiddenString6 ?? null,
            'tabNameString' => $tab6Name ?? ucfirst($tab6String ?? null),
        ])
    @endif

    @if ($tab7String ?? null)
        {{-- Tab 7 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
            'xString' => $filterByFunc,
            'xFilter' => $filterString,
            'tabFilter' => $totalTab7 ?? null,
            'tabString' => $tab7String ?? null,
            'tabHiddenString' => $tabHiddenString7 ?? null,
            'tabNameString' => $tab7Name ?? ucfirst($tab7String ?? null),
        ])
    @endif
</div>
