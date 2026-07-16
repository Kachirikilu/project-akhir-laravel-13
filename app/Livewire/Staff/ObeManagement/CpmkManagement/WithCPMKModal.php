<?php

namespace App\Livewire\Staff\ObeManagement\CpmkManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithPagination;

trait WithCPMKModal
{
    use HasErrorCount;
    use HasToast;
    use WithPagination;

    public $selected_id_cpmk;

    public $isEditingCPMK = false;

    public $showEditCPMK = false;

    public $showCPMKModal = false;

    public $cpmk_rps_items_list = [];

    public $cpmk_rps_modal_page = 3;

    public $cpmk_rps_id;

    protected $cpmk_rps_modal_paginator;

    // public $isFlyoutCPMK = false;

    public function updatedShowCPMKModal($value)
    {
        if (! $value) {
            // $this->isFlyoutCPMK = false;
            $this->isEditingCPMK = false;
        }
        // else {
        //     $this->isFlyoutCPMK =
        //         (property_exists($this, 'showRPSModal') && $this->showRPSModal) ||
        //         (property_exists($this, 'showSCPMKModal') && $this->showSCPMKModal) ||
        //         (property_exists($this, 'showCPLModal') && $this->showCPLModal) ||
        //         (property_exists($this, 'showRefModal') && $this->showRefModal);
        // }
    }

    public function addCPMK($key = 'cpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingCPMK = false;
        $this->showCPMKModal = true;
        $this->showEditCPMK = false;

        // $this->cplNameSearch['cpmk'] = '';

        // $this->cpl_id_array[$key] = [];
        // $this->cpl_items_array[$key] = [];

        $this->ref_id_array[$key] = [];
        $this->ref_items_array[$key] = [];

        $this->updatedSCPMKNameSearch($this->scpmkNameSearch);
        $this->updatedCPLNameSearch($this->cplNameSearch);
        $this->updatedRefNameSearch($this->refNameSearch);
    }

