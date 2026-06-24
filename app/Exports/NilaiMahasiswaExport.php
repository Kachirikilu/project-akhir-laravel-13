<?php

namespace App\Exports;

use App\Models\Auth\Mahasiswa;
use App\Models\Penilaian\NilaiMahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiMahasiswaExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected $mahasiswa_id;

    protected $mahasiswa;

    private $totalData = 0;

    protected $title;

    // return Excel::download(new NilaiMahasiswaExport($mhsId, $gg, $aka, $title), $fileNameSafe);

    public function __construct($mahasiswa_id, $ganjil_genap, $akademik, $title)
    {
        $this->mahasiswa_id = $mahasiswa_id;
        $this->title = $title;

        if ($ganjil_genap && $akademik) {
            $this->mahasiswa = Mahasiswa::with([
                'nilai_mahasiswas' => function ($query) use ($ganjil_genap, $akademik) {
                    if (! is_null($ganjil_genap)) {
                        $query->where('ganjil_genap', $ganjil_genap);
                    }
                    if (! is_null($akademik)) {
                        $query->where('tahun_akademik', $akademik);
                    }
                },
                'nilai_mahasiswas.rps_rel.mk_rel',
                'pr_rel',
            ])->findOrFail($mahasiswa_id);
        } else {
            $this->mahasiswa = Mahasiswa::with([
                'nilai_mahasiswas.rps_rel.mk_rel',
                'pr_rel',
            ])->findOrFail($mahasiswa_id);
        }
    }

    public function collection()
    {
        $koleksiNilai = $this->mahasiswa->nilai_mahasiswas;
        $koleksiTerurut = $koleksiNilai->sortBy(function ($nilai) {
            return $nilai->rps_rel?->mk_rel?->digit_mk ?? 0;
        });
        $this->totalData = $koleksiTerurut->count();

        return $koleksiTerurut;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        // Header Baris 1 - Kolom Tahun Akademik disisipkan setelah NIM
        $row1 = [
            'No MK', 'Mata Kuliah', 'Kode RPS', 'Nama Mahasiswa', 'NIM', 'Tahun Akademik',
            'Penilaian Akhir', '', '', '', // SKS, Nilai, Index, Mutu
        ];

        // Header Baris 2
        $row2 = [
            '', '', '', '', '', '',
            'SKS', 'Nilai Akhir', 'Index', 'Mutu',
        ];

        // Ekspansi horizontal untuk 16 Pertemuan
        for ($i = 1; $i <= 16; $i++) {
            $row1 = array_merge($row1, ["Pertemuan {$i}", '', '', '', '']);
            $row2 = array_merge($row2, ['Nilai', 'Bobot', 'Metode', 'Sub-CPMK', 'CPMK']);
        }

        return [$row1, $row2];
    }

    public function map($row): array
    {
        /** @var NilaiMahasiswa $row */

        // Ambil tahun akademik dari relasi jadwal jika ada, atau gunakan default text fallback
        $ta = $row->ganjil_genap.' '.$row->tahun_akademik;

        $data = [
            $row->digit_mk ?? '',
            $row->mk ?? '',
            $row->kode_rps ?? '',
            $row->mahasiswa_rel?->name ?? '',
            $row->mahasiswa_rel?->nim ?? '',
            $ta,
            $row->sks ?? 0,
            $row->nilai ?? 0,
            $row->nilai_index ?? '0.00',
            $row->nilai_mutu ?? 'E',
        ];

        $nilaiArray = $row->nilai_array ?? array_fill(0, 16, 0);
        $bobotArray = $row->bobot_rps_array ?? array_fill(0, 16, 0);
        $metodeScpmk = $row->metode_array ?? array_fill(0, 16, '-');
        $kodeScpmk = $row->kode_scpmk_array ?? array_fill(0, 16, '-');
        $kodeCpmk = $row->kode_cpmk_array ?? array_fill(0, 16, '-');

        for ($i = 0; $i < 16; $i++) {
            $data[] = $nilaiArray[$i] ?? 0;
            $data[] = $bobotArray[$i] ?? 0;
            $data[] = $metodeScpmk[$i] ?? 'Teori';
            $data[] = $kodeScpmk[$i] ?? '-';
            $data[] = $kodeCpmk[$i] ?? '-';
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $startRowData = 6;
        $endRowData = 5 + $this->totalData;
        $summaryRow = $endRowData + 1;

        // 1. Style Header Utama (Warna Biru Berkas RPS)
        $sheet->getStyle("A4:{$highestColumn}5")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '075985']],
        ]);

        // 2. Merge Vertikal Kolom Identitas (A sampai F)
        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }

        // 3. Merge Horizontal Kelompok Penilaian Akhir (G4:J4)
        $sheet->mergeCells('G4:J4');

        // 4. Merge Horizontal 16 Pertemuan
        $startColIndex = 11; // Kolom K
        for ($i = 0; $i < 16; $i++) {
            $col1 = Coordinate::stringFromColumnIndex($startColIndex);
            $col2 = Coordinate::stringFromColumnIndex($startColIndex + 4);
            $sheet->mergeCells("{$col1}4:{$col2}4");

            // 5. Format kolom Bobot pada tiap pertemuan menjadi Persen (2 Digit Belakang Koma)
            $bobotCol = Coordinate::stringFromColumnIndex($startColIndex + 1);
            $sheet->getStyle("{$bobotCol}6:{$bobotCol}{$endRowData}")
                ->getNumberFormat()
                ->setFormatCode('0.00%');
            $startColIndex += 5;
        }

        // 6. Format Desimal Kolom Nilai Akhir & Index Data (H & I)
        $sheet->getStyle("H6:I{$summaryRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        // 7. Penyelarasan Posisi Teks (Alignment)
        $sheet->getStyle("A6:A{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C6:C{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B6:B{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E6:F{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G6:{$highestColumn}{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A4:{$highestColumn}{$summaryRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // 8. Background highlight area ringkasan Penilaian Akhir (G sampai J)
        $sheet->getStyle("G6:J{$endRowData}")->getFill()->applyFromArray([
            'fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC'],
        ]);

        // 9. BARIS TOTAL / SUMMARY DI PALING BAWAH (DIHITUNG VIA PHP AGAR BISA FILTER RPS_ID UNIK NILAI TERTINGGI)
        $sheet->mergeCells("A{$summaryRow}:F{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", 'TOTAL AKUMULASI');

        // Logika PHP untuk menyaring rps_id unik dengan nilai tertinggi
        $nilaiUnik = $this->mahasiswa->nilai_mahasiswas
            ->groupBy('rps_id')
            ->map(function ($group) {
                // Ambil data nilai yang paling besar di dalam rps_id yang sama
                return $group->sortByDesc('nilai')->first();
            });

        // Hitung total SKS unik
        $totalSksUnik = $nilaiUnik->sum(function ($item) {
            return $item->sks ?? 0;
        });

        // Hitung total bobot (SKS * Index) untuk IPK
        $totalBobotNilai = $nilaiUnik->sum(function ($item) {
            $sks = $item->sks ?? 0;
            $index = (float) ($item->nilai_index ?? 0);

            return $sks * $index;
        });

        // Hitung rata-rata Nilai Akhir dari yang unik
        $rataNilaiAkhir = $nilaiUnik->count() > 0 ? ($nilaiUnik->sum('nilai') / $nilaiUnik->count()) : 0;

        // Hitung IPK Akhir
        $ipkAkhir = $totalSksUnik > 0 ? ($totalBobotNilai / $totalSksUnik) : 0;

        // Cetak hasil kalkulasi PHP ke dalam sel Summary Row di Excel
        $sheet->setCellValue("G{$summaryRow}", $totalSksUnik);
        $sheet->setCellValue("H{$summaryRow}", round($rataNilaiAkhir, 2));
        $sheet->setCellValue("I{$summaryRow}", round($ipkAkhir, 2));

        // Formula Predikat Mutu IPK otomatis di Excel tetap membaca kolom I
        $sheet->setCellValue("J{$summaryRow}", "=IF(I{$summaryRow}>=3.75,\"A\",IF(I{$summaryRow}>=3.5,\"A-\",IF(I{$summaryRow}>=3.0,\"B+\",IF(I{$summaryRow}>=2.75,\"B\",IF(I{$summaryRow}>=2.0,\"C\",\"D\")))))");

        // Style untuk Baris Summary
        $sheet->getStyle("A{$summaryRow}:{$highestColumn}{$summaryRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
        ]);

        // 10. Borders Seluruh Tabel
        $sheet->getStyle("A4:{$highestColumn}{$summaryRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();

                $title = $this->title;

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->setCellValue('A2', $title);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '075985']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle("A1:{$highestColumn}5")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                // $sheet->getStyle("A6:{$highestColumn}{$highestRow}")->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $sheet->getProtection()->setPassword(env('PW_EXCEL', '121104'));
                $sheet->getProtection()->setSheet(true);

                $sheet->getRowDimension(2)->setRowHeight(35);
                $sheet->getRowDimension(4)->setRowHeight(25);
                $sheet->getRowDimension(5)->setRowHeight(22);
            },
        ];
    }
}
