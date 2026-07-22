<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
// use App\Models\Kelas\KelasMahasiswa;
use App\Models\Kelas\KelasSesi;
use App\Models\Kelas\MahasiswaKehadiran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;

trait WithJadwalModal
{
    use HasErrorCount;
    use HasToast;

    public $selected_kj_id;

    public $isEditingJadwal = false;

    public $showEditJadwal = false;

    public $showJadwalModal = false;

    public $showJadwalJoin = false;

    public $showJadwalLeft = false;

    public $jadwal_input = [
        'hari_pelaksanaan' => '',
        'jam_mulai' => '',
        'jam_berakhir' => '',
        'tanggal_mulai' => '',
        'tanggal_berakhir' => '',
        'sesi_1' => '',
        'sesi_2' => '',
        'sesi_3' => '',
        'sesi_4' => '',
        'sesi_5' => '',
        'sesi_6' => '',
        'sesi_7' => '',
        'sesi_8' => '',
        'sesi_9' => '',
        'sesi_10' => '',
        'sesi_11' => '',
        'sesi_12' => '',
        'sesi_13' => '',
        'sesi_14' => '',
        'sesi_15' => '',
        'sesi_16' => '',
        'kapasitas' => '',
    ];

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

    #[On('join-jadwal-function')]
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
            if (empty($data['kj_id'])) {
                $this->toast(message: 'Kelas', type: 'unfound', variant: 'danger');

                return;
            }
            $jadwal = KelasJadwal::with(['sesis', 'mahasiswas'])->where('id', $data['kj_id'])->first();

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
            $this->dispatch('refresh-data-kelas');
            $this->clearKelasStatsCache();
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
            if (! property_exists($this, 'kj_id') || empty($this->kj_id)) {
                $this->toast(message: 'Kelas', type: 'unfound', variant: 'danger');

                return;
            }
            $jadwal = KelasJadwal::with(['sesis', 'mahasiswas'])->where('id', $this->kj_id)->first();
            if ($jadwal->mahasiswas()->detach($mahasiswa_id)) {
                $compositeKey = $jadwal->kode;
                $historyJadwal = session('jadwal.history', []);
                if (array_key_exists($compositeKey, $historyJadwal)) {
                    unset($historyJadwal[$compositeKey]);
                    session(['jadwal.history' => $historyJadwal]);
                }
                $historyMhs = session('jadwal_mahasiswa.history', []);
                if (array_key_exists($compositeKey, $historyMhs)) {
                    unset($historyMhs[$compositeKey]);
                    session(['jadwal_mahasiswa.history' => $historyMhs]);
                }

                $this->toast(message: "Kelas {$jadwal->label_extra} dengan Kode {$jadwal->kode}", type: 'left');
                $this->redirect(route('jadwal-management', $jadwal->kode_kelas));
            } else {
                $this->toast(message: "Kelas {$jadwal->label_extra} dengan Kode {$jadwal->kode}", type: 'left', variant: 'danger');

                return;
            }

            $this->resetInputJadwal();
            $this->dispatch('refresh-data-jadwal');
            $this->dispatch('refresh-data-sesi');
            $this->dispatch('refresh-data-kelas');
            $this->clearKelasStatsCache();
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

        $this->selected_kj_id = $id;
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
            $this->jadwal_input['hari_pelaksanaan'] = $jadwal->hari_pelaksanaan ?? null;
            $this->jadwal_input['jam_mulai'] = $jadwal->jam_mulai
                ? Carbon::parse($jadwal->jam_mulai)->format('H:i')
                : null;
            $this->jadwal_input['jam_berakhir'] = $jadwal->jam_berakhir
                ? Carbon::parse($jadwal->jam_berakhir)->format('H:i')
                : null;

            $this->jadwal_input['tanggal_berakhir'] = $jadwal->tanggal_berakhir ?? null;
            $this->jadwal_input['kapasitas'] = $jadwal->kapasitas ?? null;

