<?php

namespace App\Livewire\Staff\CPMKManagement;

use App\Livewire\Global\HasToast;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SCPMK;
use App\Models\Akademik\SubCPMK;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithPagination;

trait WithSubCPMKModal
{
    use HasToast;
    use WithPagination;

    public $selected_id_scpmk;

    public $isEditingSCPMK = false;

    public $showEditSCPMK = false;

    public $showSCPMKModal = false;

    public $scpmk_rps_items_list = [];

    public $scpmk_rps_modal_page = 3;

    public $scpmk_rps_id;

    protected $scpmk_rps_modal_paginator;

    public function addSCPMK($key = 'scpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditSCPMK == true) {
            $this->resetInputSCPMK();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingSCPMK = false;
        $this->showSCPMKModal = true;
        $this->showEditSCPMK = false;

        $this->refNameSearch['scpmk'] = '';

        $this->ref_id_array[$key] = [];
        $this->ref_items_array[$key] = [];

        $this->updatedRefNameSearch($this->getRefNameSearchForKey($key), 'refNameSearch.'.$key);
    }

    public function editSCPMK($id, $key = 'scpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputSCPMK();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_scpmk = $id;
        $this->isEditingSCPMK = true;
        $this->showEditSCPMK = true;

        try {
            $scpmk = SubCPMK::with([
                'refs',
                'cpmks.rps',
            ])->findOrFail($id);

            $this->ref_id_array = array_merge(
                array_map(fn () => [], $this->ref_id_array),
                [$key => $scpmk->refs->pluck('id')->toArray()]
            );
            $this->ref_items_array = array_merge(
                array_map(fn () => [], $this->ref_items_array),
                [$key => $scpmk->refs->map(function ($c) {
                    return $this->itemsRef($c);
                })->toArray()]
            );

            $this->updatedRefNameSearch($this->getRefNameSearchForKey($key), 'refNameSearch.'.$key);

            $this->scpmk_rps_id = $scpmk->id;
            $this->resetPage('scpmk_rps_modal_page');
            $this->loadSCPMKRPSPagination();

            $this->showSCPMKModal = true;

            $this->dispatch('fill-modal-scpmk', scpmk: $scpmk);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadSCPMKRPSPagination()
    {
        if (empty($this->scpmk_rps_id)) {
            return;
        }

        $scpmk = SubCPMK::find($this->scpmk_rps_id);

        if (! $scpmk) {
            return;
        }

        $rps = RPS::whereHas('cpmks.scpmks', function ($query) use ($scpmk) {
            $query->where('sub_cpmks.id', $scpmk->id);
        })->paginate($this->scpmk_rps_modal_page, ['*'], 'scpmk_rps_modal_page');

        $this->scpmk_rps_items_list = $this->mapRPS($rps);
        $this->scpmk_rps_modal_paginator = $rps;
    }

    public function updatedSCPMKRPSModalPage($page)
    {
        $this->loadSCPMKRPSPagination();
    }

    private function inputModalSCPMK($isEditingSCPMK, $data)
    {
        $rules = [
            'kode_scpmk_1' => 'required|alpha|max:10',
            'kode_scpmk_2' => 'required|numeric|min:1',
            'kode_scpmk' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingSCPMK) {
                    $query = DB::table('sub_cpmks')
                        ->where('kode_scpmk', $value);

                    if ($isEditingSCPMK) {
                        $query->where('id', '!=', $this->selected_id_scpmk);
                    }

                    if ($query->exists()) {
                        $fail("Kode SCPMK '$value' sudah digunakan di Sub-CPMK lain!");
                    }
                },
            ],
            'deskripsi' => 'required|max:1000',
            'materi' => 'required|max:1000',
            'metodologi' => 'required|max:1000',
            'indikator' => 'required|max:1000',
            'metode' => 'required|in:Teori,Aktivitas Partisipasif,Tugas,Mandiri,UTS,UAS,Kuis,Laporan Akhir,Hasil Proyek,Skripsi,Kerja Praktek,Responsi,Logbook,Portofolio',
            'deskripsi_tugas' => 'nullable|max:1000',
            'waktu_tugas' => 'nullable|integer|min:60',
            'waktu_mandiri' => 'nullable|integer|min:60',
            'bobot' => 'required|numeric|min:0.5',
            'ref_id_array' => 'required|array|min:1',
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesSCPMK());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_scpmk_1']) && empty($data['kode_scpmk_2'])) {
                $this->addError('kode_scpmk', 'Kode Sub-CPMK wajib diisi!');
            } elseif ($errors->has('kode_scpmk_1') || $errors->has('kode_scpmk_2')) {
                $combinedMessage = $errors->first('kode_scpmk_1') ?: $errors->first('kode_scpmk_2');
                $this->addError('kode_scpmk', $combinedMessage);
            }
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_scpmk_1', 'kode_scpmk_2', 'kode_scpmk'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_scpmk' && ! $this->getErrorBag()->has('kode_scpmk')) {
                    $this->addError('kode_scpmk', $messages[0]);
                }
            }
            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        $validated = $validator->validated();

        return $validated;
    }

    public function saveSCPMK($data, $key = 'scpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (empty($data['metode'])) {
            $data['metode'] = 'Teori';
        }

        $data['ref_id_array'] = $this->getRefIdArrayForKey($key);

        try {
            // 1. Jalankan validasi & pembersihan
            $validated = $this->inputModalSCPMK(false, $data);

            // 2. Eksekusi Database
            DB::transaction(function () use ($validated) {
                $scpmk = SubCPMK::create([
                    'kode_scpmk' => strtoupper($validated['kode_scpmk']),
                    'deskripsi' => $validated['deskripsi'],
                    'materi' => $validated['materi'],
                    'metodologi' => $validated['metodologi'],
                    'indikator' => $validated['indikator'],
                    'metode' => $validated['metode'],
                    'deskripsi_tugas' => $validated['deskripsi_tugas'],
                    'waktu_tugas' => $validated['waktu_tugas'],
                    'waktu_mandiri' => $validated['waktu_mandiri'],
                    'bobot' => $validated['bobot'],
                ]);

                if ($this->showRPSModal && $scpmk) {
                    $this->scpmk_id_array[] = $scpmk->id;
                    $this->scpmk_items_array[] = $this->itemsSCPMK($scpmk);
                    $mapped = $this->mapSCPMK(collect([$scpmk]));
                    $this->scpmk_sub_items_array = array_merge($this->scpmk_sub_items_array, $mapped);
                }

                // Sync Referensi (yang sudah difilter/clean)
                if (! empty($validated['ref_id_array'])) {
                    $syncData = [];
                    foreach ($validated['ref_id_array'] as $index => $id) {
                        $syncData[(int) $id] = ['sort_order' => $index];
                    }
                    $scpmk->refs()->sync($syncData);
                }
            });

            $this->toast(message: "Sub-CPMK {$validated['kode_scpmk_1']}-{$validated['kode_scpmk_2']} berhasil disimpan!");
            $this->resetInputSCPMK();
            $this->dispatch('refresh-data-scpmk');
            $this->showSCPMKModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal', variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function updateSCPMK($data, $key = 'scpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (empty($data['metode'])) {
            $data['metode'] = 'Teori';
        }

        $data['ref_id_array'] = $this->getRefIdArrayForKey($key);

        try {
            $validated = $this->inputModalSCPMK(true, $data);

            DB::transaction(function () use ($validated) {
                $scpmk = SubCPMK::findOrFail($this->selected_id_scpmk);

                // 1. Identifikasi kondisi kata kunci SEBELUM update
                $beforeText = strtoupper($scpmk->deskripsi);
                $hadUTS = str_contains($beforeText, 'UTS');
                $hadUAS = false;
                foreach (['UAS', 'LAPORAN AKHIR', 'HASIL PROJEK', 'HASIL PROYEK'] as $s) {
                    if (str_contains($beforeText, $s)) {
                        $hadUAS = true;
                        break;
                    }
                }

                // 2. Update Data Utama SCPMK
                $scpmk->update([
                    'kode_scpmk' => strtoupper($validated['kode_scpmk']),
                    'deskripsi' => $validated['deskripsi'],
                    'materi' => $validated['materi'],
                    'metodologi' => $validated['metodologi'],
                    'indikator' => $validated['indikator'],
                    'metode' => $validated['metode'],
                    'deskripsi_tugas' => $validated['deskripsi_tugas'],
                    'waktu_tugas' => $validated['waktu_tugas'] ?: null,
                    'waktu_mandiri' => $validated['waktu_mandiri'] ?: null,
                    'bobot' => (float) ($validated['bobot'] ?: 0),
                ]);

                // 3. Identifikasi kondisi kata kunci SESUDAH update
                $afterText = strtoupper($scpmk->deskripsi);
                $hasUTS = str_contains($afterText, 'UTS');
                $hasUAS = false;
                foreach (['UAS', 'Laporan Akhir', 'Hasil Proyek', 'Hasil Projek'] as $s) {
                    if (str_contains($afterText, $s)) {
                        $hasUAS = true;
                        break;
                    }
                }

                // 4. Cari RPS Terkait
                $relatedRps = RPS::whereHas('cpmks.scpmks', function ($query) use ($scpmk) {
                    $query->where('sub_cpmks.id', $scpmk->id);
                })->get();

                foreach ($relatedRps as $rps) {
                    $updateData = [];
                    if ($hasUTS && ! $hadUTS) {
                        $updateData['bobot_uts'] = 0;
                    }
                    elseif (! $hasUTS && $hadUTS) {
                        $updateData['bobot_uts'] = 15;
                    }
                    if ($hasUAS && ! $hadUAS) {
                        $updateData['bobot_uas'] = 0;
                    } elseif (! $hasUAS && $hadUAS) {
                        $updateData['bobot_uas'] = 20;
                    }
                    if ($rps->is_draf == 0) {
                        $updateData['revisi'] = now();
                    }
                    if (! empty($updateData)) {
                        $rps->update($updateData);
                    }
                }

                $syncRef = [];
                foreach ($validated['ref_id_array'] as $index => $id) {
                    $syncRef[(int) $id] = ['sort_order' => $index];
                }
                $scpmk->refs()->sync($syncRef);
            });

            $this->toast(message: 'Sub-CPMK Berhasil diperbarui', type: 'update');
            $this->showSCPMKModal = false;
            $this->dispatch('refresh-data-scpmk');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-scpmk');
            $this->showSCPMKModal = false;
        }
    }

    private function validationMessagesSCPMK()
    {
        return [
            'kode_scpmk_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_scpmk_1.alpha' => 'Kode awalan harus berupa huruf!',
            'kode_scpmk_1.max' => 'Kode awalan terlalu panjang!',

            // Kode SCPMK Bagian 2 (Angka - Kanan)
            'kode_scpmk_2.required' => 'Nomor kode (input kanan) wajib diisi!',
            'kode_scpmk_2.numeric' => 'Nomor kode harus berupa angka!',
            'kode_scpmk_2.min' => 'Nomor kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_scpmk.required' => 'Kode Sub-CPMK lengkap wajib terbentuk!',
            'kode_scpmk.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_scpmk.required' => 'Kode Sub-CPMK wajib diisi!',
            'kode_scpmk.alpha_num' => 'Kode Sub-CPMK hanya boleh berisi huruf dan angka!',
            'kode_scpmk.max' => 'Kode Sub-CPMK maksimal 20 karakter!',

            // Deskripsi & Status
            'deskripsi.required' => 'Deskripsi Sub-CPMK wajib diisi!',
            'deskripsi.max' => 'Deskripsi Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',
            'materi.required' => 'Materi Sub-CPMK wajib diisi!',
            'materi.max' => 'Materi Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',
            'metodologi.required' => 'Metodologi Sub-CPMK wajib diisi!',
            'metodologi.max' => 'Metodologi Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',
            'indikator.required' => 'Indikator Sub-CPMK wajib diisi!',
            'indikator.max' => 'Indikator Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',

            'metode.required' => 'Metode wajib dipilih!',
            'metode.in' => 'Pilih Metode yang telah tersedia!',

            'deskripsi_tugas.max' => 'Deskripsi tugas terlalu panjang (Maksimal 1000 karakter)!',

            'waktu_tugas.integer' => 'Waktu tugas harus berupa angka!',
            'waktu_tugas.min' => 'Waktu tugas minimal 60 menit!',

            'waktu_mandiri.integer' => 'Waktu mandiri harus berupa angka!',
            'waktu_mandiri.min' => 'Waktu mandiri minimal 60 menit!',

            'bobot.required' => 'Bobot penilaian wajib diisi!',
            'bobot.numeric' => 'Bobot harus berupa angka desimal!',
            'bobot.min' => 'Bobot minimal bernilai 0.5!',

            'ref_id_array.required' => 'Minimal pilih satu Referensi untuk Sub-CPMK ini!',
            'ref_id_array.array' => 'Format data Referensi tidak valid!',
            'ref_id_array.min' => 'Minimal harus ada satu Referensi yang dipilih!',
        ];
    }

    private function resetInputSCPMK()
    {
        $this->refNameSearch = array_map(fn () => '', $this->refNameSearch);

        $this->ref_id_array = array_map(fn () => [], $this->ref_id_array);
        $this->ref_items_array = array_map(fn () => [], $this->ref_items_array);

        $this->resetErrorBag();
    }
}
