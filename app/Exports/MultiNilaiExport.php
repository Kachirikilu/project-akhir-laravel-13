<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiNilaiExport implements WithMultipleSheets
{
    use Exportable;

    protected $jadwals;

    public function __construct($jadwals)
    {
        // 🌟 Urutkan berdasarkan gabungan kode_wilayah dan label
        $this->jadwals = collect($jadwals)->sortBy(function ($jadwal) {
            return ($jadwal->kode_wilayah ?? '') . ($jadwal->label ?? '');
        });
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->jadwals as $jadwal) {
            $sheetName = $jadwal->kode; 
            $sheetNameSafe = substr(
                str_replace(['*', ':', '?', '/', '\\', '[', ']'], '-', $sheetName), 
                0, 
                31
            );
            $sheets[] = new NilaiExport($jadwal, $sheetNameSafe); 
        }

        return $sheets;
    }
}