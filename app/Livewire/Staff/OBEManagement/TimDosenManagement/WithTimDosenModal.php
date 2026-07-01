<?php

namespace App\Livewire\Staff\OBEManagement\TimDosenManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Akademik\RPS;
use App\Models\Akademik\TimDosen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithPagination;

trait WithTimDosenModal
{
    use HasErrorCount;
    use HasToast;
    use WithPagination;

    public $selected_id_tim_dosen;

    public $isEditingTimDosen = false;

    public $showEditTimDosen = false;

    public $showTimDosenModal = false;

    public $showTimDosenRPSModal = false;

    public $tim_dosen_rps_items_list = [];

    public $tim_dosen_rps_modal_page = 3;

    public $tim_dosen_rps_id;

    protected $tim_dosen_rps_modal_paginator;

    public $isFlyoutTimDosen = false;

    // public $dosen_input = [];

    public function updatedShowTimDosenModal($value)
    {
        if (! $value) {
            $this->isFlyoutTimDosen = false;
            $this->isEditingTimDosen = false;
        } else {
            $this->isFlyoutTimDosen =
                (property_exists($this, 'showRPSModal') && $this->showRPSModal) ||
                (property_exists($this, 'showCPMKModal') && $this->showCPMKModal) ||
                (property_exists($this, 'showSCPMKModal') && $this->showSCPMKModal) ||
                (property_exists($this, 'showCPLModal') && $this->showCPLModal);
        }
    }

