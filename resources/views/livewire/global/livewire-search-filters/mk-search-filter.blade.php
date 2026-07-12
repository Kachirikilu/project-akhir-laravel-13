@include('livewire.global.search-and-filters.secondary-search', [
    'inputXFilterString' => 'inputMKFilter',
    'xSearchResultsString' => 'mkSearchResults',
    'iconString' => 'rectangle-stack',
    'placeholderString' => 'Filter berdasarkan Mata Kuliah...',
    'xSearchQueryString' => 'mkSearchQuery',
    'selectedXId' => $selectedMKId,
    'selectedXName' => $mk_name,
    'resetXFilter' => 'resetMKFilter()',
    'xSearchQuery' => $mkSearchQuery,
    'xSearchResults' => $mkSearchResults,
    'selectXForFilterString' => 'selectMKForFilter',
    'typeXString' => 'mk',
    'typeX2String' => 'sks_full',
    'typeX3String' => 'semester_text',
    'typeX4String' => 'wajib_text',
    'unfoundString' => 'Tidak ada Mata Kuliah ditemukan!',
    'minW' => 'sm:min-w-[512px]'
])
