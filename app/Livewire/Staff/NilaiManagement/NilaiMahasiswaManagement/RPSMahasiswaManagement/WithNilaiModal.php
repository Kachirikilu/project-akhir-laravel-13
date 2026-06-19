<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RPSMahasiswaManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait WithNilaiModal
{
    use HasErrorCount;
    use HasToast;

    public $showEditNilai = false;

    public function updateNilaiMahasiswa($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $selectedIdNilai = $data['nilai_mahasiswa_id'] ?? null;

        if (empty($selectedIdNilai)) {
            $this->toast(
                message: 'Data Nilai Mahasiswa tidak ditemukan!',
                type: 'error',
                variant: 'danger'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. Ambil Data
        |--------------------------------------------------------------------------
        */

        $nilai_mahasiswa = NilaiMahasiswa::find($selectedIdNilai);

        if (! $nilai_mahasiswa) {
            $this->toast(
                message: 'Data Nilai Mahasiswa tidak ditemukan!',
                type: 'error',
                variant: 'danger'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Validasi Dinamis
        |--------------------------------------------------------------------------
        */

        $rules = [];
        $messages = [];

        for ($i = 1; $i <= 16; $i++) {

            if (! array_key_exists("nilai_$i", $data)) {
                continue;
            }

            $rules["nilai_$i"] = [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ];

            $messages["nilai_$i.required"] = "Nilai ke-$i wajib diisi.";
            $messages["nilai_$i.numeric"] = "Nilai ke-$i harus berupa angka.";
            $messages["nilai_$i.min"] = "Nilai ke-$i minimal 0.";
            $messages["nilai_$i.max"] = "Nilai ke-$i maksimal 100.";
        }

        $validator = Validator::make(
            $data,
            $rules,
            $messages
        );

        if ($validator->fails()) {

            $this->resetValidation();

            foreach ($validator->errors()->messages() as $field => $error) {

                if (preg_match('/nilai_(\d+)/', $field, $match)) {

                    $index = $match[1];

                    $this->addError(
                        "nilai_{$index}",
                        $error[0]
                    );
                }
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Susun Nilai Array
        |--------------------------------------------------------------------------
        */

        $nilaiArray = [];

        for ($i = 1; $i <= 16; $i++) {

            if (! array_key_exists("nilai_$i", $data)) {
                continue;
            }

            $nilaiArray[] = (float) $data["nilai_$i"];
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Hitung Nilai Akhir
        |--------------------------------------------------------------------------
        */

        $bobotArray = $nilai_mahasiswa->bobot_array ?? [];
        $nilaiAkhir = 0;
        foreach ($nilaiArray as $index => $nilai) {
            $bobot = (float) ($bobotArray[$index] ?? 0);
            $nilaiAkhir += $nilai * $bobot;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Simpan
        |--------------------------------------------------------------------------
        */

        try {

            DB::beginTransaction();

            $nilai_mahasiswa->nilai_array = $nilaiArray;
            $nilai_mahasiswa->nilai = round($nilaiAkhir, 2);

            $nilai_mahasiswa->save();

            DB::commit();

            $this->dispatch('refresh-data-nilai');
            $this->resetInputNilaiMahasiswa();
            $this->showEditNilai = false;

            $this->toast(
                message: 'Nilai Mahasiswa berhasil diperbarui!',
                type: 'update'
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            $this->toast(
                message: 'Gagal menyimpan nilai: '.$e->getMessage(),
                type: 'error',
                variant: 'danger'
            );
        }
    }

    public function getNilaiErrorSections()
    {
        return [
            1 => $this->getNilaiErrorCountByIndexes(0, 3),
            2 => $this->getNilaiErrorCountByIndexes(4, 7),
            3 => $this->getNilaiErrorCountByIndexes(8, 11),
            4 => $this->getNilaiErrorCountByIndexes(12, 99),
        ];
    }

    private function getNilaiErrorCountByIndexes($start, $end)
    {
        $errors = $this->getErrorBag()->messages();
        $count = 0;

        for ($i = $start; $i <= $end; $i++) {
            $field = "nilai_$i";
            if (isset($errors[$field])) {
                $count += count($errors[$field]);
            }
        }

        return $count;
    }

    private function resetInputNilaiMahasiswa()
    {
        $this->resetErrorBag();
    }
}
