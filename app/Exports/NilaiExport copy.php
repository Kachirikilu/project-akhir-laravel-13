<?php

namespace App\Exports;

use App\Models\Akademik\SubCPMK;
use App\Models\Kelas\KelasJadwal;
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

        // ==========================
        // HEADER 3 LAPIS ASLI
        // ==========================
        $header1 = [
            'Kode RPS',
            'Nama Mata Kuliah',
            'Kode Kelas',
            'NIM',
            'Nama Mahasiswa',
            'Angkatan',
        ];

        $header2 = ['', '', '', '', '', ''];
        $header3 = ['', '', '', '', '', ''];

        $totalBobotAsli = 0;
        foreach ($this->sesis as $sesi) {
            $scpmk = $sesi->scpmk_atr;
            $totalBobotAsli += $scpmk->bobot ?? 0;
        }

        foreach ($this->sesis as $sesi) {
            $scpmk = $sesi->scpmk_atr;

            // if ($scpmk instanceof SubCPMK) {
            //     $cpmkKode = $scpmk->cpmks
            //         ?->first()
            //         ?->kode ?? null;
            // }

            $header1[] = 'CPMK '.$sesi->kode_cpmk;
            $header2[] = ($scpmk->kode ?? $scpmk->kode_scpmk).' (P-'.$sesi->pertemuan_ke.')';

            $bobotAsli = $scpmk->bobot ?? 0;
            $bobotNormalisasi = $totalBobotAsli > 0 ? ($bobotAsli / $totalBobotAsli) : 0;

            $header3[] = $bobotNormalisasi;
        }

        $header1[] = 'Nilai Angka';
        $header1[] = 'Nilai Index';
        $header1[] = 'Nilai Mutu';
        $header2[] = '';
        $header2[] = '';
        $header2[] = '';
        $header3[] = '';
        $header3[] = '';
        $header3[] = '';

        $rows[] = $header1;
        $rows[] = $header2;
        $rows[] = $header3;

        // ==========================
        // DATA MAHASISWA
        // ==========================
        $mahasiswas = $this->jadwal
            ->mahasiswas()
            ->with([
                'nilai_mahasiswas' => function ($q) {
                    $q->where('kj_id', $this->jadwalId)
                    ->where('ganjil_genap', $this->jadwal->ganjil_genap)
                    ->where('akademik', $this->jadwal->akademik);
                },
            ])
            ->distinct()
            ->get();

        $startRow = 4;
        foreach ($mahasiswas as $index => $mhs) {
            $currentRow = $startRow + $index;

            $nilai_mahasiswas =
                $mhs->nilai_mahasiswas->first();

            $nilaiArray =
                $nilai_mahasiswas?->nilai_array
                ?? [];

            $row = [
                $this->jadwal->kode_rps,
                $this->jadwal->mk,
                $this->jadwal->kode,
                $mhs->nim,
                $mhs->user?->name,
                $mhs->user?->mahasiswa->angkatan,
            ];

            foreach ($this->sesis as $index => $sesi) {

                $nilai =
                    $nilaiArray[$index]
                    ?? '';

                $row[] = $nilai;
            }

            $startSesiCol = Coordinate::stringFromColumnIndex(7);
            $endSesiCol = Coordinate::stringFromColumnIndex(6 + count($this->sesis));

            $nilaiAngkaCoordinate = Coordinate::stringFromColumnIndex(7 + count($this->sesis)).$currentRow;
            $row[] = "=SUMPRODUCT({$startSesiCol}{$currentRow}:{$endSesiCol}{$currentRow}, {$startSesiCol}3:{$endSesiCol}3)";
            $row[] = "=IF({$nilaiAngkaCoordinate}>=86,4.00,IF({$nilaiAngkaCoordinate}>=80,3.70,IF({$nilaiAngkaCoordinate}>=75,3.30,IF({$nilaiAngkaCoordinate}>=70,3.00,IF({$nilaiAngkaCoordinate}>=65,2.70,IF({$nilaiAngkaCoordinate}>=60,2.30,IF({$nilaiAngkaCoordinate}>=56,2.00,IF({$nilaiAngkaCoordinate}>=40,1.00,0.00))))))))";
            $row[] = "=IF({$nilaiAngkaCoordinate}>=86,\"A\",IF({$nilaiAngkaCoordinate}>=80,\"A-\",IF({$nilaiAngkaCoordinate}>=75,\"B+\",IF({$nilaiAngkaCoordinate}>=70,\"B\",IF({$nilaiAngkaCoordinate}>=65,\"B-\",IF({$nilaiAngkaCoordinate}>=60,\"C+\",IF({$nilaiAngkaCoordinate}>=56,\"C\",IF({$nilaiAngkaCoordinate}>=40,\"D\",\"E\"))))))))";
            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $totalSesis = count($this->sesis);

        // ==========================
        // KONFIGURASI ROW
        // ==========================
        $headerTop = 1;
        $headerMid = 2;
        $headerBottom = 3;
        $dataStart = 4;

        // ==========================
        // PROTECTION
        // ==========================
        $sheet->getProtection()->setPassword(env('PW_EXCEL') ?? 'Wildan121104');
        $sheet->getProtection()->setSheet(true);

        // ==========================
        // HEADER STYLE
        // ==========================
        $sheet->getStyle(
            "A{$headerTop}:{$highestColumn}{$headerBottom}"
        )->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '075985',
                ],
            ],
        ]);

        // ==========================
        // ANGKATAN RATA KIRI
        // ==========================
        $angkatanCol = Coordinate::stringFromColumnIndex(6);

        $sheet->getStyle(
            "{$angkatanCol}{$dataStart}:{$angkatanCol}{$highestRow}"
        )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_LEFT
            );

        // Tinggi header
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(26);
        $sheet->getRowDimension(3)->setRowHeight(24);

        // ==========================
        // FORMAT BOBOT (%)
        // ==========================
        $startSesiCol = Coordinate::stringFromColumnIndex(7);
        $endSesiCol = Coordinate::stringFromColumnIndex(
            6 + $totalSesis
        );

        $sheet->getStyle("{$startSesiCol}3:{$endSesiCol}3")->getNumberFormat()->setFormatCode('0.00%');

        // ==========================
        // NILAI AKHIR CENTER
        // ==========================
        $startFinalCol = Coordinate::stringFromColumnIndex(
            7 + $totalSesis
        );

        $sheet->getStyle(
            "{$startFinalCol}{$dataStart}:{$highestColumn}{$highestRow}"
        )->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ==========================================
        // PERUBAHAN UTAMA: FORMAT 2 DIGIT BELAKANG KOMA (.00)
        // ==========================================
        $nilaiAngkaColLetter = Coordinate::stringFromColumnIndex(7 + $totalSesis);
        $nilaiIndexColLetter = Coordinate::stringFromColumnIndex(8 + $totalSesis);

        // Terapkan mask format '0.00' ke seluruh baris data Nilai Angka & Nilai Index
        $sheet->getStyle("{$nilaiAngkaColLetter}{$dataStart}:{$nilaiAngkaColLetter}{$highestRow}")->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle("{$nilaiIndexColLetter}{$dataStart}:{$nilaiIndexColLetter}{$highestRow}")->getNumberFormat()->setFormatCode('0.00');

        // ==========================
        // INPUT NILAI EDITABLE
        // ==========================
        if ($totalSesis > 0) {
            $editableRange = "{$startSesiCol}{$dataStart}:{$endSesiCol}{$highestRow}";
            $sheet->getStyle($editableRange)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            // background editable
            $sheet->getStyle($editableRange)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('F8FAFC');
        }

        // ==========================
        // BORDER
        // ==========================
        $sheet->getStyle(
            "A1:{$highestColumn}{$highestRow}"
        )
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (
                AfterSheet $event
            ) {

                $sheet = $event->sheet->getDelegate();
                $totalSesis = count($this->sesis);

                // HEADER FIX
                $headerTop = 1;
                $headerBottom = 3;

                // ==========================
                // ROWSPAN IDENTITAS
                // ==========================
                $columnsToRowspan = [1, 2, 3, 4, 5, 6,
                    7 + $totalSesis,
                    8 + $totalSesis,
                    9 + $totalSesis,
                ];

                foreach ($columnsToRowspan as $colIndex) {
                    $letter = Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->mergeCells("{$letter}{$headerTop}:{$letter}{$headerBottom}");
                }

                // ==========================
                // MERGE CPMK
                // ==========================
                $startCol = 7;
                $endCol = 6 + $totalSesis;

                if ($totalSesis > 0) {
                    $currentLeft = $startCol;

                    $currentValue = $sheet->getCell(Coordinate::stringFromColumnIndex($startCol).'1')->getValue();

                    for ($c = $startCol + 1; $c <= $endCol; $c++) {
                        $checkValue =
                            $sheet->getCell(
                                Coordinate::stringFromColumnIndex(
                                    $c
                                ).'1'
                            )->getValue();

                        if ($checkValue !== $currentValue) {
                            if ($c - 1 > $currentLeft) {
                                $left = Coordinate::stringFromColumnIndex($currentLeft);
                                $right = Coordinate::stringFromColumnIndex($c - 1);
                                $sheet->mergeCells("{$left}1:{$right}1");
                            }
                            $currentLeft = $c;
                            $currentValue = $checkValue;
                        }
                    }

                    // merge terakhir
                    if ($endCol > $currentLeft) {
                        $left = Coordinate::stringFromColumnIndex($currentLeft);
                        $right = Coordinate::stringFromColumnIndex($endCol);
                        $sheet->mergeCells("{$left}1:{$right}1");
                    }
                }
            },
        ];
    }
}
