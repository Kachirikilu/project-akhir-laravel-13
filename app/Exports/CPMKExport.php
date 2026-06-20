<?php

namespace App\Exports;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CPMKExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryCPMK;

    protected $title;

    protected $withCapaian;

    public function __construct($queryCPMK, $title, $withCapaian = false)
    {
        $this->queryCPMK = $queryCPMK;
        $this->title = $title;
        $this->withCapaian = $withCapaian;
    }

    public function collection()
    {
        if ($this->queryCPMK instanceof LengthAwarePaginator) {
            return collect($this->queryCPMK->items());
        }

        if ($this->queryCPMK instanceof Collection) {
            return $this->queryCPMK;
        }

        return $this->queryCPMK->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        if ($this->withCapaian) {
            return [
                [
                    'ID', 'Kode CPMK', 'Deskripsi CPMK', 'Bobot CPMK',
                    'Nilai Capaian', '', '',
                    'Capaian Pembelajaran Lulusan (CPL)', '',
                    'Sub Capaian Pembelajaran Semester (Sub-CPMK)', '', '', '',
                    'Referensi', '',
                ], [
                    '', '', '', '',
                    'Nilai', 'Index', 'Mutu',
                    'Kode CPL', 'Jumlah CPL',
                    'Kode Sub-CPMK', 'Jumlah Pertemuan', 'Metode', 'Bobot Sub-CPMK',
                    'Kode Referensi', 'Jumlah Referensi',
                ],
            ];
        } else {
            return [
                [
                    'ID', 'Kode CPMK', 'Deskripsi CPMK', 'Bobot CPMK',
                    'Capaian Pembelajaran Lulusan (CPL)', '',
                    'Sub Capaian Pembelajaran Semester (Sub-CPMK)', '', '', '',
                    'Referensi', '',
                ], [
                    '', '', '', '',
                    'Kode CPL', 'Jumlah CPL',
                    'Kode Sub-CPMK', 'Jumlah Pertemuan', 'Metode', 'Bobot Sub-CPMK',
                    'Kode Referensi', 'Jumlah Referensi',
                ],
            ];
        }
    }

    public function map($c): array
    {
        // Ambil CPL dari CPMK
        $cpls = collect()
            ->concat($c->cpls)
            ->unique('id');

        // Ambil Referensi dari RPS, CPMK, dan Sub-CPMK (Unique)
        $refs = collect()
            ->concat($c->refs)
            ->concat($c->scpmks->flatMap->refs)
            ->unique('id');

        $data = [
            $c->id ?? '', // 0 (A)
            $c->kode ?? '', // 1 (B)
            $c->deskripsi_cpl ?? '', // 2 (C)
            ($c->total_bobot ?? 0).'%', // 3 (D)

            $cpls->pluck('kode')->implode(' / '), // 4 (E)
            $cpls->count() > 0 ? $cpls->count() : '0',

            $c->scpmks->pluck('kode')->implode(' / '), // 6 (G)
            $c->scpmks->count() > 0 ? $c->scpmks->count() : '0',
            $c->scpmks->pluck('metode')->unique()->filter()->implode(' / '), // 8 (I)
            ($c->scpmks->sum('bobot')).'%', // 9 (J)

            $refs->pluck('kode')->implode(' / '), // 10 (K)
            $refs->count() > 0 ? $refs->count() : '0',
        ];

        if ($this->withCapaian) {
            array_splice($data, 4, 0, [
                $c->rekap_cpmk_pr ?: '0',
                $c->index_cpmk_pr ?: '0',
                $c->mutu_cpmk_pr ?? 'E',
            ]);
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '075985'],
            ],
        ];

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style untuk Header
        $sheet->getStyle("A4:{$highestColumn}5")->applyFromArray($styleArray);

        // Vertical Merges (A-E, K-M)
        $verticalMerges = ['A', 'B', 'C', 'D'];
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        // Horizontal Merges untuk Judul Grup (Row 4)
        if ($this->withCapaian) {
            foreach (['E', 'F'] as $col) {
                $sheet->getStyle("{$col}6:{$col}{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            }
            $sheet->mergeCells('E4:G4'); // Capaian
            $sheet->mergeCells('H4:I4'); // CPl
            $sheet->mergeCells('J4:M4'); // Sub-CPMK
            $sheet->mergeCells('N4:O4'); // Referensi
        } else {
            $sheet->mergeCells('E4:F4'); // CPl
            $sheet->mergeCells('G4:J4'); // Sub-CPMK
            $sheet->mergeCells('K4:L4'); // Referensi
        }

        if ($this->withCapaian) {
            $alignmentMerges = ['A', 'B', 'D', 'E', 'F', 'G', 'I', 'K', 'M', 'O'];
        } else {
            $alignmentMerges = ['A', 'B', 'D', 'F', 'H', 'J', 'L'];
        }
        foreach ($alignmentMerges as $c) {
            $sheet->getStyle($c.'4:'.$c.$highestRow)->getAlignment()->applyFromArray([
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]);
        }

        // Borders
        $sheet->getStyle("A4:$highestColumn$highestRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $title = $this->title;

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->setCellValue('A2', $title);

                $sheet->getStyle("A1:{$highestColumn}5")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle("A6:{$highestColumn}{$highestRow}")->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $sheet->getProtection()->setPassword(env('PW_EXCEL', '121104'));
                $sheet->getProtection()->setSheet(true);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(2)->setRowHeight(30);
            },
        ];
    }
}
