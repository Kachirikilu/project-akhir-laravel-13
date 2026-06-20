<?php

namespace App\Exports;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MKExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryMK;

    protected $filterMK;

    protected $selectedPrId;

    protected $selectedDpId;

    protected $selectedFkId;

    protected $title;

    public function __construct($queryMK, $filterMK, $prId, $dpId, $fkId, $title)
    {
        $this->queryMK = $queryMK;
        $this->filterMK = $filterMK;
        $this->selectedPrId = $prId;
        $this->selectedDpId = $dpId;
        $this->selectedFkId = $fkId;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->queryMK instanceof LengthAwarePaginator) {
            return collect($this->queryMK->items());
        }

        if ($this->queryMK instanceof Collection) {
            return $this->queryMK;
        }

        return $this->queryMK->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'ID', 'No', 'Kode MK', 'Mata Kuliah', 'Semester',
                'Bobot Mata Kuliah (SKS)', '', '', '', '',
                'Wajib', 'Deskripsi', 'Bahan Kajian',
            ],
            [
                '', '', '', '', '',
                'Total', 'Tatap Muka', 'Praktikum', 'Praktek Lapangan', 'Simulasi',
                '', '', '',
            ],
        ];
    }

    public function map($mk): array
    {
        return [
            $mk->id ?? '', // A
            $mk->digit_mk ?? '', // B
            $mk->kode ?? '', // C
            $mk->mk ?? '', // D
            $mk->semester ?? '', // E

            $mk->sks ?? '', // F
            $mk->sks_tm ?? '', // G
            $mk->sks_pr ?? '', // H
            $mk->sks_pl ?? '', // I
            $mk->sks_sm ?? '', // J

            $mk->wajib_text ?? '', // K
            $mk->deskripsi ?? '', // L
            $mk->bahan_kajian ?? '', // M
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['E', 'F', 'G', 'H', 'I'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
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

        // Vertikal Baris
        $verticalMerges = ['A', 'B', 'C', 'D', 'E',
            'K', 'L', 'M'];
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }
        // Vertikal Baris

        // Horizontal Kolom
        $horizontalMerges = [
            'F4:J4',
        ];
        foreach ($horizontalMerges as $range) {
            $sheet->mergeCells($range);
        }
        // Horizontal Kolom

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Format Alignment Kolom
        $alignmentMerges = ['A', 'B', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        foreach ($alignmentMerges as $c) {
            $sheet->getStyle("{$c}4:{$c}{$highestRow}")->getAlignment()->applyFromArray([
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]);
        }
        // Format Alignment Kolom

        $sheet->getStyle("A4:{$highestColumn}5")->applyFromArray($styleArray);
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
