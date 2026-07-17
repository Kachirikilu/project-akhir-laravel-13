<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
// 🌟 1. WAJIB IMPORT CONCERN WITHTITLE DI SINI:
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// 🌟 2. TAMBAHKAN WIthTitle PADA LIST IMPLEMENTS CLASS
class NilaiExport implements FromArray, ShouldAutoSize, WithEvents, WithStyles, WithTitle
{
    protected $jadwalId;

    protected $jadwal;

    protected $sesis;

    protected $sheetName;

    public function __construct($jadwal, $sheetName = null)
    {
        $this->jadwalId = $jadwal->id;

        $this->jadwal = $jadwal;

        $this->sheetName = $sheetName ?? $jadwal->kode;

        $this->sesis = $jadwal->sesis->sortBy('pertemuan_ke')->values();
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function array(): array
    {
        $rows = [];
        $cpmkGroups = collect($this->sesis)->groupBy('kode_cpmk');
        $totalSubCpmk = count($this->sesis);
        $totalGroups = $cpmkGroups->count();

        // ==========================================
        // 1. HEADER (Disusun Ulang agar tidak duplikat)
        // ==========================================
        $header1 = ['NIM', 'Kode RPS', 'Nama MK', 'Kode Kelas', 'Nama Mahasiswa', 'Angkatan'];
        $header2 = ['', '', '', '', '', ''];
        $header3 = ['', '', '', '', '', ''];

        // Menambahkan kolom Sub-CPMK (Sesi)
        foreach ($this->sesis as $sesi) {
            $scpmk = $sesi->scpmk_atr;
            $header1[] = 'CPMK '.$sesi->kode_cpmk;
            $header2[] = ($scpmk->kode ?? $scpmk->kode_scpmk).' (P-'.$sesi->pertemuan_ke.')';
            $header3[] = $sesi->bobot_normalisasi / 100;
        }

        foreach ($cpmkGroups as $kodeCpmk => $sesiGroup) {
            $pertemuans = $sesiGroup->pluck('pertemuan_ke')->sort();
            $minP = $pertemuans->first();
            $maxP = $pertemuans->last();
            $rangePertemuan = ($minP == $maxP) ? "P-{$minP}" : "P{$minP}-P{$maxP}";

            $totalBobot = $sesiGroup->sum('bobot_normalisasi');

            $header1[] = 'CPMK '.$kodeCpmk;
            $header2[] = $rangePertemuan;
            $header3[] = $totalBobot / 100;
        }

        // Menambahkan Nilai Akhir
        $header1 = array_merge($header1, ['Nilai Angka', 'Nilai Index', 'Nilai Mutu']);
        $header2 = array_merge($header2, ['', '', '']);
        $header3 = array_merge($header3, ['', '', '']);

        $rows[] = $header1;
        $rows[] = $header2;
        $rows[] = $header3;

        // ==========================
        // 2. DATA MAHASISWA
        // ==========================
        $mahasiswas = $this->jadwal->mahasiswas()
            ->with(['nilai_mahasiswas' => function ($q) {
                $q->where('kj_id', $this->jadwalId)
                    ->where('ganjil_genap', $this->jadwal->ganjil_genap)
                    ->where('akademik', $this->jadwal->akademik);
            }])
            ->distinct()->get();

        $startRow = 4;
        $totalSubCpmk = count($this->sesis);

        foreach ($mahasiswas as $index => $mhs) {
            $currentRow = $startRow + $index;
            $nilaiArray = $mhs->nilai_mahasiswas->first()?->nilai_array ?? [];

            $row = [
                $mhs->nim,
                $this->jadwal->kode_rps,
                $this->jadwal->mk,
                $this->jadwal->kode,
                $mhs->user?->name,
                $mhs->user?->mahasiswa->angkatan,
            ];

            // Rumus CPMK (Rekap per grup, dibagi SUM bobot agar skala tetap 100)
            $currentSesiCol = 7;
            foreach ($this->sesis as $i => $sesi) {
                $row[] = $nilaiArray[$i] ?? '';
            }
            foreach ($cpmkGroups as $kodeCpmk => $sesiGroup) {
                $count = count($sesiGroup);
                $startColLetter = Coordinate::stringFromColumnIndex($currentSesiCol);
                $endColLetter = Coordinate::stringFromColumnIndex($currentSesiCol + $count - 1);

                $startRange = "{$startColLetter}{$currentRow}";
                $endRange = "{$endColLetter}{$currentRow}";

                // Rumus menggunakan pembagi SUM bobot (baris ke-3)
                $row[] = "=SUMPRODUCT({$startRange}:{$endRange}, {$startColLetter}3:{$endColLetter}3) / SUM({$startColLetter}3:{$endColLetter}3)";

                $currentSesiCol += $count;
            }

            $subCpmkEndColLetter = Coordinate::stringFromColumnIndex(6 + $totalSubCpmk);
            $nilaiAngkaColLetter = Coordinate::stringFromColumnIndex(6 + $totalSubCpmk + $totalGroups + 1);

            $row[] = "=SUMPRODUCT(G{$currentRow}:{$subCpmkEndColLetter}{$currentRow}, G3:{$subCpmkEndColLetter}3) / SUM(G3:{$subCpmkEndColLetter}3)";
            $row[] = "=IF({$nilaiAngkaColLetter}{$currentRow}>=86,4.00,IF({$nilaiAngkaColLetter}{$currentRow}>=80,3.70,IF({$nilaiAngkaColLetter}{$currentRow}>=75,3.30,IF({$nilaiAngkaColLetter}{$currentRow}>=70,3.00,IF({$nilaiAngkaColLetter}{$currentRow}>=65,2.70,IF({$nilaiAngkaColLetter}{$currentRow}>=60,2.30,IF({$nilaiAngkaColLetter}{$currentRow}>=56,2.00,IF({$nilaiAngkaColLetter}{$currentRow}>=40,1.00,0.00))))))))";
            $row[] = "=IF({$nilaiAngkaColLetter}{$currentRow}>=86,\"A\",IF({$nilaiAngkaColLetter}{$currentRow}>=80,\"A-\",IF({$nilaiAngkaColLetter}{$currentRow}>=75,\"B+\",IF({$nilaiAngkaColLetter}{$currentRow}>=70,\"B\",IF({$nilaiAngkaColLetter}{$currentRow}>=65,\"B-\",IF({$nilaiAngkaColLetter}{$currentRow}>=60,\"C+\",IF({$nilaiAngkaColLetter}{$currentRow}>=56,\"C\",IF({$nilaiAngkaColLetter}{$currentRow}>=40,\"D\",\"E\"))))))))";

            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $totalSesis = count($this->sesis);
        $cpmkGroups = collect($this->sesis)->groupBy('kode_cpmk');
        $totalGroups = $cpmkGroups->count();

        // Kalkulasi index kolom
        $colSubCpmkStart = 7;
        $colSubCpmkEnd = 6 + $totalSesis;
        $colRekapStart = $colSubCpmkEnd + 1;
        $colRekapEnd = $colRekapStart + $totalGroups - 1;
        $colAkhir = $colRekapEnd + 3;

        // 1. Header Utama Style
        $sheet->getStyle("A1:{$sheet->getHighestColumn()}3")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '075985']],
        ]);

        $lastCol = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // 2. Mengatur Angkatan (Kolom F) ke Tengah
        $sheet->getStyle("F4:F{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $colIndex = Coordinate::columnIndexFromString($lastCol);

        $rangeNilai = Coordinate::stringFromColumnIndex($colIndex - 2).'4:'.
                      $lastCol.$lastRow;

        $sheet->getStyle($rangeNilai)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A4:F{$highestRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1D5DB');

        // CPMK Rekap (Abu-abu Terang)
        $sheet->getStyle(Coordinate::stringFromColumnIndex($colRekapStart).'4:'.Coordinate::stringFromColumnIndex($colRekapEnd)."{$highestRow}")
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1D5DB');

        // Nilai Akhir (Abu-abu Sedikit Gelap)
        $sheet->getStyle('G4:'.Coordinate::stringFromColumnIndex($colAkhir)."{$highestRow}")
            ->getNumberFormat()->setFormatCode('0.00');

        // 4. Border
        $sheet->getStyle("A1:{$sheet->getHighestColumn()}{$highestRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A4:A'.$sheet->getHighestRow())
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Memaksa rata kiri (Left Alignment)
                $sheet->getStyle('A4:A'.$sheet->getHighestRow())
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle("G3:{$highestColumn}3")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);

                $totalSesis = count($this->sesis);
                $cpmkGroups = collect($this->sesis)->groupBy('kode_cpmk');
                $totalGroups = $cpmkGroups->count();

                $sheet->freezePane('B4');

                $sheet->getProtection()->setPassword(env('PW_EXCEL') ?? 'Wildan121104');
                $sheet->getProtection()->setSheet(true);

                $subStart = 7;
                // $subEnd = 6 + $totalSesis + $totalGroups;
                $subEnd = 6 + $totalSesis;

                $sheet->getStyle(Coordinate::stringFromColumnIndex($subStart).'4:'.
                                 Coordinate::stringFromColumnIndex($subEnd).$sheet->getHighestRow())
                    ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                $colRekapStart = $subEnd + 1;
                $colAkhirStart = $colRekapStart + $totalGroups;

                $columnsToRowspan = [1, 2, 3, 4, 5, 6, $colAkhirStart, $colAkhirStart + 1, $colAkhirStart + 2];
                foreach ($columnsToRowspan as $col) {
                    $letter = Coordinate::stringFromColumnIndex($col);
                    $sheet->mergeCells("{$letter}1:{$letter}3");
                }

                $currentCol = 7;
                foreach ($cpmkGroups as $group) {
                    $count = count($group);
                    $start = Coordinate::stringFromColumnIndex($currentCol);
                    $end = Coordinate::stringFromColumnIndex($currentCol + $count - 1);
                    $sheet->mergeCells("{$start}1:{$end}1");
                    $currentCol += $count;
                }
            },
        ];
    }
}
