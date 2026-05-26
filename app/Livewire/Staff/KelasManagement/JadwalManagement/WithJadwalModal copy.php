<?php

namespace App\Livewire\Staff\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Kelas\KelasJadwal;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithJadwalModal
{
    use HasErrorCount;
    use HasToast;

    public $selected_id_jadwal;

    public $isEditingJadwal = false;

    public $showEditJadwal = false;

    public $showJadwalModal = false;

    public $pr_id_2;

    public $rps_id_2;

    public function addJadwal()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditJadwal == true) {
            $this->resetInputJadwal();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingJadwal = false;
        $this->showJadwalModal = true;
        $this->showEditJadwal = false;

        $this->updatedMahasiswaNameSearch($this->mahasiswaNameSearch);
        // $this->updatedPrNameSearch($this->prNameSearch);
    }

    public function editJadwal($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputJadwal();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_jadwal = $id;
        $this->isEditingJadwal = true;
        $this->showEditJadwal = true;

        try {
            $jadwal = KelasJadwal::find($id);

            $this->selected_id_jadwal = $jadwal->id;

            $this->pr_id = $jadwal->pr_id;
            $this->pr_id_2 = $jadwal->pr_id;
            $this->pr_items = $this->itemsPr($jadwal->pr_rel);
            $this->prNameSearch = $jadwal->prodi;

            $this->rps_id = $jadwal->rps_id;
            $this->rps_id_2 = $jadwal->rps_id;
            $this->rps_items = $this->itemsRPS($jadwal->rps_rel);
            $this->rpsNameSearch = $jadwal->rps_rel?->rps;

            $this->dispatch('fill-modal-jadwal', jadwal: $jadwal);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalJadwal($isEditingJadwal, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $data['nama_jadwal'] = $this->normalizeNama($data['nama_jadwal'] ?? '');

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
            'kode_jadwal_1' => 'required|alpha|max:10',
            'kode_jadwal_2' => 'required|numeric|min:1',
            'kode_jadwal' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingJadwal) {
                    $query = DB::table('jadwal')->where('kode_jadwal', $value);

                    if ($isEditingJadwal) {
                        $query->where('id', '!=', $this->selected_id_jadwal);
                    }

                    if ($query->exists()) {
                        $fail("Kode Jadwal '$value' sudah digunakan!");
                    }
                },
            ],
            'nama_jadwal' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
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

        $validator = Validator::make($data, $rules, $this->validationMessagesJadwal());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_jadwal_1']) && empty($data['kode_jadwal_2'])) {
                $this->addError('kode_jadwal', 'Kode Jadwal wajib diisi!');
            } elseif ($errors->has('kode_jadwal_1') || $errors->has('kode_jadwal_2')) {
                $combinedMessage = $errors->first('kode_jadwal_1') ?: $errors->first('kode_jadwal_2');
                $this->addError('kode_jadwal', $combinedMessage);
            }
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_jadwal_1', 'kode_jadwal_2', 'kode_jadwal'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_jadwal' && ! $this->getErrorBag()->has('kode_jadwal')) {
                    $this->addError('kode_jadwal', $messages[0]);
                }
            }
            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        return $validator->validated();
    }

    public function saveJadwal($data, $kelasId)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $data['mahasiswa_id_array'] = $this->mahasiswa_id_array;

        // $kelasId untuk mencari Kelas
        dd($data);

        try {
            $validated = $this->inputModalJadwal(false, $data);

            DB::transaction(function () use ($validated, $kelasId) {
                KelasJadwal::create([
                    'kelas_id' => $kelasId,
                    'password' => $validated['password'],
                    'kode_wilayah' => $validated['kode_wilayah'],
                    'label_kelas' => $validated['label_kelas'],
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_selesai' => $validated['tanggal_selesai'],
                    'hari_pelasanaan' => $validated['hari_pelasanaan'],
                    'jam_pelasanaan' => $validated['jam_pelasanaan'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'kapasitas' => $validated['kapasitas'],
                ]);
            });

            $this->toast(message: "Jadwal {$validated['nama_jadwal']} ({$validated['kode_jadwal']})");
            $this->resetInputJadwal();

            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalModal = false;
        }
    }

    public function updateJadwal($data, $kelasId)
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
            $validated = $this->inputModalJadwal(true, $data);

            DB::transaction(function () use ($validated) {
                $jadwal = KelasJadwal::findOrFail($this->selected_id_jadwal);

                $jadwal->update([
                    'kode_jadwal' => $validated['kode_jadwal'],
                    'pr_id' => $validated['pr_id'],
                    'rps_id' => $validated['rps_id'],
                    'nama_jadwal' => $validated['nama_jadwal'],
                    'deskripsi' => $validated['deskripsi'],
                ]);
            });

            $this->resetInputJadwal();
            $this->dispatch('refresh-data-jadwal');

            $this->showJadwalModal = false;
            $this->toast(message: "Jadwal {$validated['nama_jadwal']} ({$validated['kode_jadwal']})", type: 'update');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalModal = false;
        }
    }

    private function validationMessagesJadwal()
    {
        return [
            'kode_jadwal_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_jadwal_1.alpha' => 'Kode awalan harus berupa huruf!',
            'kode_jadwal_1.max' => 'Kode awalan terlalu panjang!',

            // Kode Ref Bagian 2 (Angka - Kanan)
            'kode_jadwal_2.required' => 'Nomor kode (input kanan) wajib diisi!',
            'kode_jadwal_2.numeric' => 'Nomor kode harus berupa angka!',
            'kode_jadwal_2.min' => 'Nomor kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_jadwal.required' => 'Kode Jadwal lengkap wajib terbentuk!',
            'kode_jadwal.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_jadwal.required' => 'Kode Jadwal wajib diisi!',
            'kode_jadwal.alpha_num' => 'Kode Jadwal hanya boleh berisi huruf dan angka!',
            'kode_jadwal.max' => 'Kode Jadwal maksimal 20 karakter!',

            'pr_id.required' => 'Program Studi wajib diisi!',
            'pr_id.integer' => 'ID Program Studi harus berupa angka!',
            'pr_id.exists' => 'Program Studi yang dipilih tidak valid!',

            'rps_id.required' => 'RPS wajib diisi!',
            'rps_id.integer' => 'ID RPS harus berupa angka!',
            'rps_id.exists' => 'RPS yang dipilih tidak valid!',

            // Nama Jadwal
            'nama_jadwal.required' => 'Nama Jadwal wajib diisi!',
            'nama_jadwal.string' => 'Nama Jadwal harus berupa teks!',
            'nama_jadwal.max' => 'Nama Jadwal tidak boleh lebih dari 255 karakter!',

            'deskripsi.required' => 'Deskripsi Jadwal wajib diisi!',
            'deskripsi.max' => 'Deskripsi Jadwal terlalu panjang (Maksimal 1000 karakter)!',
        ];
    }

    public function getJadwalErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'kode_jadwal',
                'nama_jadwal',
                'deskripsi',
            ]),
            2 => $this->getErrorCount([
            ]),
            3 => $this->getErrorCount([
            ]),
        ];
    }

    private function resetInputKelas()
    {
        $fields = [
            'selected_id_jadwal',
            'pr_id',
            'rps_id',
        ];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
