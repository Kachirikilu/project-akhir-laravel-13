@include('livewire.global.search-and-filters.secondary-search', [
    'inputXFilterString' => 'inputDosenFilter',
    'xSearchResultsString' => 'dosenSearchResults',
    'iconString' => 'user',
    'placeholderString' => 'Filter berdasarkan Dosen...',
    'xSearchQueryString' => 'dosenSearchQuery',
    'selectedXId' => $selectedDosenId,
    'selectedXName' => $dosen_name,
    'resetXFilter' => 'resetDosenFilter()',
    'xSearchQuery' => $dosenSearchQuery,
    'xSearchResults' => $dosenSearchResults,
    'selectXForFilterString' => 'selectDosenForFilter',

    'x2HeadString' => 'NIP.',
    'x4HeadString' => 'Status:',

    'typeXString' => 'name',
    'typeX2String' => 'kode',
    'typeX3String' => 'tingkat_full',
    'typeX4String' => 'status',
    'typeKodeString' => 'kode_pr',
    'unfoundString' => 'Tidak ada Dosen ditemukan!',
])
