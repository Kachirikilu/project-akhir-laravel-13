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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RPSExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryRPS;

    protected $title;

    protected $withCapaian;

    public function __construct($queryRPS, $title, $withCapaian = false)
    {
        $this->queryRPS = $queryRPS;
        $this->title = $title;
        $this->withCapaian = $withCapaian;
    }

    public function collection()
    {
        if ($this->queryRPS instanceof LengthAwarePaginator) {
            return collect($this->queryRPS->items());
        }

        if ($this->queryRPS instanceof Collection) {
            return $this->queryRPS;
        }

        return $this->queryRPS->cursor();
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
                    'ID', 'Kode RPS', 'Deskripsi RPS', 'Tahun Akademik',
                    'Nilai Capaian', '', '',
                    'Mata Kuliah', '', '', '', '',
                    'Status RPS', 'Tanggal Revisi', 'Bobot RPS',
                    'Capaian Pembelajaran Lulusan (CPL)', '',
                    'Capaian Pembelajaran Mata Kuliah (CPMK)', '', '',
                    'Sub Capaian Pembelajaran Semester (Sub-CPMK)', '', '', '',
                    'Referensi', '',
                ], [
                    '', '', '', '',
                    'Nilai', 'Index', 'Mutu',
                    'Kode MK', 'Deskripsi', 'SKS', 'Pembelajaran', 'Wajib/Pilih',
                    '', '', '',
                    'Kode CPL', 'Jumlah CPL',
                    'Kode CPMK', 'Jumlah CPMK', 'Bobot CPMK',
                    'Kode Sub-CPMK', 'Jumlah Pertemuan', 'Metode', 'Bobot Sub-CPMK',
                    'Kode Referensi', 'Jumlah Referensi',
                ],
            ];
        } else {
            return [
                [
                    'ID', 'Kode RPS', 'Deskripsi RPS', 'Tahun Akademik',
                    'Mata Kuliah', '', '', '', '',
                    'Status RPS', 'Tanggal Revisi', 'Bobot RPS',
                    'Capaian Pembelajaran Lulusan (CPL)', '',
                    'Capaian Pembelajaran Mata Kuliah (CPMK)', '', '',
                    'Sub Capaian Pembelajaran Semester (Sub-CPMK)', '', '', '',
                    'Referensi', '',
                ], [
                    '', '', '', '',
                    'Kode MK', 'Deskripsi', 'SKS', 'Pembelajaran', 'Wajib/Pilih',
                    '', '', '',
                    'Kode CPL', 'Jumlah CPL',
                    'Kode CPMK', 'Jumlah CPMK', 'Bobot CPMK',
                    'Kode Sub-CPMK', 'Jumlah Pertemuan', 'Metode', 'Bobot Sub-CPMK',
                    'Kode Referensi', 'Jumlah Referensi',
                ],
            ];
        }
    }

    public function map($r): array
    {
        // Ambil CPL dari RPS dan dari CPMK (Unique)
        $cpls = collect()
            // ->concat($r->cpls)
            ->concat($r->cpmks->flatMap->cpls)
            ->unique('id');

        // Ambil Referensi dari RPS, CPMK, dan Sub-CPMK (Unique)
        $refs = collect()
            ->concat($r->refs)
            ->concat($r->cpmks->flatMap->refs)
            ->concat($r->cpmks->flatMap->scpmks->flatMap->refs)
            ->unique('id');

        $data = [
            $r->id ?? '',                                                                         // 0 (A)
            $r->kode ?? '',                                                                       // 1 (B)
            $r->deskripsi ?? '',                                                                  // 2 (C)
            $r->akademik ?? '',                                                                   // 3 (D)

            $r->kode_mk ?? '',                                                                    // 4 (E)
            $r->mk_rel?->deskripsi ?? '',                                                         // 5 (F)
            $r->sks ?? '',                                                                        // 6 (G)
            $r->sks_text ?? '',                                                                   // 7 (H)
            $r->wajib_text ?? '',                                                                 // 8 (I)
            $r->draf_text ?? '',                                                                  // 9 (J)
            $r->revisi_day ?? '',                                                                 // 10 (K)
            ($r->total_bobot ?? 0).'%',                                                         // 11 (L)

            $cpls->pluck('kode')->implode(' / '),                                                 // 12 (M)
            $cpls->count(),                                                                       // 13 (N)

            $r->cpmks->pluck('kode')->implode(' / '),                                             // 14 (O)
            $r->cpmks->count(),                                                                   // 15 (P)

            $r->cpmks->sum('total_bobot').'%',                                                  // 16 (Q)
            $r->cpmks->flatMap->scpmks->pluck('kode')->unique()->implode(' / '),                  // 17 (R)
            $r->count_scpmk ?: 0,                                                                 // 18 (S)

            $r->cpmks->flatMap->scpmks->pluck('metode')->unique()->filter()->implode(' / '),      // 19 (T)
            ($r->cpmks->flatMap->scpmks->sum('bobot') ?? 0).'%',                                // 20 (U)
            $refs->pluck('kode')->implode(' / '),                                                 // 21 (V)
            $refs->count(),                                                                       // 22 (W)
        ];

        if ($this->withCapaian) {
            array_splice($data, 4, 0, [
                $r->rekap_rps_pr ?: '0',
                $r->index_rps_pr ?: '0',
                $r->mutu_rps_pr ?? 'E',
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
        if ($this->withCapaian) {
            foreach (['E', 'F'] as $col) {
                $sheet->getStyle("{$col}6:{$col}{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            }
            $verticalMerges = ['A', 'B', 'C', 'D', 'M', 'N', 'O'];
        } else {
            $verticalMerges = ['A', 'B', 'C', 'D', 'J', 'K', 'L'];
        }
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        if ($this->withCapaian) {
            $sheet->mergeCells('E4:G4'); // Capaian
            $sheet->mergeCells('H4:L4'); // Mata Kuliah
            $sheet->mergeCells('P4:Q4'); // CPL
            $sheet->mergeCells('R4:S4'); // CPMK
            $sheet->mergeCells('U4:X4'); // Sub-CPMK
            $sheet->mergeCells('Y4:Z4'); // Referensi
        } else {
            $sheet->mergeCells('E4:I4'); // Mata Kuliah
            $sheet->mergeCells('M4:N4'); // CPL
            $sheet->mergeCells('O4:Q4'); // CPMK
            $sheet->mergeCells('R4:U4'); // Sub-CPMK
            $sheet->mergeCells('V4:W4'); // Referensi
        }

        if ($this->withCapaian) {
            $alignmentMerges = ['A', 'B', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'Q', 'S', 'T', 'V', 'X', 'Z'];
        } else {
            $alignmentMerges = ['A', 'B', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'L', 'N', 'P', 'Q', 'S', 'U', 'W'];
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
