<?php

namespace App\Livewire\Staff\OBEManagement\ReferensiManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithPagination;

trait WithRefModal
{
    use HasErrorCount;
    use HasToast;
    use WithPagination;

    public $selected_id_ref;

    public $isEditingRef = false;

    public $showEditRef = false;

    public $showRefModal = false;

    public $ref_rps_items_list = [];

    public $ref_rps_modal_page = 3;

    public $ref_rps_id;

    protected $ref_rps_modal_paginator;

    public $isFlyoutRef = false;

    public function updatedShowRefModal($value)
    {
        if (! $value) {
            if (!$this->isFlyoutSCPMK) {
                $this->isFlyoutRPS = false;
            }
            $this->isFlyoutRef = false;
            $this->isEditingRef = false;
        } else {
            $this->isFlyoutRef = $this->showRPSModal || $this->showCPMKModal || $this->showSCPMKModal || $this->showCPLModal;
            if ($this->showRPSModal == false) {
                $this->isFlyoutRPS = true;
            }
        }
    }

    public function addRef($isFlyout = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditRef == true) {
            $this->resetInputRef();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingRef = false;

        $this->isFlyoutRef = $isFlyout;
        $this->showRefModal = true;
        $this->showEditRef = false;

    }

    public function editRef($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputRef();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_ref = $id;
        $this->isEditingRef = true;
        $this->showEditRef = true;

        $this->isFlyoutRef = $this->showRPSModal || $this->showCPMKModal || $this->showSCPMKModal || $this->showCPLModal;

        // $this->showRefModal = true;
        // $this->dispatch('refresh-component');

        try {
            // 1. Load data Ref dengan relasi yang sangat lengkap
            $ref = Referensi::with([
                'rps',
                'cpmks.rps',
                'scpmks.cpmks.rps',
            ])->findOrFail($id);

            $this->ref_rps_id = $ref->id;
            $this->ref_rps_items_list = [];
            $this->ref_rps_modal_paginator = null;
            $this->resetPage('ref_rps_modal_page');
            $this->loadRefRPSPagination();

            $this->showRefModal = true;
            $this->dispatch('fill-modal-ref', ref: $ref);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadRefRPSPagination()
    {
        if (empty($this->ref_rps_id)) {
            return;
        }

        $ref = Referensi::find($this->ref_rps_id);

        if (! $ref) {
            return;
        }

        $rpsQuery = RPS::query()
            ->whereHas('refs', function ($query) use ($ref) {
                $query->where('referensis.id', $ref->id);
            })
            ->orWhereHas('cpmks', function ($query) use ($ref) {
                $query->whereHas('refs', function ($inner) use ($ref) {
                    $inner->where('referensis.id', $ref->id);
                })
                    ->orWhereHas('scpmks', function ($inner) use ($ref) {
                        $inner->whereHas('refs', function ($deep) use ($ref) {
                            $deep->where('referensis.id', $ref->id);
                        });
                    });
            })
            ->orWhereHas('cpmks.scpmks.refs', function ($query) use ($ref) {
                $query->where('referensis.id', $ref->id);
            })
            ->with(['mk_rel', 'cpmks', 'cpmks.scpmks'])
            ->select('rps.*')
            ->distinct();

        $rps = $rpsQuery->orderBy('rps.id')->paginate($this->ref_rps_modal_page, ['*'], 'ref_rps_modal_page');
        $this->ref_rps_items_list = collect($this->mapRPS($rps))
            ->unique('id')
            ->values()
            ->toArray();
        $this->ref_rps_modal_paginator = $rps;
    }

    public function updatedRefRPSModalPage($page)
    {
        $this->loadRefRPSPagination();
    }

    private function inputModalRef($isEditingRef, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $data['judul'] = $this->normalizeNama($data['judul']);
        $data['penulis'] = $this->normalizeNama($data['penulis']);
        $data['penerbit'] = $this->normalizeNama($data['penerbit']);

        $rules = [
            'kode_ref_1' => 'required|alpha|max:10',
            'kode_ref_2' => 'required|numeric|min:1',
            'kode_ref' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingRef) {
                    $query = DB::table('referensis')->where('kode_ref', $value);

                    if ($isEditingRef) {
                        $query->where('id', '!=', $this->selected_id_ref);
                    }

                    if ($query->exists()) {
                        $fail("Kode Referensi '$value' sudah digunakan!");
                    }
                },
            ],
            // Validasi Field Baru
            'judul' => 'required|string|max:1000',
            'penulis' => 'required|string|max:500',
            'penerbit' => 'required|string|max:500',
            'tahun' => [
                'required',
                'numeric',
                'digits:4',
                function ($attribute, $value, $fail) {
                    $year = (int) $value;
                    if ($year > now()->year) {
                        $fail('Tahun tidak boleh lebih besar dari tahun sekarang!');
                    }
                },
            ],
            'link' => 'nullable|url|max:1000',
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesRef());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_ref_1']) && empty($data['kode_ref_2'])) {
                $this->addError('kode_ref', 'Kode Referensi wajib diisi!');
            } elseif ($errors->has('kode_ref_1') || $errors->has('kode_ref_2')) {
                $combinedMessage = $errors->first('kode_ref_1') ?: $errors->first('kode_ref_2');
                $this->addError('kode_ref', $combinedMessage);
            }
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_ref_1', 'kode_ref_2', 'kode_ref'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_ref' && ! $this->getErrorBag()->has('kode_ref')) {
                    $this->addError('kode_ref', $messages[0]);
                }
            }
            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        $validated = $validator->validated();

        return $validated;
    }

    public function saveRef($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            // 1. Jalankan validasi & pembersihan
            $validated = $this->inputModalRef(false, $data);

            // 2. Eksekusi Database
            DB::transaction(function () use ($validated) {
                $ref = Referensi::create([
                    'kode_ref' => strtoupper($validated['kode_ref']),
                    'judul' => $validated['judul'],
                    'penulis' => $validated['penulis'],
                    'penerbit' => $validated['penerbit'],
                    'tahun' => $validated['tahun'],
                    'link' => $validated['link'],
                ]);

                if ($this->showRPSModal && $ref) {
                    if (! isset($this->ref_id_array['rps']) || ! is_array($this->ref_id_array['rps'])) {
                        $this->ref_id_array['rps'] = [];
                    }
                    if (! isset($this->ref_items_array['rps']) || ! is_array($this->ref_items_array['rps'])) {
                        $this->ref_items_array['rps'] = [];
                    }
                    if (! in_array($ref->id, $this->ref_id_array['rps'])) {
                        $this->ref_id_array['rps'][] = $ref->id;
                        $this->ref_items_array['rps'][] = $this->itemsRef($ref);
                    }
                }
                if ($this->showCPMKModal && $ref) {
                    if (! isset($this->ref_id_array['cpmk']) || ! is_array($this->ref_id_array['cpmk'])) {
                        $this->ref_id_array['cpmk'] = [];
                    }
                    if (! isset($this->ref_items_array['cpmk']) || ! is_array($this->ref_items_array['cpmk'])) {
                        $this->ref_items_array['cpmk'] = [];
                    }
                    if (! in_array($ref->id, $this->ref_id_array['cpmk'])) {
                        $this->ref_id_array['cpmk'][] = $ref->id;
                        $this->ref_items_array['cpmk'][] = $this->itemsRef($ref);
                    }
                }
                if ($this->showSCPMKModal && $ref) {
                    if (! isset($this->ref_id_array['scpmk']) || ! is_array($this->ref_id_array['scpmk'])) {
                        $this->ref_id_array['scpmk'] = [];
                    }
                    if (! isset($this->ref_items_array['scpmk']) || ! is_array($this->ref_items_array['scpmk'])) {
                        $this->ref_items_array['scpmk'] = [];
                    }
                    if (! in_array($ref->id, $this->ref_id_array['scpmk'])) {
                        $this->ref_id_array['scpmk'][] = $ref->id;
                        $this->ref_items_array['scpmk'][] = $this->itemsRef($ref);
                    }
                }
            });

            $this->toast(message: "Referensi {$validated['kode_ref_1']}-{$validated['kode_ref_2']}");
            $this->resetInputRef();

            $this->dispatch('refresh-data-ref');
            $this->showRefModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function updateRef($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $validated = $this->inputModalRef(true, $data);

            DB::transaction(function () use ($validated) {
                $ref = Referensi::findOrFail($this->selected_id_ref);

                // 1. Update Data Utama Referensi
                $ref->update([
                    'kode_ref' => strtoupper($validated['kode_ref']),
                    'judul' => $validated['judul'],
                    'penulis' => $validated['penulis'],
                    'penerbit' => $validated['penerbit'],
                    'tahun' => $validated['tahun'],
                    'link' => $validated['link'],
                ]);

                // 2. Kumpulkan ID RPS dari semua kemungkinan jalur
                $directRpsIds = $ref->rps()->pluck('rps.id');
                $viaCpmkRpsIds = RPS::whereHas('cpmks.refs', function ($q) use ($ref) {
                    $q->where('referensis.id', $ref->id);
                })->pluck('id');
                $viaSubCpmkRpsIds = RPS::whereHas('cpmks.scpmks.refs', function ($q) use ($ref) {
                    $q->where('referensis.id', $ref->id);
                })->pluck('id');

                // 3. Gabungkan semua ID dan hapus duplikasi
                $allRpsIds = collect($directRpsIds)
                    ->merge($viaCpmkRpsIds)
                    ->merge($viaSubCpmkRpsIds)
                    ->unique();

                // 4. Update Tanggal Revisi jika RPS bukan DRAF
                if ($allRpsIds->isNotEmpty()) {
                    RPS::whereIn('id', $allRpsIds)
                        ->where('is_draf', 0)
                        ->update(['revisi' => now()]);
                }
            });

            $this->toast(message: "Referensi {$validated['kode_ref_1']}-{$validated['kode_ref_2']}", type: 'update');
            $this->resetInputRef();

            $this->showRefModal = false;
            $this->dispatch('refresh-data-ref');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-ref');
            $this->showRefModal = false;
        }
    }

    private function validationMessagesRef()
    {
        return [
            'kode_ref_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_ref_1.alpha' => 'Kode awalan harus berupa huruf!',
            'kode_ref_1.max' => 'Kode awalan terlalu panjang!',

            // Kode Ref Bagian 2 (Angka - Kanan)
            'kode_ref_2.required' => 'Nomor Kode (input kanan) wajib diisi!',
            'kode_ref_2.numeric' => 'Nomor Kode harus berupa angka!',
            'kode_ref_2.min' => 'Nomor Kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_ref.required' => 'Kode Referensi lengkap wajib terbentuk!',
            'kode_ref.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_ref.required' => 'Kode Referensi wajib diisi!',
            'kode_ref.alpha_num' => 'Kode Referensi hanya boleh berisi huruf dan angka!',
            'kode_ref.max' => 'Kode Referensi maksimal 20 karakter!',

            // Deskripsi & Status
            'judul.required' => 'Deskripsi Ref wajib diisi!',
            'judul.max' => 'Deskripsi Ref terlalu panjang (Maksimal 1000 karakter)!',

            // Penulis
            'penulis.required' => 'Nama penulis wajib diisi!',
            'penulis.max' => 'Nama penulis terlalu panjang (Maksimal 500 karakter)!',

            // Penerbit
            'penerbit.required' => 'Nama penerbit wajib diisi!',
            'penerbit.max' => 'Nama penerbit terlalu panjang (Maksimal 500 karakter)!',

            // Tahun
            'tahun.required' => 'Tahun terbit wajib diisi!',
            'tahun.numeric' => 'Tahun harus berupa angka!',
            'tahun.digits' => 'Tahun harus berjumlah 4 digit!',

            // Link
            'link.url' => 'Format tautan (URL) tidak valid!',
            'link.max' => 'Tautan terlalu panjang (Maksimal 1000 karakter)!',
        ];
    }

    public function getRefErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'kode_ref',
                'judul',
                'penulis',
                'penerbit',
                'tahun',
                'link',
            ]),
            2 => $this->getErrorCount([
            ]),
        ];
    }

    private function resetInputRef()
    {
        $this->resetErrorBag();
    }
}
