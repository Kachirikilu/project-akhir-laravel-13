<style>
    /* Reset & Container */
    .rps-pdf {
        font-family: "Times New Roman", Times, serif;
        color: black !important;
        padding: 1px;
        margin-top: 20px;
        background-color: white;
    }

    .min-width-1000 {
        min-width: 1000px;
    }

    /* Table Styles */
    .rps-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        font-size: 12px;
    }

    .rps-table th,
    .rps-table td {
        border: 1px solid black;
        padding: 8px;
        vertical-align: middle;
    }

    /* Utility Classes */
    .text-center {
        text-align: center;
    }

    .font-bold {
        font-weight: bold;
    }

    .text-lg {
        font-size: 18px;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .list-indent {
        padding-left: 15px;
        text-indent: -15px;
        margin-bottom: 4px;
    }

    .bg-gray {
        background-color: #f9f9f9;
    }

    .text-red {
        color: #dc2626;
        font-style: italic;
    }

    .text-justify {
        text-align: justify;
    }

    /* Width & Layout */
    .w-1-6 {
        width: 16.66%;
    }

    .w-1-12 {
        width: 8.33%;
    }

    .w-12 {
        width: 12%;
    }

    .w-76 {
        width: 76%;
    }

    .h-signature {
        height: 80px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .page-break {
        page-break-after: always;
    }

    .no-border-r {
        border-right: none;
    }

    .no-border-l {
        border-left: none;
    }

    /* Penambahan untuk style B. Program Pembelajaran */
    .font-semibold {
        font-weight: 600;
    }

    .italic {
        font-style: italic;
    }

    .bg-gray-50 {
        background-color: #f9f9f9;
    }

    .opacity-40 {
        opacity: 0.4;
    }

    .float-right {
        float: right;
    }

    .text-gray-500 {
        color: #6b7280;
    }

    .align-top {
        vertical-align: top;
    }

    .leading-relaxed {
        line-height: 1.6;
    }

    /* Styling Tambahan */
    .ref-list {
        list-style: none;
        padding: 0;
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

    .scale-container {
        max-width: 320px;
        margin-top: 10px;
    }

    .nilai-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 10px;
    }

    .nilai-table th,
    .nilai-table td {
        padding: 2px 4px;
        line-height: 1.2;
        text-align: center;
        border: 1px solid black;
    }

    .bold {
        font-weight: bold;
    }
</style>

<div class="rps-pdf {{ $isImage ?? false ? '' : 'min-width-1000' }}">

    {{-- ================= HEADER ================= --}}
    <table class="rps-table">
        <tr>
            <td class="w-12 text-center">
                <img src="{{ $isImage ?? false ? public_path('images/logo-unsri.webp') : asset('favicon.svg') }}"
                    style="height: 80px; object-fit: contain;">
            </td>
            <td class="w-76 no-border-r text-center font-bold text-lg uppercase">
                @php $prodiHead = $prodi ?? $rps->mk_rel?->prodis->first(); @endphp
                <div>{{ strtoupper(env('UNIVERSITAS')) }}</div>
                <div>{{ $prodiHead->dp_rel->fk_rel->fakultas_fk ?? '' }}</div>
                <div>{{ $prodiHead->dp_rel->departemen_dp ?? '' }}</div>
                <div>{{ $prodiHead->prodi_pr ?? '' }}</div>
            </td>
            <td class="w-12 no-border-l"></td>
        </tr>
        <tr>
            <td colspan="3" class="text-center font-bold text-lg" style="padding: 10px 0;">
                RENCANA PEMBELAJARAN SEMESTER
            </td>
        </tr>
    </table>

    {{-- ================= IDENTITAS ================= --}}
    <div class="font-bold" style="margin-bottom: 8px;">A. IDENTITAS MATA KULIAH</div>
    <table class="rps-table" style="font-size: 10px;">
        <tr class="font-bold text-center bg-gray">
            <td rowspan="2" class="w-1-6">Nama Mata Kuliah</td>
            <td rowspan="2" class="w-1-6">Kode</td>
            <td rowspan="2" class="w-1-6">Bahan Kajian</td>
            <td colspan="2" class="w-1-6">SKS</td>
            <td rowspan="2" class="w-1-6">Semester</td>
            <td rowspan="2" class="w-1-6">Tanggal Revisi</td>
        </tr>
        <tr class="font-bold text-center bg-gray">
            <td class="w-1-12">Kuliah</td>
            <td class="w-1-12">
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
            <td class="text-center font-bold">Deskripsi Mata Kuliah</td>
            <td colspan="6" class="text-justify">{{ $rps->mk_rel->deskripsi ?? '' }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray text-center">Capaian Pembelajaran Mata Kuliah (CPMK)</td>
            <td colspan="6">
                @foreach ($rps->cpmks as $cpmk)
                    <div class="list-indent">
                        <span class="font-bold">{{ $loop->iteration }}. {{ $cpmk->kode }}:</span>
                        {{ $cpmk->deskripsi_cpl }}
                    </div>
                @endforeach
            </td>
        </tr>

        {{-- Dosen Section --}}
        @php
            $tim = $tim_dosen->first();
            $allDosens = $tim ? $tim->dosens : collect();
            $ketua = $allDosens->firstWhere('pivot.is_ketua', 1);
            $instruktur = $allDosens->filter(fn($d) => (int) $d->pivot->is_ketua !== 1);
            $label = $allDosens->count() > 1 ? 'Tim Pengajar' : 'Dosen Pengampu';
        @endphp
        <tr>
            <td rowspan="2" class="font-bold bg-gray text-center">{{ $label }}</td>
            <td rowspan="2" colspan="{{ $instruktur->isNotEmpty() ? 3 : 6 }}">
                @if ($allDosens->count() === 1)
                    {{ $allDosens->first()->name }}<br>NIP: {{ $allDosens->first()->nip }}
                @elseif ($allDosens->count() > 1)
                    @foreach ($allDosens as $idx => $dosen)
                        <div class="list-indent">{{ $idx + 1 }}. {{ $dosen->name }}<br>NIP: {{ $dosen->nip }}
                        </div>
                    @endforeach
                @else
                    <span class="text-red font-bold">Dosen Pengampu belum Didaftarkan!</span>
                @endif
            </td>
            @if ($instruktur->isNotEmpty())
                <td class="font-bold text-center">Ketua Pengajar</td>
                <td colspan="2">
                    <div class="list-indent font-bold">1. {{ $ketua->name ?? '-' }}</div>
                </td>
            @endif
        </tr>
        @if ($instruktur->isNotEmpty())
            <tr>
                <td class="font-bold text-center">Instruktur</td>
                <td colspan="3">
                    @foreach ($instruktur as $idx => $dosen)
                        <div class="list-indent">{{ $idx + 1 }}. {{ $dosen->name }}</div>
                    @endforeach
                </td>
            </tr>
        @endif
        <tr>
            <td class="font-bold text-center">Otoritas</td>
            <td colspan="3" class="text-center">
                <div class="h-signature">
                    <div style="flex-grow:1;"></div>
                    <div class="font-bold">Ketua Program Studi</div>
                </div>
            </td>
            <td colspan="3" class="text-center" style="border-left: 1px solid black;">
                <div class="h-signature">
                    <div style="flex-grow:1;"></div>
                    <div class="font-bold">Wakil Dekan Bidang Akademik</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>


    {{-- ================= PROGRAM PEMBELAJARAN ================= --}}
    <div class="font-bold" style="margin-bottom: 8px;">B. PROGRAM PEMBELAJARAN</div>

    <table class="rps-table" style="width: 100%; font-size: 10px; line-height: 1.2; border-collapse: collapse;">

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
        <thead class="bg-gray-50 font-bold text-center">
            <tr>
                <th style="padding: 4px;">CPMK</th>
                <th style="padding: 4px; width: 15%;">Kompetensi Mingguan<br>(Sub-CPMK)</th>
                <th style="padding: 4px; width: 15%;">Materi Pembelajaran</th>
                <th style="padding: 4px;">Metodologi Pembelajaran<br>dan Alokasi Waktunya</th>
                <th style="padding: 4px; width: 15%;">Deskripsi Tugas atau Asesmen beserta Alokasi Waktunya</th>
                <th style="padding: 4px;">Indikator</th>
                <th style="padding: 4px;">Bobot</th>
                @if (!$isDosenUniform)
                    <th style="padding: 4px;">Dosen</th>
                @endif
            </tr>
        </thead>
        <tbody style="border-top: 1px solid black;">
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

                    $textStyle = 'padding: 8px; border: 1px solid black;';
                    $rowClass = $isExam ? 'bg-gray-50 font-semibold italic' : '';
                @endphp

                <tr class="{{ $rowClass }}">
                    {{-- CPMK --}}
                    @if (isset($rowspanCpmk[$index]))
                        <td class="text-center align-top font-bold" style="{{ $textStyle }}"
                            rowspan="{{ $rowspanCpmk[$index] }}">
                            {{ $row->kode_cpmk }}
                        </td>
                    @endif

                    {{-- Kolom Materi/Sub-CPMK --}}
                    @if ($isExam && $isExamKode)
                        <td class="text-center" style="{{ $textStyle }}" colspan="5">
                            {{ $row->deskripsi }}
                        </td>
                    @else
                        <td style="{{ $textStyle }}">{{ $row->kode ?? '-' }}<br>Metode: {{ $row->metode ?? '-' }}
                        </td>
                        <td style="{{ $textStyle }}">{{ $row->materi ?? '-' }}</td>
                        <td style="{{ $textStyle }}">{{ $row->metodologi ?? '-' }}</td>
                        <td style="{{ $textStyle }}">{{ $row->tugas ?? '-' }}</td>
                        <td style="{{ $textStyle }}">{{ $row->indikator ?? '-' }}</td>
                    @endif

                    {{-- Bobot --}}
                    <td class="text-center font-bold" style="{{ $textStyle }}">
                        <div class="{{ $row->bobot_normalisasi == $lastBobot ? 'opacity-40' : '' }}">
                            {{ $row->bobot_normalisasi ?? '-' }}%
                        </div>
                    </td>

                    {{-- KOLOM DOSEN --}}
                    @if (!$isDosenUniform && isset($rowspanDosen[$index]))
                        <td class="align-top" style="padding: 8px; border: 1px solid black;"
                            rowspan="{{ $rowspanDosen[$index] }}">
                            @if ($row->dosens_collection->isEmpty())
                                <span class="italic text-gray-500">Tim Pengajar</span>
                            @else
                                @php $count = $row->dosens_collection->count(); @endphp
                                @foreach ($row->dosens_collection as $dosen)
                                    <div class="{{ $count > 1 ? 'list-indent leading-relaxed' : '' }}">
                                        @if ($count > 1)
                                            <span style="margin-right: 5px;">{{ $loop->iteration }}.</span>
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
                <td colspan="8" class="font-bold" style="padding: 8px; border: 1px solid black;">
                    <span>Beban Belajar Mahasiswa Selama Satu Semester:</span>
                    <span class="float-right">{{ $rps->sks ?? '0' }} SKS</span>
                </td>
            </tr>
        </tbody>
    </table>







    <div class="page-break"></div>

    <div>
        <h3 class="bold" style="padding: 4px; font-size: 10px;">Referensi</h3>
        <div style="padding: 0 4px;">
            <ul class="ref-list">
                @foreach ($rps->all_refs as $ref)
                    <li class="ref-item">
                        <span class="bold" style="margin-right: 5px;">{{ $loop->iteration }}.</span>
                        {{ $ref->citation }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="scale-container">
        <div class="bold" style="padding: 4px; font-size: 10px; line-height: 1.2;">
            Skala Penilaian
        </div>

        <table class="nilai-table">
            <thead>
                <tr>
                    <th style="width: 20%; font-weight: bold;">Nilai</th>
                    <th style="width: 30%; font-weight: bold;">Rentang Nilai</th>
                    <th style="width: 25%; font-weight: bold;">Index Nilai</th>
                    <th style="width: 25%; font-weight: bold;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $skala = [
                        ['A', '86-100', '4.00', 'Sangat Baik'],
                        ['A-', '80-85', '3.70', 'Sangat Baik'],
                        ['B+', '75-79', '3.30', 'Baik'],
                        ['B', '70-74', '3.00', 'Baik'],
                        ['B-', '65-69', '2.70', 'Baik'],
                        ['C+', '60-64', '2.30', 'Cukup'],
                        ['C', '56-59', '2.00', 'Cukup'],
                        ['D', '40-55', '1.00', 'Kurang'],
                        ['E', '0-39', '0.00', 'Sangat Kurang'],
                    ];
                @endphp
                @foreach ($skala as $row)
                    <tr>
                        <td>{{ $row[0] }}</td>
                        <td>{{ $row[1] }}</td>
                        <td>{{ $row[2] }}</td>
                        <td style="white-space: nowrap;">{{ $row[3] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
