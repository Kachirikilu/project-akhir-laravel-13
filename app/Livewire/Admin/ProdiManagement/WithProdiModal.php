<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Livewire\Global\HasToast;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait WithProdiModal
{
    use HasToast;

    public $selected_id_pr;

    public $showProdiModal = false;

    public $isEditingPr = false;

    public $prodiType;

    public $dp_id_2;

    public $fk_id_2;

    protected $prodis = [
        'nama_pr' => 'required|string|max:255|unique:prodis,nama_pr',
        'dp_id' => 'required|exists:departemens,id',
        'nama_dp' => 'required|string|max:255|unique:departemens,nama_dp',
        'fk_id' => 'required|exists:fakultas,id',
        'nama_fk' => 'required|string|max:255|unique:fakultas,nama_fk',
    ];

    public function addProdi($type)
    {
        if (! $this->AuthCheck()) {
            return;
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingPr = false;
        $this->prodiType = $type;
        $this->showProdiModal = true;
        if ($type === 'prodi') {
            if (Auth::user()->tingkat < 3) {
                if (Auth::user()->tingkat == 2) {
                    $this->fk_id = Auth::user()->fk_id;
                    $this->dpLevel = 2;
                }
                $this->updatedDpNameSearch($this->fkNameSearch);
            } else {
                $this->dp_id = Auth::user()->dp_id;
            }
        } elseif ($type === 'departemen') {
            if (Auth::user()->tingkat < 2) {
                $this->updatedFkNameSearch($this->fkNameSearch);
            } else {
                $this->fk_id = Auth::user()->fk_id;
            }
        }
    }

    public function editProdi($id, $type)
    {
        if (! $this->AuthCheck()) {
            return;
        }

        $this->selected_id_pr = $id;
        $this->prodiType = $type;
        $this->isEditingPr = true;

        $this->resetValidation();
        $this->resetErrorBag();

        $this->dp_id = $this->fk_id = $this->selected_id_pr = null;

        try {
            if ($type === 'prodi') {
                $prodi = Prodi::with('dp_rel')->findOrFail($id);
                $this->selected_id_pr = $prodi->id;
                if (Auth::user()->tingkat < 3) {
                    if (Auth::user()->tingkat == 2) {
                        $this->fk_id = Auth::user()->fk_id;
                        $this->dpLevel = 2;
                    }
                    $this->dp_id = $prodi->dp_id ?? null;
                    $this->dp_id_2 = $prodi->dp_id ?? null;
                    $this->fetchDp();
                } else {
                    $this->dp_id = Auth::user()->dp_id;
                }
            } elseif ($type === 'departemen') {
                $departemen = Departemen::with('fk_rel')->findOrFail($id);
                $this->selected_id_pr = $departemen->id;
                if (Auth::user()->tingkat < 2) {
                    $this->fk_id = $departemen->fk_id;
                    $this->fk_id_2 = $departemen->fk_id;
                    $this->fetchFk();
                } else {
                    $this->fk_id = Auth::user()->fk_id;
                }
            } elseif ($type === 'fakultas') {
                $fakultas = Fakultas::findOrFail($id);
                $this->selected_id_pr = $fakultas->id;
            }

            $this->showProdiModal = true;

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalProdi($isEditingPr, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $prodis = [];
        $data['target_sks'] = (int) $data['target_sks'];
        if ($data['target_sks'] == null || $data['target_sks'] == 0) {
            $data['target_sks'] = 144;
        }

        /* ===================== PROGRAM STUDI ===================== */
        if ($this->prodiType === 'prodi') {

            $kodePr = $data['kode_pr'] ?? null;
            if (! empty($kodePr) && ! empty($data['dp_id'])) {
                $departemen = DB::table('departemens')->find($data['dp_id']);
                $kodeDp = $departemen->kode_dp;

                if (empty($kodeDp) && $departemen) {
                    $fakultas = DB::table('fakultas')->find($departemen->fk_id);
                    $kodeDp = $fakultas->kode_fk;
                }
                if ($kodePr === $kodeDp) {
                    $data['kode_pr'] = null;
                }
            }

            $prodis = [
                'nama_pr' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('prodis', 'nama_pr', $isEditingPr ? $this->selected_id_pr : null),
                ],
                'target_sks' => ['integer', 'min:36', 'max:255'],
                'kode_pr' => [
                    'nullable', 'string', 'min:3', 'max:3',
                    function ($attribute, $value, $fail) use ($data) {
                        if (empty($value)) {
                            return;
                        }

                        $departemen = DB::table('departemens')->find($data['dp_id']);
                        $fakultasId = $departemen ? $departemen->fk_id : null;

                        $otherDp = DB::table('departemens')->where('kode_dp', $value)->exists();
                        $otherFk = DB::table('fakultas')->where('kode_fk', $value)->exists();

                        $otherPr = DB::table('prodis')
                            ->where('kode_pr', $value)
                            ->where('id', '!=', $this->selected_id_pr)
                            ->exists();

                        if (empty($data['dp_id'])) {
                            $fail('Isi terlebih dahulu Departemen!');
                        } elseif ($otherDp || $otherFk || $otherPr) {
                            $fail('Kode Program Studi ini sudah digunakan oleh instansi lain!');
                        }
                    },
                ],
                'dp_id' => ['required', 'integer', 'exists:departemens,id'],
                'strata' => [
                    'required',
                    Rule::in(['Sarjana', 'Magister', 'Doktor']),
                ],
            ];
        }

        /* ===================== JURUSAN ===================== */
        elseif ($this->prodiType === 'departemen') {

            $kodeDp = $data['kode_dp'] ?? null;
            if (! empty($kodeDp) && ! empty($data['fk_id'])) {
                $fakultas = DB::table('fakultas')->find($data['fk_id']);
                $kodeFk = $fakultas->kode_fk ?? null;
                if ($kodeDp === $kodeFk) {
                    $data['kode_dp'] = null;
                }
            }

            $prodis = [
                'nama_dp' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('departemens', 'nama_dp', $isEditingPr ? $this->selected_id_pr : null),
                ],
                'kode_dp' => [
                    'nullable', 'string', 'min:3', 'max:3',
                    $this->uniqueRule('departemens', 'kode_dp', $isEditingPr ? $this->selected_id_pr : null),

                    function ($attribute, $value, $fail) use ($data) {
                        if (empty($value)) {
                            return;
                        }

                        // 1. Gagal jika dipakai Fakultas lain (bukan induknya)
                        $otherFk = DB::table('fakultas')
                            ->where('kode_fk', $value)
                            ->where('id', '!=', $data['fk_id'])
                            ->exists();

                        // 2. Gagal jika dipakai Departemen lain yang beda Fakultas
                        $otherDp = DB::table('departemens')
                            ->where('kode_dp', $value)
                            ->where('fk_id', '!=', $data['fk_id'])
                            ->where('id', '!=', $this->selected_id_pr)
                            ->exists();

                        // 3. Gagal jika dipakai Prodi yang berasal dari Departemen di luar Fakultas ini
                        $otherPr = DB::table('prodis')
                            ->join('departemens', 'prodis.dp_id', '=', 'departemens.id')
                            ->where('prodis.kode_pr', $value)
                            ->where('departemens.fk_id', '!=', $data['fk_id'])
                            ->exists();

                        if (empty($data['fk_id'])) {
                            $fail('Isi terlebih dahulu Fakultas!');
                        } elseif ($otherFk || $otherDp || $otherPr) {
                            $fail('Kode Departemen ini sudah digunakan oleh instansi di luar lingkup Fakultas Anda!');
                        }
                    },
                ],
                'fk_id' => ['required', 'integer', 'exists:fakultas,id'],
            ];
        }

        /* ===================== FAKULTAS ===================== */
        elseif ($this->prodiType === 'fakultas') {
            $prodis = [
                'nama_fk' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('fakultas', 'nama_fk', $isEditingPr ? $this->selected_id_pr : null),
                ],
                'kode_fk' => [
                    'required', 'string', 'min:3', 'max:3',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            return;
                        }

                        // 1. Cek tabel Fakultas lain (Standard Unique)
                        $otherFk = DB::table('fakultas')->where('kode_fk', $value)->where('id', '!=', $this->selected_id_pr)->exists();
                        // 2. Gagal jika dipakai oleh Departemen yang bukan milik Fakultas ini
                        $otherDp = DB::table('departemens')->where('kode_dp', $value)->where('fk_id', '!=', $this->selected_id_pr)->exists();
                        // 3. Gagal jika dipakai oleh Prodi yang bukan milik Fakultas ini
                        $otherPr = DB::table('prodis')
                            ->join('departemens', 'prodis.dp_id', '=', 'departemens.id')
                            ->where('prodis.kode_pr', $value)
                            ->where('departemens.fk_id', '!=', $this->selected_id_pr)
                            ->exists();

                        if ($otherFk || $otherDp || $otherPr) {
                            $fail('Kode Fakultas ini sudah digunakan oleh Departemen/Prodi dari Fakultas lain!');
                        }
                    },
                ],
            ];
        }

        return Validator::make($data, $prodis, $this->validationMessagesProdi())->validate();
    }

    private function uniqueRule(string $table, string $column, $id = null)
    {
        return $id ? Rule::unique($table, $column)->ignore($id) : Rule::unique($table, $column);
    }

    private function prepareData(array $validated)
    {
        if ($this->prodiType === 'prodi') {
            $pattern = '/\b(s1|s2|s3|sarjana|magister|doktor)\b/i';
            $namaBersih = preg_replace($pattern, '', $validated['nama_pr']);
            $validated['nama_pr'] = $this->normalizeNama(trim($namaBersih));

        } elseif ($this->prodiType === 'departemen') {
            $nama = preg_replace('/^departemen\s+/i', '', trim($validated['nama_dp']));
            $validated['nama_dp'] = $this->normalizeNama($nama);

        } elseif ($this->prodiType === 'fakultas') {
            $nama = preg_replace('/^fakultas\s+/i', '', trim($validated['nama_fk']));
            $validated['nama_fk'] = $this->normalizeNama($nama);
        }

        return $validated;
    }

    private function formatStrata(string $strata): string
    {
        return match ($strata) {
            'Sarjana' => 'S1',
            'Magister' => 'S2',
            'Doktor' => 'S3',
            default => $strata,
        };
    }

    public function saveProdi($data)
    {
        if (! $this->AuthCheck()) {
            return;
        }

        $data['dp_id'] = $this->dp_id;
        $data['fk_id'] = $this->fk_id;

        if (empty($data['strata'])) {
            $data['strata'] = 'Sarjana';
        }

        try {

            $validated = $this->inputModalProdi(false, $data);
            $validated = $this->prepareData($validated);
            $message = '';

            DB::transaction(function () use ($validated, $message) {
                if ($this->prodiType === 'prodi') {
                    $strata = $this->formatStrata($validated['strata']);
                    $message = 'Program Studi '.$strata.' '.$validated['nama_pr'];
                    Prodi::create([
                        'nama_pr' => $validated['nama_pr'],
                        'target_sks' => $validated['target_sks'],
                        'strata' => $validated['strata'],
                        'dp_id' => $validated['dp_id'],
                        'kode_pr' => $validated['kode_pr'],
                    ]);
                } elseif ($this->prodiType === 'departemen') {
                    $message = 'Departemen '.$validated['nama_dp'];
                    Departemen::create([
                        'nama_dp' => $validated['nama_dp'],
                        'fk_id' => $validated['fk_id'],
                        'kode_dp' => $validated['kode_dp'],
                    ]);
                } elseif ($this->prodiType === 'fakultas') {
                    $message = 'Fakultas '.$validated['nama_fk'];
                    Fakultas::create([
                        'nama_fk' => $validated['nama_fk'],
                        'kode_fk' => $validated['kode_fk'],
                    ]);
                }
            });

            $this->toast(message: $message);
            $this->resetInputProdi();

            $this->dispatch('refresh-data-pr');
            $this->dispatch('refresh-stats-pr');
            $this->showProdiModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-pr');
            $this->showProdiModal = false;
        }
    }

    public function updateProdi($data)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        if ((empty($data['dp_id']) && $this->dp_id !== $this->dp_id_2) ||
            ($this->dp_id == $this->dp_id_2) || ($this->dp_id !== $this->dp_id_2)) {
            $data['dp_id'] = $this->dp_id;
        }
        if ((empty($data['fk_id']) && $this->fk_id !== $this->fk_id_2) ||
            ($this->fk_id == $this->fk_id_2) || ($this->fk_id !== $this->fk_id_2)) {
            $data['fk_id'] = $this->fk_id;
        }

        if (empty($data['strata'])) {
            $data['strata'] = 'Sarjana';
        }

        try {
            $validated = $this->inputModalProdi(true, $data);
            $validated = $this->prepareData($validated);
            $message = '';

            DB::transaction(function () use ($validated, &$message) {
                if ($this->prodiType === 'prodi') {
                    $strata = $this->formatStrata($validated['strata']);
                    $message = 'Program Studi '.$strata.' '.$validated['nama_pr'];
                    Prodi::findOrFail($this->selected_id_pr)->update([
                        'nama_pr' => $validated['nama_pr'],
                        'target_sks' => $validated['target_sks'],
                        'strata' => $validated['strata'],
                        'dp_id' => $validated['dp_id'],
                        'kode_pr' => $validated['kode_pr'],
                    ]);
                } elseif ($this->prodiType === 'departemen') {
                    $message = 'Departemen '.$validated['nama_dp'];
                    Departemen::findOrFail($this->selected_id_pr)->update([
                        'nama_dp' => $validated['nama_dp'],
                        'fk_id' => $validated['fk_id'],
                        'kode_dp' => $validated['kode_dp'],
                    ]);
                } elseif ($this->prodiType === 'fakultas') {
                    $message = 'Fakultas '.$validated['nama_fk'];
                    Fakultas::findOrFail($this->selected_id_pr)->update([
                        'nama_fk' => $validated['nama_fk'],
                        'kode_fk' => $validated['kode_fk'],
                    ]);
                }
            });

            $this->toast(message: $message, type: 'update');
            $this->resetInputProdi();

            $this->dispatch('refresh-data-pr');
            $this->showProdiModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
        }
    }

    private function validationMessagesProdi()
    {
        return [
            /* --- Program Studi --- */
            'nama_pr.required' => 'Nama Program Studi wajib diisi!',
            'nama_pr.max' => 'Nama Program Studi tidak boleh lebih dari 255 karakter!',
            'nama_pr.unique' => 'Nama Program Studi sudah ada di database!',
            'target_sks.interger' => 'Target SKS harus berupa angka!',
            'target_sks.min' => 'Target SKS minimal adalah 36 SKS!',
            'target_sks.max' => 'Target SKS maksimal adalah 255 SKS!',
            'strata.required' => 'Nama Strata wajib diisi!',
            'strata.in' => 'Nama Strata yang dipilih tidak sesuai dengan kategori yang diizinkan!',
            'kode_pr.min' => 'Kode Program Studi tidak boleh kurang dari 3 karakter!',
            'kode_pr.max' => 'Kode Program Studi tidak boleh lebih dari 3 karakter!',
            'kode_pr.string' => 'Kode Program Studi harus berupa teks!',
            'kode_pr.unique' => 'Kode Program Studi ini sudah digunakan oleh Program Studi lain!',
            'dp_id.required' => 'Departemen wajib diisi!',
            'dp_id.integer' => 'ID Departemen harus berupa angka!',
            'dp_id.exists' => 'Departemen yang dipilih tidak valid!',

            /* --- Departemen --- */
            'nama_dp.required' => 'Nama Departemen wajib diisi!',
            'nama_dp.max' => 'Nama Departemen tidak boleh lebih dari 255 karakter!',
            'nama_dp.unique' => 'Nama Departemen sudah ada di database!',
            'kode_dp.min' => 'Kode Departemen tidak boleh kurang dari 3 karakter!',
            'kode_dp.max' => 'Kode Departemen tidak boleh lebih dari 3 karakter!',
            'kode_dp.string' => 'Kode Departemen harus berupa teks!',
            'kode_dp.unique' => 'Kode Departemen ini sudah terdaftar di database!',
            'fk_id.required' => 'Fakultas wajib diisi!',
            'fk_id.integer' => 'ID Fakultas harus berupa angka!',
            'fk_id.exists' => 'Fakultas yang dipilih tidak valid!',

            /* --- Fakultas --- */
            'nama_fk.required' => 'Nama Fakultas wajib diisi!',
            'nama_fk.max' => 'Nama Fakultas tidak boleh lebih dari 255 karakter!',
            'nama_fk.unique' => 'Nama Fakultas sudah ada di database!',
            'kode_fk.required' => 'Kode Fakultas wajib diisi!',
            'kode_fk.min' => 'Kode Fakultas tidak boleh kurang dari 3 karakter!',
            'kode_fk.max' => 'Kode Fakultas tidak boleh lebih dari 3 karakter!',
            'kode_fk.unique' => 'Kode Fakultas sudah terdaftar di database!',
            'kode_fk.string' => 'Kode Fakultas harus berupa teks!',
        ];
    }

    private function resetInputProdi()
    {
        $fields = [
            'selected_id_pr',
            // 'nama_pr', 'strata', 'nama_dp', 'nama_fk',
            'dp_id', 'dp_id_2', 'fk_id', 'fk_id_2', 'dpNameSearch', 'fkNameSearch',
        ];

        // if (! $keepProdi) {
        //     $fields = array_merge($fields, ['pr_id', 'prNameSearch'
        //     // , 'prResults'
        //     ]);
        // }

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
