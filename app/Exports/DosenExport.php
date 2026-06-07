<?php

namespace App\Exports;

use App\Models\Akademik\RPS;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DosenExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryDosen;

    protected $title;

    public function __construct($queryDosen, $title)
    {
        $this->queryDosen = $queryDosen;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->queryDosen->cursor();
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
                'Identitas (ID)', '', '', '',
                'Status', 'Program Studi',
                'Rencana Pembelajaran Semester', '', '', '',
                'Kelas', '',
            ],
            [
                '', '', '', '',
                'NIP', 'NIDN', 'NIDK', 'NIK',
                '', '',
                'Kode RPS Aktif', 'Jumlah RPS Aktif', 'Kode RPS Non-Aktif', 'Jumlah RPS Non-Aktif',
                'Kode Kelas', 'Jumlah Kelas',
            ],
        ];
    }

    public function map($u): array
    {
        $d = $u->dosen;

        if (! $d) {
            return [
                $u->id ?? '', // A
                $u->role ?? '', // B
                $u->name ?? '', // C
                $u->email ?? '', // D
                '', '', '', '', // E-H
                $u->status ?? '', // I
                $u->prodi ?? '', // J
                '-', 0, '-', 0, '-', 0, // K-P
            ];
        }

        $rpsDirect = $d->rps;

        $rpsSubIds = $d->scpmks->pluck('pivot.rps_id')->unique()->filter();
        $rpsSub = RPS::whereIn('id', $rpsSubIds)->get();

        $allRps = $rpsDirect->concat($rpsSub)->unique('id');

        $rpsAktif = $allRps->where('is_draf', 0);
        $rpsNonAktif = $allRps->where('is_draf', 1);

        $kelas = $allRps
            ->flatMap(fn ($rps) => $rps->kelas)
            ->unique('id')
            ->values();

        return [
            $u->id ?? '', // A
            $u->role ?? '', // B
            $u->name ?? '', // C
            $u->email ?? '', // D

            // Identitas (ID)
            $d->nip ?? '', // E
            $d->nidn ?? '', // F
            $d->nidk ?? '', // G
            $u->nik ?? '', // H

            $u->status ?? '', // I
            $u->prodi ?? '', // J

            // Rencana Pembelajaran Semester (RPS)
            $rpsAktif->pluck('kode')->implode(' / ') ?: '', // K: Kode RPS Aktif
            $rpsAktif->count(), // L: Jumlah RPS Aktif
            $rpsNonAktif->pluck('kode')->implode(' / ') ?: '', // M: Kode RPS Non-Aktif
            $rpsNonAktif->count(), // N: Jumlah RPS Non-Aktif

            // Kelas
            $kelas->pluck('kode_kelas')->implode(' / ') ?: '', // O: Kode Kelas
            $kelas->count(), // P: Jumlah Kelas
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
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
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
        $merges = ['A4:A5', 'B4:B5', 'C4:C5', 'D4:D5', 'I4:I5', 'J4:J5'];
        foreach ($merges as $m) {
            $sheet->mergeCells($m);
        }

        // Merge untuk Header Utama
        $sheet->mergeCells('E4:H4'); // Identitas (ID)
        $sheet->mergeCells('K4:N4'); // Rencana Pembelajaran Semester
        $sheet->mergeCells('O4:P4'); // Kelas

        // Alignment untuk seluruh kolom ID, Role, Status, Prodi (Center)
        $alignmentMerges = ['A', 'B', 'E', 'F', 'G', 'H', 'I', 'J', 'L', 'N', 'P'];
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
