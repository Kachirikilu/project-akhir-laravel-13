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

class CPLExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryCPL;

    protected $title;

    public function __construct($queryCPL, $title)
    {
        $this->queryCPL = $queryCPL;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->queryCPL instanceof LengthAwarePaginator) {
            return collect($this->queryCPL->items());
        }

        if ($this->queryCPL instanceof Collection) {
            return $this->queryCPL;
        }

        return $this->queryCPL->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'ID', 'Kode CPL', 'Deskripsi CPL', 'Rencana Pembelajaran Semester (RPS)', '', 'Program Studi', '',
            ],
            [
                '', '', '', 'Kode RPS', 'Jumlah RPS', 'Kode PR', 'Jumlah Program Studi',
            ],
        ];
    }

    public function map($c): array
    {

$rps = $c->cpmks
    ->pluck('rps')
    ->flatten()
    ->unique('id')
    ->values();

        $prodis = collect()
            ->concat($c->prodis)
            ->unique('id');

        return [
            $c->id ?? '', // 0 (A)
            $c->kode ?? '', // 1 (B)
            $c->deskripsi ?? '', // 2 (C)
            // /
            $rps->pluck('kode')->implode(' / '),
            $rps->count() > 0 ? $rps->count() : '0',                       
            $prodis->pluck('kode')->implode(' / '),
            $prodis->count() > 0 ? $prodis->count() : '0', 
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

        $verticalMerges = ['A', 'B', 'C'];
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        $sheet->mergeCells('D4:E4'); // RPS
        $sheet->mergeCells('F4:G4'); // Prodi

        $alignmentMerges = ['A', 'B', 'E', 'G'];
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
