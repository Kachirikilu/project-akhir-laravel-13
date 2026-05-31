<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
// use App\Models\Kelas\KelasMahasiswa;
use App\Models\Kelas\KelasSesi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait WithJadwalModal
{
    use HasErrorCount;
    use HasToast;

    public $selected_jadwal_id;

    public $isEditingJadwal = false;

    public $showEditJadwal = false;

    public $showJadwalModal = false;

    public $showJadwalJoin = false;

    public $showJadwalLeft = false;

    public $sesi_1;

    public $sesi_2;

    public $sesi_3;

    public $sesi_4;

    public $sesi_5;

    public $sesi_6;

    public $sesi_7;

    public $sesi_8;

    public $sesi_9;

    public $sesi_10;

    public $sesi_11;

    public $sesi_12;

    public $sesi_13;

    public $sesi_14;

    public $sesi_15;

    public $sesi_16;

    public $tanggal_berakhir;

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
    }

    public function joinJadwal($data)
    {
        if (! $this->AuthCheck('mahasiswa')) {
            return;
        }

        try {
            $mahasiswa_id = Auth::user()->mahasiswa->id ?? null;

            if (empty($mahasiswa_id)) {
                $this->toast(message: 'Mahasiswa', type: 'unfound', variant: 'danger');

                return;
            }
            if (empty($data['jadwal_id'])) {
                $this->toast(message: 'Kelas', type: 'unfound', variant: 'danger');

                return;
            }
            $jadwal = KelasJadwal::with(['sesis', 'mahasiswas'])->where('id', $data['jadwal_id'])->first();

            $pw = $jadwal->password;
            $message = "Kelas {$jadwal->label_extra} dengan Kode {$jadwal->kode}";

            if ($data['password'] != $pw) {
                $this->toast(message: $message, type: 'join', variant: 'danger');

                throw ValidationException::withMessages([
                    'password' => 'Masukkan Password yang benar!',
                ]);
            }

            $jadwal->mahasiswas()->sync($mahasiswa_id);
            $this->toast(message: $message, type: 'join');

            $this->resetInputJadwal();
            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalJoin = false;

            $this->redirect(route('sesi-management', [$jadwal->kode_kelas, $jadwal->kode_jadwal]));

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function leftJadwal()
    {
        if (! $this->AuthCheck('mahasiswa')) {
            return;
        }

        try {
            $mahasiswa_id = Auth::user()->mahasiswa->id ?? null;

            if (empty($mahasiswa_id)) {
                $this->toast(message: 'Mahasiswa', type: 'unfound', variant: 'danger');

                return;
            }
            if (! property_exists($this, 'jadwal_id') || empty($this->jadwal_id)) {
                $this->toast(message: 'Kelas', type: 'unfound', variant: 'danger');

                return;
            }
            $jadwal = KelasJadwal::with(['sesis', 'mahasiswas'])->where('id', $this->jadwal_id)->first();

            if ($jadwal->mahasiswas()->detach($mahasiswa_id)) {
                $history = session('jadwal.history', []);
                $compositeKey =
                    $jadwal->kelas_rel->kode.'_'.$jadwal->kode_jadwal;
                unset($history[$compositeKey]);
                session(['jadwal.history' => $history]);
                $this->toast(message: "Kelas {$jadwal->label_extra} dengan Kode {$jadwal->kode}", type: 'left');
                $this->redirect(route('jadwal-management', $jadwal->kode_kelas));

            } else {
                $this->toast(message: "Kelas {$jadwal->label_extra} dengan Kode {$jadwal->kode}", type: 'left', variant: 'danger');

                return;
            }

            $this->resetInputJadwal();
            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalLeft = false;

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function editJadwal($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputJadwal();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_jadwal_id = $id;
        $this->isEditingJadwal = true;
        $this->showEditJadwal = true;

        try {

            $jadwal = KelasJadwal::with([
                'sesis',
                'mahasiswas',
            ])->findOrFail($id);

            $this->mahasiswa_id_array = $jadwal->mahasiswas->pluck('id')->toArray();
            $this->mahasiswa_items_array = $jadwal->mahasiswas->map(function ($d) {
                return $this->itemsMahasiswa($d);
            })->toArray();

            for ($i = 1; $i <= 16; $i++) {
                $this->{'sesi_'.$i} = null;
            }

            $this->tanggal_berakhir = $jadwal->tanggal_berakhir ?? null;

            // ======================================
            // FILL SESI
            // ======================================
            foreach ($jadwal->sesis as $sesi) {

                $index = $sesi->pertemuan_ke;

                if ($index < 1 || $index > 16) {
                    continue;
                }

                $this->{'sesi_'.$index} =
                    Carbon::parse(
                        $sesi->tanggal
                    )->format('Y-m-d');
            }

            $this->dispatch(
                'refresh-component'
            );

        } catch (\Exception $e) {

            $this->toast(
                text: 'Gagal Mengambil Data: '.
                    $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    private function inputModalJadwal($isEditingJadwal, $data, $kelasId)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $kelas = Kelas::with('jadwals')->find($kelasId);

        if (! $kelas) {
            throw ValidationException::withMessages([
                'kelas' => 'Kelas tidak ditemukan!',
            ]);
        }

        $mahasiswaCount = count(
            $data['mahasiswa_id_array'] ?? []
        );

        // ==========================================
        // TRANSFORM DATA (AMAN)
        // ==========================================

        if (
            ! empty($data['base_sesi_1']) &&
            ! empty($data['hari_pelaksanaan'])
        ) {

            $data['tanggal_mulai'] = Carbon::parse(
                $data['base_sesi_1']
            )->format('Y-m-d');
        }

        if (! empty($data['tanggal_berakhir'])) {
            [$yearPart, $weekPart] = explode(
                '-W',
                $data['tanggal_berakhir']
            );
            $year = (int) $yearPart;
            $week = (int) $weekPart;
            $data['tanggal_berakhir'] = Carbon::now()
                ->setISODate($year, $week, 7)
                ->format('Y-m-d');
        } else {
            if ($this->isEditingJadwal == true) {
                $data['tanggal_berakhir'] = $this->tanggal_berakhir;
            } else {
                $data['tanggal_berakhir'] = Carbon::parse(
                    $data['tanggal_mulai']
                )->addMonths(6)->format('Y-m-d');
            }
        }

        // ==========================================
        // RULES
        // ==========================================

        $rules = [
            'kode_wilayah' => 'required|in:IDL,PLG',
            'label_kelas' => 'required|string|max:5',
            'password' => 'nullable|string|max:14',
            'hari_pelaksanaan' => [
                'required',
                Rule::in([
                    'Senin',
                    'Selasa',
                    'Rabu',
                    'Kamis',
                    'Jumat',
                    'Sabtu',
                    'Minggu',
                ]),
            ],

            'tanggal_mulai' => [
                // 'required',
                function (
                    $attribute,
                    $value,
                    $fail
                ) use ($data) {

                    if (
                        empty($data['base_sesi_1']) && empty($data['tanggal_mulai']) && $this->isEditingJadwal == false
                    ) {
                        $fail(
                            'Tanggal mulai wajib diisi!'
                        );
                    }
                },
            ],

            // hasil transform
            'tanggal_berakhir' => [
                // 'required',
                'date',
                'after:tanggal_mulai',
            ],

            'jam_mulai' => 'required|date_format:H:i',
            'jam_berakhir' => ['required', 'date_format:H:i', 'after:jam_mulai'],

            'kapasitas' => [
                'required',
                'integer',
                'min:1',

                function (
                    $attribute,
                    $value,
                    $fail
                ) use ($mahasiswaCount) {

                    if (
                        $mahasiswaCount >
                        (int) $value
                    ) {
                        $fail(
                            'Jumlah mahasiswa melebihi kapasitas kelas!'
                        );
                    }
                },
            ],

            'mahasiswa_id_array' => 'nullable|array',

            'mahasiswa_id_array.*' => 'exists:users,id',
        ];

        // ==========================================
        // SESI 1 - 16 WAJIB
        // ==========================================

        for ($i = 1; $i <= 16; $i++) {

            $rules["sesi_{$i}"] = [
                'required',
                'date',
            ];
        }

        // ==========================================
        // VALIDATOR
        // ==========================================

        $validator = Validator::make($data, $rules, $this->validationMessagesJadwal()
        );

        // ==========================================
        // VALIDASI DUPLIKAT
        // ==========================================

        $validator->after(function ($validator) use (
            $kelas,
            $data,
            $isEditingJadwal
        ) {

            $query = $kelas->jadwals()
                ->where(
                    'label_kelas',
                    $data['label_kelas']
                )
                ->where(
                    'kode_wilayah',
                    $data['kode_wilayah']
                );

            if ($isEditingJadwal) {
                $query->where(
                    'id',
                    '!=',
                    $this->selected_jadwal_id
                );
            }

            if ($query->exists()) {
                $validator->errors()->add(
                    'label_kelas',
                    'Kelas ini sudah ada!'
                );
            }
        });

        if ($validator->fails()) {
            throw ValidationException::withMessages(
                $validator->errors()->toArray()
            );
        }

        return $validator->validated();
    }

    public function saveJadwal($data, $kelasId)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['mahasiswa_id_array'] = $this->mahasiswa_id_array ?? [];

        try {

            $validated = $this->inputModalJadwal(
                false,
                $data,
                $kelasId
            );

            DB::transaction(function () use ($validated, $data, $kelasId) {

                $jadwal = KelasJadwal::create([
                    'kelas_id' => $kelasId,
                    'password' => $validated['password'] ?? null,
                    'kode_wilayah' => $validated['kode_wilayah'],
                    'label_kelas' => $validated['label_kelas'],
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_berakhir' => $validated['tanggal_berakhir'],
                    'hari_pelaksanaan' => $validated['hari_pelaksanaan'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_berakhir' => $validated['jam_berakhir'],
                    'kapasitas' => $validated['kapasitas'],
                ]);

                // =========================================
                // CREATE SESI 1 - 16
                // =========================================
                for ($i = 1; $i <= 16; $i++) {

                    $tanggalSesi = $data["sesi_{$i}"] ?? null;

                    if (! $tanggalSesi) {
                        continue;
                    }

                    KelasSesi::create([
                        'kj_id' => $jadwal->id,
                        'pertemuan_ke' => $i,
                        'tanggal' => $tanggalSesi,
                    ]);
                }

                // =========================================
                // ATTACH MAHASISWA
                // =========================================
                if (! empty($validated['mahasiswa_id_array'])) {

                    $jadwal->mahasiswas()->sync(
                        $validated['mahasiswa_id_array']
                    );
                }
            });

            $this->toast(
                message: "Jadwal {$validated['label_kelas']} {$validated['kode_wilayah']}"
            );

            $this->resetInputJadwal();
            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalModal = false;

        } catch (ValidationException $e) {

            $this->toast(
                text: collect($e->errors())->flatten()->first(),
                variant: 'danger'
            );

            throw $e;
        } catch (\Exception $e) {

            $this->toast(
                text: 'Gagal Menambahkan: '.$e->getMessage(),
                variant: 'danger'
            );

            report($e);
        }
    }

    public function updateJadwal($data, $kelasId)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['mahasiswa_id_array'] =
            $this->mahasiswa_id_array ?? [];

        for ($i = 1; $i <= 16; $i++) {
            if (empty($data['sesi_'.$i])) {
                $data['sesi_'.$i] = $this->{'sesi_'.$i};
            }
        }

        if (empty($data['base_sesi_1'])) {
            $tanggalSesi1 = $data['sesi_1'] ?? null;
            if (! empty($tanggalSesi1)) {
                $data['base_sesi_1'] = Carbon::parse(
                    $tanggalSesi1
                )
                    ->startOfWeek(Carbon::MONDAY)
                    ->format('Y-m-d');
            }
        }

        $isRestartSesi = filter_var($data['restart_sesi'] ?? false, FILTER_VALIDATE_BOOLEAN) || ($data['restart_sesi'] ?? 0) == 1;

        try {

            $validated = $this->inputModalJadwal(true, $data, $kelasId);

            DB::transaction(function () use ($validated, $data, $isRestartSesi) {

                $jadwal = KelasJadwal::findOrFail(
                    $this->selected_jadwal_id
                );

                // =========================================
                // UPDATE JADWAL
                // =========================================
                $jadwal->update([
                    'password' => $validated['password'] ?? null,
                    'kode_wilayah' => $validated['kode_wilayah'],
                    'label_kelas' => $validated['label_kelas'],
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_berakhir' => $validated['tanggal_berakhir'],
                    'hari_pelaksanaan' => $validated['hari_pelaksanaan'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_berakhir' => $validated['jam_berakhir'],
                    'kapasitas' => $validated['kapasitas'],
                ]);

                // =========================================
                // UPDATE / CREATE SESI 1-16
                // =========================================
                for ($i = 1; $i <= 16; $i++) {
                    $tanggalSesi = $data["sesi_{$i}"] ?? null;

                    if (! $tanggalSesi) {
                        continue;
                    }

                    $sesi = KelasSesi::updateOrCreate(
                        [
                            'kj_id' => $jadwal->id,
                            'pertemuan_ke' => $i,
                        ],
                        [
                            'tanggal' => $tanggalSesi,
                        ]
                    );
                    if ($isRestartSesi) {
                        $sesi->override()->delete();
                    }
                }

                // =========================================
                // HAPUS SESI YANG TIDAK ADA
                // =========================================
                $jadwal->sesis()->whereNotIn('pertemuan_ke', range(1, 16))->delete();

                // =========================================
                // SYNC MAHASISWA
                // =========================================
                $jadwal->mahasiswas()->sync($validated['mahasiswa_id_array'] ?? []);
            });

            $this->resetInputJadwal();
            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalModal = false;

            $this->toast(message: "Jadwal {$validated['label_kelas']} {$validated['kode_wilayah']}", type: 'update');

        } catch (ValidationException $e) {

            $this->toast(
                text: collect($e->errors())
                    ->flatten()
                    ->first(),
                variant: 'danger'
            );

            throw $e;
        } catch (\Exception $e) {

            $this->toast(
                text: 'Gagal Memperbarui: '
                    .$e->getMessage(),
                variant: 'danger'
            );

            report($e);
        }
    }

    private function validationMessagesJadwal()
    {
        $messages = [
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi!',
            'tanggal_berakhir.required' => 'Tanggal berakhir wajib diisi!',
            'tanggal_berakhir.after' => 'Tanggal berakhir harus setelah tanggal mulai!',

            'kode_wilayah.required' => 'Wilayah wajib dipilih!',
            'label_kelas.required' => 'Label kelas wajib diisi!',
            'hari_pelaksanaan.required' => 'Hari pelaksanaan wajib dipilih!',

            'jam_mulai.required' => 'Jam mulai wajib diisi!',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid!',
            'jam_mulai.date_format' => 'Format jam mulai harus berupa HH:MM (contoh: 08:00)!',
            'jam_berakhir.required' => 'Jam berakhir wajib diisi!',
            'jam_berakhir.date_format' => 'Format jam berakhir harus berupa HH:MM (contoh: 09:40)!',
            'jam_berakhir.after' => 'Jam berakhir harus setelah jam mulai!',

            'kapasitas.required' => 'Kapasitas wajib diisi!',
            'kapasitas.integer' => 'Kapasitas harus berupa angka!',
            'kapasitas.min' => 'Kapasitas minimal 1!',

            'mahasiswa_id_array.required' => 'Mahasiswa wajib dipilih!',
            'mahasiswa_id_array.min' => 'Minimal harus ada satu Mahasiswa!',
            'mahasiswa_items_array.required' => 'Data detail Mahasiswa tidak boleh kosong!',
        ];

        // ==========================================
        // SESI 1 - 16
        // ==========================================

        for ($i = 1; $i <= 16; $i++) {

            $messages["sesi_{$i}.required"] =
                "Pertemuan {$i} wajib diisi!";

            $messages["sesi_{$i}.date"] =
                "Tanggal Pertemuan {$i} tidak valid!";
        }

        return $messages;
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

    private function resetInputJadwal()
    {
        $fields = [
            'selected_jadwal_id',
        ];

        $this->mahasiswaNameSearch = '';
        $this->mahasiswa_id_array = [];
        $this->mahasiswa_items_array = [];
        $this->mahasiswa_sub_items_array = [];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
