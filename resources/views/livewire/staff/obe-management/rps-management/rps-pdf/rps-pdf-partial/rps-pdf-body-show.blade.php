<div class="rps-pdf {{ $isPDF ?? false ? '' : 'rps-pdf--min-width' }}">

    <style>
        @page {
            size: A4 landscape;
            /* size: A4; */
            margin: 1cm;
        }

        .rps-pdf {
            font-family: "Times New Roman", Times, serif;
            color: black !important;
            padding: 1px;
            background: #fff;
        }

        .rps-pdf--min-width {
            min-width: 1000px;
        }

        .rps-table th,
        .rps-table td {
            border: 1px solid black !important;
            padding: 8px;
        }

        .nilai-table th,
        .nilai-table td {
            border: 1px solid black !important;
            padding: 4px;
        }

        .rps-table,
        .nilai-table {
            border-collapse: collapse;
        }

        /* .list-indent {
            padding-left: 15px;
            text-indent: -15px;
        } */

        /* .w-full {
            width: 100%;
        } */

        .text-center {
            text-align: center;
        }

        /* .text-left {
            text-align: left;
        } */

        /* .text-justify {
            text-align: justify;
        } */

        .font-bold {
            font-weight: bold;
        }

        .font-medium {
            font-weight: 500;
        }

        /* .font-semibold {
            font-weight: 600;
        } */

        .italic {
            font-style: italic;
        }

        /* .uppercase {
            text-transform: uppercase;
        } */

        .leading-relaxed {
            line-height: 1.6;
        }

        /* .leading-tight {
            line-height: 1.2;
        } */

        .align-top {
            vertical-align: top;
        }

        /* .align-middle {
            vertical-align: middle;
        } */

        .float-right {
            float: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        /* .bg-gray-50 {
            background-color: #f9fafb;
        } */

        .bg-gray-200 {
            background-color: #e5e7eb;
        }

        /*      .border-t-black {
            border-top: 1px solid black;
        }

        .border-r-black {
            border-right: 1px solid black;
        }

        .border-l-black {
            border-left: 1px solid black;
        }

        .no-border-r {
            border-right: 0 !important;
        }

        .no-border-l {
            border-left: 0 !important;
        }

        .no-border {
            border: 0 !important;
        } */

        /* .border-cell {
            border: 1px solid black;
        } */

        .page-break-table {
            page-break-after: always !important;
        }

        /* ===== Header ===== */
        /* .header-table td {
            vertical-align: middle;
        }

        .header-logo-cell {
            width: 12%;
            text-align: center;
        }

        .header-logo-wrap {
            text-align: center;
        }

        .header-logo-wrap img {
            height: 80px;
        }

        .header-table .header-title-cell {
            width: 76%;
            border-right: 0 !important;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .header-table .header-spacer-cell {
            width: 12%;
            border-left: 0 !important;
        }

        .header-title-row {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            padding: 8px 0;
            text-transform: uppercase;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: middle;
        } */

        /* .header-logo-cell {
            width: 12%;
            text-align: center;
            vertical-align: middle;
        }

        .header-logo-wrap {
            display: block;
            text-align: center;
        }

        .header-logo-wrap img {
            height: 80px;
            width: auto;
            /* Menjaga proporsi */
        display: inline-block;
        vertical-align: middle;
        }

        */

        /* ===== Section title ===== */
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* ===== Identitas table ===== */

        /* .identity-table {
            width: 100%;
            font-size: 10px;
        } */

        .mb-20px {
            margin-bottom: 20px;
        }

        /* .mb-40px {
            margin-bottom: 40px;
        }

        .identity-header-row {
            font-weight: bold;
            text-align: center;
            background: #f9fafb;
        } */

        /* .col-1-6 {
            width: 16.6667%;
        }

        .col-2-6 {
            width: 33.3333%;
        }

        .col-3-6 {
            width: 50%;
        }


        .col-1-12 {
            width: 8.3333%;
        }

        .desc-label {
            text-align: center;
            font-weight: bold;
        }

        .desc-value {
            text-align: justify;
            line-height: 1.6;
        } */

        /* .cpmk-label {
            font-weight: bold;
            background: #f9fafb;
            text-align: center;
        }

        .cpmk-item {
            padding-left: 15px;
            text-indent: -15px;
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 4px;
        }

        .mr-5-bold {
            margin-right: 5px;
            font-weight: bold;
        }

        .mr-3-bold {
            margin-right: 3px;
            font-weight: bold;
        } */

        /* .dosen-label {
            font-weight: bold;
            background: #f9fafb;
            text-align: center;
        } */

        .dosen-item {
            padding-left: 15px;
            text-indent: -15px;
            margin-bottom: 4px;
        }

        .mr-5 {
            margin-right: 5px;
        }

        /* .dosen-missing {
            color: #dc2626;
            font-weight: bold;
            font-style: italic;
        }

        .otoritas-label {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .otoritas-cell {
            text-align: center;
        } */

        /* .otoritas-flex {
            min-height: 72px;
        }

        .otoritas-spacer {
            height: 50px;
        }

        .otoritas-title {
            font-weight: bold;
            padding-top: 4px;
        }

        .identity-table .otoritas-border-left {
            border-left: 1px solid black !important;
        } */

        /* ===== Program Pembelajaran table ===== */
        .program-table {
            width: 100%;
            font-size: 10px;
            line-height: 1.2;
            margin-border: 40px;
        }

        .program-header-row {
            background: #f9fafb;
            font-weight: bold;
            text-align: center;
        }

        .p-1 {
            padding: 4px;
        }

        .col-15 {
            width: 15%;
        }

        .tbody-border-top {
            border-top: 1px solid black;
        }

        .exam-row {
            font-weight: 600;
            /* font-style: italic; */
        }

        .cell-base {
            padding: 8px;
            border: 1px solid black;
        }

        .cell-base-bold {
            padding: 8px;
            border: 1px solid black;
            font-weight: bold;
        }

        .cpmk-cell {
            text-align: left;
            vertical-align: top;
        }

        .bobot-cell {
            text-align: center;
            font-weight: bold;
        }

        .bobot-repeat {
            opacity: 0.4;
        }

        .dosen-cell {
            padding: 8px;
            vertical-align: top;
            border: 1px solid black;
        }

        .beban-row {
            font-weight: bold;
            padding: 8px;
        }

        /* ===== Referensi ===== */
        .ref-title {
            font-weight: bold;
            padding: 0 4px 0 4px;
            font-size: 10px;
        }

        .ref-wrap {
            padding-left: 4px;
            padding-right: 4px;
        }

        .ref-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .ref-item {
            padding-left: 15px;
            text-indent: -15px;
            text-align: justify;
            font-size: 10px;
            line-height: 1.6;
            margin-bottom: 4px;
        }

        .ref-num {
            /* font-weight: bold; */
            margin-right: 5px;
        }

        /* ===== Skala Penilaian ===== */
        .skala-wrap {
            max-width: 320px;
        }

        .skala-title {
            font-weight: bold;
            padding: 4px;
            margin-bottom: 3px;
            font-size: 10px;
            line-height: 1.2;
        }

        .skala-table {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
        }

        .skala-col-20 {
            width: 20%;
        }

        .skala-col-30 {
            width: 30%;
        }

        .skala-col-50 {
            width: 50%;
        }

        .skala-table .skala-th {
            text-align: center;
            font-weight: bold;
            padding: 2px 4px;
            line-height: 1.2;
            border: 0 !important;
        }

        .skala-table .skala-td {
            text-align: center;
            padding: 2px 4px;
            line-height: 1.2;
            border: 0 !important;
        }

        /* .skala-td-nowrap {
            white-space: nowrap;
        } */
    </style>

    {{-- ================= PROGRAM PEMBELAJARAN ================= --}}
    <div class="section-title">PROGRAM PEMBELAJARAN</div>

    <table class="program-table mb-20px rps-table">
        @php
            $programData = $rps->scpmkAtr->map(function ($row) use ($allDosens) {
                $row->dosens_collection = $allDosens;
                return $row;
            });

            // $calculateRowspan = function ($data, $column = null) {
            //     $counts = [];
            //     $index = 0;
            //     $total = count($data);

            //     while ($index < $total) {
            //         $currentValue =
            //             $column === 'dosen'
            //                 ? $data[$index]->dosens_collection->pluck('id')->sort()->implode(',')
            //                 : $data[$index]->$column ?? '';

            //         $step = 0;
            //         while ($index + $step < $total) {
            //             $nextValue =
            //                 $column === 'dosen'
            //                     ? $data[$index + $step]->dosens_collection->pluck('id')->sort()->implode(',')
            //                     : $data[$index + $step]->$column ?? '';

            //             if ($nextValue !== $currentValue) {
            //                 break;
            //             }
            //             $step++;
            //         }
            //         $counts[$index] = $step;
            //         $index += $step;
            //     }
            //     return $counts;
            // };

            // Function untuk menentukan baris pertama dari setiap kelompok data
            $calculateRowspan = function ($data, $column = null) {
                $flags = [];
                $index = 0;
                $total = count($data);

                while ($index < $total) {
                    $currentValue =
                        $column === 'dosen'
                            ? $data[$index]->dosens_collection->pluck('id')->sort()->implode(',')
                            : $data[$index]->$column ?? '';

                    // Tandai indeks pertama kelompok ini sebagai TRUE
                    $flags[$index] = true;

                    $step = 0;
                    while ($index + $step < $total) {
                        $nextValue =
                            $column === 'dosen'
                                ? $data[$index + $step]->dosens_collection->pluck('id')->sort()->implode(',')
                                : $data[$index + $step]->$column ?? '';

                        if ($nextValue !== $currentValue) {
                            break;
                        }
                        $step++;
                    }

                    // Lewati baris duplikat yang sama
                    $index += $step;
                }
                return $flags;
            };

            $showCpmk = $calculateRowspan($programData, 'kode_cpmk');
            $showBobot = $calculateRowspan($programData, 'bobot');
        @endphp

        @php
            // 1. Persiapkan array pertemuan (1-16)
            $totalPertemuan = 16;

            // 2. Ambil semua tim dosen dan pecah dosen-dosennya
            $allTimDosen = $tim_dosen->flatMap(function ($tim) {
                return $tim->dosens->map(function ($dosen) {
                    return [
                        'id' => $dosen->id,
                        'name' => $dosen->name,
                        'is_ketua' => $dosen->pivot->is_ketua,
                        'pertemuan_ke' => json_decode($dosen->pivot->pertemuan_ke ?? '[]'),
                    ];
                });
            });

            // 3. Mapping: Buat array [pertemuan_ke => [list_dosen]]
            $dosenPerPertemuan = [];
            for ($i = 1; $i <= $totalPertemuan; $i++) {
                $dosenPerPertemuan[$i] = $allTimDosen
                    ->filter(function ($dosen) use ($i) {
                        return !empty($dosen['pertemuan_ke']) && in_array($i, $dosen['pertemuan_ke']);
                    })
                    ->values();
            }

            $programData = $rps->scpmkAtr->map(function ($row, $index) use ($dosenPerPertemuan) {
                $pertemuan = $index + 1;
                $dosens = $dosenPerPertemuan[$pertemuan] ?? collect();

                $row->dosens_collection = $dosens->isEmpty() ? collect() : $dosens;
                return $row;
            });

            $isDosenUniform =
                $programData
                    ->map(function ($row) {
                        return $row->dosens_collection->pluck('id')->sort()->implode(',');
                    })
                    ->unique()
                    ->count() <= 1;

            $rowspanDosen = $calculateRowspan($programData, 'dosen');
        @endphp

        @php
            // 1. Pemetaan ID Referensi ke Urutan Nomor [1], [2], dst. berdasarkan $rps->all_refs
            $refOrderMap = $rps->all_refs->pluck('id')->flip()->map(fn($index) => $index + 1);

            // 2. Persiapkan array pertemuan (1-16)
            $totalPertemuan = 16;

            // 3. Ambil semua tim dosen dan pecah dosen-dosennya
            $allTimDosen = $tim_dosen->flatMap(function ($tim) {
                return $tim->dosens->map(function ($dosen) {
                    return [
                        'id' => $dosen->id,
                        'name' => $dosen->name,
                        'is_ketua' => $dosen->pivot->is_ketua,
                        'pertemuan_ke' => json_decode($dosen->pivot->pertemuan_ke ?? '[]'),
                    ];
                });
            });

            // 4. Mapping dosen per pertemuan
            $dosenPerPertemuan = [];
            for ($i = 1; $i <= $totalPertemuan; $i++) {
                $dosenPerPertemuan[$i] = $allTimDosen
                    ->filter(function ($dosen) use ($i) {
                        return !empty($dosen['pertemuan_ke']) && in_array($i, $dosen['pertemuan_ke']);
                    })
                    ->values();
            }

            $programData = $rps->scpmkAtr->map(function ($row, $index) use ($dosenPerPertemuan) {
                $pertemuan = $index + 1;
                $dosens = $dosenPerPertemuan[$pertemuan] ?? collect();

                $row->dosens_collection = $dosens->isEmpty() ? collect() : $dosens;
                return $row;
            });

            $isDosenUniform =
                $programData
                    ->map(function ($row) {
                        return $row->dosens_collection->pluck('id')->sort()->implode(',');
                    })
                    ->unique()
                    ->count() <= 1;

            $calculateRowspan = function ($data, $column = null) {
                $counts = [];
                $index = 0;
                $total = count($data);

                while ($index < $total) {
                    $currentValue =
                        $column === 'dosen'
                            ? $data[$index]->dosens_collection->pluck('id')->sort()->implode(',')
                            : $data[$index]->$column ?? '';

                    $step = 0;
                    while ($index + $step < $total) {
                        $nextValue =
                            $column === 'dosen'
                                ? $data[$index + $step]->dosens_collection->pluck('id')->sort()->implode(',')
                                : $data[$index + $step]->$column ?? '';

                        if ($nextValue !== $currentValue) {
                            break;
                        }
                        $step++;
                    }
                    $counts[$index] = $step;
                    $index += $step;
                }
                return $counts;
            };

            $rowspanCpmk = $calculateRowspan($programData, 'kode_cpmk');
            $rowspanBobot = $calculateRowspan($programData, 'bobot');
            $rowspanDosen = $calculateRowspan($programData, 'dosen');
        @endphp

        <thead class="program-header-row">
            <tr>
                <th class="p-1">CPMK</th>
                <th class="p-1 col-15">Kompetensi Mingguan<br>(Sub-CPMK)</th>
                <th class="p-1 col-15">Materi Pembelajaran</th>
                <th class="p-1">Referensi</th>
                <th class="p-1">Metodologi Pembelajaran<br>& Alokasi Waktunya</th>
                <th class="p-1 col-15">Deskripsi Tugas atau Asesmen<br>& Alokasi Waktunya</th>
                <th class="p-1">Indikator</th>
                <th class="p-1">Metode<br>Penilaian</th>
                <th class="p-1">Bobot<br>(%)</th>
                @if (!$isDosenUniform)
                    <th class="p-1">Dosen</th>
                @endif
            </tr>
            <tr>
                <th class="p-1">(1)</th>
                <th class="p-1 col-15">(2)</th>
                <th class="p-1 col-15">(3)</th>
                <th class="p-1">(4)</th>
                <th class="p-1">(5)</th>
                <th class="p-1 col-15">(6)</th>
                <th class="p-1">(7)</th>
                <th class="p-1">(8)</th>
                <th class="p-1">(9)</th>
                @if (!$isDosenUniform)
                    <th class="p-1">(10)</th>
                @endif
            </tr>
        </thead>
        <tbody class="tbody-border-top">
            @php
                $utsMethods = explode(',', env('UTS_FIELDS', 'UTS'));
                $uasMethods = explode(',', env('UAS_FIELDS', 'UAS'));
                $examMethods = array_merge($utsMethods, $uasMethods);
                $examMethods = array_map('trim', $examMethods);
                $lastBobot = null;
            @endphp

            @foreach ($programData as $index => $row)


                @php
                    $isExam = in_array(strtoupper($row->metode ?? ''), $examMethods);
                    $isExamKode = in_array(strtoupper($row->kode ?? ''), ['UTS', 'UAS']);

                    $textStyle = 'cell-base';
                    if ($isExamKode) {
                        $textStyle .= ' font-bold';
                    }
                @endphp
                <tr class="{{ $isExam ? 'exam-row bg-gray-200' : '' }} align-top">
                    {{-- CPMK --}}
                    <td class="{{ $textStyle }} cpmk-cell" colspan="{{ $row->general_exam ? 2 : 1 }}">
                        @if (isset($showCpmk[$index]))
                            @if ($row->general_exam)
                                <span class="font-bold">
                                    {{ strtoupper($row->name) }}
                                </span>
                            @else
                                <span class="font-bold">
                                    {{ $row->kode_cpmk }}
                                </span><br>
                                ({{ $row->kode_scpmk }})
                            @endif
                        @else
                            ({{ $row->kode_scpmk }})
                        @endif
                    </td>

                    {{-- Kolom Materi/Sub-CPMK --}}
                    @if ($row->general_exam)
                        <td class="{{ $textStyle }}" colspan="6">
                            {{ $row->metode }}: {{ $row->deskripsi }}
                        </td>
                    @else
                        <td class="{{ $textStyle }}">{{ $row->deskripsi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->materi ?? '-' }}</td>
                        <!-- Kolom Referensi -->
                        <td class="{{ $textStyle }}">
                            @php
                                $refNumbers = optional($row->refs)
                                    ->map(
                                        fn($ref) => isset($refOrderMap[$ref->id])
                                            ? '[' . $refOrderMap[$ref->id] . ']'
                                            : null,
                                    )
                                    ->filter()
                                    ->sort()
                                    ->implode(', ');
                            @endphp

                            {{ !empty($refNumbers) ? $refNumbers : '-' }}
                        </td>

                        <td class="{{ $textStyle }}">{{ $row->metodologi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->tugas ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->indikator ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->metode ?? 'Teori' }}</td>
                    @endif

                    {{-- Bobot --}}
                    <td class="{{ $textStyle }} bobot-cell">
                        <div class="{{ $row->bobot_normalisasi == $lastBobot ? 'bobot-repeat' : '' }}">
                            {{ $row->bobot_normalisasi ?? '-' }}
                        </div>
                    </td>

                    {{-- KOLOM DOSEN --}}
                    {{-- @if (!$isDosenUniform && isset($rowspanDosen[$index]))
                        <td class="dosen-cell" rowspan="{{ $rowspanDosen[$index] }}"> --}}
                    @if (!$isDosenUniform)
                        <td class="dosen-cell">

                            @if ($row->dosens_collection->isEmpty())
                                <span class="italic text-gray-500">Tim Pengajar</span>
                            @else
                                @php
                                    $count = $row->dosens_collection->count();
                                @endphp

                                @foreach ($row->dosens_collection as $dosen)
                                    <div class="{{ $count > 1 ? 'dosen-item leading-relaxed' : '' }}">
                                        @if ($count > 1)
                                            <span class="mr-5">{{ $loop->iteration }}.</span>
                                        @endif

                                        {{ $dosen['name'] }}

                                        @if (isset($dosen['is_ketua']) && $dosen['is_ketua'])
                                            <span class="font-bold">(Ketua)</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endif

                        </td>
                    @endif
                </tr>
                @php $lastBobot = $row->bobot_normalisasi; @endphp
            @endforeach
            <tr>
                <td colspan="{{ $isDosenUniform ? 9 : 10 }}" class="beban-row">
                    <span>Beban Belajar Mahasiswa Selama Satu Semester:</span>
                    <span class="float-right">{{ $rps->sks ?? '0' }} SKS</span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- <div class="page-break-table"></div> --}}

    <div class="mb-20px">
        <h3 class="ref-title" style="margin-bottom: 6px;">Referensi:</h3>
        <div class="ref-wrap">
            <ul class="ref-list">
                @foreach ($rps->all_refs as $ref)
                    <li class="ref-item">
                        <span class="ref-num">[{{ $loop->iteration }}]</span>
                        {{ $ref->citation }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="skala-wrap">
        <div class="skala-title">
            Skala Penilaian:
        </div>

        <table class="skala-table nilai-table">
            <thead>
                <tr>
                    <th class="skala-col-20 skala-th">
                        Nilai
                    </th>
                    <th class="skala-col-30 skala-th nowrap">
                        Rentang Nilai
                    </th>
                    <th class="skala-col-50 skala-th nowrap">
                        Index Nilai
                    </th>
                    <th class="skala-col-50 skala-th">
                        Predikat
                    </th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                    <td class="skala-td">A</td>
                    <td class="skala-td">86-100</td>
                    <td class="skala-td">4.00</td>
                    <td class="skala-td nowrap">Sangat Baik
                    </td>
                </tr>
                <tr>
                    <td class="skala-td">A-</td>
                    <td class="skala-td">80-85</td>
                    <td class="skala-td">3.70</td>
                    <td class="skala-td nowrap">Sangat Baik
                    </td>
                </tr>
                <tr>
                    <td class="font-medium skala-td">B+</td>
                    <td class="skala-td">75-79</td>
                    <td class="skala-td">3.30</td>
                    <td class="skala-td">Baik</td>
                </tr>
                <tr>
                    <td class="font-medium skala-td">B</td>
                    <td class="skala-td">70-74</td>
                    <td class="skala-td">3.00</td>
                    <td class="skala-td">Baik</td>
                </tr>
                <tr>
                    <td class="font-medium skala-td">B-</td>
                    <td class="skala-td">65-69</td>
                    <td class="skala-td">2.70</td>
                    <td class="skala-td">Baik</td>
                </tr>
                <tr>
                    <td class="font-medium skala-td">C+</td>
                    <td class="skala-td">60-64</td>
                    <td class="skala-td">2.30</td>
                    <td class="skala-td">Cukup</td>
                </tr>
                <tr>
                    <td class="font-medium skala-td">C</td>
                    <td class="skala-td">56-59</td>
                    <td class="skala-td">2.00</td>
                    <td class="skala-td">Cukup</td>
                </tr>
                <tr>
                    <td class="font-medium skala-td">D</td>
                    <td class="skala-td">40-55</td>
                    <td class="skala-td">1.00</td>
                    <td class="skala-td">Kurang</td>
                </tr>
                <tr>
                    <td class="font-medium skala-td">E</td>
                    <td class="skala-td">0-39</td>
                    <td class="skala-td">0.00</td>
                    <td class="skala-td skala-td-nowrap">Sangat Kurang
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
