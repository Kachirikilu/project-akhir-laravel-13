<div class="rps-pdf {{ $isPDF ?? false ? '' : 'rps-pdf--min-width' }}">

    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        .rps-pdf {
            font-family: "Times New Roman", Times, serif;
            color: black !important;
            padding: 1px;
            background: #fff;
            /* margin-top: 40px; */
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

        /* border-collapse wajib di-set eksplisit; tanpa ini DomPDF pakai model
           "separate" yang membuat border dobel/renggang, terutama di sel rowspan/colspan */
        .rps-table,
        .nilai-table {
            border-collapse: collapse;
        }

        .list-indent {
            padding-left: 15px;
            text-indent: -15px;
        }

        /* ===== Generic helpers ===== */
        .w-full {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .italic {
            font-style: italic;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .leading-relaxed {
            line-height: 1.6;
        }

        .leading-tight {
            line-height: 1.2;
        }

        .align-top {
            vertical-align: top;
        }

        .align-middle {
            vertical-align: middle;
        }

        .float-right {
            float: right;
        }

        .whitespace-nowrap {
            white-space: nowrap;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .border-t-black {
            border-top: 1px solid black;
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
        }

        .border-cell {
            border: 1px solid black;
        }

        .page-break-table {
            page-break-after: always !important;
        }

        /* ===== Header ===== */
        .header-table td {
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
            margin-bottom: 40px;
        }

        .header-table td {
            vertical-align: middle;
            /* Memastikan isi sel vertikal di tengah */
        }

        .header-logo-cell {
            width: 12%;
            text-align: center;
            vertical-align: middle;
            /* Penting untuk vertical centering di sel */
        }

        .header-logo-wrap {
            display: block;
            /* Menghilangkan spasi ekstra di bawah gambar */
            text-align: center;
        }

        .header-logo-wrap img {
            height: 80px;
            width: auto;
            /* Menjaga proporsi */
            display: inline-block;
            vertical-align: middle;
        }

        /* ===== Section title ===== */
        .section-title {
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* ===== Identitas table ===== */

        .identity-table {
            width: 100%;
            margin-bottom: 24px;
            font-size: 10px;
            margin-bottom: 40px;
        }

        .identity-header-row {
            font-weight: bold;
            text-align: center;
            background: #f9fafb;
        }

        .col-1-6 {
            width: 16.6667%;
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
        }

        .cpmk-label {
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
        }

        .dosen-label {
            font-weight: bold;
            background: #f9fafb;
            text-align: center;
        }

        .dosen-item {
            padding-left: 15px;
            text-indent: -15px;
            margin-bottom: 4px;
        }

        .mr-5 {
            margin-right: 5px;
        }

        .dosen-missing {
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
        }

        /* DomPDF tidak support flexbox: cukup pakai block stacking biasa.
           div spacer diberi tinggi tetap supaya judul "turun" ke posisi bawah,
           efek visualnya setara dengan flex justify-content:space-between. */
        .otoritas-flex {
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
        }

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
            background: #f9fafb;
            font-weight: 600;
            font-style: italic;
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
            text-align: center;
            vertical-align: top;
            font-weight: bold;
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
            padding: 4px;
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
            font-weight: bold;
            margin-right: 5px;
        }

        /* ===== Skala Penilaian ===== */
        .skala-wrap {
            max-width: 320px;
        }

        .skala-title {
            font-weight: bold;
            padding: 4px;
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

        .skala-td-nowrap {
            white-space: nowrap;
        }
    </style>

    {{-- @php
        $dataX = $detailRPSData ?? [];
    @endphp --}}
    {{-- ================= HEADER ================= --}}
    <table class="w-full rps-table header-table">
        <tr>
            <td class="header-logo-cell">
                <div class="header-logo-wrap">
                    @if ($logoBase64 ?? null)
                        <img src="{{ $logoBase64 }}" class="h-20 object-contain">
                    @else
                        <img src="{{ asset('favicon.svg') }}" class="h-20 object-contain">
                    @endif
            </td>
            <td class="header-title-cell">
                @php
                    $prodiHead = $prodi ?? $rps->mk_rel?->prodis->first();
                @endphp
                <div>
                    <div>{{ strtoupper(env('UNIVERSITAS')) }}</div>
                    {{-- <div>Fakultas {{ $dataX['fakultas'] ?? '' }}</div>
                    <div>Departemen {{ $dataX['departemen'] ?? '' }}</div>
                    <div>Program Studi {{ $dataX['prodi'] ?? '' }}</div> --}}
                    <div>{{ $prodiHead->dp_rel->fk_rel->fakultas_fk ?? '' }}</div>
                    <div>{{ $prodiHead->dp_rel->departemen_dp ?? '' }}</div>
                    <div>{{ $prodiHead->prodi_pr ?? '' }}</div>
                </div>
            </td>
            <td class="header-spacer-cell">
            </td>
        </tr>
        <tr>
            <td colspan="3" class="header-title-row">
                RENCANA PEMBELAJARAN SEMESTER
            </td>
        </tr>
    </table>

    {{-- ================= IDENTITAS ================= --}}
    <div class="section-title">A. IDENTITAS MATA KULIAH</div>
    <table class="identity-table rps-table page-break-table">
        <tr class="identity-header-row">
            <td rowspan="2" class="col-1-6">Nama Mata Kuliah</td>
            <td rowspan="2" class="col-1-6">Kode</td>
            <td rowspan="2" class="col-1-6">Bahan Kajian</td>
            <td colspan="2" class="col-1-6">SKS</td>
            <td rowspan="2" class="col-1-6">Semester</td>
            <td rowspan="2" class="col-1-6">Tanggal Revisi</td>
        </tr>
        <tr class="identity-header-row">
            <td class="col-1-12">Kuliah</td>
            <td class="col-1-12">
                {{ ($rps->mk_rel->sks_text ?? 'Tatap Muka') === 'Tatap Muka' ? 'Praktikum' : $rps->mk_rel->sks_text }}
            </td>
        </tr>
        <tr class="text-center">
            <td>{{ $rps->mk_rel->mk ?? '' }}</td>
            <td>{{ $rps->mk_rel->kode ?? '' }}</td>
            <td>{{ $rps->mk_rel->bahan_kajian ?? '' }}</td>
            <td>{{ $rps->mk_rel->sks_tp ?? '-' }}</td>
            <td>{{ $rps->mk_rel->sks_pr ?? ($rps->mk_rel->sks_pl ?? ($rps->mk_rel->sks_sm ?? '-')) }}</td>
            <td>{{ $rps->mk_rel->semester ?? '' }}</td>
            <td>{{ $rps->revisi_day ?? '' }}</td>
        </tr>
        <tr>
            <td class="desc-label">Deskripsi Mata Kuliah</td>
            <td colspan="6" class="desc-value">
                {{ $rps->mk_rel->deskripsi ?? '' }}
            </td>
        </tr>

        <tr>
            <td class="cpmk-label">Capaian Pembelajaran Mata
                Kuliah<br>(CPMK)</td>
            <td colspan="6">
                @foreach ($rps->cpmks as $cpmk)
                    <div class="cpmk-item">
                        <span class="mr-5-bold">{{ $loop->iteration }}.</span>
                        <span class="mr-3-bold">{{ $cpmk->kode }}:</span> {{ $cpmk->deskripsi_cpl }}
                    </div>
                @endforeach
            </td>
        </tr>
        @php
            $tim = $tim_dosen->first();
            $allDosens = $tim ? $tim->dosens : collect();
            $ketua = $allDosens->firstWhere('pivot.is_ketua', 1);
            $instruktur = $allDosens->filter(fn($d) => (int) $d->pivot->is_ketua !== 1);
            $label = $allDosens->count() > 1 ? 'Tim Pengajar' : 'Dosen Pengampu';
            $hasInstruktur = $instruktur->isNotEmpty();
        @endphp

        <tr>
            {{-- Label Dosen --}}
            <td rowspan="{{ $hasInstruktur ? 2 : 1 }}" class="dosen-label">
                {{ $label }}
            </td>

            {{-- Daftar Dosen --}}
            <td rowspan="{{ $hasInstruktur ? 2 : 1 }}" colspan="{{ $hasInstruktur ? 3 : 6 }}">
                @if ($allDosens->count() === 1)
                    {{ $allDosens->first()->name }}<br>NIP: {{ $allDosens->first()->nip }}
                @elseif ($allDosens->count() > 1)
                    @foreach ($allDosens as $idx => $dosen)
                        <div class="dosen-item">
                            <span class="mr-5">{{ $idx + 1 }}.</span>
                            {{ $dosen->name }}<br>NIP: {{ $dosen->nip }}
                        </div>
                    @endforeach
                @else
                    <span class="dosen-missing">Dosen Pengampu belum Didaftarkan!</span>
                @endif
            </td>

            {{-- Hanya muncul jika ada instruktur --}}
            @if ($hasInstruktur)
                <td class="font-bold text-center">Ketua Pengajar</td>
                <td colspan="2">
                    <div class="dosen-item font-bold">
                        <span class="mr-5">1. </span>
                        {{ $ketua->name ?? '-' }}
                    </div>
                </td>
            @endif
        </tr>

        @if ($hasInstruktur)
            <tr>
                <td class="font-bold text-center">Instruktur</td>
                <td colspan="2">
                    @foreach ($instruktur as $idx => $dosen)
                        <div class="dosen-item">
                            <span class="mr-5">{{ $idx + 1 }}.</span>
                            {{ $dosen->name }}
                        </div>
                    @endforeach
                </td>
            </tr>
        @endif
        <tr>
            <td class="otoritas-label">Otoritas</td>

            <td colspan="3" class="otoritas-cell">
                <div class="otoritas-flex">
                    <div class="otoritas-spacer">
                    </div>
                    <div class="otoritas-title">
                        Ketua Program Studi
                    </div>
                </div>
            </td>

            <td colspan="3" class="otoritas-cell otoritas-border-left">
                <div class="otoritas-flex">
                    <div class="otoritas-spacer">
                    </div>
                    <div class="otoritas-title">
                        Wakil Dekan Bidang Akademik
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- <div class="page-break-table"></div> --}}


    {{-- ================= PROGRAM PEMBELAJARAN ================= --}}
    <div class="section-title">B. PROGRAM PEMBELAJARAN</div>

    <table class="program-table rps-table page-break-table">
        @php
            $programData = $rps->scpmkAtr;

            $programData = $rps->scpmkAtr->map(function ($row) use ($allDosens) {
                $row->dosens_collection = $allDosens;
                return $row;
            });

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

        <thead class="program-header-row">
            <tr>
                <th class="p-1">CPMK</th>
                <th class="p-1 col-15">Kompetensi Mingguan<br>(Sub-CPMK)</th>
                <th class="p-1 col-15">Materi Pembelajaran</th>
                <th class="p-1">Metodologi Pembelajaran<br>dan Alokasi Waktunya</th>
                <th class="p-1 col-15">Deskripsi Tugas atau Asesmen beserta Alokasi Waktunya</th>
                <th class="p-1">Indikator</th>
                <th class="p-1">Bobot</th>
                @if (!$isDosenUniform)
                    <th class="p-1">Dosen</th>
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
                <tr class="{{ $isExam ? 'exam-row' : '' }}">
                    {{-- CPMK --}}
                    @if (isset($rowspanCpmk[$index]))
                        <td class="{{ $textStyle }} cpmk-cell" rowspan="{{ $rowspanCpmk[$index] }}">
                            {{ $row->kode_cpmk }}
                        </td>
                    @endif

                    {{-- Kolom Materi/Sub-CPMK --}}
                    @if ($isExam && $isExamKode)
                        <td class="{{ $textStyle }} text-center" colspan="5">
                            {{ $row->deskripsi }}
                        </td>
                    @else
                        <td class="{{ $textStyle }}">{{ $row->kode ?? '-' }}<br>Metode: {{ $row->metode ?? '-' }}
                        </td>
                        <td class="{{ $textStyle }}">{{ $row->materi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->metodologi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->tugas ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->indikator ?? '-' }}</td>
                    @endif

                    {{-- Bobot --}}
                    <td class="{{ $textStyle }} bobot-cell">
                        <div class="{{ $row->bobot_normalisasi == $lastBobot ? 'bobot-repeat' : '' }}">
                            {{ $row->bobot_normalisasi ?? '-' }}%
                        </div>
                    </td>

                    {{-- KOLOM DOSEN --}}
                    @if (!$isDosenUniform && isset($rowspanDosen[$index]))
                        <td class="dosen-cell" rowspan="{{ $rowspanDosen[$index] }}">

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
                <td colspan="8" class="beban-row">
                    <span>Beban Belajar Mahasiswa Selama Satu Semester:</span>
                    <span class="float-right">{{ $rps->sks ?? '0' }} SKS</span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- <div class="page-break-table"></div> --}}

    <div>
        <h3 class="ref-title">Referensi</h3>
        <div class="ref-wrap">
            <ul class="ref-list">
                @foreach ($rps->all_refs as $ref)
                    <li class="ref-item">
                        <span class="ref-num">{{ $loop->iteration }}.</span>
                        {{ $ref->citation }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="skala-wrap">
        <div class="skala-title">
            Skala Penilaian
        </div>

        <table class="skala-table nilai-table">
            <thead>
                <tr>
                    <th class="skala-col-20 skala-th">
                        Nilai
                    </th>
                    <th class="skala-col-30 skala-th">
                        Rentang Nilai
                    </th>
                    <th class="skala-col-50 skala-th">
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
                    <td class="skala-td skala-td-nowrap">Sangat Baik
                    </td>
                </tr>
                <tr>
                    <td class="skala-td">A-</td>
                    <td class="skala-td">80-85</td>
                    <td class="skala-td">3.70</td>
                    <td class="skala-td skala-td-nowrap">Sangat Baik
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
