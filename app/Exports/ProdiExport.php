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

class ProdiExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryPr;

    protected $switchTable;

    protected $title;

    public function __construct($queryPr, $switchTable, $title)
    {
        $this->queryPr = $queryPr;
        $this->switchTable = $switchTable;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->queryPr instanceof LengthAwarePaginator) {
            return collect($this->queryPr->items());
        }

        if ($this->queryPr instanceof Collection) {
            return $this->queryPr;
        }

        return $this->queryPr->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        if ($this->switchTable == 'fakultas') {
            return [
                [
                    'ID', 'Kode FK', 'Fakultas',
                    'Nilai Capaian Fakultas', '', '',
                    'Program Studi', '',
                    'Departemen', '',
                ],
                [
                    '', '', '',
                    'Nilai', 'Index', 'Akreditas',
                    'Kode PR', 'Jumlah Program Studi',
                    'Kode DP', 'Jumlah Departemen',
                ],
            ];
        } elseif ($this->switchTable == 'departemen') {
            return [
                [
                    'ID', 'Kode DP', 'Departemen',
                    'Nilai Capaian Departemen', '', '',
                    'Program Studi', '',
                    'Fakultas', '',
                ],
                [
                    '', '', '',
                    'Nilai', 'Index', 'Akreditas',
                    'Kode PR', 'Jumlah Program Studi',
                    'Kode FK', 'Nama Fakultas',
                ],
            ];
        } else {
            return [
                [
                    'ID', 'Kode PR', 'Program Studi',
                    'Nilai Capaian Program Studi', '', '', '',
                    'Mata Kuliah & Rencana Pembelajaran Semester', '', '', '',
                    'Departemen', '',
                    'Fakultas', '',
                ],
                [
                    '', '', '',
                    'Nilai', 'Index', 'Akreditas', 'Target SKS',
                    'Jumlah MK', 'Jumlah RPS', 'Jumlah RPS Aktif', 'Jumlah RPS Draf',
                    'Kode DP', 'Nama Departemen',
                    'Kode FK', 'Nama Fakultas',
                ],
            ];
        }
    }

    public function map($pr): array
    {
        if ($this->switchTable == 'fakultas') {
            return [
                $pr->id ?? '', // A
                $pr->kode ?? '', // B
                $pr->fakultas_fk ?? '', // C
                $pr->rekap_fk ?? 0,
                $pr->index_fk ?? 0,
                $pr->akreditas_fk ?? 0,
                $pr->prodis
                    ->map(fn ($prodi) => $prodi->kode)
                    ->unique()
                    ->implode(' / '),
                $pr->prodis->count() > 0 ? $pr->prodis->count() : '0',

                $pr->departemens->pluck('kode')->unique()->implode(' / '),
                $pr->departemens->count() > 0 ? $pr->departemens->count() : '0',
            ];
        } elseif ($this->switchTable == 'departemen') {
            return [
                $pr->id ?? '', // A
                $pr->kode ?? '', // B
                $pr->departemen ?? '', // C
                $pr->rekap_dp ?? 0,
                $pr->index_dp ?? 0,
                $pr->akreditas_dp ?? 0,
                $pr->prodis
                    ->map(fn ($prodi) => $prodi->kode)
                    ->unique()
                    ->implode(' / '),
                $pr->prodis->count() > 0 ? $pr->prodis->count() : '0',

                $pr->kode_fk ?? '', // I: Kode FK
                $pr->fakultas_fk ?? '', // J: Nama Fakultas
            ];
        } else {
            return [
                $pr->id ?? '', // A
                $pr->kode ?? '', // B
                $pr->prodi ?? '', // C
                $pr->rekap_pr ?? 0,
                $pr->index_pr ?? 0,
                $pr->akreditas_pr ?? 0,
                $pr->target_sks ?? 0,
                $pr->count_mk ?? 0,
                $pr->count_rps ?? 0,
                $pr->count_rps_aktif ?? 0,
                $pr->count_rps_draf ?? 0,

                $pr->kode_dp ?? '', // G
                $pr->departemen_dp ?? '', // H
                $pr->kode_fk ?? '', // I
                $pr->fakultas_fk ?? '', // J
            ];
        }
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

        $verticalMerges = ['A', 'B', 'C'];
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        if ($this->switchTable == '' || $this->switchTable == 'prodi') {
            $sheet->mergeCells('D4:G4');
            $sheet->mergeCells('H4:K4');
            $sheet->mergeCells('L4:M4');
            $sheet->mergeCells('N4:O4');
        } else {
            $sheet->mergeCells('D4:F4');
            $sheet->mergeCells('G4:H4');
            $sheet->mergeCells('I4:J4');
        }


        if ($this->switchTable == 'fakultas') {
            $alignmentMerges = ['A', 'B', 'D', 'E', 'F', 'H', 'J'];
        } elseif ($this->switchTable == 'departemen') {
            $alignmentMerges = ['A', 'B', 'D', 'E', 'F', 'H', 'I'];
        } else {
            $alignmentMerges = ['A', 'B', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N'];
        }

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        foreach (['D', 'E'] as $col) {
            $sheet->getStyle("{$col}6:{$col}{$highestRow}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        }

        foreach ($alignmentMerges as $c) {
            $sheet->getStyle("{$c}4:{$c}{$highestRow}")->getAlignment()->applyFromArray([
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]);
        }

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
