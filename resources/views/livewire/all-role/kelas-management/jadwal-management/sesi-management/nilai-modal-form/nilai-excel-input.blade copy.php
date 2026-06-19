<div class="w-full min-w-full">
    {{-- ****************************************************** --}}
    {{-- 1. UPLOAD EXCEL FILE --}}
    {{-- ***********************F******************************* --}}
    <div
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] table-border
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
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] table-border shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

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

                        $widthColumn = $subCpmks->count() + 11;
                    @endphp

                    <table wire:loading.class="opacity-50"
                        wire:target="excel_nilai_file, parseExcelNilaiFile, removeParsedNilaiRow"
                        class="min-w-full border-separate border-spacing-0 text-sm">

                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-800 z-10">

                            {{-- ============================= --}}
                            {{-- ROW 1 = CPMK (COLSPAN) --}}
                            {{-- ============================= --}}
                            <tr class="text-left">
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    #</th>
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Kode MK
                                </th>
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Nama MK
                                </th>
                                <th rowspan="3"
                                    class="sticky left-0 z-40 px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 text-center bg-gray-100 dark:bg-neutral-800">
                                    NIM</th>
                                <th rowspan="3"
                                    class="sticky left-[120px] z-40 px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 text-center bg-gray-100 dark:bg-neutral-800 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                    Nama Mahasiswa</th>
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Nama Kelas
                                </th>
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Angkatan
                                </th>

                                {{-- CPMK --}}
                                @foreach ($headerCpmk as $cpmk)
                                    <th colspan="{{ $cpmk['colspan'] }}"
                                        class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center font-bold bg-gray-100 dark:bg-neutral-800">
                                        {{ $cpmk['label'] }}
                                    </th>
                                @endforeach

                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Nilai Angka
                                </th>
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Nilai Index
                                </th>
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Nilai Mutu
                                </th>
                                <th rowspan="3"
                                    class="px-3 py-2 border-b border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800">
                                    Aksi
                                </th>
                            </tr>

                            {{-- ============================= --}}
                            {{-- ROW 2 = SUB CPMK --}}
                            {{-- ============================= --}}
                            <tr class="text-left">
                                @foreach ($subCpmks as $sub)
                                    <th
                                        class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center text-xs bg-gray-100 dark:bg-neutral-800">
                                        {{ $sub['sub_cpmk'] }}
                                        @if (!empty($sub['pertemuan']))
                                            <div class="text-[10px] opacity-70">
                                                P-{{ $sub['pertemuan'] }}
                                            </div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>

                            {{-- ============================= --}}
                            {{-- ROW 3 = BOBOT --}}
                            {{-- ============================= --}}
                            <tr class="text-left">
                                @foreach ($subCpmks as $sub)
                                    <th
                                        class="px-3 py-2 border-b border-r border-gray-200 dark:border-neutral-700 whitespace-nowrap text-center text-xs bg-gray-100 dark:bg-neutral-800">
                                        {{ number_format(($sub['bobot'] ?? 0) * 100, 2) }}%
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        @php
                            $kolomExcel =
                                'border bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)] placeholder-[var(--contrast-third-text)] px-2 py-1 border';
                        @endphp

                        <tbody class="bg-white dark:bg-neutral-800" wire:loading.class="opacity-50 pointer-events-none"
                            wire:target="loadingNilaiExcel">
                            @foreach ($this->paginatedNilaiRows as $row)
                                @php
                                    $i = $row['_index'] ?? 0;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors">
                                    <td
                                        class="px-2 py-1 text-center font-semibold border border-gray-200 dark:border-neutral-700">
                                        {{ $i + 1 }}
                                    </td>

                                    <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.kode_mk" readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['kode_mk']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['kode_mk']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['kode_mk'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.nama_mk" readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['nama_mk']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['nama_mk']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['nama_mk'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.kelas_kuliah"
                                                readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['kelas_kuliah']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['kelas_kuliah']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['kelas_kuliah'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

               
                                    <td class="text-xs sm:text-sm sticky left-0 z-20 p-0 border-b border-r border-gray-200 dark:border-neutral-700 align-top bg-white dark:bg-neutral-800 group-hover:bg-gray-50 dark:group-hover:bg-neutral-700/50">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.nim" readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['nim']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['nim']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['nim'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-xs sm:text-sm sticky left-0 z-20 p-0 border-b border-r border-gray-200 dark:border-neutral-700 align-top bg-white dark:bg-neutral-800 group-hover:bg-gray-50 dark:group-hover:bg-neutral-700/50">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.nama" readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['nama']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['nama']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['nama'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.angkatan" readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['angkatan']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['angkatan']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['angkatan'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    @foreach ($row['sub_cpmk'] ?? [] as $nilai)
                                        <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                            <div class="flex flex-col h-full min-h-[34px]">
                                                <input type="number" step="0.01"
                                                    wire:model.live="parsedNilaiRows.{{ $i }}.sub_cpmk.{{ $loop->index }}.nilai"
                                                    class="w-full h-full border-0 rounded-none px-2 py-2 text-xs text-center outline-none bg-white dark:bg-neutral-800 focus:bg-blue-50/30 focus:ring-1 focus:ring-blue-500 {{ isset($rowNilaiErrors[$i]['sub_cpmk' . $loop->index . 'nilai']) ? 'bg-red-50 text-red-600' : '' }}">
                                                @if (isset($rowNilaiErrors[$i]['sub_cpmk' . $loop->index . 'nilai']))
                                                    <p
                                                        class="text-red-500 text-[10px] px-1 py-0.5 bg-red-50 border-t border-red-200 text-center">
                                                        {{ $rowNilaiErrors[$i]['sub_cpmk' . $loop->index . 'nilai'][0] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.nilai_angka"
                                                readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['nilai_angka']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['nilai_angka']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['nilai_angka'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.nilai_index"
                                                readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['nilai_index']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['nilai_index']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['nilai_index'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-xs sm:text-sm p-0 border border-gray-200 dark:border-neutral-700 align-top">
                                        <div class="flex flex-col h-full min-h-[34px]">
                                            <input wire:model="parsedNilaiRows.{{ $i }}.nilai_mutu"
                                                readonly
                                                class="w-full h-full border-0 rounded-none px-3 py-2 text-xs outline-none bg-gray-100/70 dark:bg-neutral-700/50 cursor-text select-text {{ isset($rowNilaiErrors[$i]['nilai_mutu']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : '' }}">
                                            @if (isset($rowNilaiErrors[$i]['nilai_mutu']))
                                                <p
                                                    class="text-red-500 text-[10px] px-2 py-0.5 bg-red-50 border-t border-red-200">
                                                    {{ $rowNilaiErrors[$i]['nilai_mutu'][0] }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td
                                        class="px-2 py-1 border border-gray-200 dark:border-neutral-700 text-center align-middle bg-gray-50/50 dark:bg-neutral-800/30">
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
                                @if (!empty($rowNilaiErrors[$i]))
                                    <tr>
                                        <td colspan="{{ $widthColumn }}"
                                            class="px-4 py-1.5 bg-red-50 text-red-600 text-[10px] border border-red-200 italic font-medium">
                                            ⚠️
                                            @foreach ($rowNilaiErrors[$i] as $fieldErrors)
                                                @foreach ($fieldErrors as $error)
                                                    {{ $error }}@if (!$loop->parent->last || !$loop->last)
                                                        <span class="mx-2 text-red-300">|</span>
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
