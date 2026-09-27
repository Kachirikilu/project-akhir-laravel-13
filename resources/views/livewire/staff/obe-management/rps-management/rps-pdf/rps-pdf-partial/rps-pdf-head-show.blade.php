<div class="rps-pdf {{ $isPDF ?? false ? '' : 'rps-pdf--min-width' }}">

    <style>
        @page {
            /* size: A4 landscape; */
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

        /* .nilai-table th,
        .nilai-table td {
            border: 1px solid black !important;
            padding: 4px;
        } */
        .rps-table

        /* , .nilai-table */
            {
            border-collapse: collapse;
        }

        /* .list-indent {
            padding-left: 15px;
            text-indent: -15px;
        } */

        /* ===== Generic helpers ===== */
        .w-full {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* .text-justify {
            text-align: justify;
        } */

        .font-bold {
            font-weight: bold;
        }

        /* .font-medium {
            font-weight: 500;
        } */

        /* .font-semibold {
            font-weight: 600;
        } */

        /* .italic {
            font-style: italic;
        } */

        /* .uppercase {
            text-transform: uppercase;
        }

        .leading-relaxed {
            line-height: 1.6;
        }

        .leading-tight {
            line-height: 1.2;
        } */

        .align-top {
            vertical-align: top;
        }

        /* .align-middle {
            vertical-align: middle;
        } */

        /* .float-right {
            float: right;
        } */

        /* .whitespace-nowrap {
            white-space: nowrap;
        } */

        /* .text-gray-500 {
            color: #6b7280;
        } */

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .bg-gray-200 {
            background-color: #e5e7eb;
        }

        /* .border-t-black {
            border-top: 1px solid black;
        }

        .border-r-black {
            border-right: 1px solid black;
        }

        .border-l-black {
            border-left: 1px solid black;
        } */

        /* .no-border-r {
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
            margin-bottom: 20px;
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
            margin-bottom: 10px;
        }

        /* ===== Identitas table ===== */

        .identity-table {
            width: 100%;
            font-size: 10px;
        }

        .mb-20px {
            margin-bottom: 20px;
        }

        /* .mb-40px {
            margin-bottom: 40px;
        } */

        /* .identity-header-row {
            font-weight: bold;
            text-align: center;
            background: #f9fafb;
        } */

        .col-1-6 {
            width: 16.6667%;
        }

        /* .col-2-6 {
            width: 33.3333%;
        } */
        /* .col-3-6 {
            width: 50%;
        } */
        .col-1-12 {
            width: 8.3333%;
        }

        /* .desc-label {
            text-align: center;
            font-weight: bold;
        } */

        .desc-value {
            text-align: justify;
            line-height: 1.6;
        }

        /* .cpmk-label {
            font-weight: bold;
            background: #f9fafb;
            text-align: center;
        } */

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

        .dosen-missing {
            color: #dc2626;
            font-weight: bold;
            font-style: italic;
        }

        /* .otoritas-label {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        } */

        .otoritas-cell {
            text-align: center;
        }

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
    </style>

    @php
        $prodiHead = $prodi ?? $rps->mk_rel?->prodis->first();
    @endphp

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
                <div>
                    <div>{{ strtoupper(env('UNIVERSITAS')) }}</div>
                    <div>{{ $prodiHead->dp_rel->fk_rel->fakultas_fk ?? '' }}</div>
                    <div>{{ $prodiHead->dp_rel->departemen_dp ?? '' }}</div>
                    <div>{{ $prodiHead->prodi_pr ?? '' }}</div>
                </div>
            </td>
            <td class="header-spacer-cell">
            </td>
        </tr>
        <tr>
            <td colspan="3" class="header-title-row bg-gray-200">
                RENCANA PEMBELAJARAN SEMESTER
            </td>
        </tr>
    </table>

    {{-- ================= IDENTITAS ================= --}}
    <div class="section-title">IDENTITAS MATA KULIAH</div>
    <table class="identity-table mb-20px rps-table">
        <tr class="font-bold align-center bg-gray-50 text-center">
            <td rowspan="2" colspan="2">Nama Mata Kuliah</td>
            <td rowspan="2" colspan="2">Kode Mata Kuliah</td>
            <td rowspan="2" colspan="6">Bahan Kajian</td>
            <td colspan="2">SKS</td>
        </tr>
        <tr class="font-bold align-top bg-gray-50 text-center">
            <td class="col-1-12">Kuliah</td>
            <td class="col-1-12">
                {{ ($rps->mk_rel->sks_text ?? 'Tatap Muka') === 'Tatap Muka' ? 'Praktikum' : $rps->mk_rel->sks_text }}
            </td>
        </tr>
        <tr class="text-left align-top">
            <td colspan="2">{{ $rps->mk_rel->mk ?? '' }}</td>
            <td colspan="2">{{ $rps->mk_rel->kode ?? '' }}</td>
            <td colspan="6">{{ $rps->mk_rel->bahan_kajian ?? '' }}</td>
            <td class="text-center">{{ $rps->mk_rel->sks_tm ?? '-' }}</td>
            <td class="text-center">
                {{ $rps->mk_rel->sks_pr ?? ($rps->mk_rel->sks_pl ?? ($rps->mk_rel->sks_sm ?? '-')) }}</td>
        </tr>
        <tr class="text-left align-top">
            <td colspan="2" class="font-bold">Semester</td>
            <td colspan="2">{{ $rps->mk_rel->semester ?? '' }} ({{ $rps->mk_rel->ganjil_genap ?? '-' }})</td>
            <td colspan="3" class="font-bold">Tanggal Revisi Terakhir</td>
            <td colspan="5">{{ $rps->revisi_hari ?? '' }}</td>
        </tr>
        <tr class="text-left align-top">
            <td colspan="2" class="font-bold">Deskripsi Mata Kuliah</td>
            <td colspan="10" class="desc-value">
                {{ $rps->mk_rel->deskripsi ?? '' }}
            </td>
        </tr>

        @php
            $cpls = $rps->cpmks->pluck('cpls')->flatten()->unique('id');
        @endphp

        <tr class="text-left align-top">
            <td colspan="2" class="font-bold">Capaian Pembelajaran Lulusan (CPL)</td>
            <td colspan="10">
                @foreach ($cpls as $cpl)
                    <div class="cpmk-item">
                        <span class="mr-5-bold">{{ $loop->iteration }}.</span>
                        <span class="mr-3-bold">{{ $cpl->kode }}:</span> {{ $cpl->deskripsi }}
                    </div>
                @endforeach
            </td>
        </tr>
        <tr class="text-left align-top">
            <td colspan="2" class="font-bold">Capaian Pembelajaran Mata Kuliah (CPMK)</td>
            <td colspan="10">
                @foreach ($rps->cpmks as $cpmk)
                    <div class="cpmk-item">
                        <span class="mr-5-bold">{{ $loop->iteration }}.</span>
                        <span class="mr-3-bold">
                            {{ $cpmk->kode }}
                            @if ($cpmk->cpls->isNotEmpty())
                                ({{ $cpmk->cpls->pluck('kode')->implode(', ') }})
                                :
                            @else
                                :
                            @endif
                        </span>
                        {{ $cpmk->deskripsi_cpl }}
                    </div>
                @endforeach
            </td>
        </tr>


        @php
            $tim = $tim_dosen->first();
            $allDosens = $tim ? $tim->dosens : collect();
            $ketua = $allDosens->firstWhere('pivot.is_ketua', 1);

            $label = $allDosens->count() > 1 ? 'Tim Pengajar' : 'Dosen Pengampu';

            $instruktur = $allDosens->filter(fn($d) => $d->pivot->peran === 'Instruktur');
            $asisten = $allDosens->filter(fn($d) => $d->pivot->peran === 'Asisten');

            $hasTim = $allDosens->count() > 1;
            $hasInstruktur = $instruktur->isNotEmpty();
            $hasAsisten = $asisten->isNotEmpty();

            $rowDos = 1;
            $colDos = 10;

            if ($hasInstruktur && $hasAsisten) {
                $rowDos = 3;
                $colDos = 4;
            } elseif ($hasInstruktur || $hasAsisten) {
                $rowDos = 2;
                $colDos = 4;
            } elseif ($hasTim) {
                $colDos = 4;
            }

        @endphp

        <tr>
            {{-- Label Dosen --}}
            <td colspan="2" rowspan="{{ $rowDos }}" class="font-bold text-left align-top">
                {{ $label }}
                @if (optional($tim_dosen->first())->kode)
                    ({{ $tim_dosen->first()->kode }})
                @endif
            </td>

            {{-- Daftar Dosen --}}
            <td class="align-top" rowspan="{{ $rowDos }}" colspan="{{ $colDos }}">
                @if ($allDosens->count() === 1)
                    {{ $allDosens->first()->name }}<br>NIP. {{ $allDosens->first()->nip }}
                @elseif ($allDosens->count() > 1)
                    @foreach ($allDosens as $idx => $dosen)
                        <div class="dosen-item">
                            <span class="mr-5">{{ $idx + 1 }}.</span>
                            {{ $dosen->name }}<br>NIP. {{ $dosen->nip }}
                        </div>
                    @endforeach
                @else
                    <span class="dosen-missing">Tim Dosen atau Dosen Pengampu belum Didaftarkan!</span>
                @endif
            </td>

            {{-- Hanya muncul jika ada instruktur --}}
            @if ($hasTim)
                <td colspan="1" class="font-bold text-left align-top">Ketua Tim Pengajar</td>
                <td colspan="5" class="align-top">
                    <div class="dosen-item font-bold">
                        {{ $ketua->name ?? '-' }}
                    </div>
                </td>
            @endif
        </tr>

        @if ($hasInstruktur)
            <tr>
                <td colspan="1" class="font-bold text-left align-top">Instruktur</td>
                <td colspan="5" class="align-top">
                    @foreach ($instruktur as $dosen)
                        <div class="dosen-item">
                            @if ($loop->count > 1)
                                <span class="mr-5">{{ $loop->iteration }}.</span>
                            @endif
                            {{ $dosen->name }}
                        </div>
                    @endforeach
                </td>
            </tr>
        @endif

        @if ($hasAsisten)
            <tr>
                <td colspan="1" class="font-bold text-left align-top">Asisten</td>
                <td colspan="5" class="align-top">
                    @foreach ($asisten as $dosen)
                        <div class="dosen-item">
                            @if ($loop->count > 1)
                                <span class="mr-5">{{ $loop->iteration }}.</span>
                            @endif
                            {{ $dosen->name }}
                        </div>
                    @endforeach
                </td>
            </tr>
        @endif
    </table>

    {{-- <table class="identity-table mb-20px rps-table page-break-table"> --}}
    <table class="identity-table mb-20px rps-table">
        <tr class="font-bold align-center bg-gray-50 font-left">
            <td colspan="2" class="col-1-6">Otorisasi</td>
        </tr>
        <tr>
            <td class="otoritas-cell col-1-12">
                <div class="otoritas-flex">
                    <div class="otoritas-title font-bold text-center">
                        Ketua Program Studi<br>&nbsp;
                    </div>

                    <!-- Space untuk TTD -->
                    <div class="otoritas-spacer">
                    </div>

                    @if ($prodiHead->kaprodi_rel)
                    <div class="text-center">
                        {{ $prodiHead->kaprodi_rel->name ?? '-' }}
                        <br>
                        NIP. {{ $prodiHead->kaprodi_rel->nip }}
                    </div>
                    @else
                    <div class="text-center dosen-missing">Ketua {{ $prodiHead->prodi_pr }}<br>belum Didaftarkan!</div>
                    @endif
                </div>
            </td>

            <!-- Otorisasi 2: Wakil Dekan -->
            <td class="otoritas-cell otoritas-border-left col-1-12">
                <div class="otoritas-flex">
                    <div class="otoritas-title font-bold text-center">
                        Wakil Dekan Bidang Akademik,<br>
                        Kemahasiswaan dan Penjaminan Mutu
                    </div>

                    <!-- Space untuk TTD -->
                    <div class="otoritas-spacer">
                    </div>

                    @if ($prodiHead->dp_rel->fk_rel->wadek_rel)
                    <div class="text-center">
                        {{ $prodiHead->dp_rel->fk_rel->wadek_rel->name ?? '-' }}
                        <br>
                        NIP. {{ $prodiHead->dp_rel->fk_rel->wadek_rel->nip }}
                    </div>
                    @else
                    <div class="text-center dosen-missing">Wakil Dekan {{ $prodiHead->fakultas_fk }}<br>belum Didaftarkan!</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>
