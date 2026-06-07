<div class="w-full min-w-full">
    {{-- ****************************************************** --}}
    {{-- 1. UPLOAD EXCEL FILE --}}
    {{-- ***********************F******************************* --}}
    <div
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Upload File Excel Pengguna</h4>

        {{-- 📁 File Input --}}
        @include('livewire.global.modal-form.file-input-form', [
            'alpine' => 'user',
            'modelString' => 'excel_user_file',
            'wireKeyString' => 'excel-input-field',
            'nameXString' => 'Pilih File Excel Pengguna',
            'wireLoading' => 'parseExcelUserFile',
            'multiFile' => 1,
            'fileDelete' => 'clearUserNilaiFile',
            'message' => $errors->first('excel_user_file'),
        ])
    </div>

    {{-- ****************************************************** --}}
    {{-- 2. TABEL INPUT HASIL PARSING --}}
    {{-- ****************************************************** --}}

    <div
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)] shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

        @include('livewire.global.modal-form.input-array.search-input-form', [
            'alpine' => 'user',
            'xResults' => $prResults,
            'selectX' => 'selectPr',
            'modelString' => 'nama_pr',
        
            'idString' => 'pr_id',
            'itemsAllString' => 'pr_items',
        
            'resetXInput' => 'resetPrInput()',
            'typeXString' => 'prodi',
            'typeX2String' => 'departemen',
            'typeX3String' => 'fakultas',
        
            'nameXString' => 'Program Studi',
            'nameSearchString' => 'prNameSearch',
            'fetchString' => 'fetchPr',
            'iconString' => 'academic-cap',
            'wireLoading' => 'fetchPr',
        ])

        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Preview & Edit Data Pengguna
        </h4>

        <div class="relative">
            @if (empty($parsedUserRows))
                <div class="text-sm text-gray-500 italic h-16">
                    Data dari Excel akan tampil di sini setelah file diunggah.
                </div>
            @else
                <div class="w-full overflow-x-auto max-h-[90vh] overflow-y-auto border rounded-lg">

                    @php
                        $headColumn =
                            'px-3 py-2 border-b border-r border-gray-300 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800';
                    @endphp

                    <table wire:loading.class="opacity-50"
                        wire:target="excel_user_file, parseExcelUserFile, removeParsedUserRow"
                        class="min-w-full border-separate border-spacing-0 text-sm">


                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-800 z-10">
                            <tr class="text-left">
                                <th class="{{ $headColumn }}">#</th>
                                <th class="{{ $headColumn }}">Email</th>
                                <th class="{{ $headColumn }}">Password</th>
                                <th class="{{ $headColumn }}">Nama</th>
                                <th class="{{ $headColumn }}">NIP</th>
                                <th class="{{ $headColumn }}">NITK</th>
                                <th class="{{ $headColumn }}">NIDN</th>
                                <th class="{{ $headColumn }}">NIDK</th>
                                <th class="{{ $headColumn }}">NIM</th>
                                <th class="{{ $headColumn }}">NIK</th>
                                <th class="{{ $headColumn }}">Tahun Masuk</th>
                                <th class="{{ $headColumn }}">Kode Wilayah</th>
                                {{-- <th  class="{{ $headColumn }}">Program Studi</th> --}}
                                <th class="{{ $headColumn }}">Role</th>
                                <th class="{{ $headColumn }}">Aksi</th>
                            </tr>
                        </thead>

                        @php
                            $kolomExcel =
                                'border bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] placeholder-[var(--contrast-third-text)] px-2 py-1 border';
                        @endphp

                        <tbody class="bg-white dark:bg-neutral-600" wire:loading.class="opacity-50 pointer-events-none"
                            wire:target="loadingUserExcel">
                            @foreach ($this->paginatedUserRows as $row)
                                @php
                                    $i = $row['_index'] ?? 0;
                                @endphp
                                <tr>
                                    <td
                                        class="bg-gray-100 dark:bg-neutral-800 px-2 py-1 text-center font-semibold border border-gray-200 dark:border-neutral-800">
                                        {{ $i + 1 }}21
                                    </td>

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['email'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.email",
                                        'message' => $rowUserErrors[$i]['email'] ?? null,
                                        'inputType' => 'email',
                                        'minW' => '192',
                                    ])

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['password'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.password",
                                        'message' => $rowUserErrors[$i]['password'] ?? null,
                                        'minW' => '192',
                                    ])


                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['name'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.name",
                                        'message' => $rowUserErrors[$i]['name'] ?? null,
                                        'minW' => '192',
                                    ])

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['nip'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.nip",
                                        'numberOnly' => 1,
                                        'maxLength' => 20,
                                        'message' => $rowUserErrors[$i]['nip'] ?? null,
                                        'minW' => '192',
                                    ])

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['nitk'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.nitk",
                                        'numberOnly' => 1,
                                        'maxLength' => 20,
                                        'message' => $rowUserErrors[$i]['nitk'] ?? null,
                                        'minW' => '192',
                                    ])

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['nidn'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.nidn",
                                        'numberOnly' => 1,
                                        'maxLength' => 20,
                                        'message' => $rowUserErrors[$i]['nidn'] ?? null,
                                        'minW' => '192',
                                    ])

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['nidk'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.nidk",
                                        'numberOnly' => 1,
                                        'maxLength' => 20,
                                        'message' => $rowUserErrors[$i]['nidk'] ?? null,
                                        'minW' => '192',
                                    ])

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['nim'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.nim",
                                        'numberOnly' => 1,
                                        'maxLength' => 20,
                                        'message' => $rowUserErrors[$i]['nim'] ?? null,
                                        'minW' => '192',
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['nik'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.nik",
                                        'numberOnly' => 1,
                                        'maxLength' => 20,
                                        'message' => $rowUserErrors[$i]['nik'] ?? null,
                                        'minW' => '192',
                                    ])

                                    {{-- Tahun Masuk: Input dikecilkan --}}
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['angkatan'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.angkatan",
                                        'numberOnly' => 1,
                                        'maxLength' => 4,
                                        'message' => $rowUserErrors[$i]['angkatan'] ?? null,
                                        'isDark' => 1,
                                        'minW' => '16',
                                    ])
                                    {{-- Tahun Masuk: Input dikecilkan --}}
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['kode_wilayah'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.kode_wilayah",
                                        'isSelect' => 1,
                                        'xOptions' => ['IDL', 'PLG'],
                                        'message' => $rowUserErrors[$i]['kode_wilayah'] ?? null,
                                        'isDark' => 1,
                                        'minW' => '32',
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedUserRows[$i]['role'] ?? '',
                                        'wireModel' => "parsedUserRows.$i.kode_wilayah",
                                        'isSelect' => 1,
                                        'xOptions' => ['Admin', 'Dosen', 'Mahasiswa'],
                                        'message' => $rowUserErrors[$i]['role'] ?? null,
                                        'isDark' => 1,
                                        'minW' => '32',
                                    ])

                                    <td
                                        class="bg-gray-100 dark:bg-neutral-800 px-2 py-1 border border-gray-300 dark:border-neutral-700 text-center align-middle">
                                        <button wire:click="removeParsedNilaiRow({{ $i }})" type="button"
                                            class="cursor-pointer text-red-500 hover:text-red-700 p-1 transition-colors block mx-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Error Baris --}}
                                @if (!empty($rowUserErrors[$i]))
                                    <tr>
                                        <td colspan="14"
                                            class="px-4 py-1 bg-red-50 dark:bg-red-950/30 text-red-600 text-[10px] border italic">
                                            ⚠️
                                            @foreach ($rowUserErrors[$i] as $fieldErrors)
                                                @foreach ($fieldErrors as $error)
                                                    {{ $error }}@if (!$loop->parent->last || !$loop->last)
                                                        <span class="mx-2">|</span>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 📄 Pagination Controls (Tailwind Style) --}}

                @if ($this->paginatedUserRows->hasPages())
                    <div class="mt-6"
                        wire:target="gotoPage, previousPage, nextPage, {{ $this->paginatedUserRows->getPageName() }}">
                        {{ $this->paginatedUserRows->links('vendor.pagination.tailwind', [
                            'typeXLoading' => 'loadingUserExcel',
                        ]) }}
                    </div>
                @endif
            @endif

            <div>
                @include('livewire.global.modal-form.loading-animation', [
                    'wireLoading' => 'excel_user_file, parseExcelUserFile, removeParsedUserRow',
                    // 'heightContainer' => 32,
                    'textString' => 'Memproses data dari file Excel...',
                ])
            </div>
        </div>

    </div>


</div>
