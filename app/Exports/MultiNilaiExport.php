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
        $this->jadwals = $jadwals;
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