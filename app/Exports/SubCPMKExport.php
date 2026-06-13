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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubCPMKExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $querySCPMK;

    protected $title;

    public function __construct($querySCPMK, $title)
    {
        $this->querySCPMK = $querySCPMK;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->querySCPMK instanceof LengthAwarePaginator) {
            return collect($this->querySCPMK->items());
        }

        if ($this->querySCPMK instanceof Collection) {
            return $this->querySCPMK;
        }

        return $this->querySCPMK->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'ID', 'Kode Sub-CPMK', 'Deskripsi Sub-CPMK',
                'Pembelajaran', '', '', '',
                'Tugas', '', '', '',
                'Referensi', '',
            ], [
                '', '', '',
                'Materi', 'Metodologi', 'Indikator', 'Metode',
                'Bobot Sub-CPMK', 'Deskripsi Tugas', 'Waktu Tugas', 'Waktu Mandiri',
                'Kode Referensi', 'Jumlah Referensi',
            ],
        ];
    }

    public function map($s): array
    {
        // Ambil Referensi dari RPS, CPMK, dan Sub-CPMK (Unique)
        $refs = collect()
            ->concat($s->refs)
            ->unique('id');

        return [
            $s->id ?? '', // 0 (A)
            $s->kode ?? '', // 1 (B)
            $s->deskripsi ?? '', // 2 (C)

            $s->materi ?? '', // 3 (D)
            $s->metodologi ?? '', // 4 (E)
            $s->indikator ?? '', // 5 (F)
            $s->metode ?? '', // 6 (G)

            ($s->bobot ?? 0).'%', // 7 (H)
            $s->tugas ?? '', // 8 (I)
            $s->w_tugas ?? '', // 9 (J)
            $s->w_mandiri ?? '', // 10 (K)

            $refs->pluck('kode')->implode(' / '), // 11 (L)
            $refs->count() > 0 ? $refs->count() : '0', 
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
        $verticalMerges = ['A', 'B', 'C'];
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        // Horizontal Merges untuk Judul Grup (Row 4)
        $sheet->mergeCells('D4:G4'); // Pembelajaran
        $sheet->mergeCells('H4:K4'); // Tugas
        $sheet->mergeCells('L4:M4'); // Referensi

        $alignmentMerges = ['A', 'B', 'G', 'H', 'J', 'K', 'M'];
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
