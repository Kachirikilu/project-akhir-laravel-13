<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiJadwalNilaiExport implements WithMultipleSheets
{
    use Exportable;

    protected $jadwals;

    public function __construct($jadwals)
    {
        $this->jadwals = $jadwals;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->jadwals as $jadwal) {
            // 🌟 AMBIL KODE JADWAL SEBAGAI NAMA SHEET
            $sheetName = $jadwal->kode; 

            // 🌟 BERSIHKAN DARI KARAKTER ILLEGAL EXCEL & POTONG MAKSIMAL 31 KARAKTER
            $sheetNameSafe = substr(
                str_replace(['*', ':', '?', '/', '\\', '[', ']'], '-', $sheetName), 
                0, 
                31
            );

            // Masukkan ke array sheets
            $sheets[] = new NilaiExport($jadwal->id, $sheetNameSafe); 
        }

        return $sheets;
    }
}