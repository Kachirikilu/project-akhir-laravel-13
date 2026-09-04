@include('livewire.global.search-and-filters.secondary-search', [
    'inputXFilterString' => 'inputPrFilter',
    'xSearchResultsString' => 'prSearchResults',
    'iconString' => 'academic-cap',
    'placeholderString' => 'Filter berdasarkan Program Studi...',
    'xSearchQueryString' => 'prSearchQuery',
    'selectedXId' => $selectedPrId,
    'selectedXName' => $pr_name,
    'resetXFilter' => 'resetPrFilter()',
    'xSearchQuery' => $prSearchQuery,
    'xSearchResults' => $prSearchResults,
    'selectXForFilterString' => 'selectPrForFilter',

    'x2HeadString' => $isSmall ? null : 'Departemen',
    'x3HeadString' => 'Fakultas',

    'typeXString' => 'prodi',
    'typeX2String' => $isSmall ? null : 'departemen',
    'typeX3String' => 'fakultas',
    'unfoundString' => 'Tidak ada Program Studi ditemukan!',
    'minW' => 'sm:min-w-[360px]',
])
