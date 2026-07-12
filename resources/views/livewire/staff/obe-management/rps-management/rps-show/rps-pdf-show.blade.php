<div class="rps-pdf space-y-10 mt-10 bg-white min-w-[1000px]">

    <style>
        .rps-pdf {
            font-family: "Times New Roman", Times, serif;
            color: black !important;
        }

        .rps-table>thead>tr>th,
        .rps-table>tbody>tr>td,
        .nilai-table>thead>tr>th,
        .nilai-table>tbody>tr>td {
            border: 1px solid black !important;
            padding: 8px;
        }

        .rps-table>thead>tr>th,
        .rps-table>tbody>tr>td {
            padding: 8px;
        }

        .nilai-table>thead>tr>th,
        .nilai-table>tbody>tr>td {
            padding: 4px;
        }

        .list-indent {
            padding-left: 15px;
            text-indent: -15px;
        }
    </style>

    {{-- @php
        $dataX = $detailRPSData ?? [];
    @endphp --}}
    {{-- ================= HEADER ================= --}}
    <table class="w-full rps-table">
        <tr>
            <td class="w-[12%] text-center">
                <div class="flex items-center justify-center">
                    @if ($logoBase64 ?? null)
                        <img src="{{ $logoBase64 }}" class="h-20 object-contain">
                    @else
                        <img src="{{ asset('favicon.svg') }}" class="h-20 object-contain">
                    @endif
                </div>
            </td>
            <td class="w-[76%] !border-r-0 text-center font-bold text-lg leading-tight uppercase">
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
            <td class="w-[12%] !border-l-0">
            </td>
        </tr>
        <tr>
            <td colspan="3" class="text-center font-bold text-lg py-2 uppercase">
                RENCANA PEMBELAJARAN SEMESTER
            </td>
        </tr>
    </table>

    {{-- ================= IDENTITAS ================= --}}
    <div class="font-bold mb-2">A. IDENTITAS MATA KULIAH</div>
    <table class="w-full mb-6 text-[10px] rps-table">
        <tr class="font-bold text-center bg-gray-50">
            <td rowspan="2" class="w-1/6">Nama Mata Kuliah</td>
            <td rowspan="2" class="w-1/6">Kode</td>
            <td rowspan="2" class="w-1/6">Bahan Kajian</td>
            <td colspan="2" class="w-1/6">SKS</td>
            <td rowspan="2" class="w-1/6">Semester</td>
            <td rowspan="2" class="w-1/6">Tanggal Revisi</td>
        </tr>
        <tr class="font-bold text-center bg-gray-50">
            <td class="w-1/12">Kuliah</td>
            <td class="w-1/12">
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
            <td colspan="6" class="text-justify leading-relaxed">
                {{ $rps->mk_rel->deskripsi ?? '' }}
            </td>
        </tr>

        <tr>
            <td class="font-bold bg-gray-50 text-center">Capaian Pembelajaran Mata
                Kuliah<br>(CPMK)</td>
            <td colspan="6">
                @foreach ($rps->cpmks as $cpmk)
                    <div class="list-indent text-justify leading-relaxed mb-1">
                        <span class="mr-[5px] font-bold">{{ $loop->iteration }}.</span>
                        <span class="mr-[3px] font-bold">{{ $cpmk->kode }}:</span> {{ $cpmk->deskripsi_cpl }}
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
        @endphp
        <tr>
            <td rowspan="2" class="font-bold bg-gray-50 text-center">
                {{ $label }}
            </td>
            <td rowspan="2" @if ($instruktur->isNotEmpty()) colspan="3" @else colspan="6" @endif>
                @if ($allDosens->count() === 1)
                    {{ $allDosens->first()->name }}<br>NIP: {{ $allDosens->first()->nip }}
                @elseif ($allDosens->count() > 1)
                    @foreach ($allDosens as $idx => $dosen)
                        <div class="list-indent mb-1">
                            <span class="mr-[5px]">{{ $idx + 1 }}.</span>
                            {{ $dosen->name }}<br>NIP: {{ $dosen->nip }}
                        </div>
                    @endforeach
                @else
                    <span class="text-red-600 font-bold italic">Dosen Pengampu belum Didaftarkan!</span>
                @endif
            </td>
            @if ($instruktur->isNotEmpty())
                <td class="font-bold text-center">Ketua Pengajar</td>
                <td colspan="2">
                    <div class="list-indent font-bold">
                        <span class="mr-[5px]">1. </span>
                        {{ $ketua->name ?? '-' }}
                    </div>
                </td>
            @endif
        </tr>
        <tr>
            @if ($instruktur->isNotEmpty())
                <td class="font-bold text-center">Instruktur</td>
                <td colspan="3">
                    @foreach ($instruktur as $idx => $dosen)
                        <div class="list-indent">
                            <span class="mr-[5px]">{{ $idx + 1 }}.</span>
                            {{ $dosen->name }}
                        </div>
                    @endforeach
                </td>
            @endif
        </tr>
        <tr>
            <td class="font-bold text-center align-middle">Otoritas</td>

            <td colspan="3" class="text-center">
                <div class="flex flex-col justify-between h-18">
                    <div class="h-full">
                    </div>
                    <div class="font-bold pt-1">
                        Ketua Program Studi
                    </div>
                </div>
            </td>

            <td colspan="3" class="text-center border-l">
                <div class="flex flex-col justify-between h-18">
                    <div class="h-full">
                    </div>
                    <div class="font-bold pt-1">
                        Wakil Dekan Bidang Akademik
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>










    {{-- ================= PROGRAM PEMBELAJARAN ================= --}}
    <div class="font-bold mb-2">B. PROGRAM PEMBELAJARAN</div>

    <table class="w-full text-[10px] leading-tight rps-table">
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
                <th class="p-1">CPMK</th>
                <th class="p-1 w-[15%]">Kompetensi Mingguan<br>(Sub-CPMK)</th>
                <th class="p-1 w-[15%]">Materi Pembelajaran</th>
                <th class="p-1">Metodologi Pembelajaran<br>dan Alokasi Waktunya</th>
                <th class="p-1 w-[15%]">Deskripsi Tugas atau Asesmen beserta Alokasi Waktunya</th>
                <th class="p-1">Indikator</th>
                <th class="p-1">Bobot</th>
                @if (!$isDosenUniform)
                    <th class="p-1">Dosen</th>
                @endif
            </tr>
        </thead>
        <tbody class="border-t border-black">
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

                    $textStyle = 'p-2 border border-black';
                    if ($isExamKode) {
                        $textStyle .= ' font-bold';
                    }
                @endphp
                <tr class="{{ $isExam ? 'bg-gray-50 font-semibold italic' : '' }}">
                    {{-- CPMK --}}
                    @if (isset($rowspanCpmk[$index]))
                        <td class="{{ $textStyle }} text-center align-top font-bold"
                            rowspan="{{ $rowspanCpmk[$index] }}">
                            {{ $row->kode_cpmk }}
                        </td>
                    @endif

                    {{-- Kolom Materi/Sub-CPMK --}}
                    @if ($isExam && $isExamKode)
                        <td class="{{ $textStyle }} text-center" colspan="5">
                            {{ $row->deskripsi }}
                        </td>
                    @else
                        <td class="{{ $textStyle }}">{{ $row->kode ?? '-' }}<br>Metode: {{ $row->metode ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->materi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->metodologi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->tugas ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->indikator ?? '-' }}</td>
                    @endif

                    {{-- Bobot --}}
                    <td class="{{ $textStyle }} text-center font-bold">
                        <div class="{{ $row->bobot_normalisasi == $lastBobot ? 'opacity-40' : '' }}">
                            {{ $row->bobot_normalisasi ?? '-' }}%
                        </div>
                    </td>

                    {{-- KOLOM DOSEN --}}
                    @if (!$isDosenUniform && isset($rowspanDosen[$index]))
                        <td class="p-2 align-top border border-black" rowspan="{{ $rowspanDosen[$index] }}">

                            @if ($row->dosens_collection->isEmpty())
                                <span class="italic text-gray-500">Tim Pengajar</span>
                            @else
                                @php
                                    $count = $row->dosens_collection->count();
                                @endphp

                                @foreach ($row->dosens_collection as $dosen)
                                    <div class="{{ $count > 1 ? 'list-indent leading-relaxed' : '' }}">
                                        @if ($count > 1)
                                            <span class="mr-[5px]">{{ $loop->iteration }}.</span>
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
                <td colspan="8" class="font-bold p-2">
                    <span>Beban Belajar Mahasiswa Selama Satu Semester:</span>
                    <span class="float-right">{{ $rps->sks ?? '0' }} SKS</span>
                </td>
            </tr>
        </tbody>
    </table>


    <div class="page-break"></div>

    <div>
        <h3 class="font-bold p-1 text-[10px]">Referensi</h3>
        <div class="px-1">
            <ul class="list-none">
                @foreach ($rps->all_refs as $ref)
                    <li class="list-indent text-justify text-[10px] leading-relaxed mb-1">
                        <span class="font-bold mr-[5px]">{{ $loop->iteration }}.</span>
                        {{ $ref->citation }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="max-w-xs">
        <div class="font-bold p-1 text-[10px] leading-tight">
            Skala Penilaian
        </div>

        <table class="w-full text-[10px] border-collapse nilai-table">
            <thead>
                <tr>
                    <th class="w-[20%] text-center font-bold py-0.5 px-1 leading-tight !border-0">
                        Nilai
                    </th>
                    <th class="w-[30%] text-center font-bold py-0.5 px-1 leading-tight !border-0">
                        Rentang Nilai
                    </th>
                    <th class="w-[50%] text-center font-bold py-0.5 px-1 leading-tight !border-0">
                        Index Nilai
                    </th>
                    <th class="w-[50%] text-center font-bold py-0.5 px-1 leading-tight !border-0">
                        Predikat
                    </th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                    <td class="py-0.5 px-1 leading-tight !border-0">A</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">86-100</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">4.00</td>
                    <td class="py-0.5 px-1 leading-tight !border-0 whitespace-nowrap">Sangat Baik
                    </td>
                </tr>
                <tr>
                    <td class="py-0.5 px-1 leading-tight !border-0">A-</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">80-85</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">3.70</td>
                    <td class="py-0.5 px-1 leading-tight !border-0 whitespace-nowrap">Sangat Baik
                    </td>
                </tr>
                <tr>
                    <td class="font-medium py-0.5 px-1 leading-tight !border-0">B+</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">75-79</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">3.30</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">Baik</td>
                </tr>
                <tr>
                    <td class="font-medium py-0.5 px-1 leading-tight !border-0">B</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">70-74</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">3.00</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">Baik</td>
                </tr>
                <tr>
                    <td class="font-medium py-0.5 px-1 leading-tight !border-0">B-</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">65-69</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">2.70</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">Baik</td>
                </tr>
                <tr>
                    <td class="font-medium py-0.5 px-1 leading-tight !border-0">C+</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">60-64</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">2.30</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">Cukup</td>
                </tr>
                <tr>
                    <td class="font-medium py-0.5 px-1 leading-tight !border-0">C</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">56-59</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">2.00</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">Cukup</td>
                </tr>
                <tr>
                    <td class="font-medium py-0.5 px-1 leading-tight !border-0">D</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">40-55</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">1.00</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">Kurang</td>
                </tr>
                <tr>
                    <td class="font-medium py-0.5 px-1 leading-tight !border-0">E</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">0-39</td>
                    <td class="py-0.5 px-1 leading-tight !border-0">0.00</td>
                    <td class="py-0.5 px-1 leading-tight !border-0 whitespace-nowrap">Sangat Kurang
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
