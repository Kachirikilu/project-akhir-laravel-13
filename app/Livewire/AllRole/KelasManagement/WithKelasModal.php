<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Kelas\Kelas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithKelasModal
{
    use HasErrorCount;
    use HasToast;

    public $selected_id_kelas;

    public $isEditingKelas = false;

    public $showEditKelas = false;

    public $showKelasModal = false;

    public $pr_id_2;

    public $rps_id_2;

    public function addKelas()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditKelas == true) {
            $this->resetInputKelas();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingKelas = false;
        $this->showKelasModal = true;
        $this->showEditKelas = false;

        $this->updatedRPSNameSearch($this->dpNameSearch);
        $this->updatedPrNameSearch($this->prNameSearch);
    }

    public function editKelas($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputKelas();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_kelas = $id;
        $this->isEditingKelas = true;
        $this->showEditKelas = true;

        try {
            $kelas = Kelas::find($id);

            $this->pr_id = $kelas->pr_id;
            $this->pr_id_2 = $kelas->pr_id;
            $this->pr_items = $this->itemsPr($kelas->pr_rel);
            $this->prNameSearch = $kelas->prodi;

            $this->rps_id = $kelas->rps_id;
            $this->rps_id_2 = $kelas->rps_id;
            $this->rps_items = $this->itemsRPS($kelas->rps_rel);
            $this->rpsNameSearch = $kelas->rps_rel?->rps;

            $this->dispatch('fill-modal-kelas', kelas: $kelas);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalKelas($isEditingKelas, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $data['nama_kelas'] = $this->normalizeNama($data['nama_kelas'] ?? '');

        $rps = DB::table('rps')->where('id', $this->rps_id ?? null)->first();
        $desRPS = $rps?->deskripsi ?? '';
        $desMK = $rps?->mk_rel?->deskripsi ?? '';
        if (! str_ends_with($desRPS, '.') && ! empty($desRPS)) {
            $desRPS .= '.';
        }
        if ($data['deskripsi'] == $desRPS || $data['deskripsi'] == $desMK) {
            $data['deskripsi'] = '';
        }

        $data['deskripsi'] = $this->normalizeText($data['deskripsi'] ?? '');

        $prId = $data['pr_id'] ?? $this->pr_id ?? null;

        $rules = [
            'kode_kelas_1' => 'required|alpha|max:10',
            'kode_kelas_2' => 'required|numeric|min:1',
            'kode_kelas' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingKelas) {
                    $query = DB::table('kelas')->where('kode_kelas', $value);

                    if ($isEditingKelas) {
                        $query->where('id', '!=', $this->selected_id_kelas);
                    }

                    if ($query->exists()) {
                        $fail("Kode Kelas '$value' sudah digunakan!");
                    }
                },
            ],
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|min:5|max:1000',
            'pr_id' => 'required|integer|exists:prodis,id',
            'rps_id' => [
                'required',
                'integer',
                'exists:rps,id',
                function ($attribute, $value, $fail) use ($prId) {
                    $isValid = Prodi::whereHas('mata_kuliahs.rps', fn ($q) => $q->where('id', $value))
                        ->where('prodis.id', $prId)
                        ->exists();

                    if (! $isValid) {
                        $fail('RPS tidak terdaftar pada Program Studi yang dipilih!');
                    }
                },
            ],
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesKelas());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_kelas_1']) && empty($data['kode_kelas_2'])) {
                $this->addError('kode_kelas', 'Kode Kelas wajib diisi!');
            } elseif ($errors->has('kode_kelas_1') || $errors->has('kode_kelas_2')) {
                $combinedMessage = $errors->first('kode_kelas_1') ?: $errors->first('kode_kelas_2');
                $this->addError('kode_kelas', $combinedMessage);
            }
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_kelas_1', 'kode_kelas_2', 'kode_kelas'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_kelas' && ! $this->getErrorBag()->has('kode_kelas')) {
                    $this->addError('kode_kelas', $messages[0]);
                }
            }
            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        return $validator->validated();
    }

    public function saveKelas($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $data['pr_id'] = $this->pr_id;
        $data['rps_id'] = $this->rps_id;

        try {
            $validated = $this->inputModalKelas(false, $data);

            DB::transaction(function () use ($validated) {
                Kelas::create([
                    'kode_kelas' => $validated['kode_kelas'],
                    'pr_id' => $validated['pr_id'],
                    'rps_id' => $validated['rps_id'],
                    'nama_kelas' => $validated['nama_kelas'],
                    'deskripsi' => $validated['deskripsi'],
                ]);
            });

            $this->toast(message: "Kelas {$validated['nama_kelas']} ({$validated['kode_kelas']})");
            $this->resetInputKelas();

            $this->dispatch('refresh-data-kelas');
            $this->showKelasModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-kelas');
            $this->showKelasModal = false;
        }
    }

    public function updateKelas($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        if ((empty($data['pr_id']) && $this->pr_id !== $this->pr_id_2) ||
            ($this->pr_id == $this->pr_id_2) || ($this->pr_id !== $this->pr_id_2)) {
            $data['pr_id'] = $this->pr_id;
        }
        if ((empty($data['rps_id']) && $this->rps_id !== $this->rps_id_2) ||
            ($this->rps_id == $this->rps_id_2) || ($this->rps_id !== $this->rps_id_2)) {
            $data['rps_id'] = $this->rps_id;
        }

        try {
            $validated = $this->inputModalKelas(true, $data);

            DB::transaction(function () use ($validated) {
                $kelas = Kelas::findOrFail($this->selected_id_kelas);

                $kelas->update([
                    'kode_kelas' => $validated['kode_kelas'],
                    'pr_id' => $validated['pr_id'],
                    'rps_id' => $validated['rps_id'],
                    'nama_kelas' => $validated['nama_kelas'],
                    'deskripsi' => $validated['deskripsi'],
                ]);
            });

            $this->resetInputKelas();
            $this->dispatch('refresh-data-kelas');

            $this->showKelasModal = false;
            $this->toast(message: "Kelas {$validated['nama_kelas']} ({$validated['kode_kelas']})", type: 'update');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-kelas');
            $this->showKelasModal = false;
        }
    }

    private function validationMessagesKelas()
    {
        return [
            'kode_kelas_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_kelas_1.alpha' => 'Kode awalan harus berupa mutu!',
            'kode_kelas_1.max' => 'Kode awalan terlalu panjang!',

            // Kode Ref Bagian 2 (Angka - Kanan)
            'kode_kelas_2.required' => 'Nomor Kode (input kanan) wajib diisi!',
            'kode_kelas_2.numeric' => 'Nomor Kode harus berupa angka!',
            'kode_kelas_2.min' => 'Nomor Kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_kelas.required' => 'Kode Kelas lengkap wajib terbentuk!',
            'kode_kelas.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_kelas.required' => 'Kode Kelas wajib diisi!',
            'kode_kelas.alpha_num' => 'Kode Kelas hanya boleh berisi mutu dan angka!',
            'kode_kelas.max' => 'Kode Kelas maksimal 20 karakter!',

            'pr_id.required' => 'Program Studi wajib diisi!',
            'pr_id.integer' => 'ID Program Studi harus berupa angka!',
            'pr_id.exists' => 'Program Studi yang dipilih tidak valid!',

            'rps_id.required' => 'RPS wajib diisi!',
            'rps_id.integer' => 'ID RPS harus berupa angka!',
            'rps_id.exists' => 'RPS yang dipilih tidak valid!',

            // Nama Kelas
            'nama_kelas.required' => 'Nama Kelas wajib diisi!',
            'nama_kelas.string' => 'Nama Kelas harus berupa teks!',
            'nama_kelas.max' => 'Nama Kelas tidak boleh lebih dari 255 karakter!',

            'deskripsi.required' => 'Deskripsi Kelas wajib diisi!',
            'deskripsi.min' => 'Deskripsi Kelas terlalu pendek (Maksimal 5 karakter)!',
            'deskripsi.max' => 'Deskripsi Kelas terlalu panjang (Maksimal 1000 karakter)!',
        ];
    }

    public function getKelasErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'kode_kelas',
                'nama_kelas',
                'deskripsi',
            ]),
            2 => $this->getErrorCount([
                'pr_id',
                'rps_id',
            ]),
        ];
    }

    private function resetInputKelas()
    {
        $fields = [
            'selected_id_kelas',
            'pr_id',
            'rps_id',
        ];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
