<?php

namespace App\Livewire\Staff\ObeManagement\RpsManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\ProgramStudi\Prodi;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithRPSModal
{
    use HasErrorCount;
    use HasToast;
    use WithRPSPertemuan;
    use WithRPSShow;

    public $selected_id_rps;

    public $isEditingRPS = false;

    public $showEditRPS = false;

    public $showRPSModal = false;

    public $mk_id_2;

    // public $isFlyoutRPS = false;

    public function updatedShowRPSModal($value)
    {
        if (! $value) {
            // $this->isFlyoutRPS = false;
            $this->isEditingRPS = false;
        }
        // else {
        //     $this->isFlyoutRPS =
        //         (property_exists($this, 'showCPMKModal') && $this->showCPMKModal) ||
        //         (property_exists($this, 'showSCPMKModal') && $this->showSCPMKModal) ||
        //         (property_exists($this, 'showCPLModal') && $this->showCPLModal) ||
        //         (property_exists($this, 'showRefModal') && $this->showRefModal);
        // }
    }

    public function addRPS($key = 'rps')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditRPS == true) {
            $this->resetInputRPS();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingRPS = false;
        $this->showRPSModal = true;

        $this->showEditRPS = false;

        // $this->cplNameSearch['rps'] = '';
        // $this->refNameSearch['rps'] = '';

        // $this->cpl_id_array[$key] = [];
        // $this->cpl_items_array[$key] = [];

        $this->ref_id_array[$key] = [];
        $this->ref_items_array[$key] = [];

        $this->updatedMKNameSearch($this->mkNameSearch);
        $this->updatedCPMKNameSearch($this->cpmkNameSearch);
        // $this->updatedCPLNameSearch($this->getCPLNameSearchForKey($key), 'cplNameSearch.'.$key);
        // $this->updatedRefNameSearch($this->getRefNameSearchForKey($key), 'refNameSearch.'.$key);
        $this->updatedRefNameSearch($this->refNameSearch);

        $this->updatedTimDosenNameSearch($this->timDosenNameSearch);
    }

    public function editRPS($id, $key = 'rps')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputRPS();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_rps = $id;
        $this->isEditingRPS = true;
        $this->showEditRPS = true;

        try {
            // 1. Load data RPS dengan relasi yang sangat lengkap
            $rps = RPS::with([
                'mk_rel',
                'tim_dosens',
                'cpmks.scpmks',
                'cpmks.scpmks.refs',
                'cpmks.refs',
                'cpmks.cpls',
                // 'cpls',
                'refs',
            ])->findOrFail($id);

            $this->mk_id = $rps->mk_id;
            $this->mk_id_2 = $rps->mk_id;
            $this->fetchMK();
            // $this->mk_items = $this->itemsMK($rps->mk_rel);

            // 2. Fill Data Dosen
            $this->tim_dosen_id_array = $rps->tim_dosens->pluck('id')->toArray();
            $this->tim_dosen_items_array = $rps->tim_dosens->map(function ($d) {
                return $this->itemsTimDosen($d);
            })->toArray();
            $this->tim_dosen_sub_items_array = $this->mapTimDosen($rps->tim_dosens);

            // 3. MAPPING CPMK (MENGGUNAKAN FUNGSI mapCPMK ANDA)
            $this->cpmk_id_array = $rps->cpmks->pluck('id')->toArray();
            $this->cpmk_items_array = $rps->cpmks->map(function ($c) {
                return $this->itemsCPMK($c);
            })->toArray();
            $this->cpmk_sub_items_array = $this->mapCPMK($rps->cpmks);

            $this->ref_id_array = collect($this->ref_id_array)
                ->merge($rps->refs->pluck('id'))->unique()->values()->all();
            $this->ref_items_array = collect($this->ref_items_array)
                ->merge(
                    $rps->refs->map(fn ($r) => $this->itemsRef($r))
                )->unique('id')->values()->all();

            $this->updatedCPMKNameSearch($this->cpmkNameSearch);
            // $this->updatedCPLNameSearch($this->getCPLNameSearchForKey($key), 'cplNameSearch.'.$key);
            $this->updatedRefNameSearch($this->refNameSearch);
            $this->updatedTimDosenNameSearch($this->timDosenNameSearch);

            $this->showRPSModal = true;

            // Dispatch ke AlpineJS
            $this->dispatch('fill-modal-rps', rps: $rps);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalRPS($isEditingRPS, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $mkId = $data['mk_id'] ?? null;
        $mk = DB::table('mata_kuliahs')->where('id', $mkId)->first();
        $desMK = $mk?->deskripsi ?? '';

        $desMK = $this->normalizeText($desMK);
        $data['deskripsi'] = $this->normalizeText($data['deskripsi'] ?? '');

        if ($data['deskripsi'] == $desMK) {
            $data['deskripsi'] = '';
        }

        $cplFromCpmk = [];
        $refFromCpmkScpmk = [];

        if (! empty($data['cpmk_id_array'])) {
            $cpmks = CPMK::with(['cpls', 'refs'])->whereIn('id', $data['cpmk_id_array'])->get();
            foreach ($cpmks as $cpmk) {
                $cplFromCpmk = array_merge($cplFromCpmk, $cpmk->cpls?->pluck('id')->toArray() ?? []);
                $refFromCpmkScpmk = array_merge($refFromCpmkScpmk, $cpmk->refs?->pluck('id')->toArray() ?? []);
            }
        }

        // --- 🌟 HITUNG TOTAL SUB-CPMK & DETEKSI UTS/UAS (HANYA SATU BLOK YANG BENAR) ---
        $totalSubCPMK = 0;
        $totalBobot = 0;
        $hasUTS = false;
        $hasUAS = false;

        if (! empty($data['cpmk_sub_items_array']) && is_array($data['cpmk_sub_items_array'])) {
            $utsEnv = env('UTS_FIELDS', 'UTS,EVALUASI AWAL');
            $uasEnv = env('UAS_FIELDS', 'UAS,EVALUASI AKHIR,LAPORAN AKHIR,HASIL PROYEK,HASIL PROJEK');

            $utsFields = array_map('trim', array_map('strtoupper', explode(',', $utsEnv)));
            $uasFields = array_map('trim', array_map('strtoupper', explode(',', $uasEnv)));

            foreach ($data['cpmk_sub_items_array'] as $cpmkItem) {
                $scpmkList = $cpmkItem['scpmk'] ?? [];

                if (is_array($scpmkList)) {
                    foreach ($scpmkList as $scpmk) {
                        $totalSubCPMK++;
                        $totalBobot += (float) ($scpmk['bobot'] ?? 0);
                        $method = strtoupper(trim((string) ($scpmk['metode'] ?? '')));

                        if (in_array($method, $utsFields, true)) {
                            $hasUTS = true;
                        }

                        if (in_array($method, $uasFields, true)) {
                            $hasUAS = true;
                        }

                        if (! empty($scpmk['ref_ids'])) {
                            $refFromCpmkScpmk = array_merge($refFromCpmkScpmk, (array) $scpmk['ref_ids']);
                        }
                    }
                }
            }
        }

        // dd($hasUTS, $hasUAS);

        // --- PROSES PEMBERSIHAN ---
        $cleanRef = [];
        if (isset($data['ref_id_array']) && is_array($data['ref_id_array'])) {
            $cleanRef = array_values(array_diff(array_unique($data['ref_id_array']), $refFromCpmkScpmk));
        }

        $data['bobot_uts'] = $data['bobot_uts'] ?? null;
        $data['bobot_uas'] = $data['bobot_uas'] ?? null;

        $bobotUTS = $data['bobot_uts'];
        $bobotUAS = $data['bobot_uas'];

        // dd($bobotUTS, $hasUTS);
        // dd(
        //     $data['bobot_uts'] ?? 'NOT_FOUND',
        //     isset($rules['bobot_uts']),
        //     array_key_exists('bobot_uts', $data)
        // );

        if (($data['is_draf'] ?? 0) == 0) {
            $errorsBobot = [];
            // Pengecekan UTS
            if ($hasUTS) {
                if ($bobotUTS !== null && $bobotUTS !== '') {
                    $errorsBobot['bobot_uts'][] = 'Bobot UTS tidak boleh diisi dari luar karena metode UTS sudah ada di dalam Sub-CPMK!';
                }
            } else {
                if ($bobotUTS === null || $bobotUTS === '') {
                    $errorsBobot['bobot_uts'][] = 'Bobot UTS wajib diisi karena metode UTS tidak ditemukan di dalam pertemuan Sub-CPMK!';
                }
            }
            // Pengecekan UAS
            if ($hasUAS) {
                if ($bobotUAS !== null && $bobotUAS !== '') {
                    $errorsBobot['bobot_uas'][] = 'Bobot UAS tidak boleh diisi dari luar karena metode UAS/Evaluasi Akhir sudah ada di dalam Sub-CPMK!';
                }
            } else {
                if ($bobotUAS === null || $bobotUAS === '') {
                    $errorsBobot['bobot_uas'][] = 'Bobot UAS wajib diisi karena metode UAS/Evaluasi Akhir tidak ditemukan di dalam pertemuan Sub-CPMK!';
                }
            }

            if (! empty($errorsBobot)) {
                throw ValidationException::withMessages($errorsBobot);
            }
        }

        // --- RULES VALIDASI ---
        $rules = [
            'deskripsi' => 'nullable|string|min:5|max:1000',
            'mk_id' => 'required|exists:mata_kuliahs,id',
            'akademik' => [
                'required', 'string', 'regex:/^\d{4}\/\d{4}$/',
                function ($attribute, $value, $fail) use ($data, $isEditingRPS) {
                    $query = DB::table('rps')->where('mk_id', $data['mk_id'])->where('akademik', $value);
                    if ($isEditingRPS) {
                        $query->where('id', '!=', $this->selected_id_rps);
                    }
                    if ($query->exists()) {
                        $fail("RPS untuk Mata Kuliah ini pada tahun akademik $value sudah ada!");
                    }
                },
            ],
            'akademik_1' => 'required|integer|min:1970',
            'akademik_2' => 'required|integer|min:1971',
            'is_draf' => ['required', 'boolean', function ($attribute, $value, $fail) use ($data, $totalSubCPMK, $totalBobot) {
                if (($totalSubCPMK < 14 || $totalSubCPMK > 16) && $data['is_draf'] == 0) {
                    $fail('Jumlah Sub-CPMK harus antara 14 dan 16 pertemuan!');
                }

                if ($value == 0) {
                    $rounded = round($totalBobot, 2);
                    if (($rounded < 70 || $rounded > 200) && $data['is_draf'] == 0) {
                        $fail("Total bobot harus 70-200% (Saat ini: $rounded%)!");
                    }
                }
            }],
            'cpmk_id_array' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) use ($data, $hasUTS, $hasUAS, $totalSubCPMK) {
                    $max = 14;
                    if ($hasUTS && $hasUAS) {
                        $max = 16;
                    } elseif ($hasUTS || $hasUAS) {
                        $max = 15;
                    }

                    if ($totalSubCPMK < 14 && $data['is_draf'] == 0) {
                        $fail('Sub-CPMK minimal 14 pertemuan!');

                        return;
                    }
                    if ($totalSubCPMK > $max && $data['is_draf'] == 0) {
                        if ($max === 14) {
                            $fail('Karena tidak ada UTS/UAS, Sub-CPMK hanya boleh 14 pertemuan!');
                        } elseif ($max === 15) {
                            $fail('Karena hanya ada satu dari UTS (Evaluasi Awal) atau UAS (Evaluasi Akhir/Laporan Akhir/Hasil Proyek), Sub-CPMK hanya boleh 15 pertemuan!');
                        } else {
                            $fail('Karena ada UTS dan UAS (atau penggantinya), Sub-CPMK hanya boleh 16 pertemuan!');
                        }
                    }
                },
            ],
            'bobot_uts' => 'nullable|numeric|min:1|max:60',
            'bobot_uas' => 'nullable|numeric|min:1|max:60',
            'ref_id_array' => 'nullable|array',
            'tim_dosen_id_array' => 'required|array|min:1',
            'tim_dosen_items_array' => [
                'array',
                function ($attribute, $value, $fail) {
                    $validations = collect($value)
                        ->pluck('validation')
                        ->filter(fn($item) => !is_null($item))
                        ->map(fn($item) => (string) $item);

                    $counts = array_count_values($validations->toArray());
                    $duplicates = array_filter($counts, fn($count) => $count > 1);
                    if (!empty($duplicates)) {
                        $fail('Tidak boleh menggunakan lebih dari satu Tim Dosen dari Program Studi yang sama!');
                    }
                },
            ],
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesRPS());

        if ($validator->fails()) {
            $pesanFormatSama = 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!';
            $isThnEmpty = empty($data['akademik']) && empty($data['akademik_1']) && empty($data['akademik_2']);
            $formattedErrors = [];

            foreach ($validator->errors()->toArray() as $key => $messages) {
                if (in_array($key, ['akademik', 'akademik_1', 'akademik_2'])) {
                    $hasDuplicateError = false;
                    foreach ($messages as $msg) {
                        if (str_contains($msg, 'sudah ada')) {
                            $formattedErrors['akademik'][] = $msg;
                            $hasDuplicateError = true;
                            break;
                        }
                    }
                    if (! $hasDuplicateError && ! isset($formattedErrors['akademik'])) {
                        $formattedErrors['akademik'][] = $isThnEmpty ? 'Tahun Akademik wajib diisi!' : $pesanFormatSama;
                    }
                } elseif ($key === 'tim_dosen_items_array') {
                    $formattedErrors['tim_dosen_id_array'] = array_merge(
                        $formattedErrors['tim_dosen_id_array'] ?? [], 
                        $messages
                    );
                } else {
                    $formattedErrors[$key] = array_merge($formattedErrors[$key] ?? [], $messages);
                }
            }

            throw ValidationException::withMessages($formattedErrors);
        }

        $validated = $validator->validated();
        $validated['bobot_uts'] = $data['bobot_uts'] ?? null;
        $validated['bobot_uas'] = $data['bobot_uas'] ?? null;

        $validated['ref_id_array'] = $cleanRef;
        $validated['cpmk_id_array'] = array_values(array_unique($data['cpmk_id_array'] ?? []));
        $validated['tim_dosen_items_array'] = $data['tim_dosen_items_array'] ?? [];
        $validated['cpmk_sub_items_array'] = $data['cpmk_sub_items_array'] ?? [];
        $validated['tim_dosen_id_array'] = array_values(array_unique($data['tim_dosen_id_array'] ?? []));

        return $validated;
    }

    public function saveRPS($data, $key = 'rps')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        // 1. Sinkronisasi awal data dari state Livewire ke parameter
        $data['mk_id'] = $this->mk_id;
        $data['is_draf'] = ($data['is_draf'] !== '') ? (int) $data['is_draf'] : 1;
        $data['cpmk_id_array'] = $this->cpmk_id_array ?? [];
        $data['cpmk_sub_items_array'] = $this->cpmk_sub_items_array ?? [];
        // $data['cpl_id_array'] = $this->getCPLIdArrayForKey($key);
       $data['ref_id_array'] = $this->ref_id_array ?? [];
        $data['tim_dosen_id_array'] = $this->tim_dosen_id_array ?? [];
        $data['tim_dosen_items_array'] = $this->tim_dosen_items_array ?? [];

        try {
            // 2. Jalankan validasi dan pembersihan duplikat
            $validated = $this->inputModalRPS(false, $data);

            // 3. Eksekusi Database
            DB::transaction(function () use ($validated) {
                $rps = RPS::create([
                    'deskripsi' => $validated['deskripsi'],
                    'mk_id' => $validated['mk_id'],
                    'akademik' => $validated['akademik'],
                    'tahun_awal' => $validated['akademik_1'],
                    'tahun_akhir' => $validated['akademik_2'],
                    'bobot_uts' => $validated['bobot_uts'],
                    'bobot_uas' => $validated['bobot_uas'],
                    'is_draf' => $validated['is_draf'],
                ]);

                $rps->refresh();

                // 1. Sync Dosen
                if (! empty($validated['tim_dosen_id_array'])) {
                    $timDosenSync = [];
                    foreach ($validated['tim_dosen_id_array'] as $index => $id) {
                        if (! empty($id)) {
                            $timDosenSync[(int) $id] = [
                                'sort_order' => $index,
                            ];
                        }
                    }
                    $rps->tim_dosens()->sync($timDosenSync);
                }

                // 3. Mapping CPMK
                if (! empty($validated['cpmk_id_array'])) {
                    $cpmkSync = [];
                    foreach ($validated['cpmk_id_array'] as $index => $id) {
                        if (! empty($id)) {
                            $cpmkSync[(int) $id] = [
                                'sort_order' => $index,
                            ];
                        }
                    }
                    $rps->cpmks()->sync($cpmkSync);
                }

                // 4. Mapping CPL (ID Baru/Manual)
                // if (! empty($validated['cpl_id_array'])) {
                //     $cplSync = [];
                //     foreach ($validated['cpl_id_array'] as $index => $id) {
                //         if (! empty($id)) {
                //             $cplSync[(int) $id] = [
                //                 'sort_order' => $index,
                //             ];
                //         }
                //     }
                //     $rps->cpls()->sync($cplSync);
                // }

                // 5. Mapping Referensi (ID Baru/Manual)
                if (! empty($validated['ref_id_array'])) {
                    $refSync = [];
                    foreach ($validated['ref_id_array'] as $index => $id) {
                        if (! empty($id)) {
                            $refSync[(int) $id] = [
                                'sort_order' => $index,
                            ];
                        }
                    }
                    $rps->refs()->sync($refSync);
                }

            });
            // 4. Feedback & Reset
            $kodeMK = data_get($this->mk_items, 'kode', $this->mk_name);
            $kodeRPS = $data['digit_akademik'] ?? ($data['akademik_1'] ?? '');
            $namaMK = data_get($this->mk_items, 'slot1', $this->mk_name);

            $this->toast(message: "RPS $kodeMK-$kodeRPS $namaMK ({$validated['akademik']})");
            if (! empty($this->cpmk_rps_id)) {
                $this->loadCPMKRPSPagination();
            }

            $this->resetInputRPS();
            $this->dispatch('refresh-data-rps');
            $this->dispatch('refresh-stats-rps');
            $this->showRPSModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-rps');
            $this->showRPSModal = false;
        }
    }

    public function updateRPS($data, $key = 'rps')
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ((empty($data['mk_id']) && $this->mk_id !== $this->mk_id_2) ||
            ($this->mk_id == $this->mk_id_2) || ($this->mk_id !== $this->mk_id_2)) {
            $data['mk_id'] = $this->mk_id;
        }
        // dd($data['mk_id']);

        $data['cpmk_id_array'] = $this->cpmk_id_array ?? [];
        $data['tim_dosen_id_array'] = $this->tim_dosen_id_array ?? [];
        $data['tim_dosen_items_array'] = $this->tim_dosen_items_array ?? [];
        $data['cpmk_sub_items_array'] = $this->cpmk_sub_items_array ?? [];
        // $data['cpl_id_array'] = $this->getCPLIdArrayForKey($key);
       $data['ref_id_array'] = $this->ref_id_array ?? [];

        try {
            $validated = $this->inputModalRPS(true, $data);

            DB::transaction(function () use ($validated) {
                $rps = RPS::findOrFail($this->selected_id_rps);

                // 1. Update Data Utama
                $updateData = [
                    'deskripsi' => $validated['deskripsi'],
                    'mk_id' => $validated['mk_id'],
                    'akademik' => $validated['akademik'],
                    'tahun_awal' => $validated['akademik_1'],
                    'tahun_akhir' => $validated['akademik_2'],
                    'bobot_uts' => $validated['bobot_uts'],
                    'bobot_uas' => $validated['bobot_uas'],
                    'is_draf' => $validated['is_draf'],
                ];

                if ($validated['is_draf'] == 0) {
                    $updateData['revisi'] = now();
                }

                $rps->update($updateData);

                // 2. Sync Dosen dengan Pivot Data
                $syncTimDosen = [];
                foreach ($validated['tim_dosen_id_array'] as $index => $id) {
                    $syncTimDosen[(int) $id] = ['sort_order' => $index];
                }
                $rps->tim_dosens()->sync($syncTimDosen);

                // 3. Sync CPMK
                $syncCpmk = [];
                foreach ($validated['cpmk_id_array'] as $index => $id) {
                    $syncCpmk[(int) $id] = ['sort_order' => $index];
                }
                $rps->cpmks()->sync($syncCpmk);

                // 4. Sync CPL (Manual/Tambahan)
                $refsDariCpmk = $rps->cpmks->flatMap->refs->pluck('id')->toArray();
                $refsDariScpmk = $rps->cpmks->flatMap->scpmks->flatMap->refs->pluck('id')->toArray();
                $forbiddenIds = array_unique(array_merge($refsDariCpmk, $refsDariScpmk));

                $forbiddenIds = $rps->cpmks->flatMap(function ($cpmk) {
                        $ids = $cpmk->refs->pluck('id');
                        $scpmkIds = $cpmk->scpmks->flatMap->refs->pluck('id');
                        return $ids->merge($scpmkIds);
                    })->unique()->toArray();

                $inputIds = array_map('intval', $validated['ref_id_array']);
                $finalRefIds = array_diff($inputIds, $forbiddenIds);

                $syncRef = [];
                foreach ($finalRefIds as $index => $id) {
                    $syncRef[(int) $id] = ['sort_order' => $index];
                }
                $rps->refs()->sync($syncRef);
            });

            $kodeMK = data_get($this->mk_items, 'kode', $this->mk_name);
            $kodeRPS = $data['digit_akademik'] ?? ($data['akademik_1'] ?? '');
            $namaMK = data_get($this->mk_items, 'slot1', $this->mk_name);

            $this->toast(message: "RPS $kodeMK-$kodeRPS $namaMK ({$validated['akademik']})", type: 'update');
            if (! empty($this->cpmk_rps_id)) {
                $this->loadCPMKRPSPagination();
            }
            $this->showRPSModal = false;

            if ($this->detailRPSModal == true) {
                $this->detailRPSModal = false;
                $this->showRPS($this->selected_id_rps);
            }
            if ($this->parent) {
                $this->dispatch('refresh-data-rps-'.$this->parent);
            } else {
                $this->dispatch('refresh-data-rps-no-parent');
            }

            $this->resetInputRPS();
            $this->dispatch('refresh-data-rps');
            $this->dispatch('refresh-stats-rps');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            if (! empty($this->cpmk_rps_id)) {
                $this->loadCPMKRPSPagination();
            }
            $this->dispatch('refresh-data-rps');
            $this->showRPSModal = false;
        }
    }

    private function validationMessagesRPS()
    {
        return [
            // Relasi Mata Kuliah & Prodi
            'mk_id.required' => 'Mata Kuliah asal wajib dipilih!',
            'mk_id.exists' => 'Mata Kuliah yang dipilih tidak valid!',
            // 'pr_id.required' => 'Program Studi wajib diisi!',
            // 'pr_id_array.required' => 'Program Studi wajib diisi!',
            // 'pr_id_array.min' => 'Pilih minimal satu Program Studi!',
            // Tahun Akademik
            'akademik.required' => 'Tahun Akademik wajib diisi!',
            'akademik.regex' => 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!',
            'akademik_1.required' => 'Tahun awal (input kiri) wajib diisi!',
            'akademik_1.min' => 'Tahun awal minimal adalah 1970!',
            'akademik_2.required' => 'Tahun akhir (input kanan) wajib diisi!',
            'akademik_2.min' => 'Tahun akhir minimal adalah 1971!',
            // Deskripsi & Status
            'deskripsi.required' => 'Deskripsi RPS wajib diisi!',
            'deskripsi.string' => 'Deskripsi RPS harus berupa text!',
            'deskripsi.min' => 'Deskripsi RPS terlalu pendek (Minimal 5 karakter)!',
            'deskripsi.max' => 'Deskripsi RPS terlalu panjang (Maksimal 1000 karakter)!',
            'is_draf.required' => 'Status RPS wajib ditentukan!',
            'is_draf.boolean' => 'Format status draf tidak valid!',
            // Bobot UTS & UAS
            'bobot_uts.numeric' => 'Bobot UTS harus berupa angka desimal!',
            'bobot_uts.min' => 'Bobot UTS minimal 1!',
            'bobot_uts.max' => 'Bobot UTS maksimal 60!',
            'bobot_uas.numeric' => 'Bobot UAS harus berupa angka desimal!',
            'bobot_uas.min' => 'Bobot UAS minimal 1!',
            'bobot_uas.max' => 'Bobot UAS maksimal 60!',

            // CPMK & Relasi Data
            'cpmk_id_array.required' => 'Minimal pilih satu CPMK untuk RPS ini!',
            'cpmk_id_array.array' => 'Format data CPMK tidak valid!',
            'cpmk_id_array.min' => 'Minimal harus ada satu CPMK yang dipilih!',

            // 'cpl_id_array.array' => 'Format data CPL tidak valid!',
            'ref_id_array.array' => 'Format data Referensi tidak valid!',

            // Dosen Pengampu
            'tim_dosen_id_array.required' => 'Tim Dosen wajib dipilih!',
            'tim_dosen_id_array.min' => 'Minimal harus ada satu Tim Dosen!',
            'tim_dosen_items_array.required' => 'Data detail Tim Dosen tidak boleh kosong!',
            'tim_dosen_id_array.required' => 'Tim Dosen wajib diisi!',

        ];
    }

    public function getRPSErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'mk_id',
                // 'pr_id',
                // 'pr_id_array',
                'akademik',
                // 'akademik_1',
                // 'akademik_2',
                'deskripsi',
                'is_draf',
            ]),
            2 => $this->getErrorCount([
                'cpmk_id_array',
                'bobot_uts',
                'bobot_uas',
            ]),
            3 => $this->getErrorCount([
                // 'cpl_id_array',
            ]),
            4 => $this->getErrorCount([
                'ref_id_array',
            ]),
            5 => $this->getErrorCount([
                'tim_dosen_id_array',
                'tim_dosen_items_array',
            ]),
        ];
    }

    private function resetInputRPS()
    {
        $this->cpmkNameSearch = '';
        // $this->cplNameSearch = array_map(fn () => '', $this->cplNameSearch);
        $this->refNameSearch = '';

        $this->mkNameSearch = '';
        // ambil id untuk simpan ke rps_pivot_cpmk
        $this->cpmk_id_array = [];
        $this->cpmk_items_array = [];
        $this->cpmk_sub_items_array = [];

        // $this->cpl_id_array = array_map(fn () => [], $this->cpl_id_array);
        // $this->cpl_items_array = array_map(fn () => [], $this->cpl_items_array);

        $this->ref_id_array = [];
        $this->ref_items_array = [];

        $this->tim_dosen_id_array = [];
        $this->tim_dosen_items_array = [];
        $this->resetErrorBag();
    }
}
