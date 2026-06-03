<?php

namespace App\Livewire\Staff\CPLManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Akademik\CPL;
use App\Models\Akademik\RPS;
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

    public $cpl_rps_items_list = [];

    public $cpl_rps_modal_page = 3;

    public $cpl_rps_id;

    protected $cpl_rps_modal_paginator;

    public $isFlyoutCPL = false;

    public function updatedShowCPLModal($value)
    {
        if (! $value) {
            $this->isFlyoutCPL = false;
            $this->isEditingCPL = false;
        } else {
            $this->isFlyoutCPL = $this->showRPSModal || $this->showCPMKModal || $this->showRefModal;
        }
    }

    public function addCPL()
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
        $this->isFlyoutCPL = $this->showRPSModal || $this->showCPMKModal || $this->showRefModal;

        $this->showCPLModal = true;

        $this->showEditCPL = false;

    }

    public function editCPL($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputCPL();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_cpl = $id;
        $this->isEditingCPL = true;
        $this->showEditCPL = true;
        $this->isFlyoutCPL = $this->showRPSModal || $this->showCPMKModal || $this->showRefModal;

        // $this->showCPLModal = true;
        // $this->dispatch('refresh-component');

        try {
            // 1. Load data CPL dengan relasi yang sangat lengkap
            $cpl = CPL::with([
                'rps',
                'cpmks.rps',
            ])->findOrFail($id);

            $this->cpl_rps_id = $cpl->id;
            $this->cpl_rps_items_list = [];
            $this->cpl_rps_modal_paginator = null;
            $this->resetPage('cpl_rps_modal_page');
            $this->loadCPLRPSPagination();

            $this->showCPLModal = true;

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
            ->whereHas('cpls', function ($query) use ($cpl) {
                $query->where('cpls.id', $cpl->id);
            })
            ->orWhereHas('cpmks', function ($query) use ($cpl) {
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

    private function inputModalCPL($isEditingCPL, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $data['deskripsi'] = $this->normalizeText($data['deskripsi'] ?? '');

        $rules = [
            'kode_cpl_1' => 'required|alpha|max:10',
            'kode_cpl_2' => 'required|numeric|min:1',
            'kode_cpl' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingCPL) {
                    $query = DB::table('cpls')->where('kode_cpl', $value);

                    if ($isEditingCPL) {
                        $query->where('id', '!=', $this->selected_id_cpl);
                    }

                    if ($query->exists()) {
                        $fail("Kode CPL '$value' sudah digunakan!");
                    }
                },
            ],
            'deskripsi' => [
                'required',
                'string',
                'min:5',
                'max:1000',
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

        $validator = Validator::make($data, $rules, $this->validationMessagesCPL());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_cpl_1']) && empty($data['kode_cpl_2'])) {
                $this->addError('kode_cpl', 'Kode CPL wajib diisi!');
            } elseif ($errors->has('kode_cpl_1') || $errors->has('kode_cpl_2')) {
                $combinedMessage = $errors->first('kode_cpl_1') ?: $errors->first('kode_cpl_2');
                $this->addError('kode_cpl', $combinedMessage);
            }
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

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function saveCPL($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            // 1. Jalankan validasi & pembersihan
            $validated = $this->inputModalCPL(false, $data);

            // 2. Eksekusi Database
            DB::transaction(function () use ($validated) {
                $cpl = CPL::create([
                    'kode_cpl' => strtoupper($validated['kode_cpl']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                if ($this->showRPSModal && $cpl) {
                    if (! isset($this->cpl_id_array['rps']) || ! is_array($this->cpl_id_array['rps'])) {
                        $this->cpl_id_array['rps'] = [];
                    }
                    if (! isset($this->cpl_items_array['rps']) || ! is_array($this->cpl_items_array['rps'])) {
                        $this->cpl_items_array['rps'] = [];
                    }
                    if (! in_array($cpl->id, $this->cpl_id_array['rps'])) {
                        $this->cpl_id_array['rps'][] = $cpl->id;
                        $this->cpl_items_array['rps'][] = $this->itemsCPL($cpl);
                    }
                }
                if ($this->showCPMKModal && $cpl) {
                    if (! isset($this->cpl_id_array['cpmk']) || ! is_array($this->cpl_id_array['cpmk'])) {
                        $this->cpl_id_array['cpmk'] = [];
                    }
                    if (! isset($this->cpl_items_array['cpmk']) || ! is_array($this->cpl_items_array['cpmk'])) {
                        $this->cpl_items_array['cpmk'] = [];
                    }
                    if (! in_array($cpl->id, $this->cpl_id_array['cpmk'])) {
                        $this->cpl_id_array['cpmk'][] = $cpl->id;
                        $this->cpl_items_array['cpmk'][] = $this->itemsCPL($cpl);
                    }
                }
            });

            $this->toast(message: "CPL {$validated['kode_cpl_1']}-{$validated['kode_cpl_2']}");
            $this->resetInputCPL();

            $this->dispatch('refresh-data-cpl');
            $this->showCPLModal = false;

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

        try {
            $validated = $this->inputModalCPL(true, $data);

            DB::transaction(function () use ($validated) {
                $cpl = CPL::findOrFail($this->selected_id_cpl);

                // 1. Update Data Utama CPL
                $cpl->update([
                    'kode_cpl' => strtoupper($validated['kode_cpl']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                // 2. Update Tanggal Revisi pada RPS Terkait
                $rpsIds = collect();

                $directRpsIds = $cpl->rps()->pluck('rps.id');
                $rpsIds = $rpsIds->merge($directRpsIds);

                $cpmkRpsIds = DB::table('rps_pivot_cpmk')
                    ->join('cpmk_pivot_cpl', 'rps_pivot_cpmk.cpmk_id', '=', 'cpmk_pivot_cpl.cpmk_id')
                    ->where('cpmk_pivot_cpl.cpl_id', $cpl->id)
                    ->pluck('rps_pivot_cpmk.rps_id');

                $rpsIds = $rpsIds->merge($cpmkRpsIds)->unique();

                if ($rpsIds->isNotEmpty()) {
                    RPS::whereIn('id', $rpsIds)
                        ->where('is_draf', 0)
                        ->update(['revisi' => now()]);
                }

            });

            $this->toast(message: "CPL {$validated['kode_cpl_1']}-{$validated['kode_cpl_2']}", type: 'update');
            $this->resetInputCPL();

            $this->dispatch('refresh-data-cpl');
            $this->showCPLModal = false;
            $this->dispatch('refresh-data-cpl');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-cpl');
            $this->showCPLModal = false;
        }
    }

    private function validationMessagesCPL()
    {
        return [
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
            'kode_cpl.alpha_num' => 'Kode CPL hanya boleh berisi huruf dan angka!',
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
                'deskripsi',
            ]),
            2 => $this->getErrorCount([
            ]),
        ];
    }

    private function resetInputCPL()
    {
        $this->resetErrorBag();
    }
}