    public function editCPMK($id, $key = 'cpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputCPMK();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_cpmk = $id;
        $this->isEditingCPMK = true;
        $this->showEditCPMK = true;

        try {
            // 1. Load data CPMK dengan relasi yang sangat lengkap
            $cpmk = CPMK::with([
                'scpmks',
                'scpmks.refs',
                'cpls',
                'refs',
                'rps',
            ])->findOrFail($id);

            // 3. MAPPING CPMK (MENGGUNAKAN FUNGSI mapCPMK ANDA)
            $this->scpmk_id_array = $cpmk->scpmks->pluck('id')->toArray();
            $this->scpmk_items_array = $cpmk->scpmks->map(function ($c) {
                return $this->itemsSCPMK($c);
            })->toArray();

            $this->scpmk_sub_items_array = $cpmk->scpmks->map(function ($s) {
                return ['scpmk' => [collect($this->mapSCPMK(collect([$s])))->first()]];
            })->toArray();

            $this->cpl_id_array = collect($this->cpl_id_array)
                ->merge($cpmk->cpls->pluck('id'))->unique()->values()->all();
            $this->cpl_items_array = collect($this->cpl_items_array)
                ->merge(
                    $cpmk->cpls->map(fn ($c) => $this->itemsCPL($c))
                )->unique('id')->values()->all();

            $this->ref_id_array = collect($this->ref_id_array)
                ->merge($cpmk->refs->pluck('id'))->unique()->values()->all();
            $this->ref_items_array = collect($this->ref_items_array)
                ->merge(
                    $cpmk->refs->map(fn ($r) => $this->itemsRef($c))
                )->unique('id')->values()->all();

            $this->updatedSCPMKNameSearch($this->scpmkNameSearch);
            $this->updatedCPLNameSearch($this->cplNameSearch);
            $this->updatedRefNameSearch($this->refNameSearch);

            $this->cpmk_rps_id = $cpmk->id;
            $this->resetPage('cpmk_rps_modal_page');
            $this->loadCPMKRPSPagination();

            $this->showCPMKModal = true;

            $this->dispatch('fill-modal-cpmk', cpmk: $cpmk);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadCPMKRPSPagination()
    {
        if (empty($this->cpmk_rps_id)) {
            return;
        }

        $cpmk = CPMK::find($this->cpmk_rps_id);

        if (! $cpmk) {
            return;
        }

        $rps = $cpmk->rps()->paginate($this->cpmk_rps_modal_page, ['*'], 'cpmk_rps_modal_page');
        $this->cpmk_rps_items_list = $this->mapRPS($rps);
        $this->cpmk_rps_modal_paginator = $rps;
    }

    public function updatedCPMKRPSModalPage($page)
    {
        $this->loadCPMKRPSPagination();
    }

    private function inputModalCPMK($isEditingCPMK, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        // 1. Ambil data Referensi yang melekat pada SCPMK terpilih saja
        $refFromScpmk = [];

        if (! empty($data['scpmk_id_array'])) {
            $scpmks = SubCPMK::with('refs')->whereIn('id', $data['scpmk_id_array'])->get();
            foreach ($scpmks as $scpmk) {
                $refFromScpmk = array_merge($refFromScpmk, $scpmk->refs?->pluck('id')->toArray() ?? []);
            }
        }

        $cleanRef = [];
        if (isset($data['ref_id_array']) && is_array($data['ref_id_array'])) {
            $cleanRef = array_values(array_diff(array_unique($data['ref_id_array']), $refFromScpmk));
        }

        $combinedCPLText = '';
        $cplIds = $data['cpl_id_array'] ?? ($data['cpl_id_array'] ?? []);

        if (! empty($cplIds)) {
            $cplDescriptions = DB::table('cpls')
                ->whereIn('id', (array) $cplIds)
                ->orderByRaw('FIELD(id, '.implode(',', (array) $cplIds).')')
                ->pluck('deskripsi');

            foreach ($cplDescriptions as $desc) {
                $desc = trim($desc);
                if (! str_ends_with($desc, '.')) {
                    $desc .= '.';
                }
                $combinedCPLText .= (empty($combinedCPLText) ? '' : ' ').$desc;
            }
        }

        if (empty($inputDeskripsi) || $inputDeskripsi === trim($combinedCPLText)) {
            $data['deskripsi'] = null;
        } else {
            $data['deskripsi'] = $this->normalizeText($data['deskripsi'] ?? '');
        }

        $rules = [
            'kode_cpmk_1' => 'required|alpha|max:10',
            'kode_cpmk_2' => 'required|numeric|min:1',
            'kode_cpmk' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingCPMK) {
                    $query = DB::table('cpmks')
                        ->where('kode_cpmk', $value);

                    if ($isEditingCPMK) {
                        $query->where('id', '!=', $this->selected_id_cpmk);
                    }

                    if ($query->exists()) {
                        $fail("Kode CPMK '$value' sudah digunakan di CPMK lain!");
                    }
                },
            ],
            'deskripsi' => 'nullable|string|min:1|max:1000',
            'scpmk_id_array' => 'required|array|min:1',
            'cpl_id_array' => 'required|array|min:1',
            'ref_id_array' => 'nullable|array',
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesCPMK());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_cpmk_1']) && empty($data['kode_cpmk_2'])) {
                $this->addError('kode_cpmk', 'Kode CPMK wajib diisi!');
            } elseif ($errors->has('kode_cpmk_1') || $errors->has('kode_cpmk_2')) {
                $combinedMessage = $errors->first('kode_cpmk_1') ?: $errors->first('kode_cpmk_2');
                $this->addError('kode_cpmk', $combinedMessage);
            }
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_cpmk_1', 'kode_cpmk_2', 'kode_cpmk'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_cpmk' && ! $this->getErrorBag()->has('kode_cpmk')) {
                    $this->addError('kode_cpmk', $messages[0]);
                }
            }
            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        $validated = $validator->validated();

        $validated['ref_id_array'] = $cleanRef;
        $validated['scpmk_id_array'] = array_values(array_unique($data['scpmk_id_array'] ?? []));
        $validated['cpl_id_array'] = array_values(array_unique($data['cpl_id_array'] ?? []));

        return $validated;
    }

    public function saveCPMK($data, $key = 'cpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['scpmk_id_array'] = $this->scpmk_id_array ?? [];
        $data['cpl_id_array'] = $this->cpl_id_array ?? [];
        // $data['cpl_id_array'] = $this->getCPLIdArrayForKey($key);
       $data['ref_id_array'] = $this->ref_id_array ?? [];

        try {
            // 1. Jalankan validasi & pembersihan
            $validated = $this->inputModalCPMK(false, $data);
            $cpmk = collect();
            // 2. Eksekusi Database
            DB::transaction(function () use ($validated, &$cpmk) {
                $cpmk = CPMK::create([
                    'kode_cpmk' => strtoupper($validated['kode_cpmk']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                // if (property_exists($this, 'showCPMKModal') && $this->showCPMKModal && $scpmk) {
                //     $this->scpmk_id_array[] = $scpmk->id;
                //     $this->scpmk_items_array[] = $this->itemsSCPMK($scpmk);
                //     $mapped = $this->mapSCPMK(collect([$scpmk]));
                //     // $this->scpmk_sub_items_array = array_merge($this->scpmk_sub_items_array, $mapped);
                // }

                // Sync Sub-CPMK (SCPMK)
                if (! empty($validated['scpmk_id_array'])) {
                    $syncData = [];
                    foreach ($validated['scpmk_id_array'] as $index => $id) {
                        $syncData[(int) $id] = ['sort_order' => $index];
                    }
                    $cpmk->scpmks()->sync($syncData);
                }

                // Sync CPL
                if (! empty($validated['cpl_id_array'])) {
                    $syncData = [];
                    foreach ($validated['cpl_id_array'] as $index => $id) {
                        $syncData[(int) $id] = ['sort_order' => $index];
                    }
                    $cpmk->cpls()->sync($syncData);
                }

                // Sync Referensi (yang sudah difilter/clean)
                if (! empty($validated['ref_id_array'])) {
                    $syncData = [];
                    foreach ($validated['ref_id_array'] as $index => $id) {
                        $syncData[(int) $id] = ['sort_order' => $index];
                    }
                    $cpmk->refs()->sync($syncData);
                }
            });

            if ($this->parent == 'rps' && $cpmk) {
                $this->dispatch('cpmk-created-rps', id: $cpmk->id);
            }

            $this->toast(message: "CPMK {$validated['kode_cpmk_1']}-{$validated['kode_cpmk_2']}");
            $this->resetInputCPMK();

            $this->dispatch('refresh-data-cpmk');
            $this->dispatch('refresh-stats-cpmk'); 
            $this->showCPMKModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function updateCPMK($data, $key = 'cpmk')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['scpmk_id_array'] = $this->scpmk_id_array ?? [];
        $data['cpl_id_array'] = $this->cpl_id_array ?? [];
        // $data['cpl_id_array'] = $this->getCPLIdArrayForKey($key);
       $data['ref_id_array'] = $this->ref_id_array ?? [];

        try {
            $validated = $this->inputModalCPMK(true, $data);

            $cpmk = CPMK::with(['rps.cpmks.scpmks'])->findOrFail($this->selected_id_cpmk);
            $selectedScpmkIds = array_values(array_unique($validated['scpmk_id_array'] ?? []));
            $selectedScpmks = SubCPMK::whereIn('id', $selectedScpmkIds)->get();

            $invalidRps = $cpmk->rps->first(function ($rps) use ($cpmk, $selectedScpmks) {
                if ($rps->is_draf != 0) {
                    return false;
                }

                $allScpmks = collect();
                foreach ($rps->cpmks as $rpsCpmk) {
                    if ($rpsCpmk->id === $cpmk->id) {
                        $allScpmks = $allScpmks->concat($selectedScpmks);
                    } else {
                        $allScpmks = $allScpmks->concat($rpsCpmk->scpmks);
                    }
                }

                $hasUTS = $allScpmks->contains(function ($item) {
                    return SubCPMK::isUTS($item->metode ?? '', $item->deskripsi ?? '');
                });

                $hasUAS = $allScpmks->contains(function ($item) {
                    return SubCPMK::isUAS($item->metode ?? '', $item->deskripsi ?? '');
                });

                $baseTotal = $allScpmks->sum(function ($item) {
                    return (float) ($item->bobot ?? 0);
                });
                $uts = $hasUTS ? 0 : 15;
                $uas = $hasUAS ? 0 : 20;
                $adjustedTotal = $baseTotal + $uts + $uas;

                return $adjustedTotal < 70 || $adjustedTotal > 200;
            });

            if ($invalidRps) {
                $this->addError('scpmk_id_array', 'Bobot tidak valid: total bobot RPS terkait harus berada di antara 70 dan 200 setelah perubahan CPMK!');
                throw ValidationException::withMessages($this->getErrorBag()->messages());
            }

            DB::transaction(function () use ($validated, $selectedScpmks) {
                $cpmk = CPMK::findOrFail($this->selected_id_cpmk);

                // 1. Update Data Utama CPMK
                $cpmk->update([
                    'kode_cpmk' => strtoupper($validated['kode_cpmk']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                // 2. Sync Sub-CPMK (SCPMK) ke Pivot
                $syncScpmk = [];
                foreach ($validated['scpmk_id_array'] as $index => $id) {
                    $syncScpmk[(int) $id] = ['sort_order' => $index];
                }
                // Pastikan nama relasi di model CPMK adalah scpmks()
                $cpmk->scpmks()->sync($syncScpmk);

                // 3. Update bobot UTS/UAS dan revisi RPS terkait
                foreach ($cpmk->rps as $rps) {
                    $allScpmks = collect();
                    foreach ($rps->cpmks as $rpsCpmk) {
                        if ($rpsCpmk->id === $cpmk->id) {
                            $allScpmks = $allScpmks->concat($selectedScpmks);
                        } else {
                            $allScpmks = $allScpmks->concat($rpsCpmk->scpmks);
                        }
                    }

                    $hasUTS = $allScpmks->contains(function ($item) {
                        return SubCPMK::isUTS($item->metode ?? '', $item->deskripsi ?? '');
                    });

                    $hasUAS = $allScpmks->contains(function ($item) {
                        return SubCPMK::isUAS($item->metode ?? '', $item->deskripsi ?? '');
                    });

                    $updateData = [];
                    if ($hasUTS) {
                        if ($rps->bobot_uts !== null) {
                            $updateData['bobot_uts'] = null;
                        }
                    } elseif ($rps->bobot_uts !== 15) {
                        $updateData['bobot_uts'] = 15;
                    }

                    if ($hasUAS) {
                        if ($rps->bobot_uas !== null) {
                            $updateData['bobot_uas'] = null;
                        }
                    } elseif ($rps->bobot_uas !== 20) {
                        $updateData['bobot_uas'] = 20;
                    }

                    if ($rps->is_draf == 0) {
                        $updateData['revisi'] = now();
                    }

                    if (! empty($updateData)) {
                        $rps->update($updateData);
                    }
                }

                // 4. Sync CPL (Manual/Tambahan)
                $syncCpl = [];
                foreach ($validated['cpl_id_array'] as $index => $id) {
                    $syncCpl[(int) $id] = ['sort_order' => $index];
                }
                $cpmk->cpls()->sync($syncCpl);

                // 5. Sync Referensi (Manual/Tambahan)
                $syncRef = [];
                foreach ($validated['ref_id_array'] as $index => $id) {
                    $syncRef[(int) $id] = ['sort_order' => $index];
                }
                $cpmk->refs()->sync($syncRef);
            });

            $this->toast(message: "CPMK {$validated['kode_cpmk_1']}-{$validated['kode_cpmk_2']}", type: 'update');
            $this->resetInputCPMK();

            $this->showCPMKModal = false;
            $this->dispatch('refresh-data-cpmk');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-cpmk');
            $this->showCPMKModal = false;
        }
    }

    private function validationMessagesCPMK()
    {
        return [

            'kode_cpmk_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_cpmk_1.alpha' => 'Kode awalan harus berupa huruf!',
            'kode_cpmk_1.max' => 'Kode awalan terlalu panjang!',

            // Kode CPMK Bagian 2 (Angka - Kanan)
            'kode_cpmk_2.required' => 'Nomor Kode (input kanan) wajib diisi!',
            'kode_cpmk_2.numeric' => 'Nomor Kode harus berupa angka!',
            'kode_cpmk_2.min' => 'Nomor Kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_cpmk.required' => 'Kode CPMK lengkap wajib terbentuk!',
            'kode_cpmk.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_cpmk.required' => 'Kode CPMK wajib diisi!',
            'kode_cpmk.alpha_num' => 'Kode CPMK hanya boleh berisi mutu dan angka!',
            'kode_cpmk.max' => 'Kode CPMK maksimal 20 karakter!',

            // Deskripsi & Status
            'deskripsi.required' => 'Deskripsi CPMK wajib diisi!',
            'deskripsi.string' => 'Deskripsi CPMK harus berupa text!',
            'deskripsi.min' => 'Deskripsi CPMK terlalu pendek (Minimal 5 karakter)!',
            'deskripsi.max' => 'Deskripsi CPMK terlalu panjang (Maksimal 1000 karakter)!',

            // CPMK & Relasi Data
            'scpmk_id_array.required' => 'Minimal pilih satu Sub-CPMK untuk CPMK ini!',
            'scpmk_id_array.array' => 'Format data Sub-CPMK tidak valid!',
            'scpmk_id_array.min' => 'Minimal harus ada satu Sub-CPMK yang dipilih!',

            'cpl_id_array.required' => 'Minimal pilih satu CPL untuk CPMK ini!',
            'cpl_id_array.array' => 'Format data CPL tidak valid!',
            'cpl_id_array.min' => 'Minimal harus ada satu CPL yang dipilih!',

            'ref_id_array.array' => 'Format data Referensi tidak valid!',
        ];
    }

    public function getCPMKErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'kode_cpmk',
                'cpl_id_array',
                'deskripsi',
            ]),
            2 => $this->getErrorCount([
                'scpmk_id_array',
            ]),
            3 => $this->getErrorCount([
                'ref_id_array',
            ]),
            4 => $this->getErrorCount([
            ]),
        ];
    }

    private function resetInputCPMK()
    {
        $this->scpmkNameSearch = '';
        $this->cplNameSearch = '';
        // $this->cplNameSearch = array_map(fn () => '', $this->cplNameSearch);
        $this->refNameSearch = '';

        $this->scpmk_id_array = [];
        $this->scpmk_items_array = [];
        $this->scpmk_sub_items_array = [];

        $this->cpl_id_array = [];
        $this->cpl_items_array = [];

        // $this->cpl_id_array = array_map(fn () => [], $this->cpl_id_array);
        // $this->cpl_items_array = array_map(fn () => [], $this->cpl_items_array);

        $this->ref_id_array = array_map(fn () => [], $this->ref_id_array);
        $this->ref_items_array = array_map(fn () => [], $this->ref_items_array);

        $this->resetErrorBag();
    }
}
