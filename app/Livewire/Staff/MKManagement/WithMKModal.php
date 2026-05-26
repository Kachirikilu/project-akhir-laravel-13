<?php

namespace App\Livewire\Staff\MKManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Akademik\MataKuliah;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithMKModal
{
    use HasErrorCount;
    use HasToast;

    public $selected_id_mk;

    public $isEditingMK = false;

    public $showEditMK = false;

    public $showMKModal = false;

    public $mkType = '';

    public function addMK($tingkatan)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditMK == true) {
            $this->resetInputMK();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingMK = false;
        $this->mkType = $tingkatan;
        $this->showMKModal = true;
        $this->showEditMK = false;

        if ($tingkatan == 1 || $tingkatan == 4) {
            $this->updatedPrNameSearch($this->prNameSearch);
        } elseif ($tingkatan == 2) {
            $this->updatedDpNameSearch($this->dpNameSearch);
        } elseif ($tingkatan == 3) {
            $this->updatedFkNameSearch($this->fkNameSearch);
        }
    }

    public function editMK($id, $tingkatan)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputMK();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_mk = $id;
        $this->mkType = $tingkatan;
        $this->isEditingMK = true;
        $this->showEditMK = true;

        $this->prResults = [];

        try {
            $mk = MataKuliah::with(['prodis'])->findOrFail($id);

            $this->pr_id_array = $mk->prodis->pluck('id')->toArray();
            foreach ($mk->prodis as $pr) {
                $this->pr_items_array[] = $this->itemsPr($pr);
            }

            // $this->dispatch('refresh-component');

            $firstProdi = $mk->prodis->first();

            if ($firstProdi) {
                if ($tingkatan == 2) {
                    $this->dp_id = $firstProdi->dp_id;
                    $dataDp = Departemen::where('id', $this->dp_id)->first();
                    $this->dp_items[] = $this->itemsDp($dataDp);
                    $this->dpNameSearch = $firstProdi->departemen_dp;
                }
                if ($tingkatan == 3) {
                    $this->fk_id = $firstProdi->fk_id;
                    $dataFk = Fakultas::where('id', $this->fk_id)->first();
                    if ($dataFk) {
                        $this->fk_items = $this->itemsFk($dataFk);
                        $this->fkNameSearch = $firstProdi->fakultas_fk;
                    }
                }

                if ($tingkatan == 1 || $tingkatan == 4) {
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

            $this->showMKModal = true;

            $this->dispatch('fill-modal-mk', mk: $mk);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalMK($isEditingMK, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $data['nama_mk'] = $this->normalizeNama($data['nama_mk'] ?? '');
        $data['deskripsi'] = $this->normalizeText($data['deskripsi'] ?? '');
        $data['bahan_kajian'] = $this->normalizeText($data['bahan_kajian'] ?? '');

        $tingkatan = $this->mkType ?? 1;
        $targetProdiIds = ($tingkatan === 1) ? [$this->pr_id] : ($this->pr_id_array ?: []);

        $rules = [
            'nama_mk' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:8',
            'digit_semester' => 'required|string|size:2',
            'digit_mk' => [
                'required', 'string', 'size:2',
                function ($attribute, $value, $fail) use ($targetProdiIds, $isEditingMK) {
                    if (empty($value) || empty($targetProdiIds)) {
                        return;
                    }

                    foreach ($targetProdiIds as $index => $pId) {
                        if (empty($pId)) {
                            continue;
                        }

                        $query = DB::table('mata_kuliahs')
                            ->join('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
                            ->where('prodi_pivot_mk.pr_id', $pId)
                            ->where('mata_kuliahs.digit_mk', $value);

                        if ($isEditingMK) {
                            $query->where('mata_kuliahs.id', '!=', $this->selected_id_mk);
                        }

                        if ($query->exists()) {
                            $namaProdi = DB::table('prodis')->where('id', $pId)->value('nama_pr') ?? "Prodi ID: $pId";
                            $fail("Digit MK '$value' sudah terpakai di Program Studi: ***$namaProdi***.");
                            break;
                        }
                    }
                },
            ],
            'sks_kuliah' => 'required|integer|min:1',
            'tipe_sks' => 'required|in:1,2,3,4',
            'is_wajib' => 'required|boolean',
            'deskripsi' => 'required|string|min:5|max:1000',
            'bahan_kajian' => 'required|string|min:5|max:1000',
        ];

        if ($tingkatan === 1) {
            $rules['pr_id'] = 'required|integer|exists:prodis,id';
        } else {
            if ($tingkatan == 2) {
                $dpId = $data['dp_id'] ?? null;
                $rules['dp_id'] = [
                    'required', 'integer',
                    'exists:departemens,id',
                    function ($attribute, $value, $fail) use ($data) {
                        $validProdiIds = Prodi::where('dp_id', $value)->pluck('id')->toArray();
                        $selectedProdiIds = $data['pr_id_array'] ?? [];

                        $invalidSelected = array_diff($selectedProdiIds, $validProdiIds);

                        if (! empty($invalidSelected)) {
                            $fail('Beberapa Program Studi yang dipilih tidak terdaftar di Departemen ini!');
                        }

                        if (empty($validProdiIds)) {
                            $fail('Departemen ini belum memiliki Program Studi!');
                        }
                    },
                ];
            } elseif ($tingkatan == 3) {
                $fkId = $data['fk_id'] ?? null;
                $rules['fk_id'] = [
                    'required', 'integer',
                    'exists:fakultas,id',
                    function ($attribute, $value, $fail) use ($data) {
                        $validProdiIds = Prodi::whereHas('dp_rel', fn ($q) => $q->where('fk_id', $value))
                            ->pluck('id')->toArray();
                        $selectedProdiIds = $data['pr_id_array'] ?? [];

                        $invalidSelected = array_diff($selectedProdiIds, $validProdiIds);

                        if (! empty($invalidSelected)) {
                            $fail('Beberapa Program Studi yang dipilih tidak terdaftar di Fakultas ini!');
                        }

                        if (empty($validProdiIds)) {
                            $fail('Fakultas ini belum memiliki Program Studi!');
                        }
                    },
                ];
            }
            $rules['pr_id_array'] = 'required|array|min:1';
        }

        $validator = Validator::make($data, $rules, $this->validationMessagesMK());

        if ($validator->fails()) {
            foreach ($validator->errors()->toArray() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
            $validator->validate();
        }

        return $validator->validated();
    }

    private function generateKodePrefix($data, $tingkatan)
    {
        if ($tingkatan === 1) { // Prodi
            return $this->prodi_kode ?? $this->departemen_kode ?? $this->fakultas_kode ?? 'UNI';
        } elseif ($tingkatan === 2) { // Departemen
            return $this->departemen_kode ?? $this->fakultas_kode ?? 'UNI';
        } elseif ($tingkatan === 3) { // Fakultas
            return $this->fakultas_kode ?? 'UNI';
        } elseif ($tingkatan === 4) {
            return 'UNI';
        }
    }

    public function saveMK($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['pr_id'] = $this->pr_id;
        $data['pr_id_array'] = $this->pr_id_array;

        $data['dp_id'] = $this->dp_id;
        $data['fk_id'] = $this->fk_id;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            $tingkatan = $this->mkType;
            $validated = $this->inputModalMK(false, $data);
            $kodePrefix = $this->generateKodePrefix($data, $tingkatan);

            DB::transaction(function () use ($validated, $tingkatan) {

                $mk = MataKuliah::create([
                    'level_mk' => $tingkatan,
                    // 'kode_mk' => $kodePrefix,
                    'digit_semester' => $validated['digit_semester'],
                    'digit_mk' => $validated['digit_mk'],
                    'nama_mk' => $validated['nama_mk'],
                    'semester' => $validated['semester'],
                    'sks_kuliah' => $validated['sks_kuliah'],
                    'tipe_sks' => $validated['tipe_sks'],
                    'is_wajib' => $validated['is_wajib'],
                    'deskripsi' => $validated['deskripsi'],
                    'bahan_kajian' => $validated['bahan_kajian'],
                ]);

                $targetIds = ($tingkatan === 1) ? [$this->pr_id] : ($this->pr_id_array ?: []);
                $targetIds = array_filter($targetIds);
                if (! empty($targetIds)) {
                    $mk->prodis()->attach($targetIds);
                }
            });

            $this->resetInputMK();
            $this->dispatch('refresh-data-mk');

            $this->showMKModal = false;
            $this->toast(message: "Mata Kuliah {$validated['nama_mk']}");

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-mk');
            $this->showMKModal = false;
        }
    }

    public function updateMK($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $data['pr_id'] = $this->pr_id;
        $data['pr_id_array'] = $this->pr_id_array;

        $data['dp_id'] = $this->dp_id;
        $data['fk_id'] = $this->fk_id;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            $validated = $this->inputModalMK(true, $data);
            $tingkatan = $this->mkType;
            $kodePrefix = $this->generateKodePrefix($data, $tingkatan);

            DB::transaction(function () use ($validated, $tingkatan) {
                $mk = MataKuliah::findOrFail($this->selected_id_mk);

                // 3. UPDATE DATA UTAMA
                $mk->update([
                    // 'kode_mk' => $kodePrefix,
                    'digit_semester' => $validated['digit_semester'],
                    'digit_mk' => $validated['digit_mk'],
                    'nama_mk' => $validated['nama_mk'],
                    'semester' => $validated['semester'],
                    'sks_kuliah' => $validated['sks_kuliah'],
                    'tipe_sks' => $validated['tipe_sks'],
                    'is_wajib' => $validated['is_wajib'],
                    'deskripsi' => $validated['deskripsi'],
                    'bahan_kajian' => $validated['bahan_kajian'],
                ]);

                // 4. LOGIKA TARGET IDs
                $targetIds = ($tingkatan === 1)
                            ? [$this->pr_id]
                            : ($this->pr_id_array ?: []);

                $cleanIds = array_values(array_filter($targetIds));

                // 5. SINKRONISASI RELASI PIVOT DENGAN SORT ORDER
                $syncData = [];
                foreach ($cleanIds as $index => $id) {
                    $syncData[$id] = ['sort_order' => $index];
                }

                $mk->prodis()->sync($syncData);
            });

            $this->toast(message: "Mata Kuliah {$validated['nama_mk']}", type: 'update');
            $this->resetInputMK();

            $this->dispatch('refresh-data-mk');
            $this->showMKModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-mk');
            $this->showMKModal = false;
        }
    }

    private function validationMessagesMK()
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

            // Nama Mata Kuliah
            'nama_mk.required' => 'Nama Mata Kuliah wajib diisi!',
            'nama_mk.string' => 'Nama Mata Kuliah harus berupa teks!',
            'nama_mk.max' => 'Nama Mata Kuliah tidak boleh lebih dari 255 karakter!',

            // Semester (Integer)
            'semester.required' => 'Semester wajib diisi!',
            'semester.integer' => 'Semester harus berupa angka!',
            'semester.min' => 'Semester minimal adalah 1!',
            'semester.max' => 'Semester maksimal adalah 8!',

            // Digit Semester & Digit MK (String size 2)
            'digit_semester.required' => 'Digit Semester wajib diisi!',
            'digit_semester.size' => 'Digit Semester harus tepat 2 karakter (contoh: 01)!',

            'digit_mk.required' => 'Digit MK wajib diisi!',
            'digit_mk.size' => 'Digit MK harus tepat 2 karakter (contoh: 07)!',

            // SKS
            'sks_kuliah.required' => 'SKS Mata Kuliah wajib diisi!',
            'sks_kuliah.integer' => 'SKS harus berupa angka!',
            'sks_kuliah.min' => 'SKS minimal adalah 1!',

            // Tipe SKS & Status Wajib
            'tipe_sks.required' => 'Tipe SKS wajib dipilih!',
            'tipe_sks.in' => 'Tipe SKS yang dipilih tidak valid!',
            'is_wajib.required' => 'Status kewajiban Mata Kuliah wajib ditentukan!',
            'is_wajib.boolean' => 'Format status wajib tidak valid!',

            'deskripsi.string' => 'Deskripsi Mata Kuliah harus berupa text!',
            'deskripsi.required' => 'Deskripsi Mata Kuliah wajib diisi!',
            'deskripsi.min' => 'Deskripsi Mata Kuliah terlalu pendek (Minimal 5 karakter)!',
            'deskripsi.max' => 'Deskripsi Mata Kuliah terlalu panjang (Maksimal 1000 karakter)!',

            'bahan_kajian.required' => 'Bahan Kajian Mata Kuliah wajib diisi!',
            'bahan_kajian.string' => 'Bahan Kajian harus berupa text!',
            'bahan_kajian.min' => 'Bahan Kajian Mata Kuliah terlalu pendek (Minimal 5 karakter)!',
            'bahan_kajian.max' => 'Bahan Kajian Mata Kuliah terlalu panjang (Maksimal 1000 karakter)!',
        ];
    }

    public function getMKErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'nama_mk',
                'digit_mk',
                'semester',
                'tipe_sks',
                'sks_kuliah',
                'is_wajib',
            ]),
            2 => $this->getErrorCount([
                'deskripsi',
                'bahan_kajian',
            ]),
        ];
    }

    private function resetInputMK()
    {
        $fields = [
            'selected_id_mk',
            'pr_id', 'dp_id', 'fk_id',
            'pr_items', 'dp_items', 'fk_items',
            'pr_id_array', 'pr_items_array',
            'prNameSearch', 'dpNameSearch', 'fkNameSearch',
        ];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