            foreach ($jadwal->sesis as $sesi) {
                $index = $sesi->pertemuan_ke;
                if ($index < 1 || $index > 16) {
                    continue;
                }
                $this->jadwal_input['sesi_'.$index] = Carbon::parse(
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

        // if (! empty($data['tanggal_berakhir'])) {
        //     [$yearPart, $weekPart] = explode(
        //         '-W',
        //         $data['tanggal_berakhir']
        //     );
        //     $year = (int) $yearPart;
        //     $week = (int) $weekPart;
        //     $data['tanggal_berakhir'] = Carbon::now()
        //         ->setISODate($year, $week, 7)
        //         ->format('Y-m-d');

        // } else {
        if ($this->isEditingJadwal == true) {
            $data['tanggal_berakhir'] = $this->jadwal_input['tanggal_berakhir'];
        } else {
            $data['tanggal_berakhir'] = Carbon::parse(
                $data['tanggal_mulai']
            )->addMonths(6)->format('Y-m-d');
        }
        // }

        // ==========================================
        // RULES
        // ==========================================

        $rules = [
            'kode_wilayah' => 'required|in:IDL,PLG',
            'label_kelas' => 'required|string|max:5',
            'password' => 'nullable|string|min:4|max:14',
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
                    $this->selected_kj_id
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

    public function saveJadwal($dataAlpine, $kelasId)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data = array_merge($this->jadwal_input, $dataAlpine);

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
                $jamMulai = $validated['jam_mulai'];
                $sesiSent = $data['sesi_sent'];

                for ($i = 1; $i <= 16; $i++) {
                    $tanggalSesi = $data["sesi_{$i}"] ?? null;
                    if (! $tanggalSesi) {
                        continue;
                    }
                    // Jika true/1 -> custom_sent jadi 0 (Aktif)
                    // Jika false/0 -> custom_sent jadi 1 (Nonaktif)
                    $customSent = $sesiSent ? 0 : 1;

                    $jamAcuan = ! empty($jamMulai) ? $jamMulai : '00:00';
                    $waktuSesi = Carbon::parse($tanggalSesi.' '.$jamAcuan);

                    if ($waktuSesi->isPast()) {
                        $customSent = 1;
                    }

                    KelasSesi::create([
                        'kj_id' => $jadwal->id,
                        'pertemuan_ke' => $i,
                        'tanggal' => $tanggalSesi,
                        'reminder_sent' => $customSent,
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
            $this->dispatch('refresh-data-kelas');
            $this->dispatch('refresh-stats-kelas');
            $this->clearKelasStatsCache();
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

    public function updateJadwal($dataAlpine, $kelasId)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data = array_merge($this->jadwal_input, $dataAlpine);

        $data['mahasiswa_id_array'] =
            $this->mahasiswa_id_array ?? [];

        for ($i = 1; $i <= 16; $i++) {
            $sesiKey = 'sesi_'.$i;
            if (empty($data[$sesiKey])) {
                $data[$sesiKey] = $this->jadwal_input[$sesiKey] ?? null;
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
            $kodeKelas = '';
            $kodeJadwal = '';
            DB::transaction(function () use ($validated, $data, $isRestartSesi, &$kodeKelas, &$kodeJadwal) {

                $jadwal = KelasJadwal::findOrFail(
                    $this->selected_kj_id
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

                $kodeKelas = $jadwal->kelas_rel->kode;
                $kodeJadwal = $jadwal->kode_jadwal;

                $resetAbsen = $data['restart_absensi'] ?? false;

                if ($resetAbsen) {
                    $activeStudentIds = $jadwal->mahasiswas()->pluck('mahasiswas.id');
                    $sesiIds = $jadwal->sesis()->pluck('id');
                    MahasiswaKehadiran::whereIn('sesi_id', $sesiIds)
                        ->whereNotIn('mahasiswa_id', $activeStudentIds)
                        ->delete();
                }

                // =========================================
                // UPDATE OR CREATE SESI 1 - 16
                // =========================================
                $jamMulai = $validated['jam_mulai'];
                $sesiSent = $data['sesi_sent_edit'];

                for ($i = 1; $i <= 16; $i++) {
                    $tanggalSesi = $data["sesi_{$i}"] ?? null;

                    if (! $tanggalSesi) {
                        continue;
                    }

                    $sesiLama = KelasSesi::where('kj_id', $jadwal->id)
                        ->where('pertemuan_ke', $i)
                        ->first();

                    if ($sesiSent === 'active') {
                        $customSent = 0;
                    } elseif ($sesiSent === 'inactive') {
                        $customSent = 1;
                    } else {
                        $customSent = $sesiLama ? $sesiLama->reminder_sent : 0;
                    }

                    $jamAcuan = ! empty($jamMulai) ? $jamMulai : '00:00';
                    $waktuSesi = Carbon::parse($tanggalSesi.' '.$jamAcuan);

                    if ($waktuSesi->isPast()) {
                        $customSent = 1;
                    }

                    $sesi = KelasSesi::updateOrCreate(
                        [
                            'kj_id' => $jadwal->id,
                            'pertemuan_ke' => $i,
                        ],
                        [
                            'tanggal' => $tanggalSesi,
                            'reminder_sent' => $customSent,
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

            $this->toast(message: "Jadwal {$validated['label_kelas']} {$validated['kode_wilayah']}", type: 'update');

            if ($data['digit_tahun'] !== $data['digit_tahun_old']) {
                return $this->redirect(route('sesi-management', [$kodeKelas, $kodeJadwal]), navigate: true);
            } else {
                $this->resetInputJadwal();
                $this->dispatch('refresh-data-jadwal');
                $this->showJadwalModal = false;
            }
            $this->dispatch('refresh-data-kelas');
            $this->dispatch('refresh-stats-kelas');
            $this->clearKelasStatsCache();

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

            'kode_wilayah.required' => 'Kode Wilayah wajib dipilih!',
            'label_kelas.required' => 'Label kelas wajib diisi!',
            'hari_pelaksanaan.required' => 'Hari pelaksanaan wajib dipilih!',

            'jam_mulai.required' => 'Jam mulai wajib diisi!',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid!',
            'jam_mulai.date_format' => 'Format jam mulai harus berupa HH:MM (contoh: 08:00)!',
            'jam_berakhir.required' => 'Jam berakhir wajib diisi!',
            'jam_berakhir.date_format' => 'Format jam berakhir harus berupa HH:MM (contoh: 09:40)!',
            'jam_berakhir.after' => 'Jam berakhir harus setelah jam mulai!',

            'password.min' => 'Password Kelas minimal 4 karakter!',
            'password.max' => 'Password Kelas maksimal 14 karakter!',

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
        $sesiFields = array_map(fn ($i) => "sesi_$i", range(1, 16));

        return [
            1 => $this->getErrorCount([
                'kode_wilayah',
                'label_kelas',
                'password',
            ]),
            2 => $this->getErrorCount(array_merge([
                'hari_pelaksanaan',
                'tanggal_mulai',
                'tanggal_berakhir',
                'jam_mulai',
                'jam_berakhir',
            ], $sesiFields)),
            3 => $this->getErrorCount([
                'kapasitas',
                'mahasiswe_id_array',
            ]),
        ];
    }

    private function resetInputJadwal()
    {
        $fields = [
            'selected_kj_id',
            'jadwal_input',
        ];

        $this->mahasiswaNameSearch = '';
        $this->mahasiswa_id_array = [];
        $this->mahasiswa_items_array = [];
        $this->mahasiswa_sub_items_array = [];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
