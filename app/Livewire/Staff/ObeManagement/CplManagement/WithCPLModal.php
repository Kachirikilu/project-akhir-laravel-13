<?php

namespace App\Livewire\Staff\ObeManagement\CplManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Akademik\CPL;
use App\Models\Akademik\Departemen;
use App\Models\Akademik\Fakultas;
use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithPagination;

trait WithCPLModal
{
    use HasErrorCount;
    use HasToast;
    use WithPagination;

    public $selected_id_cpl;

    public $isEditingCPL = false;

    public $showEditCPL = false;

    public $showCPLModal = false;

    public $showCPLRPSModal = false;

    public $cpl_rps_items_list = [];

    public $cpl_rps_modal_page = 3;

    public $cpl_rps_id;

    protected $cpl_rps_modal_paginator;

    // public $isFlyoutCPL = false;

    public $cplType = '';

    public function updatedShowCPLModal($value)
    {
        if (! $value) {
            // $this->isFlyoutCPL = false;
            $this->isEditingCPL = false;
        }
        // else {
        //     $this->isFlyoutCPL =
        //         (property_exists($this, 'showRPSModal') && $this->showRPSModal) ||
        //         (property_exists($this, 'showCPMKModal') && $this->showCPMKModal) ||
        //         (property_exists($this, 'showRefModal') && $this->showRefModal);
        // }
    }

