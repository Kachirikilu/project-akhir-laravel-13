<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CPMKExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryCPMK;

    protected $switchTable;

    protected $title;

    public function __construct($queryCPMK, $switchTable, $title)
    {
        $this->queryCPMK = $queryCPMK;
        $this->switchTable = $switchTable;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->queryCPMK->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'ID', 'Kode CPMK', 'Deskripsi CPMK', 'Bobot CPMK',
                'Capaian Pembelajaran Lulusan', '',
                'Sub-CPMK', '', '', '',
                'Referensi', '',
            ], [
                '', '', '', '',
                'Kode CPL', 'Jumlah CPL',
                'Kode Sub-CPMK', 'Jumlah Pertemuan', 'Metode', 'Bobot Sub-CPMK',
                'Kode Referensi', 'Jumlah Referensi',
            ],
        ];
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

        return [
            $c->id ?? '', // 0 (A)
            $c->kode ?? '', // 1 (B)
            $c->deskripsi ?? '', // 2 (C)
            ($c->total_bobot ?? 0).'%', // 3 (D)

            $cpls->pluck('kode')->implode(' / ') ?: '-', // 4 (E)
            $cpls->count(), // 5 (F)

            $c->scpmks->pluck('kode')->implode(' / ') ?: '-', // 6 (G)
            $c->scpmks->count(), // 7 (H)
            $c->scpmks->pluck('metode')->unique()->filter()->implode(' / ') ?: '-', // 8 (I)
            ($c->scpmks->sum('bobot')).'%', // 9 (J)

            $refs->pluck('kode')->implode(' / ') ?: '-', // 10 (K)
            $refs->count(), // 11 (L)
        ];
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
        $sheet->mergeCells('E4:F4'); // CPl
        $sheet->mergeCells('G4:J4'); // Sub-CPMK
        $sheet->mergeCells('K4:L4'); // Referensi

        $alignmentMerges = ['A', 'B', 'D', 'F', 'H', 'J', 'L'];
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

                $highestColumn = $sheet->getHighestColumn();

                $title = $this->title;

                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->setCellValue('A2', $title);

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
