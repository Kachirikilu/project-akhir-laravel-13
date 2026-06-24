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

class MahasiswaExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryMahasiswa;

    protected $title;

    public function __construct($queryMahasiswa, $title)
    {
        $this->queryMahasiswa = $queryMahasiswa;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->queryMahasiswa instanceof LengthAwarePaginator) {
            return collect($this->queryMahasiswa->items());
        }

        if ($this->queryMahasiswa instanceof Collection) {
            return $this->queryMahasiswa;
        }

        return $this->queryMahasiswa->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'ID', 'Role', 'Nama', 'Email',
                'Identitas (ID)', '',
                'Nilai Capaian', '', '',
                'Status', 'Program Studi',
                'Rencana Pembelajaran Semester (RPS)', '', '',
            ],
            [
                '', '', '', '',
                'NIP', 'NIK',
                'Nilai', 'IPK', 'Mutu',
                '', '',
                'Kode RPS', 'SKS', 'Jumlah RPS',
            ],
        ];
    }

    public function map($u): array
    {
        $m = $u->mahasiswa;

        $rps = $m->nilai_mahasiswas()
            ->with('rps_rel')
            ->get()
            ->pluck('rps_rel')
            ->filter()
            ->unique('id')
            ->values();

        return [
            $u->id ?? '', // A
            $u->role ?? '', // B
            $u->name ?? '', // C
            $u->email ?? '', // D

            // Identitas (ID)
            $m->nim ?? '', // E
            $u->nik ?? '', // H

            $m->rekap_mhs ?: '0',
            $m->ipk_mhs ?: '0',
            $m->mutu_mhs ?? 'E',

            $u->status ?? '', // I
            $u->prodi ?? '', // J

            $rps->pluck('kode')->implode(' / '), // K
            $m->total_sks, // L
            $m->count_rps > 0 ? $m->count_rps : '0', // M
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['E', 'F', 'G', 'H'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
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

        // Style untuk Header (2 baris: 4 & 5)
        $sheet->getStyle("A4:{$highestColumn}5")->applyFromArray($styleArray);

        // Merge cells untuk header yang tidak punya sub-kolom
        $verticalMerges = ['A', 'B', 'C', 'D', 'J', 'K'];
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        foreach (['G', 'H'] as $col) {
            $sheet->getStyle("{$col}6:{$col}{$highestRow}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        }

        // Merge untuk Header Utama
        $sheet->mergeCells('E4:F4'); // Identitas (ID)
        $sheet->mergeCells('G4:I4'); // Capaian
        $sheet->mergeCells('L4:N4'); // Rencana Pembelajaran Semester

        // Alignment untuk seluruh kolom ID, Role, Status, Prodi (Center)
        $alignmentMerges = ['A', 'B', 'E', 'F', 'G', 'H', 'I', 'J', 'M', 'N'];
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