    public function addCPL($tingkatan)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditCPL == true) {
            $this->resetInputCPL();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingCPL = false;
        $this->cplType = $tingkatan;

        $this->showCPLModal = true;
        $this->showRPSCPLModal = true;
        $this->showEditCPL = false;

        if ($tingkatan == 1 || $tingkatan == 4) {
            $this->updatedPrNameSearch($this->prNameSearch);
        } elseif ($tingkatan == 2) {
            $this->updatedDpNameSearch($this->dpNameSearch);
        } elseif ($tingkatan == 3) {
            $this->updatedFkNameSearch($this->fkNameSearch);
        }
    }

    public function editCPL($id, $tingkatan, $isRPS = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputCPL();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_cpl = $id;
        $this->isEditingCPL = true;
        $this->cplType = $tingkatan;
        $this->showEditCPL = true;

        // $this->showCPLModal = true;
        // $this->dispatch('refresh-component');

        if ($isRPS) {
            $this->showCPLRPSModal = true;
            $this->showCPLModal = false;
        } else {
            $this->showCPLModal = true;
            $this->showCPLRPSModal = false;
        }

        try {
            // 1. Load data CPL dengan relasi yang sangat lengkap
            $cpl = CPL::with([
                // 'rps',
                'cpmks.rps',
                'prodis',
                'prodis.dp_rel',
                'prodis.dp_rel.fk_rel',
            ])->findOrFail($id);

            if (! $isRPS) {
                $this->pr_id_array = $cpl->prodis->pluck('id')->toArray();
                foreach ($cpl->prodis as $pr) {
                    $this->pr_items_array[] = $this->itemsPr($pr);
                }

                // $this->dispatch('refresh-component');

                $firstProdi = $cpl->prodis->first();

                // dd($firstProdi);

                if ($firstProdi) {
                    if ($tingkatan == 2) {
                        $this->dp_id = $firstProdi->dp_id;
                        if ($firstProdi->dp_rel) {
                            $this->dp_items = $this->itemsDp($firstProdi->dp_rel);
                            $this->dpNameSearch = $firstProdi->departemen_dp;
                        }
                    }
                    if ($tingkatan == 3) {
                        $this->fk_id = $firstProdi->fk_id;
                        if ($firstProdi->dp_rel?->fk_rel) {
                            $this->fk_items = $this->itemsFk($firstProdi->dp_rel->fk_rel);
                            $this->fkNameSearch = $firstProdi->fakultas_fk;
                        }
                    }
                    if (in_array($tingkatan, [1, 4])) {
                        $this->pr_id = $firstProdi->id;
                    }
                    if ($tingkatan == 1) {
                        $this->prNameSearch = $firstProdi->prodi;
                        $this->fetchPr($this->prNameSearch);
                    }
                }

                if ($tingkatan == 4) {
                    $this->updatedPrNameSearch($this->prNameSearch);
                } elseif ($tingkatan == 2) {
                    $this->updatedDpNameSearch($this->dpNameSearch);
                    $this->fetchDp();
                } elseif ($tingkatan == 3) {
                    $this->updatedFkNameSearch($this->fkNameSearch);
                    $this->fetchFk();
                }
            }

            $this->cpl_rps_id = $cpl->id;
            $this->cpl_rps_items_list = [];
            $this->cpl_rps_modal_paginator = null;
            $this->resetPage('cpl_rps_modal_page');
            $this->loadCPLRPSPagination();

            $this->dispatch('fill-modal-cpl', cpl: $cpl);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadCPLRPSPagination()
    {
        if (empty($this->cpl_rps_id)) {
            return;
        }

        $cpl = CPL::find($this->cpl_rps_id);

        if (! $cpl) {
            return;
        }

        $rpsQuery = RPS::query()
            // ->whereHas('cpls', function ($query) use ($cpl) {
            //     $query->where('cpls.id', $cpl->id);
            // })
            ->whereHas('cpmks', function ($query) use ($cpl) {
                $query->whereHas('cpls', function ($inner) use ($cpl) {
                    $inner->where('cpls.id', $cpl->id);
                });
            })
            ->with(['mk_rel', 'cpmks', 'cpmks.scpmks'])
            ->select('rps.*')
            ->distinct();

        $rps = $rpsQuery->orderBy('rps.id')->paginate($this->cpl_rps_modal_page, ['*'], 'cpl_rps_modal_page');
        $this->cpl_rps_items_list = collect($this->mapRPS($rps))
            ->unique('id')
            ->values()
            ->toArray();

        $this->cpl_rps_modal_paginator = $rps;
    }

    public function updatedCPLRPSModalPage($page)
    {
        $this->loadCPLRPSPagination();
    }


    // private function inputModalCPL($isEditingCPL, $data)
    // {
    //     $this->resetErrorBag();
    //     $this->resetValidation();

    //     $data['deskripsi'] = $this->normalizeText($data['deskripsi'] ?? '');

    //     $tingkatan = $this->cplType ?? 1;
    //     $targetProdiIds = ($tingkatan === 1) ? [$this->pr_id] : ($this->pr_id_array ?: []);

    //     $rules = [
    //         'kode_cpl_1' => 'required|alpha|max:10',
    //         'kode_cpl_2' => 'required|numeric|min:1',
    //         // 'kode_cpl' => [
    //         //     'required',
    //         //     'alpha_num',
    //         //     'max:20',
    //         //     function ($attribute, $value, $fail) use ($isEditingCPL) {
    //         //         $query = DB::table('cpls')->whereZ('kode_cpl', $value);

    //         //         if ($isEditingCPL) {
    //         //             $query->where('id', '!=', $this->selected_id_cpl);
    //         //         }

    //         //         if ($query->exists()) {
    //         //             $fail("Kode CPL '$value' sudah digunakan!");
    //         //         }
    //         //     },
    //         // ],
    //         'kode_cpl' => [
    //             'required', 'alpha_num',
    //             function ($attribute, $value, $fail) use ($targetProdiIds, $isEditingCPL) {
    //                 if (empty($value) || empty($targetProdiIds)) {
    //                     return;
    //                 }

    //                 foreach ($targetProdiIds as $index => $pId) {
    //                     if (empty($pId)) {
    //                         continue;
    //                     }

    //                     $query = DB::table('cpls')
    //                         ->join('prodi_pivot_cpl', 'cpls.id', '=', 'prodi_pivot_cpl.cpl_id')
    //                         ->where('prodi_pivot_cpl.pr_id', $pId)
    //                         ->where('cpls.kode_cpl', $value);

    //                     if ($isEditingCPL) {
    //                         $query->where('cpls.id', '!=', $this->selected_id_cpl);
    //                     }

    //                     if ($query->exists()) {
    //                         $prodiModel = Prodi::find($pId);
    //                         $namaProdi = $prodiModel ? $prodiModel->prodi : "Prodi ID: $pId";
    //                         $fail("Kode CPL '$value' sudah terpakai di Program Studi: ***$namaProdi***.");
    //                         break;
    //                     }
    //                 }
    //             },
    //         ],
    //         'deskripsi' => [
    //             'required',
    //             'string',
    //             'min:5',
    //             'max:1000',
    //             function ($attribute, $value, $fail) use ($isEditingCPL) {
    //                 $query = DB::table('cpls')->where('deskripsi', $value);

    //                 if ($isEditingCPL) {
    //                     $query->where('id', '!=', $this->selected_id_cpl);
    //                 }

    //                 if ($query->exists()) {
    //                     $fail('Deskripsi CPL ini sudah ada, gunakan deskripsi yang berbeda!');
    //                 }
    //             },
    //         ],
    //     ];

    //     $validator = Validator::make($data, $rules, $this->validationMessagesCPL());

    //     if ($validator->fails()) {
    //         $errors = $validator->errors();
    //         if (empty($data['kode_cpl_1']) && empty($data['kode_cpl_2'])) {
    //             $this->addError('kode_cpl', 'Kode CPL wajib diisi!');
    //         } elseif ($errors->has('kode_cpl_1') || $errors->has('kode_cpl_2')) {
    //             $combinedMessage = $errors->first('kode_cpl_1') ?: $errors->first('kode_cpl_2');
    //             $this->addError('kode_cpl', $combinedMessage);
    //         }
    //         foreach ($errors->toArray() as $key => $messages) {
    //             if (! in_array($key, ['kode_cpl_1', 'kode_cpl_2', 'kode_cpl'])) {
    //                 foreach ($messages as $message) {
    //                     $this->addError($key, $message);
    //                 }
    //             }
    //             if ($key === 'kode_cpl' && ! $this->getErrorBag()->has('kode_cpl')) {
    //                 $this->addError('kode_cpl', $messages[0]);
    //             }
    //         }

    //         if ($tingkatan == 1) {
    //             $rules['pr_id'] = 'required|integer|exists:prodis,id';
    //         } else {
    //             if ($tingkatan == 2) {
    //                 $dpId = $data['dp_id'] ?? null;
    //                 $rules['dp_id'] = [
    //                     'required', 'integer',
    //                     'exists:departemens,id',
    //                     function ($attribute, $value, $fail) use ($data) {
    //                         $validProdiIds = Prodi::where('dp_id', $value)->pluck('id')->toArray();
    //                         $selectedProdiIds = $data['pr_id_array'] ?? [];

    //                         $invalidSelected = array_diff($selectedProdiIds, $validProdiIds);

    //                         if (! empty($invalidSelected)) {
    //                             $fail('Beberapa Program Studi yang dipilih tidak terdaftar di Departemen ini!');
    //                         }

    //                         if (empty($validProdiIds)) {
    //                             $fail('Departemen ini belum memiliki Program Studi!');
    //                         }
    //                     },
    //                 ];
    //             } elseif ($tingkatan == 3) {
    //                 $fkId = $data['fk_id'] ?? null;
    //                 $rules['fk_id'] = [
    //                     'required', 'integer',
    //                     'exists:fakultas,id',
    //                     function ($attribute, $value, $fail) use ($data) {
    //                         $validProdiIds = Prodi::whereHas('dp_rel', fn ($q) => $q->where('fk_id', $value))
    //                             ->pluck('id')->toArray();
    //                         $selectedProdiIds = $data['pr_id_array'] ?? [];

    //                         $invalidSelected = array_diff($selectedProdiIds, $validProdiIds);

    //                         if (! empty($invalidSelected)) {
    //                             $fail('Beberapa Program Studi yang dipilih tidak terdaftar di Fakultas ini!');
    //                         }

    //                         if (empty($validProdiIds)) {
    //                             $fail('Fakultas ini belum memiliki Program Studi!');
    //                         }
    //                     },
    //                 ];
    //             }
    //             $rules['pr_id_array'] = 'required|array|min:1';
    //         }

    //         throw ValidationException::withMessages($this->getErrorBag()->messages());
    //     }

    //     if ($validator->fails()) {
    //         throw new ValidationException($validator);
    //     }

    //     return $validator->validated();
    // }

    private function inputModalCPL($isEditingCPL, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $data['deskripsi'] = $this->normalizeText($data['deskripsi'] ?? '');
        $tingkatan = (int) ($this->cplType ?? 1);
        $targetProdiIds = ($tingkatan === 1) ? [$this->pr_id] : ($data['pr_id_array'] ?? []);
        $targetProdiIds = array_filter(array_map('intval', $targetProdiIds));

        // =========================================================================
        // 1. STRUKTUR RULES DASAR (KODE & DESKRIPSI)
        // =========================================================================
        $rules = [
            'kode_cpl_1' => 'required|alpha|max:10',
            'kode_cpl_2' => 'required|numeric|min:1',
            'deskripsi' => [
                'required', 'string', 'min:5', 'max:1000',
                function ($attribute, $value, $fail) use ($isEditingCPL) {
                    $query = DB::table('cpls')->where('deskripsi', $value);
                    if ($isEditingCPL) {
                        $query->where('id', '!=', $this->selected_id_cpl);
                    }
                    if ($query->exists()) {
                        $fail('Deskripsi CPL ini sudah ada, gunakan deskripsi yang berbeda!');
                    }
                },
            ],
        ];

        // =========================================================================
        // 2. STRUKTUR RULES BERDASARKAN TINGKATAN (1, 2, 3, 4) + RELASI PRODI
        // =========================================================================
        if ($tingkatan === 1) {
            $rules['pr_id'] = 'required|integer|exists:prodis,id';
        } elseif ($tingkatan === 2) {
            $rules['dp_id'] = [
                'required', 'integer', 'exists:departemens,id',
                function ($attribute, $value, $fail) use ($targetProdiIds) {
                    if (empty($targetProdiIds)) {
                        return;
                    }

                    // Ambil prodi yang sah di bawah departemen ini
                    $validProdiIds = DB::table('prodis')->where('dp_id', $value)->pluck('id')->toArray();
                    $invalidSelected = array_diff($targetProdiIds, $validProdiIds);

                    if (empty($validProdiIds)) {
                        $fail('Departemen ini belum memiliki Program Studi!');
                    } elseif (! empty($invalidSelected)) {
                        $fail('Beberapa Program Studi yang dipilih tidak terdaftar di Departemen ini!');
                    }
                },
            ];
            $rules['pr_id_array'] = 'required|array|min:1';
        } elseif ($tingkatan === 3) {
            $rules['fk_id'] = [
                'required', 'integer', 'exists:fakultas,id',
                function ($attribute, $value, $fail) use ($targetProdiIds) {
                    if (empty($targetProdiIds)) {
                        return;
                    }

                    // Ambil prodi yang sah di bawah naungan departemen milik fakultas ini
                    $validProdiIds = DB::table('prodis')
                        ->join('departemens', 'prodis.dp_id', '=', 'departemens.id')
                        ->where('departemens.fk_id', $value)
                        ->pluck('prodis.id')
                        ->toArray();

                    $invalidSelected = array_diff($targetProdiIds, $validProdiIds);

                    if (empty($validProdiIds)) {
                        $fail('Fakultas ini belum memiliki Program Studi!');
                    } elseif (! empty($invalidSelected)) {
                        $fail('Beberapa Program Studi yang dipilih tidak terdaftar di Fakultas ini!');
                    }
                },
            ];
            $rules['pr_id_array'] = 'required|array|min:1';
        } elseif ($tingkatan === 4) {
            $rules['pr_id_array'] = 'nullable|array';
        }

        // =========================================================================
        // 3. VALIDASI KEUNIKAN KODE CPL SESUAI ATURAN TINGKATAN (1, 2, 3, 4)
        // =========================================================================
        $rules['kode_cpl'] = [
            'required', 'alpha_num',
            function ($attribute, $value, $fail) use ($tingkatan, $targetProdiIds, $data, $isEditingCPL) {
                if (empty($value)) {
                    return;
                }

                if ($tingkatan === 1) {
                    // Tingkat 1: Gak boleh sama di 1 prodi yang sama & tingkatan 1
                    foreach ($targetProdiIds as $pId) {
                        $query = DB::table('cpls')
                            ->join('prodi_pivot_cpl', 'cpls.id', '=', 'prodi_pivot_cpl.cpl_id')
                            ->where('prodi_pivot_cpl.pr_id', $pId)
                            ->where('cpls.kode_cpl', $value)
                            ->where('cpls.level_cpl', 1);

                        if ($isEditingCPL) {
                            $query->where('cpls.id', '!=', $this->selected_id_cpl);
                        }

                        if ($query->exists()) {
                            $prodi = Prodi::find($pId);
                            $prefix = $prodi->kode ?? $prodi->kode_pr ?? '';
                            $fullKode = $prefix ? "{$prefix}-{$value}" : $value;

                            $fail("Kode CPL penuh ***{$fullKode}*** sudah terpakai di Program Studi: {$prodi->nama_pr}.");
                            break;
                        }
                    }
                } elseif ($tingkatan === 2) {
                    // Tingkat 2: Gak boleh sama di 1 departemen yang sama & tingkatan 2
                    $dpId = $data['dp_id'] ?? null;
                    if ($dpId) {
                        $query = DB::table('cpls')
                            ->join('prodi_pivot_cpl', 'cpls.id', '=', 'prodi_pivot_cpl.cpl_id')
                            ->join('prodis', 'prodi_pivot_cpl.pr_id', '=', 'prodis.id')
                            ->where('prodis.dp_id', $dpId)
                            ->where('cpls.kode_cpl', $value)
                            ->where('cpls.level_cpl', 2);

                        if ($isEditingCPL) {
                            $query->where('cpls.id', '!=', $this->selected_id_cpl);
                        }

                        if ($query->exists()) {
                            $dept = Departemen::find($dpId);
                            $prefix = $dept->kode ?? $dept->kode_dp ?? '';
                            $fullKode = $prefix ? "{$prefix}-{$value}" : $value;

                            $fail("Kode CPL penuh ***{$fullKode}*** sudah terpakai di Departemen: {$dept->nama_dp}.");
                        }
                    }
                } elseif ($tingkatan === 3) {
                    // Tingkat 3: Gak boleh sama di 1 fakultas yang sama & tingkatan 3
                    $fkId = $data['fk_id'] ?? null;
                    if ($fkId) {
                        $query = DB::table('cpls')
                            ->join('prodi_pivot_cpl', 'cpls.id', '=', 'prodi_pivot_cpl.cpl_id')
                            ->join('prodis', 'prodi_pivot_cpl.pr_id', '=', 'prodis.id')
                            ->join('departemens', 'prodis.dp_id', '=', 'departemens.id')
                            ->where('departemens.fk_id', $fkId)
                            ->where('cpls.kode_cpl', $value)
                            ->where('cpls.level_cpl', 3);

                        if ($isEditingCPL) {
                            $query->where('cpls.id', '!=', $this->selected_id_cpl);
                        }

                        if ($query->exists()) {
                            $fak = Fakultas::find($fkId);
                            $prefix = $fak->kode ?? $fak->kode_fk ?? '';
                            $fullKode = $prefix ? "{$prefix}-{$value}" : $value;

                            $fail("Kode CPL penuh ***{$fullKode}*** sudah terpakai di Fakultas: {$fak->nama_fk}.");
                        }
                    }
                } elseif ($tingkatan === 4) {
                    $query = DB::table('cpls')
                        ->where('kode_cpl', $value)
                        ->where('cpls.level_cpl', 4);

                    if ($isEditingCPL) {
                        $query->where('id', '!=', $this->selected_id_cpl);
                    }

                    if ($query->exists()) {
                        $fullKode = "UNI-{$value}";
                        $fail("Kode CPL penuh ***{$fullKode}*** sudah terdaftar pada Tingkat Universitas.");
                    }
                }
            },
        ];

        // =========================================================================
        // 4. EKSEKUSI VALIDATOR & PENATAAN TAMPILAN ERROR LIVEWIRE
        // =========================================================================
        $validator = Validator::make($data, $rules, $this->validationMessagesCPL());

        if ($validator->fails()) {
            $errors = $validator->errors();

            // Mapping error gabungan untuk inputan kode_cpl komponen pecahan
            if (empty($data['kode_cpl_1']) && empty($data['kode_cpl_2'])) {
                $this->addError('kode_cpl', 'Kode CPL wajib diisi!');
            } elseif ($errors->has('kode_cpl_1') || $errors->has('kode_cpl_2')) {
                $combinedMessage = $errors->first('kode_cpl_1') ?: $errors->first('kode_cpl_2');
                $this->addError('kode_cpl', $combinedMessage);
            }

            // Pindahkan sisa error bawaan Laravel ke ErrorBag Livewire
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_cpl_1', 'kode_cpl_2', 'kode_cpl'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_cpl' && ! $this->getErrorBag()->has('kode_cpl')) {
                    $this->addError('kode_cpl', $messages[0]);
                }
            }

            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        return $validator->validated();
    }

    public function saveCPL($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['pr_id'] = $this->pr_id;
        $data['pr_id_array'] = $this->pr_id_array;

        $data['dp_id'] = $this->dp_id;
        $data['fk_id'] = $this->fk_id;

        try {
            $tingkatan = $this->cplType;
            $validated = $this->inputModalCPL(false, $data);

            $fullKodeCpl = '';

            DB::transaction(function () use ($data, $validated, $tingkatan, &$fullKodeCpl) {
                $cpl = CPL::create([
                    'kode_cpl' => strtoupper($validated['kode_cpl']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                $kodePrefix = '';

                if ($tingkatan === 1) {
                    $kodePrefix = data_get($data, 'pr_items.kode', '');
                } elseif ($tingkatan === 2) {
                    $kodePrefix = data_get($data, 'dp_items.kode', '');
                } elseif ($tingkatan === 3) {
                    $kodePrefix = data_get($data, 'fk_items.kode', '');
                } elseif ($tingkatan === 4) {
                    $kodePrefix = 'UNI';
                }

                $kodeCpl = $validated['kode_cpl'] ?? '';
                $fullKodeCpl = $kodePrefix ? "{$kodePrefix}-{$kodeCpl}" : $kodeCpl;

                $targetIds = ($tingkatan === 1) ? [$this->pr_id] : ($this->pr_id_array ?: []);
                $targetIds = array_filter($targetIds);
                if (! empty($targetIds)) {
                    $cpl->prodis()->attach($targetIds);
                }

                // if (property_exists($this, 'showCPMKModal') && $this->showCPMKModal && $cpl) {
                //     $cpl->load('prodis');
                //     $this->cpl_id_array[] = $cpl->id;
                //     $this->cpl_items_array[] = $this->itemsCPL($cpl, strtoupper($fullKodeCpl));
                // }

                if ($this->parent == 'cpmk' && $cpl) {
                    $this->dispatch('cpl-created-cpmk', id: $cpl->id);
                }

                // if (property_exists($this, 'showRPSModal') && $this->showRPSModal && $cpl) {
                //     if (! isset($this->cpl_id_array['rps']) || ! is_array($this->cpl_id_array['rps'])) {
                //         $this->cpl_id_array['rps'] = [];
                //     }
                //     if (! isset($this->cpl_items_array['rps']) || ! is_array($this->cpl_items_array['rps'])) {
                //         $this->cpl_items_array['rps'] = [];
                //     }
                //     if (! in_array($cpl->id, $this->cpl_id_array['rps'])) {
                //         $this->cpl_id_array['rps'][] = $cpl->id;
                //         $this->cpl_items_array['rps'][] = $this->itemsCPL($cpl);
                //     }
                // }
                // if (property_exists($this, 'showCPMKModal') && $this->showCPMKModal && $cpl) {
                //     if (! isset($this->cpl_id_array['cpmk']) || ! is_array($this->cpl_id_array['cpmk'])) {
                //         $this->cpl_id_array['cpmk'] = [];
                //     }
                //     if (! isset($this->cpl_items_array['cpmk']) || ! is_array($this->cpl_items_array['cpmk'])) {
                //         $this->cpl_items_array['cpmk'] = [];
                //     }
                //     if (! in_array($cpl->id, $this->cpl_id_array['cpmk'])) {
                //         $this->cpl_id_array['cpmk'][] = $cpl->id;
                //         $this->cpl_items_array['cpmk'][] = $this->itemsCPL($cpl);
                //     }
                // }
            });

            $this->toast(message: "CPL {$fullKodeCpl}");
            $this->resetInputCPL();

            $this->dispatch('refresh-data-cpl');
            $this->dispatch('refresh-stats-cpl'); 
            $this->showCPLModal = false;
            $this->showRPSCPLModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function updateCPL($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['pr_id'] = $this->pr_id;
        $data['pr_id_array'] = $this->pr_id_array;

        $data['dp_id'] = $this->dp_id;
        $data['fk_id'] = $this->fk_id;

        try {
            $tingkatan = $this->cplType;
            $validated = $this->inputModalCPL(true, $data);

            DB::transaction(function () use ($validated, $tingkatan) {
                $cpl = CPL::findOrFail($this->selected_id_cpl);

                // 1. Update Data Utama CPL
                $cpl->update([
                    'kode_cpl' => strtoupper($validated['kode_cpl']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                $targetIds = ($tingkatan === 1)
                            ? [$this->pr_id]
                            : ($this->pr_id_array ?: []);
                $cleanIds = array_values(array_filter($targetIds));
                $syncData = [];
                foreach ($cleanIds as $index => $id) {
                    $syncData[$id] = ['sort_order' => $index];
                }
                $cpl->prodis()->sync($syncData);

                // 2. Update Tanggal Revisi pada RPS Terkait
                $rpsIds = $cpl->cpmks()
                    ->with('rps:id')
                    ->get()
                    ->flatMap(fn ($cpmk) => $cpmk->rps->pluck('id'))
                    ->unique();

                if ($rpsIds->isNotEmpty()) {
                    RPS::whereIn('id', $rpsIds)
                        ->where('is_draf', 0)
                        ->update([
                            'revisi' => now(),
                        ]);
                }

            });

            $this->toast(message: "CPL {$validated['kode_cpl_1']}-{$validated['kode_cpl_2']}", type: 'update');
            $this->resetInputCPL();

            $this->dispatch('refresh-data-cpl');
            $this->showCPLModal = false;
            $this->showRPSCPLModal = false;
            $this->dispatch('refresh-data-cpl');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-cpl');
            $this->showCPLModal = false;
            $this->showRPSCPLModal = false;
        }
    }

    private function validationMessagesCPL()
    {
        return [
            'fk_id.required' => 'Fakultas wajib diisi!',
            'fk_id.integer' => 'ID Fakultas harus berupa angka!',
            'fk_id.exists' => 'Fakultas yang dipilih tidak valid!',
            'dp_id.required' => 'Departemen wajib diisi!',
            'dp_id.integer' => 'ID Departemen harus berupa angka!',
            'dp_id.exists' => 'Departemen yang dipilih tidak valid!',
            'pr_id.required' => 'Program Studi wajib diisi!',
            'pr_id.integer' => 'ID Program Studi harus berupa angka!',
            'pr_id.exists' => 'Program Studi yang dipilih tidak valid!',

            'pr_id_array.required' => 'Program Studi wajib diisi!',
            'pr_id_array.array' => 'Program Studi dalam bentuk Array!',
            'pr_id_array.min' => 'Program Studi minimal berisi satu data!',

            'kode_cpl_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_cpl_1.alpha' => 'Kode awalan harus berupa huruf!',
            'kode_cpl_1.max' => 'Kode awalan terlalu panjang!',

            // Kode CPL Bagian 2 (Angka - Kanan)
            'kode_cpl_2.required' => 'Nomor Kode (input kanan) wajib diisi!',
            'kode_cpl_2.numeric' => 'Nomor Kode harus berupa angka!',
            'kode_cpl_2.min' => 'Nomor Kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_cpl.required' => 'Kode CPL lengkap wajib terbentuk!',
            'kode_cpl.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_cpl.required' => 'Kode CPL wajib diisi!',
            'kode_cpl.alpha_num' => 'Kode CPL hanya boleh berisi mutu dan angka!',
            'kode_cpl.max' => 'Kode CPL maksimal 20 karakter!',

            // Deskripsi & Status
            'deskripsi.required' => 'Deskripsi CPL wajib diisi!',
            'deskripsi.string' => 'Deskripsi CPL harus berupa text!',
            'deskripsi.min' => 'Deskripsi CPL terlalu pendek (Maksimal 5 karakter)!',
            'deskripsi.max' => 'Deskripsi CPL terlalu panjang (Maksimal 1000 karakter)!',
            'deskripsi.unique' => 'Deskripsi CPL sudah tersedia!',
        ];
    }

    public function getCPLErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'kode_cpl',
                'fk_id',
                'dp_id',
                'pr_id',
                'pr_id_array',
                'deskripsi',
            ]),
            2 => $this->getErrorCount([
            ]),
        ];
    }

    private function resetInputCPL()
    {
        $fields = [
            'selected_id_cpl',
            'pr_id', 'dp_id', 'fk_id',
            'pr_items', 'dp_items', 'fk_items',
            'pr_id_array', 'pr_items_array',
            'prNameSearch', 'dpNameSearch', 'fkNameSearch',
        ];
        $this->reset($fields);
        $this->resetErrorBag();
    }
}
