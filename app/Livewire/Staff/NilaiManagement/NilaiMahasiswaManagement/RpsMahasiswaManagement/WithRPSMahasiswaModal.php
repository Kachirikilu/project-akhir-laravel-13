<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RpsMahasiswaManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait WithRPSMahasiswaModal
{
    use HasErrorCount;
    use HasToast;

    public $showModalNilai = false;

    public $nilai_input = [
        'list_nilai_array' => [],
        'list_cpmk_array' => [],
    ];

    public $selected_id_nilai;

    public function editNilaiMahasiswa($id)
    {
        $this->resetInputNilaiMahasiswa();
        $this->resetValidation();
        $this->resetErrorBag();

        if (empty($id)) {
            $this->toast(message: 'Data Nilai Mahasiswa tidak ditemukan', type: 'unfound', variant: 'danger');

            return;
        }

        $nilai_mahasiswa = NilaiMahasiswa::with(['rps_rel.cpmks.cpls'])->find($id);

        if (! $nilai_mahasiswa) {
            $this->toast(message: 'Nilai Mahasiswa tidak ditemukan', type: 'unfound', variant: 'danger');

            return;
        }

        $nilaiArray = $nilai_mahasiswa->nilai_array ?? [];
        $scpmkList = collect($nilai_mahasiswa->rps_rel?->scpmk_atr ?? []);

        $this->nilai_input['list_nilai_array'] = $scpmkList->map(function ($item, $index) use ($nilaiArray) {
            $nilaiRaw = $nilaiArray[$index] ?? null;
            $nilaiFinal = ($nilaiRaw === null || $nilaiRaw === '') ? 0 : (float) $nilaiRaw;

            $metode = data_get($item, 'metode', '---');
            $kodeScpmk = data_get($item, 'kode') ?? data_get($item, 'kode_scpmk', '---');
            $kodeCpmk = data_get($item, 'kode_cpmk', '---');
            $cpmkId = data_get($item, 'cpmk_id');

            $rawBobot = (float) data_get($item, 'bobot_normalisasi', 0);

            $bobotPersen = $rawBobot > 1
                ? round($rawBobot, 2).'%'
                : round($rawBobot * 100, 2).'%';

            return [
                'metode' => $metode ?: '---',
                'kode_scpmk' => $kodeScpmk ?: '---',
                'kode_cpmk' => $kodeCpmk ?: '---',
                'cpmk_id' => $cpmkId,
                'nilai' => $nilaiFinal,
                'bobot' => $rawBobot,
                'bobot_text' => $bobotPersen,
            ];
        })->toArray();

        $groupedByCpmk = collect($this->nilai_input['list_nilai_array'])->groupBy('kode_cpmk');
        $rpsCpmks = $nilai_mahasiswa->rps_rel?->cpmks;

        $cpmkLookup = collect($rpsCpmks)->flatMap(function ($cpmk) {
            $keys = array_filter([
                $cpmk->kode_cpmk ?? null,
                $cpmk->kode ?? null,
                $cpmk->id ?? null,
            ]);

            $map = [];
            foreach ($keys as $k) {
                $map[$k] = $cpmk;
            }

            return $map;
        });

        // 4. Map ke $this->nilai_input['list_cpmk_array']
        $this->nilai_input['list_cpmk_array'] = $groupedByCpmk->map(function ($itemsInCpmk, $kodeCpmk) use ($cpmkLookup) {
            $rawTotalBobot = (float) $itemsInCpmk->sum('bobot');
            $totalBobotDesimal = $rawTotalBobot > 1 ? ($rawTotalBobot / 100) : $rawTotalBobot;

            $nilaiMurni = $itemsInCpmk->sum(function ($sub) {
                $nilai = (float) ($sub['nilai'] ?? 0);
                $bobot = (float) ($sub['bobot'] ?? 0);
                $bobotDesimal = $bobot > 1 ? ($bobot / 100) : $bobot;

                return $nilai * $bobotDesimal;
            });

            if ($totalBobotDesimal > 0) {
                $rawKontribusi = ($nilaiMurni / $totalBobotDesimal);
                $nilaiKontribusi = min(100, max(0, round($rawKontribusi, 2)));
            } else {
                $nilaiKontribusi = 0;
            }

            $cpmkIdVal = $itemsInCpmk->pluck('cpmk_id')->filter()->first();
            $cpmkModel = $cpmkLookup->get($cpmkIdVal) ?? $cpmkLookup->get($kodeCpmk);
            $cplsData = $cpmkModel?->cpls ?? collect([$cpmkModel?->cpl])->filter();

            return [
                'cpmk_id' => $cpmkModel?->id ?? $cpmkIdVal,
                'kode_cpmk' => $cpmkModel?->kode_cpmk ?? $cpmkModel?->kode ?? $kodeCpmk,
                'bobot_cpmk' => round($totalBobotDesimal * 100, 2).'%',
                'nilai_murni' => round($nilaiMurni, 2),
                'nilai_kontribusi' => $nilaiKontribusi,
                'deskripsi' => $cpmkModel?->deskripsi_cpl ?? $cpmkModel?->deskripsi ?? $cpmkModel?->cpl?->deskripsi ?? '---',
                'cpls' => $cplsData->values()->toArray(),
            ];
        })->values()->toArray();

        $this->selected_id_nilai = $id;
        $this->showModalNilai = true;
        $this->dispatch('refresh-component');
    }

    public function updateNilaiMahasiswa()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $id = $this->selected_id_nilai ?? null;

        if (empty($id)) {
            $this->toast(
                message: 'Data Nilai Mahasiswa tidak ditemukan!',
                type: 'unfound',
                variant: 'danger'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. Ambil Data Model
        |--------------------------------------------------------------------------
        */
        $nilai_mahasiswa = NilaiMahasiswa::find($id);

        if (! $nilai_mahasiswa) {
            $this->toast(
                message: 'Data Nilai Mahasiswa tidak ditemukan!',
                type: 'unfound',
                variant: 'danger'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Validasi Dinamis berbasis Livewire Array ($this->nilai_input)
        |--------------------------------------------------------------------------
        */
        $listNilai = $this->nilai_input['list_nilai_array'] ?? [];

        $rules = [
            'list_nilai_array' => 'required|array|min:1',
            'list_nilai_array.*.nilai' => 'required|numeric|min:0|max:100',
        ];

        $messages = [
            'list_nilai_array.*.nilai.required' => 'Nilai evaluasi wajib diisi.',
            'list_nilai_array.*.nilai.numeric' => 'Nilai evaluasi harus berupa angka.',
            'list_nilai_array.*.nilai.min' => 'Nilai evaluasi minimal 0.',
            'list_nilai_array.*.nilai.max' => 'Nilai evaluasi maksimal 100.',
        ];

        // Menyesuaikan penamaan error message agar dinamis per nomor evaluasi
        foreach ($listNilai as $index => $item) {
            $ke = $index + 1;
            $messages["list_nilai_array.{$index}.nilai.required"] = "Nilai evaluasi ke-{$ke} wajib diisi.";
            $messages["list_nilai_array.{$index}.nilai.numeric"] = "Nilai evaluasi ke-{$ke} harus berupa angka.";
            $messages["list_nilai_array.{$index}.nilai.min"] = "Nilai evaluasi ke-{$ke} minimal 0.";
            $messages["list_nilai_array.{$index}.nilai.max"] = "Nilai evaluasi ke-{$ke} maksimal 100.";
        }

        $validator = Validator::make(
            $this->nilai_input,
            $rules,
            $messages
        );

        if ($validator->fails()) {
            $this->resetValidation();
            foreach ($validator->errors()->messages() as $field => $errorMessages) {
                $this->addError($field, $errorMessages[0]);
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Susun Array Nilai Murni (0-Based Index)
        |--------------------------------------------------------------------------
        */
        $nilaiArray = collect($listNilai)->pluck('nilai')->map(fn ($v) => (float) $v)->toArray();

        /*
        |--------------------------------------------------------------------------
        | 4. Hitung Nilai Akhir Berdasarkan Bobot Murni
        |--------------------------------------------------------------------------
        */
        // Ambil bobot dari model (atau dari rps_rel->scpmk_atr)
        $bobotArray = $nilai_mahasiswa->bobot_array ?? [];

        // Fallback jika bobot_array di model kosong, ambil dari scpmk_atr rps_rel
        if (empty($bobotArray) && isset($nilai_mahasiswa->rps_rel?->scpmk_atr)) {
            $bobotArray = collect($nilai_mahasiswa->rps_rel->scpmk_atr)
                ->pluck('bobot_normalisasi')
                ->toArray();
        }

        $nilaiAkhir = 0;
        foreach ($nilaiArray as $index => $nilai) {
            $bobot = (float) ($bobotArray[$index] ?? 0);
            $nilaiAkhir += $nilai * $bobot;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Simpan ke Database
        |--------------------------------------------------------------------------
        */
        try {
            DB::beginTransaction();

            $nilai_mahasiswa->forceFill([
                'nilai_array' => $nilaiArray,
                'nilai' => round($nilaiAkhir, 2),
            ]);
            $nilai_mahasiswa->save();

            DB::commit();

            $this->resetInputNilaiMahasiswa();
            $this->dispatch('refresh-data-rps-mahasiswa');
            // $this->dispatch('modal-close', name: 'rps-mahasiswa-modal');

            $this->showModalNilai = false;

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
            1 => 0,
            2 => $this->getNilaiErrorCountByIndexes(0, 3),
            3 => $this->getNilaiErrorCountByIndexes(4, 7),
            4 => $this->getNilaiErrorCountByIndexes(8, 11),
            5 => $this->getNilaiErrorCountByIndexes(12, 99),
        ];
    }

    private function getNilaiErrorCountByIndexes($start, $end)
    {
        $errors = $this->getErrorBag()->messages();
        $count = 0;

        for ($i = $start; $i <= $end; $i++) {
            $prefix = "list_nilai_array.{$i}.";
            if (isset($errors[$prefix.'nilai'])) {
                $count += count($errors[$prefix.'nilai']);
            }
        }

        return $count;
    }

    private function resetInputNilaiMahasiswa()
    {
        $fields = [
            'selected_id_nilai',
        ];
        $this->nilai_input['list_nilai_array'] = [];
        $this->nilai_input['list_cpmk_array'] = [];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
