<?php

namespace App\Exports;

use App\Models\Akademik\RPS;
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

class TimDosenExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryTimDosen;

    protected $title;

    public function __construct($queryTimDosen, $title)
    {
        $this->queryTimDosen = $queryTimDosen;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->queryTimDosen instanceof LengthAwarePaginator) {
            return collect($this->queryTimDosen->items());
        }

        if ($this->queryTimDosen instanceof Collection) {
            return $this->queryTimDosen;
        }

        return $this->queryTimDosen->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'ID', 'Kode', 'Nama Tim', 'Ketua', 'NIP Ketua',
                'Anggota Tim', '', '', '',
                'Program Studi',
                'Rencana Pembelajaran Semester (RPS)', '', '', '', '', '',
                'Kelas', '',
            ],
            [
                '', '', '', '', '',
                'Nama', 'NIP', 'Peran', 'Jumlah Dosen',
                '',
                'Kode RPS Aktif', 'SKS Aktif', 'Jumlah RPS Aktif', 'Kode RPS Non-Aktif', 'SKS Non-Aktif', 'Jumlah RPS Non-Aktif',
                'Kode Kelas', 'Jumlah Kelas',
            ],
        ];
    }

public function map($tim): array
{
    // Mengambil semua anggota (termasuk ketua)
    $anggota = $tim->dosens; 
    
    // Kita ambil anggota pertama untuk ditampilkan di baris ini
    // (Jika ingin menampilkan semuanya sekaligus, gunakan implode di bawah)
    $anggotaUtama = $anggota;

    // Data RPS dan Kelas
    $allRps = $tim->rps;
    $rpsAktif = $allRps->where('is_draf', 0);
    $rpsNonAktif = $allRps->where('is_draf', 1);
    $kelas = $allRps->flatMap->kelas->unique('id');

    return [
        $tim->id,             
        $tim->kode_tim_dosen, 
        $tim->nama_tim,       
        // Jika tetap ingin kolom ketua terpisah:
        $anggota->firstWhere('pivot.is_ketua', 1)?->name ?? '-', 
        $anggota->firstWhere('pivot.is_ketua', 1)?->nip ?? '-',  

        // Kolom Anggota Tim (Sekarang ketua juga bisa muncul di sini)
        $anggotaUtama->pluck('name')->implode(' / '),
        $anggotaUtama->pluck('nip')->implode(' / '),
        $anggota->pluck('pivot.peran')->implode(' / '),
        $anggota->count(), // Total anggota termasuk ketua
        
        // Program Studi
        $tim->pr_rel?->prodi ?? '-', 
        
        // RPS & Kelas (Logika tetap sama)
        $rpsAktif->pluck('kode')->implode(' / '),
        $rpsAktif->sum('sks') > 0 ? $rpsAktif->sum('sks') : '0',
        $rpsAktif->count() > 0 ? $rpsAktif->count() : '0',

        $rpsNonAktif->pluck('kode')->implode(' / '),
        $rpsNonAktif->sum('sks') > 0 ? $rpsAktif->sum('sks') : '0',
        $rpsNonAktif->count() > 0 ? $rpsNonAktif->count() : '0',

        // Kelas
        $kelas->pluck('kode_kelas')->implode(' / '),
        $kelas->count() > 0 ? $kelas->count() : '0',
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
        $verticalMerges = ['A', 'B', 'C', 'D', 'E', 'J'];
        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        // Merge untuk Header Utama
        $sheet->mergeCells('F4:I4'); // Identitas (ID)
        $sheet->mergeCells('K4:P4'); // Rencana Pembelajaran Semester
        $sheet->mergeCells('Q4:R4'); // Kelas

        // Alignment untuk seluruh kolom ID, Role, Status, Prodi (Center)
        $alignmentMerges = ['A', 'B', 'E', 'I', 'J', 'L', 'M', 'O', 'P', 'R'];
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