    public function addTimDosen()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditTimDosen == true) {
            $this->resetInputTimDosen();
        }

        $this->updatedPrNameSearch($this->prNameSearch);
        $this->updatedDosenNameSearch($this->dosenNameSearch);

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingTimDosen = false;

        $this->showTimDosenModal = true;
        $this->showEditTimDosen = false;

    }

    public function editTimDosen($id, $isRPS = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputTimDosen();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_tim_dosen = $id;
        $this->isEditingTimDosen = true;
        $this->showEditTimDosen = true;

        if ($isRPS) {
            $this->showTimDosenRPSModal = true;
            $this->showTimDosenModal = false;
        } else {
            $this->showTimDosenModal = true;
            $this->showTimDosenRPSModal = false;
        }

        // $this->showTimDosenModal = true;
        // $this->dispatch('refresh-component');
        try {
            $tim_dosen = TimDosen::with([
                'rps', 'dosens',
            ])->findOrFail($id);

            if (! $isRPS) {
                $this->pr_id = $tim_dosen->pr_id;
                $this->pr_items = $this->itemsPr($tim_dosen->pr_rel);
                $this->prNameSearch = $tim_dosen->prodi;

                $dosens = $tim_dosen->dosens->sortBy('pivot.sort_order');
                $this->dosen_id_array = $dosens->pluck('id')->toArray();
                $this->dosen_items_array = $dosens->map(function ($d) {
                    return $this->itemsDosen($d);
                })->values()->toArray();
                $this->dosen_pertemuan_array = $dosens->map(function ($dosen) {
                    $rawData = $dosen->pivot->pertemuan_ke;
                    $numbers = is_string($rawData) ? json_decode($rawData, true) : $rawData;

                    if (! is_array($numbers)) {
                        $numbers = [];
                    }

                    return $this->formatPertemuanTimDosen($numbers);
                })->values()->toArray();

                $this->updatedDosenNameSearch($this->dosenNameSearch);
            }

            $this->tim_dosen_rps_id = $tim_dosen->id;
            $this->tim_dosen_rps_items_list = [];
            $this->tim_dosen_rps_modal_paginator = null;
            $this->resetPage('tim_dosen_rps_modal_page');
            $this->loadTimDosenRPSPagination();

            $this->dispatch('fill-modal-tim_dosen', tim_dosen: $tim_dosen);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadTimDosenRPSPagination()
    {
        if (empty($this->tim_dosen_rps_id)) {
            return;
        }

        $tim_dosen = TimDosen::find($this->tim_dosen_rps_id);

        if (! $tim_dosen) {
            return;
        }

        $rpsQuery = RPS::query()
            ->whereHas('tim_dosens', function ($query) use ($tim_dosen) {
                $query->where('tim_dosens.id', $tim_dosen->id);
            })
            ->with(['mk_rel', 'cpmks', 'cpmks.scpmks'])
            ->select('rps.*')
            ->distinct();

        $rps = $rpsQuery->orderBy('rps.id')->paginate($this->tim_dosen_rps_modal_page, ['*'], 'tim_dosen_rps_modal_page');
        $this->tim_dosen_rps_items_list = collect($this->mapRPS($rps))
            ->unique('id')
            ->values()
            ->toArray();
        $this->tim_dosen_rps_modal_paginator = $rps;
    }

    public function updatedTimDosenRPSModalPage($page)
    {
        $this->loadTimDosenRPSPagination();
    }

    private function formatPertemuanTimDosen(array $numbers): string
    {
        if (empty($numbers)) {
            return '';
        }
        sort($numbers);

        $ranges = [];
        $start = $numbers[0];
        $end = $numbers[0];

        for ($i = 1; $i < count($numbers); $i++) {
            if ($numbers[$i] == $end + 1) {
                $end = $numbers[$i];
            } else {
                $ranges[] = ($start == $end) ? "$start" : "$start-$end";
                $start = $numbers[$i];
                $end = $numbers[$i];
            }
        }
        $ranges[] = ($start == $end) ? "$start" : "$start-$end";

        return implode(', ', $ranges);
    }

    protected function parsePertemuanTimDosen(array $pertemuanData): array
    {
        $results = [];

        foreach ($pertemuanData as $item) {
            $validNumbers = [];
            if (empty($item)) {
                $results[] = [];

                continue;
            }
            $parts = explode(',', $item);
            foreach ($parts as $part) {
                $part = trim($part);
                if (str_contains($part, '-')) {
                    [$start, $end] = explode('-', $part);
                    for ($i = (int) $start; $i <= (int) $end; $i++) {
                        if ($i >= 1 && $i <= 16) {
                            $validNumbers[] = $i;
                        }
                    }
                } else {
                    $num = (int) $part;
                    if ($num >= 1 && $num <= 16) {
                        $validNumbers[] = $num;
                    }
                }
            }
            $results[] = array_values(array_unique($validNumbers));
        }

        return $results;
    }

    private function inputModalTimDosen($isEditingTimDosen, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if (count($data['dosen_id_array'] ?? []) === 1) {
            $data['dosen_pertemuan_array'] = null;
        } else {
            $data['dosen_pertemuan_array'] = $this->parsePertemuanTimDosen(
                $data['dosen_pertemuan_array'] ?? [],
                $data['dosen_id_array'] ?? [],
            );
        }

        $rules = [
            'kode_tim_dosen_1' => 'required|alpha|max:10',
            'kode_tim_dosen_2' => 'required|numeric|min:1',
            'kode_tim_dosen' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingTimDosen) {
                    $query = DB::table('tim_dosens')
                        ->where('kode_tim_dosen', $value);

                    if ($isEditingTimDosen) {
                        $query->where('id', '!=', $this->selected_id_tim_dosen);
                    }

                    if ($query->exists()) {
                        $fail("Kode Tim Dosen '$value' sudah digunakan di Tim Dosen lain!");
                    }
                },
            ],
            'nama_tim' => 'required|string|min:5|max:255',
            'pr_id' => 'required|exists:prodis,id',
            'dosen_pertemuan_array' => 'nullable|array',
            'dosen_id_array' => 'required|array|min:1',
            'dosen_items_array' => [
                'array',
                function ($attribute, $value, $fail) use ($data) {
                    $hasKetua = collect($value)->contains(function ($item) {
                        return isset($item['is_ketua']) && ($item['is_ketua'] === 1 || $item['is_ketua'] === '1' || $item['is_ketua'] === true);
                    });

                    if (! $hasKetua && ! collect($data['dosen_id_array'] ?? [])->isEmpty()) {
                        $fail('Harus ada minimal satu Dosen yang dipilih sebagai Ketua Tim!');
                    }
                },
            ],
            'dosen_items_array.*.peran' => 'required|in:Koordinator,Pengajar,Asisten',
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesTimDosen());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_tim_dosen_1']) && empty($data['kode_tim_dosen_2'])) {
                $this->addError('kode_tim_dosen', 'Kode Tim Dosen wajib diisi!');
            } elseif ($errors->has('kode_tim_dosen_1') || $errors->has('kode_tim_dosen_2')) {
                $combinedMessage = $errors->first('kode_tim_dosen_1') ?: $errors->first('kode_tim_dosen_2');
                $this->addError('kode_tim_dosen', $combinedMessage);
            }
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_tim_dosen_1', 'kode_tim_dosen_2', 'kode_tim_dosen'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_tim_dosen' && ! $this->getErrorBag()->has('kode_tim_dosen')) {
                    $this->addError('kode_tim_dosen', $messages[0]);
                }
            }
            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        $validated = $validator->validated();
        $validated['dosen_items_array'] = $data['dosen_items_array'] ?? [];
        $validated['dosen_id_array'] = $data['dosen_id_array'] ?? [];

        return $validated;
    }

    public function saveTimDosen($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['pr_id'] = $this->pr_id;

        $data['dosen_id_array'] = $this->dosen_id_array ?? [];
        $data['dosen_items_array'] = $this->dosen_items_array ?? [];
        $data['dosen_pertemuan_array'] = $this->dosen_pertemuan_array ?? [];
        // $data['dosen_pertemuan_array'] = array_filter($this->dosen_pertemuan_array) ?? [];

        try {
            $validated = $this->inputModalTimDosen(false, $data);

            DB::transaction(function () use ($validated) {
                $tim_dosen = TimDosen::create([
                    'kode_tim_dosen' => strtoupper($validated['kode_tim_dosen']),
                    'pr_id' => $validated['pr_id'],
                    'nama_tim' => $validated['nama_tim'],
                ]);

                if (! empty($validated['dosen_id_array'])) {
                    $syncDosen = [];

                    foreach ($validated['dosen_id_array'] as $index => $id) {
                        $detail = $validated['dosen_items_array'][$index] ?? [];
                        $pertemuan = $validated['dosen_pertemuan_array'][$index] ?? [];

                        $syncDosen[(int) $id] = [
                            'peran' => $detail['peran'] ?? 'Pengajar',
                            'is_ketua' => (bool) ($detail['is_ketua'] ?? false),
                            'pertemuan_ke' => json_encode(array_values($pertemuan)),
                            'sort_order' => $index,
                        ];
                    }

                    $tim_dosen->dosens()->sync($syncDosen);
                } else {
                    $tim_dosen->dosens()->detach();
                }

                // if (property_exists($this, 'showRPSModal') && $this->showRPSModal && $tim_dosen) {
                //     $newTimDosen = TimDosen::with(['dosens'])
                //         ->find($tim_dosen->id);
                //     $this->tim_dosen_id_array[] = $newTimDosen->id;
                //     $this->tim_dosen_items_array[] = $this->itemsTimDosen($newTimDosen);
                //     $mapped = $this->mapTimDosen(collect([$newTimDosen]));
                //     $this->pushToTimDosenItems($mapped);
                // }
                if ($this->parent == 'rps' && $tim_dosen) {
                    $this->dispatch('tim-dosen-created-rps', id: $tim_dosen->id);
                }
            });

            $this->toast(message: "Tim Dosen {$validated['nama_tim']}");
            $this->resetInputTimDosen();
            $this->dispatch('refresh-data-tim-dosen');
            $this->showTimDosenModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function updateTimDosen($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        // if ((empty($data['pr_id']) && $this->pr_id !== $this->pr_id_2) ||
        //     ($this->pr_id == $this->pr_id_2) || ($this->pr_id !== $this->pr_id_2)) {
        //     $data['pr_id'] = $this->pr_id;
        // }
        $data['pr_id'] = $this->pr_id;

        $data['dosen_id_array'] = $this->dosen_id_array ?? [];
        $data['dosen_items_array'] = $this->dosen_items_array ?? [];
        $data['dosen_pertemuan_array'] = $this->dosen_pertemuan_array ?? [];

        try {
            $validated = $this->inputModalTimDosen(true, $data);

            DB::transaction(function () use ($validated) {
                $tim_dosen = TimDosen::findOrFail($this->selected_id_tim_dosen);
                $tim_dosen->update([
                    'kode_tim_dosen' => strtoupper($validated['kode_tim_dosen']),
                    'pr_id' => $validated['pr_id'],
                    'nama_tim' => $validated['nama_tim'],
                ]);

                $tim_dosen->dosens()->detach();
                $syncDosen = [];
                foreach ($validated['dosen_id_array'] as $index => $id) {
                    $detail = $validated['dosen_items_array'][$index] ?? [];
                    $pertemuan = $validated['dosen_pertemuan_array'][$index] ?? [];

                    $syncDosen[(int) $id] = [
                        'peran' => $detail['peran'] ?? 'Pengajar',
                        'is_ketua' => (bool) ($detail['is_ketua'] ?? false),
                        'pertemuan_ke' => json_encode(array_values($pertemuan)),
                        'sort_order' => $index,
                    ];
                }
                $tim_dosen->dosens()->attach($syncDosen);

                $allRpsIds = $tim_dosen->rps()->pluck('rps.id')->unique();
                if ($allRpsIds->isNotEmpty()) {
                    RPS::whereIn('id', $allRpsIds)
                        ->where('is_draf', 0)
                        ->update(['revisi' => now()]);
                }
            });

            $this->toast(message: "Tim Dosen {$validated['nama_tim']}", type: 'update');
            $this->resetInputTimDosen();
            $this->showTimDosenModal = false;
            $this->dispatch('refresh-data-tim-dosen');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-tim-dosen');
            $this->showTimDosenModal = false;
        }
    }

    private function validationMessagesTimDosen()
    {
        return [
            'kode_tim_dosen_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_tim_dosen_1.alpha' => 'Kode awalan harus berupa huruf!',
            'kode_tim_dosen_1.max' => 'Kode awalan terlalu panjang!',

            // Kode SCPMK Bagian 2 (Angka - Kanan)
            'kode_tim_dosen_2.required' => 'Nomor Kode (input kanan) wajib diisi!',
            'kode_tim_dosen_2.numeric' => 'Nomor Kode harus berupa angka!',
            'kode_tim_dosen_2.min' => 'Nomor Kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_tim_dosen.required' => 'Kode Tim Dosen lengkap wajib terbentuk!',
            'kode_tim_dosen.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_tim_dosen.required' => 'Kode Tim Dosen wajib diisi!',
            'kode_tim_dosen.alpha_num' => 'Kode Tim Dosen hanya boleh berisi mutu dan angka!',
            'kode_tim_dosen.max' => 'Kode Tim Dosen maksimal 20 karakter!',

            'nama_tim.required' => 'Nama Tim Dosen wajib diisi!',
            'nama_tim.string' => 'Nama Tim Dosen harus berupa text!',
            'nama_tim.min' => 'Nama Tim Dosen terlalu pendek (Minimal 5 karakter)!',
            'nama_tim.max' => 'Nama Tim Dosen terlalu panjang (Maksimal 255 karakter)!',

            'pr_id.required' => 'Program Studi wajib dipilih!',
            'pr_id.integer' => 'ID Program Studi harus berupa angka!',
            'pr_id.exists' => 'Program Studi yang dipilih tidak valid!',
            // Dosen Pengampu
            'dosen_id_array.required' => 'Dosen pengampu wajib dipilih!',
            'dosen_id_array.min' => 'Minimal harus ada satu Dosen pengampu!',
            'dosen_items_array.required' => 'Data detail Dosen tidak boleh kosong!',
            'dosen_items_array.*.peran.required' => 'Peran Dosen (Koordinator/Pengajar/Asisten) wajib dipilih!',
            'dosen_items_array.*.peran.in' => 'Peran Dosen hanya boleh: Koordinator, Pengajar, atau Asisten!',
            'dosen_id_array.required' => 'Dosen pengampu wajib diisi!',
        ];
    }

    public function getTimDosenErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'kode_tim_dosen',
                'nama_tim',
                'pr_id',
                'dosen_id_array',
                'dosen_items_array',
            ]),
            2 => $this->getErrorCount([
            ]),
        ];
    }

    private function resetInputTimDosen()
    {
        $fields = [
            'selected_id_tim_dosen',
            'pr_id', 'prNameSearch',
        ];
        $this->reset($fields);

        $this->dosen_id_array = [];
        $this->dosen_items_array = [];
        $this->dosen_pertemuan_array = [];

        $this->resetErrorBag();
    }
}
