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
            'multiFile' => 1,
            'fileDelete' => 'clearNilaiExcelFile',
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
                <div class="text-sm text-gray-500 italic h-16">
                    Data dari Excel akan tampil di sini setelah file diunggah.
                </div>
            @else
                <div class="w-full overflow-x-auto max-h-[90vh] overflow-y-auto border rounded-lg">
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

                        $arrBobotJson = json_encode(
                            collect($subCpmks)->map(fn($sub) => (float) ($sub['bobot'] ?? 0))->toArray(),
                        );

                        $widthColumn = $subCpmks->count() + 11;

                        $headColumn =
                            'px-3 py-2 border-b border-r border-gray-300 dark:border-neutral-700 whitespace-nowrap text-center bg-gray-100 dark:bg-neutral-800';
                    @endphp

                    <table wire:loading.class="opacity-50"
                        wire:target="excel_nilai_file, parseExcelNilaiFile, removeParsedNilaiRow"
                        class="min-w-full border-separate border-spacing-0 text-sm">

                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-800 z-10">

                            {{-- ============================= --}}
                            {{-- ROW 1 = CPMK (COLSPAN) --}}
                            {{-- ============================= --}}
                            <tr class="text-left">
                                <th rowspan="3" class="{{ $headColumn }}">#</th>
                                <th rowspan="3" class="{{ $headColumn }}">Kode RPS</th>
                                <th rowspan="3" class="{{ $headColumn }}">Nama MK</th>
                                <th rowspan="3" class="{{ $headColumn }}">Nama Kelas</th>

                                <th rowspan="3"
                                    class="{{ $headColumn }} sticky left-0 z-20">
                                    NIM
                                </th>
                                <th rowspan="3" class="{{ $headColumn }}">Nama Mahasiswa</th>
                                <th rowspan="3" class="{{ $headColumn }}">Angkatan</th>

                                {{-- CPMK --}}
                                @foreach ($headerCpmk as $cpmk)
                                    <th colspan="{{ $cpmk['colspan'] }}" class="{{ $headColumn }}">
                                        {{ $cpmk['label'] }}
                                    </th>
                                @endforeach

                                <th rowspan="3" class="{{ $headColumn }}">Nilai Angka</th>
                                <th rowspan="3" class="{{ $headColumn }}">Nilai Index</th>
                                <th rowspan="3" class="{{ $headColumn }}">Nilai Huruf</th>
                                <th rowspan="3" class="{{ $headColumn }}">Aksi</th>
                            </tr>

                            {{-- ============================= --}}
                            {{-- ROW 2 = SUB CPMK --}}
                            {{-- ============================= --}}
                            <tr class="text-left">
                                @foreach ($subCpmks as $sub)
                                    <th
                                        class="{{ $headColumn }}">
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
                                        class="{{ $headColumn }}">
                                        {{ number_format(($sub['bobot'] ?? 0) * 100, 2) }}%
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="sticky top-[124px] bg-white dark:bg-neutral-600" wire:loading.class="opacity-50 pointer-events-none"
                            wire:target="loadingNilaiExcel">
                            @foreach ($this->paginatedNilaiRows as $row)
                                @php
                                    $i = $row['_index'] ?? 0;
                                @endphp
                                <tr x-data="{
                                    bobotList: {{ $arrBobotJson }},
                                    nilaiInputs: {{ json_encode(collect($row['sub_cpmk'] ?? [])->map(fn($n) => $n['nilai'] ?? 0)->toArray()) }},
                                
                                    // 1. GETTER NILAI ANGKA
                                    get nilaiAngka() {
                                        let total = 0;
                                        this.nilaiInputs.forEach((nilai, idx) => {
                                            let bbt = this.bobotList[idx] || 0;
                                            total += (parseFloat(nilai) || 0) * bbt;
                                        });
                                        return parseFloat(total.toFixed(2));
                                    },
                                
                                    // 2. GETTER NILAI INDEX
                                    get nilaiIndex() {
                                        let na = this.nilaiAngka;
                                        if (na >= 86) return 4.00;
                                        if (na >= 80) return 3.70;
                                        if (na >= 75) return 3.30;
                                        if (na >= 70) return 3.00;
                                        if (na >= 65) return 2.70;
                                        if (na >= 60) return 2.30;
                                        if (na >= 56) return 2.00;
                                        if (na >= 40) return 1.00;
                                        return 0.0;
                                    },
                                
                                    // 3. GETTER NILAI HURUF
                                    get nilaiHuruf() {
                                        let na = this.nilaiAngka;
                                        if (na >= 86) return 'A';
                                        if (na >= 80) return 'A-';
                                        if (na >= 75) return 'B+';
                                        if (na >= 70) return 'B';
                                        if (na >= 65) return 'B-';
                                        if (na >= 60) return 'C+';
                                        if (na >= 56) return 'C';
                                        if (na >= 40) return 'D';
                                        return 'E';
                                    }
                                }">
                                    <td
                                        class="bg-white dark:bg-neutral-600 px-2 py-1 text-center font-semibold border border-gray-200 dark:border-neutral-800">
                                        {{ $i + 1 }}21
                                    </td>

                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['kode_rps'] ?? '',
                                        'wireModel' => "parsedNilaiRows.$i.kode_rps",
                                        'message' => $rowNilaiErrors[$i]['kode_rps'] ?? null,
                                        'isReadonly' => 1,
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['nama_mk'] ?? '',
                                        'wireModel' => "parsedNilaiRows.$i.nama_mk",
                                        'message' => $rowNilaiErrors[$i]['nama_mk'] ?? null,
                                        'isReadonly' => 1,
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['kode_jadwal'] ?? '',
                                        'wireModel' => "parsedNilaiRows.$i.kode_jadwal",
                                        'message' => $rowNilaiErrors[$i]['kode_jadwal'] ?? null,
                                        'isReadonly' => 1,
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['nim'] ?? '',
                                        'wireModel' => "parsedNilaiRows.$i.nim",
                                        'message' => $rowNilaiErrors[$i]['nim'] ?? null,
                                        'isReadonly' => 1,
                                        'isSticky' => 1
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['nama'] ?? '',
                                        'wireModel' => "parsedNilaiRows.$i.nama",
                                        'message' => $rowNilaiErrors[$i]['nama'] ?? null,
                                        'isReadonly' => 1,
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['angkatan'] ?? '',
                                        'wireModel' => "parsedNilaiRows.$i.angkatan",
                                        'message' => $rowNilaiErrors[$i]['angkatan'] ?? null,
                                        'isReadonly' => 1,
                                    ])

                                    @foreach ($row['sub_cpmk'] ?? [] as $nilai)
                                        <td class="p-0 border border-gray-200 dark:border-neutral-800 align-top">
                                            <div class="flex flex-col h-full min-h-[34px]">
                                                <input type="number" step="0.01" min="0" max="100"
                                                    x-model="nilaiInputs[{{ $loop->index }}]"
                                                    @input="
                                                        $el.value = $store.sesi?.normalizeFloat($el.value);
                                                        nilaiInputs[{{ $loop->index }}] = $el.value;
                                                    "
                                                    wire:model.blur="parsedNilaiRows.{{ $i }}.sub_cpmk.{{ $loop->index }}.nilai"
                                                    class="{{ isset($rowNilaiErrors[$i]['sub_cpmk.' . $loop->index . '.nilai']) ? 'bg-red-50 dark:bg-red-950/30 text-red-600' : 'bg-white dark:bg-neutral-600' }} w-full h-full border-0 rounded-none px-3 py-2 text-xs text-center outline-none focus:bg-blue-50/30 focus:ring-1 focus:ring-blue-500">

                                                @if (isset($rowNilaiErrors[$i]['sub_cpmk.' . $loop->index . '.nilai']))
                                                    <p
                                                        class="text-red-500 text-[10px] px-1 py-0.5 bg-red-50 dark:bg-red-950/30 border-t border-red-200 text-center">
                                                        {{ $rowNilaiErrors[$i]['sub_cpmk.' . $loop->index . '.nilai'][0] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach


                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['nilai_angka'] ?? '',
                                        'modelAlpine' => 'nilaiAngka',
                                        'wireModel' => "parsedNilaiRows.$i.nilai_angka",
                                        'message' => $rowNilaiErrors[$i]['nilai_angka'] ?? null,
                                        'isReadonly' => 1,
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['nilai_index'] ?? '',
                                        'modelAlpine' => 'nilaiIndex.toFixed(2)',
                                        'wireModel' => "parsedNilaiRows.$i.nilai_index",
                                        'message' => $rowNilaiErrors[$i]['nilai_index'] ?? null,
                                        'isReadonly' => 1,
                                    ])
                                    @include('livewire.global.modal-form.table.excel-input-form', [
                                        'model' => $this->parsedNilaiRows[$i]['nilai_huruf'] ?? '',
                                        'modelAlpine' => 'nilaiHuruf',
                                        'wireModel' => "parsedNilaiRows.$i.nilai_huruf",
                                        'message' => $rowNilaiErrors[$i]['nilai_huruf'] ?? null,
                                        'isReadonly' => 1,
                                    ])

                                    <td
                                        class="bg-white dark:bg-neutral-600 px-2 py-1 border border-gray-200 dark:border-neutral-800 text-center align-middle">
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
                                            class="px-4 py-1.5 bg-red-50 dark:bg-red-950/30 text-red-600 text-[10px] border border-red-200 italic font-medium">
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
                    <div class="mt-6"
                        wire:target="gotoPage, previousPage, nextPage, {{ $this->paginatedNilaiRows->getPageName() }}">
                        {{ $this->paginatedNilaiRows->links('vendor.pagination.tailwind', [
                            'typeXLoading' => 'loadingNilaiExcel',
                        ]) }}
                    </div>
                @endif
            @endif

            <div>
                @include('livewire.global.modal-form.loading-animation', [
                    'wireLoading' => 'excel_nilai_file, parseExcelNilaiFile, removeParsedNilaiRow',
                    // 'heightContainer' => 32,
                    'textString' => 'Memproses data dari file Excel...',
                ])
            </div>
        </div>

    </div>


</div>
