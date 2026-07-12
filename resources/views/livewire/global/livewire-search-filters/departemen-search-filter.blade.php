@include('livewire.global.search-and-filters.secondary-search', [
    'inputXFilterString' => 'inputDpFilter',
    'xSearchResultsString' => 'dpSearchResults',
    'iconString' => 'book-open',
    'placeholderString' => 'Filter berdasarkan Departemen...',
    'xSearchQueryString' => 'dpSearchQuery',
    'selectedXId' => $selectedDpId,
    'selectedXName' => $dp_name,
    'resetXFilter' => 'resetDpFilter()',
    'xSearchQuery' => $dpSearchQuery,
    'xSearchResults' => $dpSearchResults,
    'selectXForFilterString' => 'selectDpForFilter',
    'typeXString' => 'departemen',
    'typeX2String' => 'kode_text',
    'typeX3String' => 'fakultas',
    'unfoundString' => 'Tidak ada Departemen ditemukan!',
    'minW' => 'sm:min-w-[360px]'
])
