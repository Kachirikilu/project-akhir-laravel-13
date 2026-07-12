@include('livewire.global.search-and-filters.secondary-search', [
    'inputXFilterString' => 'inputRPSFilter',
    'xSearchResultsString' => 'rpsSearchResults',
    'iconString' => 'clipboard-document-list',
    'placeholderString' => 'Filter berdasarkan RPS...',
    'xSearchQueryString' => 'rpsSearchQuery',
    'selectedXId' => $selectedRPSId,
    'selectedXName' => $rps_name,
    'resetXFilter' => 'resetRPSFilter()',
    'xSearchQuery' => $rpsSearchQuery,
    'xSearchResults' => $rpsSearchResults,
    'selectXForFilterString' => 'selectRPSForFilter',
    'typeXString' => 'rps_with_kode',
    'typeX2String' => 'sks_full',
    'typeX3String' => 'wajib_text',
    'typeX4String' => 'draf_full',
    'unfoundString' => 'Tidak ada RPS ditemukan!',
    'minW' => 'sm:min-w-[512px]'
])
