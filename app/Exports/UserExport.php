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

class UserExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $queryUser;

    protected $switchTable;

    protected $title;

    public function __construct($queryUser, $switchTable, $title)
    {
        $this->queryUser = $queryUser;
        $this->switchTable = $switchTable;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->queryUser instanceof LengthAwarePaginator) {
            return collect($this->queryUser->items());
        }

        if ($this->queryUser instanceof Collection) {
            return $this->queryUser;
        }

        return $this->queryUser->cursor();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    // Mengatur Heading 2 Baris (Merge Cell)
    public function headings(): array
    {
        if ($this->switchTable == 'admin') {
            return [
                [
                    'ID', 'AMN ID',
                    'Otoritas Web', '',
                    'Nama', 'Email',
                    'Identitas (ID)', '', '',
                    'Status', 'Program Studi', '', 'Kode Kampus',
                    'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Agama', 'No. HP',
                    'Pangkat/Golongan (Admin)', '', '', '', '',
                    'Pendidikan SMA/SMK/MAN', '',
                    'Pendidikan S1/D4', '', '', '',
                    'Pendidikan S2', '', '', '',
                    'Pendidikan S3', '', '', '',
                ],
                [
                    '', '',
                    'Role', 'Tingkat Role',
                    '', '',
                    'NIP', 'NITK', 'NIK',
                    '', 'Kode PR', 'Program Studi', '',
                    '', '', '', '', '',
                    'Pangkat', 'Gol. Awal', 'Gol. Akhir', 'TMT CP/BLU', 'TMT BLU',
                    'Institusi', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                ],
            ];
        } elseif ($this->switchTable == 'dosen') {
            return [
                [
                    'ID', 'DSN ID',
                    'Otoritas Web', '',
                    'Nama', 'Email',
                    'Identitas (ID)', '', '', '',
                    'Status', 'Program Studi', '',
                    'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Agama', 'No. HP', 'No. Karpeg',
                    'Pangkat/Golongan (Dosen)', '', '', '', '',
                    'Pendidikan SMA/SMK/MAN', '',
                    'Pendidikan S1/D4', '', '', '',
                    'Pendidikan S2', '', '', '',
                    'Pendidikan S3', '', '', '',
                ],
                [
                    '', '',
                    'Role', 'Tingkat Role',
                    '', '',
                    'NIP', 'NIDN', 'NIDK', 'NIK',
                    '', 'Kode PR', 'Program Studi',
                    '', '', '', '', '', '',
                    'Pkt. Dosen', 'Gol. Dosen', 'TMT Gol', 'Jabatan', 'TMT Jab',
                    'Institusi', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                ],
            ];
        } elseif ($this->switchTable == 'mahasiswa') {
            return [
                [
                    'ID', 'MHS ID',
                    'Otoritas Web', '',
                    'Nama', 'Email',
                    'Identitas (ID)', '',
                    'Angkatan', 'Status', 'Program Studi', '', 'Kode Kampus',
                    'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Agama', 'No. HP',
                    'Pendidikan SMA/SMK/MAN', '',
                    'Pendidikan S1/D4', '', '', '',
                    'Pendidikan S2', '', '', '',
                    'Pendidikan S3', '', '', '',
                ],
                [
                    '', '',
                    'Role', 'Tingkat Role',
                    '', '',
                    'NIM', 'NIK',
                    '', '', 'Kode PR', 'Program Studi', '',
                    '', '', '', '', '',
                    'Institusi', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                ],
            ];
        } else {
            return [
                [
                    'ID', 'RL ID',
                    'Otoritas Web', '',
                    'Nama', 'Email',
                    'Identitas (ID)', '', '', '', '', '',
                    'Angkatan', 'Status', 'Program Studi', '', 'Kode Kampus',
                    'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Agama', 'No. HP', 'No. Karpeg',
                    'Pangkat/Golongan (Admin)', '', '', '', '',
                    'Pangkat/Golongan (Dosen)', '', '', '', '',
                    'Pendidikan SMA/SMK/MAN', '',
                    'Pendidikan S1/D4', '', '', '',
                    'Pendidikan S2', '', '', '',
                    'Pendidikan S3', '', '', '',
                ],
                [
                    '', '',
                    'Role', 'Tingkat Role',
                    '', '',
                    'NIP', 'NIM', 'NIDN', 'NIDK', 'NITK', 'NIK',
                    '', '', 'Kode PR', 'Program Studi', '',
                    '', '', '', '', '', '',
                    'Pangkat', 'Gol. Awal', 'Gol. Akhir', 'TMT CP/BLU', 'TMT BLU',
                    'Pkt. Dosen', 'Gol. Dosen', 'TMT Gol', 'Jabatan', 'TMT Jab',
                    'Institusi', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                    'Institusi', 'Bidang Ilmu', 'Gelar', 'Tahun Lulus',
                ],
            ];
        }
    }

    // Mapping Data (NIP/NIK ditambahkan tanda kutip tunggal agar dibaca string)
    public function map($user): array
    {
        if ($this->switchTable == 'admin') {
            return [
                $user->id ?? '', // A
                $user->admin->id ?? '', // B
                $user->role ?? '', // B
                $user->tingkat_text ?? '', // B
                $user->name ?? '', // C
                $user->email ?? '', // D

                // ID Identitas
                $user->admin->nip ?? $user->dosen->nip ?? '', // E
                $user->admin->nitk ?? '', // F
                $user->nik ?? '', // G
                // ID Identitas

                $user->status ?? '', // H
                $user->kode_pr ?? '', // I
                $user->prodi ?? '', // I
                $user->admin->kode_wilayah ?? $user->mahasiswa->kode_wilayah ?? '', // J
                $user->tmt_lahir ?? '', // K
                $user->tanggal_lahir ?? '', // L
                $user->gender ?? '', // M
                $user->agama ?? '', // N
                $user->no_hp ?? '', // O

                // Hanya Admin
                $user->admin->pangkat ?? '', // P
                $user->admin->golongan_awal ?? '', // Q
                $user->admin->golongan_akhir ?? '', // R
                $user->admin->tmt_cp_blu ?? '', // S
                $user->admin->tmt_blu ?? '', // T
                // Hanya Admin

                // Pendidikan SMA/SMK/MAN (U-V)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('institusi', ' / '), // U
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('tahun_lulus', ' / '), // V

                // Pendidikan S1/D4 (W-Z)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('institusi', ' / '), // W
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('bidang_ilmu', ' / '), // X
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('gelar', ' / '), // Y
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('tahun_lulus', ' / '), // Z

                // Pendidikan S2 (AA-AD)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('institusi', ' / '), // AA
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('bidang_ilmu', ' / '), // AB
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('gelar', ' / '), // AC
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('tahun_lulus', ' / '), // AD

                // Pendidikan S3 (AE-AH)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('institusi', ' / '), // AE
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('bidang_ilmu', ' / '), // AF
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('gelar', ' / '), // AG
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('tahun_lulus', ' / '), // AH
            ];
        } elseif ($this->switchTable == 'dosen') {
            return [
                $user->id ?? '', // A
                $user->dosen->id ?? '', // B
                $user->role ?? '', // B
                $user->tingkat_text ?? '', // B
                $user->name ?? '', // C
                $user->email ?? '', // D

                // ID Identitas
                $user->dosen->nip ?? '', // E
                $user->dosen->nidn ?? '', // F
                $user->dosen->nidk ?? '', // G
                $user->nik ?? '', // H
                // ID Identitas

                $user->status ?? '', // I
                $user->kode_pr ?? '', // I
                $user->prodi ?? '', // J
                $user->tmt_lahir ?? '', // K
                $user->tanggal_lahir ?? '', // L
                $user->gender ?? '', // M
                $user->agama ?? '', // N
                $user->no_hp ?? '', // O
                $user->dosen->no_karpeg ?? '', // P

                // Hanya Dosen
                $user->dosen->pangkat_terakhir ?? '', // Q
                $user->dosen->golongan_terakhir ?? '', // R
                $user->dosen->tmt_golongan ?? '', // S
                $user->dosen->jabatan_fungsional ?? '', // T
                $user->dosen->tmt_jabatan ?? '', // U
                // Hanya Dosen

                // Pendidikan SMA/SMK/MAN (V-W)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('institusi', ' / '), // V
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('tahun_lulus', ' / '), // W

                // Pendidikan S1/D4 (X-AA)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('institusi', ' / '), // X
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('bidang_ilmu', ' / '), // Y
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('gelar', ' / '), // Z
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('tahun_lulus', ' / '), // AA

                // Pendidikan S2 (AB-AE)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('institusi', ' / '), // AB
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('bidang_ilmu', ' / '), // AC
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('gelar', ' / '), // AD
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('tahun_lulus', ' / '), // AE

                // Pendidikan S3 (AF-AI)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('institusi', ' / '), // AF
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('bidang_ilmu', ' / '), // AG
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('gelar', ' / '), // AH
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('tahun_lulus', ' / '), // AI
            ];
        } elseif ($this->switchTable == 'mahasiswa') {
            return [
                $user->id ?? '', // A
                $user->mahasiswa->id ?? '', // B
                $user->role ?? '', // B
                $user->tingkat_text ?? '', // B
                $user->name ?? '', // C
                $user->email ?? '', // D

                // ID Identitas
                $user->mahasiswa->nim ?? '', // E
                $user->nik ?? '', // I
                // ID Identitas

                $user->mahasiswa->angkatan ?? '', // J
                $user->status ?? '', // K
                $user->kode_pr ?? '', // I
                $user->prodi ?? '', // L
                $user->admin->kode_wilayah ?? $user->mahasiswa->kode_wilayah ?? '', // M
                $user->tmt_lahir ?? '', // N
                $user->tanggal_lahir ?? '', // O
                $user->gender ?? '', // P
                $user->agama ?? '', // Q
                $user->no_hp ?? '', // R

                // Pendidikan SMA/SMK/MAN (S-T)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('institusi', ' / '), // S
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('tahun_lulus', ' / '), // T

                // Pendidikan S1/D4 (U-X)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('institusi', ' / '), // U
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('bidang_ilmu', ' / '), // V
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('gelar', ' / '), // W
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('tahun_lulus', ' / '), // X

                // Pendidikan S2 (Y-AB)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('institusi', ' / '), // Y
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('bidang_ilmu', ' / '), // Z
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('gelar', ' / '), // AA
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('tahun_lulus', ' / '), // AB

                // Pendidikan S3 (AC-AF)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('institusi', ' / '), // AC
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('bidang_ilmu', ' / '), // AD
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('gelar', ' / '), // AE
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('tahun_lulus', ' / '), // AF
            ];
        } else {
            return [
                $user->id ?? '', // A
                $user->admin->id ?? $user->dosen->id ?? $user->mahasiswa->id ?? '', // A
                $user->role ?? '', // B
                $user->tingkat_text ?? '', // B
                $user->name ?? '', // C
                $user->email ?? '', // D

                // ID Identitas
                $user->admin->nip ?? $user->dosen->nip ?? '', // E
                $user->mahasiswa->nim ?? '', // F
                $user->dosen->nidn ?? '', // G
                $user->dosen->nidk ?? '', // H
                $user->admin->nitk ?? '', // I
                $user->nik ?? '', // J
                // ID Identitas

                $user->mahasiswa->angkatan ?? '', // K
                $user->status ?? '', // L
                $user->kode_pr ?? '', // M
                $user->prodi ?? '', // N
                $user->admin->kode_wilayah ?? $user->mahasiswa->kode_wilayah ?? '', // O
                $user->tmt_lahir ?? '', // P
                $user->tanggal_lahir ?? '', // Q
                $user->gender ?? '', // R
                $user->agama ?? '', // S
                $user->no_hp ?? '', // T
                $user->dosen->no_karpeg ?? '', // U

                // Hanya Admin
                $user->admin->pangkat ?? '', // V
                $user->admin->golongan_awal ?? '', // W
                $user->admin->golongan_akhir ?? '', // X
                $user->admin->tmt_cp_blu ?? '', // Y
                $user->admin->tmt_blu ?? '', // Z
                // Hanya Admin

                // Hanya Dosen
                $user->dosen->pangkat_terakhir ?? '', // Z
                $user->dosen->golongan_terakhir ?? '', // AA
                $user->dosen->tmt_golongan ?? '', // AB
                $user->dosen->jabatan_fungsional ?? '', // AC
                $user->dosen->tmt_jabatan ?? '', // AD
                // Hanya Dosen

                // Pendidikan SMA/SMK/MAN (AE-AF)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('institusi', ' / '), // AE
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['SMA', 'SMK', 'MAN']))->implode('tahun_lulus', ' / '), // AF

                // Pendidikan S1/D4 (AG-AJ)
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('institusi', ' / '), // AG
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('bidang_ilmu', ' / '), // AH
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('gelar', ' / '), // AI
                $user->pendidikans->filter(fn ($p) => in_array($p->jenjang_pendidikan, ['S1', 'D4']))->implode('tahun_lulus', ' / '), // AJ

                // Pendidikan S2 (AK-AN)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('institusi', ' / '), // AK
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('bidang_ilmu', ' / '), // AL
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('gelar', ' / '), // AM
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S2')->implode('tahun_lulus', ' / '), // AN

                // Pendidikan S3 (AO-AR)
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('institusi', ' / '), // AO
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('bidang_ilmu', ' / '), // AP
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('gelar', ' / '), // AQ
                $user->pendidikans->filter(fn ($p) => $p->jenjang_pendidikan === 'S3')->implode('tahun_lulus', ' / '), // AR
            ];
        }

    }

    public function bindValue(Cell $cell, $value)
    {
        if ($this->switchTable == 'admin') {
            if (in_array($cell->getColumn(), ['G', 'H', 'I', 'R'])) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING);

                return true;
            }
        } elseif ($this->switchTable == 'dosen') {
            if (in_array($cell->getColumn(), ['G', 'H', 'I', 'J', 'R'])) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING);

                return true;
            }
        } elseif ($this->switchTable == 'mahasiswa') {
            if (in_array($cell->getColumn(), ['G', 'H', 'R'])) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING);

                return true;
            }
        } else {
            if (in_array($cell->getColumn(), ['G', 'H', 'I', 'J', 'K', 'L', 'V'])) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING);

                return true;
            }
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        if ($this->switchTable == 'admin') {
            return [
                'G' => NumberFormat::FORMAT_TEXT,
                'H' => NumberFormat::FORMAT_TEXT,
                'I' => NumberFormat::FORMAT_TEXT,
                'R' => NumberFormat::FORMAT_TEXT,
            ];
        } elseif ($this->switchTable == 'dosen') {
            return [
                'G' => NumberFormat::FORMAT_TEXT,
                'H' => NumberFormat::FORMAT_TEXT,
                'I' => NumberFormat::FORMAT_TEXT,
                'J' => NumberFormat::FORMAT_TEXT,
                'R' => NumberFormat::FORMAT_TEXT,
            ];
        } elseif ($this->switchTable == 'mahasiswa') {
            return [
                'G' => NumberFormat::FORMAT_TEXT,
                'H' => NumberFormat::FORMAT_TEXT,
                'R' => NumberFormat::FORMAT_TEXT,
            ];
        } else {
            return [
                'G' => NumberFormat::FORMAT_TEXT,
                'H' => NumberFormat::FORMAT_TEXT,
                'I' => NumberFormat::FORMAT_TEXT,
                'J' => NumberFormat::FORMAT_TEXT,
                'K' => NumberFormat::FORMAT_TEXT,
                'L' => NumberFormat::FORMAT_TEXT,
                'V' => NumberFormat::FORMAT_TEXT,
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

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        if ($this->switchTable == 'admin') {
            $verticalMerges = ['A', 'B', 'E', 'F', 'J', 'M', 'N', 'O', 'P', 'Q', 'R'];
            $horizontalMerges = ['C4:D4' ,'G4:I4', 'K4:L4', 'S4:W4', 'X4:Y4', 'Z4:AC4', 'AD4:AG4', 'AH4:AK4'];
            $alignmentMerges = ['A', 'B', 'C', 'G', 'H', 'I', 'J', 'K', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'AB', 'AC', 'AF', 'AG', 'AJ', 'AK'];
        } elseif ($this->switchTable == 'dosen') {
            $verticalMerges = ['A', 'B', 'E', 'F', 'K', 'N', 'O', 'P', 'Q', 'R', 'S'];
            $horizontalMerges = ['C4:D4' ,'G4:J4', 'L4:M4', 'T4:X4', 'Y4:Z4', 'AA4:AD4', 'AE4:AH4', 'AI4:AL4'];
            $alignmentMerges = ['A', 'B', 'C', 'G', 'H', 'I', 'J', 'K', 'L', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Z', 'AC', 'AD', 'AG', 'AH', 'AK', 'AL'];
        } elseif ($this->switchTable == 'mahasiswa') {
            $verticalMerges = ['A', 'B', 'E', 'F', 'I', 'J', 'M', 'N', 'O', 'P', 'Q', 'R'];
            $horizontalMerges = ['C4:D4', 'G4:H4', 'K4:L4', 'S4:T4', 'U4:X4', 'Y4:AB4', 'AC4:AF4'];
            $alignmentMerges = ['A', 'B', 'C', 'G', 'H', 'I', 'J', 'K', 'M', 'O', 'P', 'R', 'T', 'W', 'X', 'AA', 'AB', 'AE', 'AF'];
        } else {
            $verticalMerges = ['A', 'B', 'E', 'F', 'M', 'N', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
            $horizontalMerges = ['C4:D4' ,'G4:L4', 'O4:P4', 'X4:AB4', 'AC4:AG4', 'AH4:AI4', 'AJ4:AM4', 'AN4:AQ4', 'AR4:AU4'];
            $alignmentMerges = ['A', 'B', 'C', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'Q', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AP', 'AQ', 'AT', 'AU'];
        }
        $headerRange = "A4:{$highestColumn}5";

        foreach ($verticalMerges as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        foreach ($horizontalMerges as $range) {
            $sheet->mergeCells($range);
        }

        $excluded = ['E', 'F'];
        foreach ($alignmentMerges as $c) {
            if (in_array($c, $excluded)) {
                continue;
            }
            $sheet->getStyle($c.'4:'.$c.$highestRow)->getAlignment()->applyFromArray([
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]);
        }

        $sheet->getStyle($headerRange)->applyFromArray($styleArray);
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
