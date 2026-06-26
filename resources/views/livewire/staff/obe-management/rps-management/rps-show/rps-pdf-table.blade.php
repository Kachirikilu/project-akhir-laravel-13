<div class="rps-pdf bg-white">

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
                        <img src="{{ asset('images/logo-unsri.png') }}" class="h-20 object-contain">
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
    <div class="font-bold mt-8 mb-2 ">A. IDENTITAS MATA KULIAH</div>
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
            $allDosens = $rps->dosens ?? collect();
            $ketua = $allDosens->firstWhere('pivot.is_ketua', 1);
            $instruktur = $allDosens->filter(fn($d) => (int) $d->pivot->is_ketua !== 1);
            $label = $allDosens->count() === 1 ? 'Dosen Pengampu' : 'Tim Pengajar';
        @endphp
        <tr>
            <td rowspan="2" class="font-bold bg-gray-50 text-center">
                {{ $label }}
            </td>
            <td rowspan="2" @if ($instruktur->isNotEmpty()) colspan="3" @else colspan="6" @endif>
                @if ($allDosens->count() === 1)
                    {{ $allDosens->first()->name }}<br>NIP: {{ $allDosens->first()->nip }}
                @else
                    @foreach ($allDosens as $idx => $dosen)
                        <div class="list-indent mb-1">
                            <span class="mr-[5px]">{{ $idx + 1 }}.</span>
                            {{ $dosen->name }}<br>NIP: {{ $dosen->nip }}
                        </div>
                    @endforeach
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
    <div class="font-bold mt-8 mb-2">B. PROGRAM PEMBELAJARAN</div>

    <table class="w-full text-[10px] leading-tight rps-table">
        @php
            $programData = $rps->scpmkAtr;
            $calculateRowspan = function ($data, $column) {
                $counts = [];
                $index = 0;
                while ($index < count($data)) {
                    $row = $data[$index];

                    if ($column === 'dosen_id_string') {
                        $currentValue = $row->dosen_id_string ?? '';
                    } else {
                        $val = $row->$column ?? '';
                        $currentValue =
                            is_array($val) || $val instanceof \Illuminate\Support\Collection
                                ? (string) $val->pluck('id')->sort()->implode(',')
                                : (string) $val;
                    }

                    $step = 0;
                    while ($index + $step < count($data)) {
                        $nextRow = $data[$index + $step];
                        $nextVal = $nextRow->$column ?? '';

                        $nextValue =
                            is_array($nextVal) || $nextVal instanceof \Illuminate\Support\Collection
                                ? (string) $nextVal->pluck('id')->sort()->implode(',')
                                : (string) $nextVal;

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

            $dosenList = $programData->map(fn($item) => (string) ($item->dosen_id_string ?? ''))->unique();
            $isDosenUniform = $dosenList->count() <= 1;

            $rowspanCpmk = $calculateRowspan($programData, 'kode_cpmk');
            $rowspanBobot = $calculateRowspan($programData, 'bobot');
            $rowspanDosen = $calculateRowspan($programData, 'dosen_id_string');
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
                        <td class="{{ $textStyle }}">{{ $row->kode ?? '-' }}<br>Metode: {{ $row->metode }}</td>
                        <td class="{{ $textStyle }}">{{ $row->materi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->metodologi ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->tugas ?? '-' }}</td>
                        <td class="{{ $textStyle }}">{{ $row->indikator ?? '-' }}</td>
                    @endif

                    {{-- Bobot --}}
                    @if (isset($rowspanBobot[$index]))
                        <td class="{{ $textStyle }} text-center font-bold" rowspan="{{ $rowspanBobot[$index] }}">
                            {{ $row->bobot_normalisasi ?? '-' }}%
                        </td>
                    @endif

                    {{-- KOLOM DOSEN --}}
                    @if (!$isDosenUniform && isset($rowspanDosen[$index]))
                        <td class="p-2 align-top border border-black" rowspan="{{ $rowspanDosen[$index] }}">
                            @if ($row->dosens_collection->isEmpty())
                                <span class="italic text-gray-500">Tim Pengajar</span>
                            @else
                                @foreach ($row->dosens_collection as $dosen)
                                    @php
                                        $rowDosCount = false;
                                        if ($row->dosens_collection->count() > 1) {
                                            $rowDosCount = true;
                                        }
                                    @endphp
                                    <div class="{{ $rowDosCount ? 'list-indent leading-relaxed' : '' }}">
                                        @if ($rowDosCount)
                                            <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                                        @endif
                                        {{ $dosen->name }}
                                    </div>
                                @endforeach
                            @endif
                        </td>
                    @endif

                </tr>
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

    <div class="mt-6">
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

    <div class="mt-6 max-w-xs">
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
