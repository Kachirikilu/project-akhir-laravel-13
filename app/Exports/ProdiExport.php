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
                    'Dekan', '', 'Wakil Dekan', '',
                    'Nilai Capaian Fakultas', '', '',
                    'Program Studi', '',
                    'Departemen', '',

                ],
                [
                    '', '', '',
                    'Nama', 'NIP', 'Nama', 'NIP',
                    'Nilai', 'Index', 'Akreditas',
                    'Kode PR', 'Jumlah Program Studi',
                    'Kode DP', 'Jumlah Departemen',
                ],
            ];
        } elseif ($this->switchTable == 'departemen') {
            return [
                [
                    'ID', 'Kode DP', 'Departemen',
                    'Ketua Departemen', '', 'Sekretaris Departemen', '',
                    'Nilai Capaian Departemen', '', '',
                    'Program Studi', '',
                    'Fakultas', '',
                ],
                [
                    '', '', '',
                    'Nama', 'NIP', 'Nama', 'NIP',
                    'Nilai', 'Index', 'Akreditas',
                    'Kode PR', 'Jumlah Program Studi',
                    'Kode FK', 'Nama Fakultas',
                ],
            ];
        } else {
            return [
                [
                    'ID', 'Kode PR', 'Program Studi',
                    'Ketua Program Studi', '', 'Sekretaris Program Studi', '',
                    'Nilai Capaian Program Studi', '', '', '',
                    'Mata Kuliah & Rencana Pembelajaran Semester', '', '', '',
                    'Departemen', '',
                    'Fakultas', '',
                ],
                [
                    '', '', '',
                    'Nama', 'NIP', 'Nama', 'NIP',
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
                $pr->id ?? '',          // A: ID
                $pr->kode ?? '',        // B: Kode
                $pr->fakultas_fk ?? '', // C: Nama Fakultas

                // Kolom Baru D, E, F, G
                $pr->nama_dekan ?? '',  // D: Nama Dekan
                $pr->nip_dekan ?? '',   // E: NIP Dekan
                $pr->nama_wadek ?? '',  // F: Nama Wadek
                $pr->nip_wadek ?? '',   // G: NIP Wadek

                // Kolom Bergeser (H, I, J, K, ...)
                $pr->rekap_fk ?? 0,     // H
                $pr->index_fk ?? 0,     // I
                $pr->akreditas_fk ?? 0, // J
                $pr->prodis
                    ->map(fn ($prodi) => $prodi->kode)
                    ->unique()
                    ->implode(' / '),   // K
                $pr->prodis->count() > 0 ? $pr->prodis->count() : '0', // L

                $pr->departemens->pluck('kode')->unique()->implode(' / '), // M
                $pr->departemens->count() > 0 ? $pr->departemens->count() : '0', // N
            ];
        } elseif ($this->switchTable == 'departemen') {
            return [
                $pr->id ?? '',          // A: ID
                $pr->kode ?? '',        // B: Kode
                $pr->departemen ?? '',  // C: Nama Departemen

                // Kolom Baru D, E, F, G
                $pr->nama_kadep ?? '',  // D: Nama Kadep
                $pr->nip_kadep ?? '',   // E: NIP Kadep
                $pr->nama_sekdep ?? '', // F: Nama Sekdep
                $pr->nip_sekdep ?? '',  // G: NIP Sekdep

                // Kolom Bergeser (H, I, J, K, ...)
                $pr->rekap_dp ?? 0,     // H
                $pr->index_dp ?? 0,     // I
                $pr->akreditas_dp ?? 0, // J
                $pr->prodis
                    ->map(fn ($prodi) => $prodi->kode)
                    ->unique()
                    ->implode(' / '),   // K
                $pr->prodis->count() > 0 ? $pr->prodis->count() : '0', // L

                $pr->kode_fk ?? '',     // M: Kode FK
                $pr->fakultas_fk ?? '', // N: Nama Fakultas
            ];
        } else {
            return [
                $pr->id ?? '',           // A: ID
                $pr->kode ?? '',         // B: Kode
                $pr->prodi ?? '',        // C: Nama Prodi

                // Kolom Baru D, E, F, G
                $pr->nama_kaprodi ?? '', // D: Nama Kaprodi
                $pr->nip_kaprodi ?? '',  // E: NIP Kaprodi
                $pr->nama_sekprodi ?? '', // F: Nama Sekprodi
                $pr->nip_sekprodi ?? '', // G: NIP Sekprodi

                // Kolom Bergeser (H, I, J, K, ...)
                $pr->rekap_pr ?? 0,      // H
                $pr->index_pr ?? 0,      // I
                $pr->akreditas_pr ?? 0,  // J
                $pr->target_sks ?? 0,    // K
                $pr->count_mk ?? 0,      // L
                $pr->count_rps ?? 0,     // M
                $pr->count_rps_aktif ?? 0, // N
                $pr->count_rps_draf ?? 0,  // O

                $pr->kode_dp ?? '',      // P: Kode DP
                $pr->departemen_dp ?? '', // Q: Nama DP
                $pr->kode_fk ?? '',      // R: Kode FK
                $pr->fakultas_fk ?? '',  // S: Nama FK
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
            $sheet->mergeCells('D4:E4'); // Kaprodi (Nama & NIP)
            $sheet->mergeCells('F4:G4'); // Sekprodi (Nama & NIP)

            $sheet->mergeCells('H4:K4'); // Rekap / Index / Akreditas / Target SKS
            $sheet->mergeCells('L4:O4'); // Count MK & RPS
            $sheet->mergeCells('P4:Q4'); // Departemen (Kode & Nama)
            $sheet->mergeCells('R4:S4'); // Fakultas (Kode & Nama)
        } else {
            $sheet->mergeCells('D4:E4'); // Dekan / Kadep (Nama & NIP)
            $sheet->mergeCells('F4:G4'); // Wadek / Sekdep (Nama & NIP)

            $sheet->mergeCells('H4:J4'); // Rekap / Index / Akreditas
            $sheet->mergeCells('K4:L4'); // Rekap Prodi & Jumlah Prodi
            $sheet->mergeCells('M4:N4'); // Fakultas / Departemen Terkait
        }

        if ($this->switchTable == 'fakultas') {
            $alignmentMerges = ['A', 'B', 'E', 'G', 'H', 'I', 'J', 'L', 'N'];
        } elseif ($this->switchTable == 'departemen') {
            $alignmentMerges = ['A', 'B', 'E', 'G', 'H', 'I', 'J', 'L', 'M'];
        } else {
            $alignmentMerges = ['A', 'B', 'E', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R'];
        }

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        foreach (['H', 'I'] as $col) {
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
