<div class="w-full min-w-full">
    {{-- ****************************************************** --}}
    {{-- 1. UPLOAD EXCEL FILE --}}
    {{-- ***********************F******************************* --}}
    <div
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Upload File Excel Nilai</h4>

        {{-- 📁 File Input --}}
        @include('livewire.global.modal-form.file-input-form', [
            'alpine' => 'sesi',
            'modelString' => 'excel_nilai_file',
            'wireKeyString' => 'excel-input-field',
            'nameXString' => 'Pilih File Excel Nilai',
            'wireLoading' => 'parseExcelNilaiFile',
            'message' => $errors->first('excel_nilai_file'),
        ])
    </div>

    {{-- ****************************************************** --}}
    {{-- 2. TABEL INPUT HASIL PARSING --}}
    {{-- ****************************************************** --}}

    <div
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)] shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Preview & Edit Data Nilai Mahasiswa
        </h4>

        <div class="relative">
            @if (empty($parsedNilaiRows))
                <div class="text-sm text-gray-500 italic" wire:loading.remove
                    wire:target="excel_nilai_file, parseExcelNilaiFile">
                    Data dari Excel akan tampil di sini setelah file diunggah.
                </div>
            @else
                <div class="w-full overflow-x-auto max-h-[55vh] overflow-y-auto border rounded-lg">
                    @php
                        $subCpmks = collect($this->parsedNilaiHeaders ?? []);

                        $headerCpmk = $subCpmks
                            ->groupBy('cpmk')
                            ->map(function ($items) {
                                return [
                                    'label' => $items->first()['cpmk'] ?? '-',
                                    'colspan' => $items->count(),
                                ];
                            })
                            ->values();
                    @endphp
                 
                    <table wire:loading.class="opacity-50"
                        wire:target="excel_nilai_file, parseExcelNilaiFile, removeParsedNilaiRow"
                        class="min-w-full border-collapse text-sm">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-800 z-10">

                            {{-- ============================= --}}
                            {{-- ROW 1 = CPMK (COLSPAN) --}}
                            {{-- ============================= --}}
                            <tr class="text-left">
                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">#</th>
                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Kode MK
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Nama MK
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    NIM
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Nama Mahasiswa
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Nama Kelas
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Angkatan
                                </th>

                                {{-- CPMK --}}
                                @foreach ($headerCpmk as $cpmk)
                                    <th colspan="{{ $cpmk['colspan'] }}"
                                        class="px-3 py-2 border whitespace-nowrap text-center font-bold">

                                        {{ $cpmk['label'] }}
                                    </th>
                                @endforeach


                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Nilai Angka
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Nilai Index
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Nilai Huruf
                                </th>

                                <th rowspan="3" class="px-3 py-2 border whitespace-nowrap text-center">
                                    Aksi
                                </th>
                            </tr>

                            {{-- ============================= --}}
                            {{-- ROW 2 = SUB CPMK --}}
                            {{-- ============================= --}}
                            @foreach ($subCpmks as $sub)
                                <th class="px-3 py-2 border whitespace-nowrap text-center text-xs">
                                    {{ $sub['sub_cpmk'] }}
                                    @if (!empty($sub['pertemuan']))
                                        <div class="text-[10px] opacity-70">
                                            P-{{ $sub['pertemuan'] }}
                                        </div>
                                    @endif
                                </th>
                            @endforeach

                            {{-- ============================= --}}
                            {{-- ROW 3 = BOBOT --}}
                            {{-- ============================= --}}
                            <tr class="text-left">
                                @foreach ($subCpmks as $sub)
                                    <th class="px-3 py-2 border whitespace-nowrap text-center text-xs">
                                        {{ number_format(($sub['bobot'] ?? 0) * 100, 2) }}%
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        @php
                            $kolomExcel =
                                'border bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] placeholder-[var(--contrast-third-text)] px-2 py-1 border';
                        @endphp

                        <tbody class="bg-white dark:bg-neutral-800" wire:loading.class="opacity-50 pointer-events-none"
                            wire:target="loadingNilaiExcel">
                            @foreach ($this->paginatedNilaiRows as $row)
                                @php
                                    $i = $row['_index'] ?? 0;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors">
                                    <td class="{{ $kolomExcel }} text-center font-semibold">
                                        {{ $i + 1 }}
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.kode_mk" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['kode_mk']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['kode_mk']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['kode_mk'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.nama_mk" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['nama_mk']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['nama_mk']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['nama_mk'][0] }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.kelas_kuliah" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['kelas_kuliah']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['kelas_kuliah']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['kelas_kuliah'][0] }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.nim" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['nim']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['nim']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['nim'][0] }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.nama" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['nama']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['nama']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['nama'][0] }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.angkatan" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['angkatan']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['angkatan']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['angkatan'][0] }}
                                            </p>
                                        @endif
                                    </td>


                                    @foreach ($row['sub_cpmk'] ?? [] as $nilai)
                                        <td class="{{ $kolomExcel }}">
                                            <input type="number" step="0.01"
                                                wire:model.live="parsedNilaiRows.{{ $i }}.sub_cpmk.{{ $loop->index }}.nilai"
                                                class="w-20 border rounded px-2 py-1 text-xs text-center">
                                        </td>
                                    @endforeach

                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.nilai_angka" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['nilai_angka']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['nilai_angka']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['nilai_angka'][0] }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.nilai_index" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['nilai_index']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['nilai_index']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['nilai_index'][0] }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="{{ $kolomExcel }}">
                                        <input wire:model="parsedNilaiRows.{{ $i }}.nilai_huruf" disabled
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none bg-gray-50 dark:bg-neutral-800 cursor-not-allowed opacity-70 {{ isset($rowNilaiErrors[$i]['nilai_huruf']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowNilaiErrors[$i]['nilai_huruf']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowNilaiErrors[$i]['nilai_huruf'][0] }}
                                            </p>
                                        @endif
                                    </td>



                                    <td class="px-2 py-1 border text-center">
                                        <button wire:click="removeParsedNilaiRow({{ $i }})" type="button"
                                            class="cursor-pointer text-red-500 hover:text-red-700 p-1 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Error Baris --}}
                                @if (!empty($rowNilaiErrors[$i]))
                                    <tr>
                                        <td colspan="13"
                                            class="px-4 py-1 bg-red-50 text-red-600 text-[10px] border italic">
                                            ⚠️
                                            @foreach ($rowNilaiErrors[$i] as $fieldErrors)
                                                @foreach ($fieldErrors as $error)
                                                    {{ $error }}@if (!$loop->parent->last || !$loop->last)
                                                        |
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

                @if ($this->paginatedNilaiRows->hasPages())
                    <div
                        wire:target="gotoPage, previousPage, nextPage, {{ $this->paginatedNilaiRows->getPageName() }}">
                        {{ $this->paginatedNilaiRows->links('vendor.pagination.tailwind', [
                            'typeXLoading' => 'loadingNilaiExcel',
                        ]) }}
                    </div>
                @endif
            @endif

            @include('livewire.global.modal-form.loading-animation', [
                'wireLoading' => 'excel_nilai_file, parseExcelNilaiFile, removeParsedNilaiRow',
                'heightContainer' => 32,
                'textString' => 'Memproses data dari file Excel...',
            ])
        </div>

    </div>


</div>
