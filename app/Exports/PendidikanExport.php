<?php

namespace App\Exports;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendidikanExport implements FormCollection, FromQuery, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    protected $userName;

    public function __construct($query, $userName = 'User')
    {
        $this->query = $query;
        $this->userName = $userName;
    }

    public function collection()
    {
        if ($this->query instanceof LengthAwarePaginator) {
            return collect($this->query->items());
        }

        if ($this->query instanceof Collection) {
            return $this->query;
        }

        return $this->query->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'Pendidikan SMA/SMK/MAN', '', '', '', '', '', '',
            ],
            [
                'Institusi', 'Negara', 'Tahun Lulus', 'Pendidikan', 'Bidang Ilmu', 'Gelar', 'BLU',
            ],
        ];
    }

    public function map($p): array
    {
        return [
            $p->institusi ?? '',
            $p->negara ?? '',
            $p->tahun_lulus ?? '',
            $p->jenjang_pendidikan ?? '',
            $p->bidang_ilmu ?? '',
            $p->gelar ?? '',
            ($p->is_pendidikan_blu ?? false) ? 'Ya' : 'Tidak',
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

        // Merge baris atas header (A4:G4)
        $sheet->mergeCells('A4:G4');

        // Apply style ke header (baris 4 dan 5)
        $sheet->getStyle('A4:G5')->applyFromArray($styleArray);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Border untuk seluruh tabel yang ada isinya
        if ($highestRow >= 4) {
            $sheet->getStyle("A4:G$highestRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $title = 'DATA PENDIDIKAN '.strtoupper($this->userName);

                $sheet->mergeCells('A2:G2');
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
