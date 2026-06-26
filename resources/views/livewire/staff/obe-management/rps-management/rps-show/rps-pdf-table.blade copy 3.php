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

    @php
        $data = $detailRPSData ?? [];
    @endphp
    {{-- ================= HEADER ================= --}}
    <table class="w-full rps-table">
        <tr>
            <td class="text-xs sm:text-sm w-[12%] text-center">
                <div class="flex items-center justify-center">
                    @if ($logoBase64 ?? null)
                        <img src="{{ $logoBase64 }}" class="h-20 object-contain">
                    @else
                        <img src="{{ asset('images/logo-unsri.png') }}" class="h-20 object-contain">
                    @endif
                </div>
            </td>
            <td class="text-xs sm:text-sm w-[76%] !border-r-0 text-center font-bold text-lg leading-tight uppercase">
                <div>
                    <div>{{ strtoupper(env('UNIVERSITAS')) }}</div>
                    <div>Fakultas {{ $data['fakultas'] ?? '' }}</div>
                    <div>Departemen {{ $data['departemen'] ?? '' }}</div>
                    <div>Program Studi {{ $data['prodi'] ?? '' }}</div>
                </div>
            </td>
            <td class="text-xs sm:text-sm w-[12%] !border-l-0">
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
            <td class="text-xs sm:text-sm w-1/12">Kuliah</td>
            <td class="text-xs sm:text-sm w-1/12">Praktikum</td>
        </tr>
        <tr class="text-center">
            <td>{{ $data['nama_mk'] ?? '' }}</td>
            <td>{{ $data['kode_mk'] ?? '' }}</td>
            <td>{{ $data['bahan_kajian'] ?? '' }}</td>
            <td>{{ $data['sks'] ?? '-' }}</td>
            <td>{{ $data['sks_pr'] ?? '-' }}</td>
            <td>{{ $data['semester'] ?? '' }}</td>
            <td>{{ $data['revisi'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="text-xs sm:text-sm font-bold bg-gray-50 text-center">Deskripsi Mata Kuliah</td>
            <td colspan="6" class="text-justify leading-relaxed">
                {{ $data['deskripsi'] ?? '' }}
            </td>
        </tr>
        @php
            $hasCpl = collect(explode("\n", $data['cpl'] ?? ''))
                ->filter(fn($line) => trim($line))
                ->isNotEmpty();
        @endphp
        <tr>
            <td rowspan="{{ $hasCpl ? 2 : 1 }}" class="font-bold bg-gray-50 text-center">Capaian Pembelajaran Mata
                Kuliah
                (CPMK)</td>
            @if ($hasCpl)
                <td colspan="6">
                    @foreach (explode("\n", $data['cpl'] ?? '') as $line)
                        @if (trim($line))
                            <div class="list-indent text-justify leading-relaxed mb-1">
                                <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                                {{ trim($line) }}
                            </div>
                        @endif
                    @endforeach
                </td>
            @else
                <td colspan="6">
                    @foreach (explode("\n", $data['cpmk'] ?? '') as $line)
                        @if (trim($line))
                            <div class="list-indent text-justify leading-relaxed mb-1">
                                <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                                {{ trim($line) }}
                            </div>
                        @endif
                    @endforeach
                </td>
            @endif
        </tr>
        @if ($hasCpl)
            <tr>
                <td colspan="6">
                    @foreach (explode("\n", $data['cpmk'] ?? '') as $line)
                        @if (trim($line))
                            <div class="list-indent text-justify leading-relaxed mb-1">
                                <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                                {{ trim($line) }}
                            </div>
                        @endif
                    @endforeach
                </td>
            </tr>
        @endif
        <tr>
            <td rowspan="2" class="font-bold bg-gray-50 text-center"">
                {{ $data['tim_pengajar_label'] ?? 'Tim Pengajar' }}</td>
            <td rowspan="2" @if (!empty($data['instruktur'])) colspan="3" @else colspan="6" @endif>
                @if (str_contains($data['tim_pengajar'] ?? '', "\n"))
                    @foreach (explode("\n", $data['tim_pengajar']) as $idx => $line)
                        @if (trim($line))
                            <div class="list-indent mb-1">
                                <span class="mr-[5px]">{{ $idx + 1 }}.</span>
                                {!! $line !!}
                            </div>
                        @endif
                    @endforeach
                @else
                    {!! $data['tim_pengajar'] ?? '' !!}
                @endif
            </td>
            @if (!empty($data['instruktur']))
                <td class="text-xs sm:text-sm font-bold bg-gray-50 text-center">Ketua Pengajar</td>
                <td colspan="2">
                    <div class="font-bold">{{ $data['ketua_tim_pengajar'] ?? '-' }}</div>
                </td>
            @endif
        </tr>
        <tr>
            @if (!empty($data['instruktur']))
                <td class="text-xs sm:text-sm font-bold bg-gray-50 text-center">Instruktur</td>
                <td colspan="3">
                    @if (str_contains($data['instruktur'] ?? '', "\n"))
                        @foreach (explode("\n", $data['instruktur']) as $idx => $line)
                            @if (trim($line))
                                <div class="list-indent">
                                    <span class="mr-[5px]">{{ $idx + 1 }}.</span>
                                    {!! $line !!}
                                </div>
                            @endif
                        @endforeach
                    @else
                        {{ $data['instruktur'] ?? '-' }}
                    @endif
                </td>
            @endif
        </tr>
        <tr>
            <td class="text-xs sm:text-sm font-bold bg-gray-50 text-center align-middle">Otoritas</td>

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

                    // Ambil nilai dan pastikan tipenya string untuk perbandingan yang stabil
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

                        // Jika nilai berbeda, hentikan perhitungan untuk kelompok ini
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

            $allRpsDosens = $rps->dosens; 

    foreach ($programData as $row) {
        $rowDosens = collect($row->dosens ?? []);

        if ($rowDosens->isNotEmpty()) {
            // Cek apakah Wildan (ID: 1) ada di list dosen RPS
            $wildan = $allRpsDosens->firstWhere('id', 1);
            
            // Tambahkan Wildan ke $rowDosens jika Wildan ada di master dan belum ada di row
            if ($wildan && !$rowDosens->contains('id', 1)) {
                $rowDosens->push($wildan);
            }
            
            $row->dosens_collection = $rowDosens->unique('id');
        } else {
            // Jika array kosong dari database, biarkan tetap koleksi kosong
            $row->dosens_collection = collect(); 
        }

        $row->dosen_id_string = $row->dosens_collection->pluck('id')->sort()->implode(',');
    }



            $dosenList = $programData->map(fn($item) => (string) ($item->dosen_id_string ?? ''))->unique();
            $isDosenUniform = $dosenList->count() <= 1;

            $rowspanCpmk = $calculateRowspan($programData, 'kode_cpmk');
            $rowspanBobot = $calculateRowspan($programData, 'bobot');
            $rowspanDosen = $calculateRowspan($programData, 'dosen_id_string');



        @endphp



        <thead class="bg-gray-50 font-bold text-center">
            <tr>
                <th class="p-1">CPMK</th>
                <th class="p-1 w-[15%]">Kompetensi Mingguan (Sub-CPMK)</th>
                <th class="p-1 w-[15%]">Materi Pembelajaran</th>
                <th class="p-1">Metodologi & Alokasi Waktu</th>
                <th class="p-1 w-[15%]">Asesmen</th>
                <th class="p-1">Indikator</th>
                <th class="p-1">Bobot</th>
                @if (!$isDosenUniform)
                    <th class="p-1">Dosen</th>
                @endif
            </tr>
        </thead>
        <tbody class="border-t border-black">
            @foreach ($programData as $index => $row)
                @php
                    $isExam = in_array(strtoupper($row->metode ?? ''), ['UTS', 'UAS']);
                @endphp
                <tr class="{{ $isExam ? 'bg-gray-50 font-semibold italic' : '' }}">
                    {{-- CPMK --}}
                    @if (isset($rowspanCpmk[$index]))
                        <td class="p-2 border border-black text-center align-top" rowspan="{{ $rowspanCpmk[$index] }}">
                            {{ $row->kode_cpmk }}
                        </td>
                    @endif

                    {{-- Kolom Materi/Sub-CPMK --}}
                    @if ($isExam)
                        <td class="p-2 border border-black text-center font-bold" colspan="6">
                            {{ $row->deskripsi }}
                        </td>
                    @else
                        <td class="p-2 border border-black">{{ $row->kode ?? '-' }}</td>
                        <td class="p-2 border border-black">{{ $row->materi ?? '-' }}</td>
                        <td class="p-2 border border-black">{{ $row->metodologi ?? '-' }}</td>
                        <td class="p-2 border border-black">{{ $row->tugas ?? '-' }}</td>
                        <td class="p-2 border border-black">{{ $row->indikator ?? '-' }}</td>
                    @endif

                    {{-- Bobot --}}
                    @if (!$isExam && isset($rowspanBobot[$index]))
                        <td class="p-2 border border-black text-center font-bold"
                            rowspan="{{ $rowspanBobot[$index] }}">
                            {{ $row->bobot ?? '-' }}%
                        </td>
                    @endif


                    {{-- KOLOM DOSEN --}}
                    {{-- @if (!$isDosenUniform && isset($rowspanDosen[$index]))
                        <td class="p-2 align-top border border-black" rowspan="{{ $rowspanDosen[$index] }}">
                            @if ($row->dosens_collection->isEmpty())
                                <span class="italic text-gray-500">Tim Pengajar</span>
                            @else
                                @foreach ($row->dosens_collection as $dosen)
                                    {{ $dosen->name }}<br>
                                @endforeach
                            @endif
                        </td>
                    @endif --}}

{{ $row->dosens }}

@if (!$isDosenUniform && isset($rowspanDosen[$index]))
    <td class="p-2 align-top border border-black" rowspan="{{ $rowspanDosen[$index] }}">
        {{-- Jika collection sama dengan jumlah dosen RPS, anggap Tim Pengajar --}}
        @if ($row->dosen_id_string === $allRpsDosens->pluck('id')->sort()->implode(','))
            <span class="italic text-gray-500">Tim Pengajar</span>
        @else
            @foreach ($row->dosens_collection as $dosen)
                {{ $dosen->name }}<br>
            @endforeach
        @endif
    </td>
@endif



                </tr>
            @endforeach
            <tr>
                <td colspan="8" class="font-bold p-2">
                    Beban Belajar: {{ $rps->sks ?? '0' }} SKS
                </td>
            </tr>
        </tbody>
    </table>

    <br><br><br><br>




















    <table class="w-full text-[10px] leading-tight rps-table">
        @php
            $programData = collect($data['program_pembelajaran'] ?? []);

            $calculateRowspan = function ($data, $column) {
                $counts = [];
                $index = 0;
                while ($index < count($data)) {
                    $currentValue = $data[$index][$column] ?? '';
                    $step = 0;
                    while ($index + $step < count($data) && ($data[$index + $step][$column] ?? '') === $currentValue) {
                        $step++;
                    }
                    $counts[$index] = $step;
                    $index += $step;
                }
                return $counts;
            };

            $rowspanCpmk = $calculateRowspan($programData, 'cpmk');
            // $rowspanMetode = $calculateRowspan($programData, 'metode');
            // $rowspanIndikator = $calculateRowspan($programData, 'indikator');
            $rowspanBobot = $calculateRowspan($programData, 'bobot');
            $rowspanDosen = $calculateRowspan($programData, 'dosen');
            $isDosenUniform = $programData->pluck('dosen')->unique()->count() === 1;
        @endphp
        <thead class="bg-gray-50 font-bold text-center">
            <tr>
                <th class="p-1">CPMK</th>
                <th class="p-1 w-[15%]">Kompetensi Mingguan (Sub-CPMK)</th>
                <th class="p-1 w-[15%]">Materi Pembelajaran</th>
                <th class="p-1">Metodologi Pembelajaran dan Alokasi Waktunya</th>
                {{-- <th class="p-1">Metode</th> --}}
                <th class="p-1 w-[15%]">Deskripsi Tugas atau Asesmen beserta Alokasi Waktunya</th>
                <th class="p-1">Indikator</th>
                <th class="p-1">Bobot</th>
                @if (!$isDosenUniform)
                    <th class="p-1">Dosen</th>
                @endif
            </tr>
        </thead>
        <tbody class="border-t border-black">
            @foreach ($programData as $index => $row)
                @php
                    $isPlaceholder = $row['is_placeholder'] ?? false;
                    $isExam =
                        strtoupper(trim($row['metode'] ?? '')) === 'UTS' ||
                        strtoupper(trim($row['metode'] ?? '')) === 'UAS';

                    // Check if this is UTS/UAS with empty columns
                    $isEmptyExam =
                        $isExam &&
                        empty(trim($row['cpmk'] ?? '')) &&
                        empty(trim($row['materi'] ?? '')) &&
                        empty(trim($row['metodologi'] ?? '')) &&
                        empty(trim($row['tugas'] ?? '')) &&
                        empty(trim($row['indikator'] ?? ''));
                @endphp

                <tr class="{{ $isPlaceholder || $isExam ? 'bg-gray-50 font-semibold italic' : '' }}">

                    {{-- KOLOM CPMK --}}
                    @if (!$isEmptyExam && isset($rowspanCpmk[$index]))
                        <td class="text-xs sm:text-sm p-2 border border-black text-center font-bold align-top"
                            rowspan="{{ $rowspanCpmk[$index] }}">
                            {{ $row['cpmk'] ?? '-' }}
                        </td>
                    @endif

                    {{-- Kolom yang TIDAK digabung (Sub CPMK & Materi) --}}
                    @if ($isEmptyExam)
                        {{-- UTS/UAS dengan kolom kosong: gunakan colspan 6 --}}
                        <td class="text-xs sm:text-sm p-2 border border-black text-center text-center font-bold"
                            colspan="6">
                            @if ($row['metode'] == 'UTS')
                                Ujian Tengah Semester
                            @elseif ($row['metode'] == 'UAS')
                                Ujian Akhir Semester
                            @endif
                        </td>
                    @else
                        {{-- Normal row --}}
                        @if ($row['metode'] == 'UTS')
                            <td class="text-xs sm:text-sm p-2 border border-black text-left">Ujian Tengah Semester</td>
                        @elseif ($row['metode'] == 'UAS')
                            <td class="text-xs sm:text-sm p-2 border border-black text-left">Ujian Akhir Semester</td>
                        @else
                            <td class="text-xs sm:text-sm p-2 border border-black text-left">
                                {{ $row['sub_cpmk'] ?? '-' }}</td>
                        @endif
                        <td class="text-xs sm:text-sm p-2 border border-black text-left">{{ $row['materi'] ?? '-' }}
                        </td>
                        <td class="text-xs sm:text-sm p-2 border border-black text-left">
                            {{ $row['metodologi'] ?? '-' }}</td>
                        <td class="text-xs sm:text-sm p-2 border border-black text-left">{{ $row['tugas'] ?? '-' }}
                        </td>
                        <td class="text-xs sm:text-sm p-2 border border-black text-left">
                            {{ $row['indikator'] ?? '-' }}
                        </td>
                    @endif

                    {{-- KOLOM BOBOT --}}
                    @if (isset($rowspanBobot[$index]))
                        <td class="text-xs sm:text-sm p-2 border border-black text-center font-bold"
                            rowspan="{{ $rowspanBobot[$index] }}">
                            {{ $row['bobot'] ?? '-' }}
                        </td>
                    @endif

                    {{-- KOLOM DOSEN --}}
                    @if (!$isDosenUniform)
                        @if (isset($rowspanDosen[$index]))
                            <td class="text-xs sm:text-sm p-2 align-top border border-black"
                                rowspan="{{ $rowspanDosen[$index] }}">
                                @if (str_contains($row['dosen'] ?? '', "\n"))
                                    @foreach (explode("\n", $row['dosen'] ?? '') as $line)
                                        @if (trim($line))
                                            <div class="list-indent leading-relaxed mb-1">
                                                <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                                                {{ trim($line) }}
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    {{ $row['dosen'] ?? 'Tim Pengajar' }}
                                @endif
                            </td>
                        @endif
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="8" class="font-bold p-2">
                    Beban Belajar Mahasiswa Selama Satu Semester: <span class="pl-1">{{ $data['total_sks'] ?? '0' }}
                        SKS</span>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="mt-8">
        <h3 class="font-bold p-1 text-[10px]">Referensi</h3>
        <div class="px-1">
            <ul class="list-none">
                @foreach (explode("\n", $data['referensi'] ?? '') as $ref)
                    @if (trim($ref))
                        <li class="list-indent text-justify text-[10px] leading-relaxed mb-1">
                            <span class="font-bold mr-[5px]">{{ $loop->iteration }}.</span> {{ trim($ref) }}
                        </li>
                    @endif
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
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">A</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">86-100</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">4.00</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0 whitespace-nowrap">Sangat Baik
                    </td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">A-</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">80-85</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">3.70</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0 whitespace-nowrap">Sangat Baik
                    </td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm font-medium py-0.5 px-1 leading-tight !border-0">B+</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">75-79</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">3.30</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">Baik</td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm font-medium py-0.5 px-1 leading-tight !border-0">B</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">70-74</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">3.00</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">Baik</td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm font-medium py-0.5 px-1 leading-tight !border-0">B-</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">65-69</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">2.70</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">Baik</td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm font-medium py-0.5 px-1 leading-tight !border-0">C+</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">60-64</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">2.30</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">Cukup</td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm font-medium py-0.5 px-1 leading-tight !border-0">C</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">56-59</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">2.00</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">Cukup</td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm font-medium py-0.5 px-1 leading-tight !border-0">D</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">40-55</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">1.00</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">Kurang</td>
                </tr>
                <tr>
                    <td class="text-xs sm:text-sm font-medium py-0.5 px-1 leading-tight !border-0">E</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">0-39</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0">0.00</td>
                    <td class="text-xs sm:text-sm py-0.5 px-1 leading-tight !border-0 whitespace-nowrap">Sangat Kurang
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
