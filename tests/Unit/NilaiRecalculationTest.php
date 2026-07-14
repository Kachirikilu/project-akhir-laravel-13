<?php

namespace Tests\Unit;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class NilaiRecalculationTest extends TestCase
{
    public function test_grade_recalculation()
    {
        $dummy = new class
        {
            use WithNilaiExcel;

            // Mock methods used by trait
            public function AuthCheck($role = null)
            {
                return true;
            }

            public function toast($text, $variant = 'success') {}

            public function dispatch($event, ...$args) {}

            public function getPage($pageName)
            {
                return 1;
            }
        };

        // Set up test data
        $dummy->parsedNilaiRows = [
            [
                'sub_cpmk' => [
                    ['nilai' => 90],
                    ['nilai' => 86],
                ],
                'nilai_angka' => 0,
                'nilai_index' => null,
                'nilai_mutu' => null,
            ],
        ];

        // Recalculate
        $dummy->recalculateRowNilai(0);

        // Assert average = 88.0
        $this->assertEquals(88.0, $dummy->parsedNilaiRows[0]['nilai_angka']);
        $this->assertEquals(4, $dummy->parsedNilaiRows[0]['nilai_index']);
        $this->assertEquals('A', $dummy->parsedNilaiRows[0]['nilai_mutu']);

        // Test B threshold (71)
        $dummy->parsedNilaiRows[0]['sub_cpmk'] = [
            ['nilai' => 71],
            ['nilai' => 71],
        ];
        $dummy->recalculateRowNilai(0);
        $this->assertEquals(71.0, $dummy->parsedNilaiRows[0]['nilai_angka']);
        $this->assertEquals(3, $dummy->parsedNilaiRows[0]['nilai_index']);
        $this->assertEquals('B', $dummy->parsedNilaiRows[0]['nilai_mutu']);

        // Test C threshold (56)
        $dummy->parsedNilaiRows[0]['sub_cpmk'] = [
            ['nilai' => 56],
            ['nilai' => 56],
        ];
        $dummy->recalculateRowNilai(0);
        $this->assertEquals(56.0, $dummy->parsedNilaiRows[0]['nilai_angka']);
        $this->assertEquals(2, $dummy->parsedNilaiRows[0]['nilai_index']);
        $this->assertEquals('C', $dummy->parsedNilaiRows[0]['nilai_mutu']);

        // Test D threshold (41)
        $dummy->parsedNilaiRows[0]['sub_cpmk'] = [
            ['nilai' => 41],
            ['nilai' => 41],
        ];
        $dummy->recalculateRowNilai(0);
        $this->assertEquals(41.0, $dummy->parsedNilaiRows[0]['nilai_angka']);
        $this->assertEquals(1, $dummy->parsedNilaiRows[0]['nilai_index']);
        $this->assertEquals('D', $dummy->parsedNilaiRows[0]['nilai_mutu']);

        // Test E threshold (0)
        $dummy->parsedNilaiRows[0]['sub_cpmk'] = [
            ['nilai' => 40],
            ['nilai' => 40],
        ];
        $dummy->recalculateRowNilai(0);
        $this->assertEquals(40.0, $dummy->parsedNilaiRows[0]['nilai_angka']);
        $this->assertEquals(0, $dummy->parsedNilaiRows[0]['nilai_index']);
        $this->assertEquals('E', $dummy->parsedNilaiRows[0]['nilai_mutu']);
    }

    public function test_updated_parsed_rows_recalculates_all_rows()
    {
        $dummy = new class
        {
            use WithNilaiExcel;

            public function AuthCheck($role = null)
            {
                return true;
            }

            public function toast($text, $variant = 'success') {}

            public function dispatch($event, ...$args) {}

            public function getPage($pageName)
            {
                return 1;
            }
        };

        $dummy->parsedNilaiRows = [
            [
                'sub_cpmk' => [
                    ['nilai' => 80],
                    ['nilai' => 60],
                ],
                'nilai_angka' => 0,
                'nilai_index' => null,
                'nilai_mutu' => null,
            ],
            [
                'sub_cpmk' => [
                    ['nilai' => 50],
                ],
                'nilai_angka' => 0,
                'nilai_index' => null,
                'nilai_mutu' => null,
            ],
        ];

        $dummy->updatedParsedNilaiRows($dummy->parsedNilaiRows);

        $this->assertEquals(70.0, $dummy->parsedNilaiRows[0]['nilai_angka']);
        $this->assertEquals(2, $dummy->parsedNilaiRows[0]['nilai_index']);
        $this->assertEquals('C', $dummy->parsedNilaiRows[0]['nilai_mutu']);
        $this->assertEquals(50.0, $dummy->parsedNilaiRows[1]['nilai_angka']);
        $this->assertEquals('D', $dummy->parsedNilaiRows[1]['nilai_mutu']);
    }

    public function test_updated_parsed_rows_recalculates_all_rows_via_hook()
    {
        $dummy = new class
        {
            use WithNilaiExcel;

            public function AuthCheck($role = null)
            {
                return true;
            }

            public function toast($text, $variant = 'success') {}

            public function dispatch($event, ...$args) {}

            public function getPage($pageName)
            {
                return 1;
            }
        };

        $dummy->parsedNilaiRows = [
            [
                'sub_cpmk' => [
                    ['nilai' => 90],
                    ['nilai' => 70],
                ],
                'nilai_angka' => 0,
                'nilai_index' => null,
                'nilai_mutu' => null,
            ],
        ];

        $dummy->updatedParsedNilaiRows($dummy->parsedNilaiRows);

        $this->assertEquals(80.0, $dummy->parsedNilaiRows[0]['nilai_angka']);
        $this->assertEquals(3, $dummy->parsedNilaiRows[0]['nilai_index']);
        $this->assertEquals('B', $dummy->parsedNilaiRows[0]['nilai_mutu']);
    }

    public function test_key_normalization_matching()
    {
        $excelKey = 'Sub-CPMK-1';
        $dbKey = 'Sub-CPMK-1';

        $normalizedExcelKey = preg_replace('/[^A-Za-z0-9]/', '', $excelKey);
        $normalizedDbKey = preg_replace('/[^A-Za-z0-9]/', '', $dbKey);

        $this->assertEquals($normalizedExcelKey, $normalizedDbKey);
        $this->assertEquals('SubCPMK1', $normalizedExcelKey);
    }

    public function test_sub_cpmk_validation_error_keys()
    {
        $dummy = new class
        {
            use WithNilaiExcel;

            public function AuthCheck($role = null)
            {
                return true;
            }

            public function toast($text, $variant = 'success') {}

            public function dispatch($event, ...$args) {}

            public function getPage($pageName)
            {
                return 1;
            }

            public function callInputModalNilai($data)
            {
                return $this->inputModalNilai($data);
            }
        };

        $invalidData = [
            'nim' => '123456',
            'nama' => 'Test Mahasiswa',
            'nilai_angka' => 80,
            'sub_cpmk' => [
                [
                    'kode_scpmk' => 'SCPMK-1',
                    'nilai' => 150, // Invalid (max is 100)
                    'bobot' => 0.5,
                ],
            ],
        ];

        $this->expectException(ValidationException::class);

        try {
            $dummy->callInputModalNilai($invalidData);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $this->assertArrayHasKey('sub_cpmk.0.nilai', $errors);
            throw $e;
        }
    }
}
