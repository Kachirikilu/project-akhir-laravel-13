<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasSesi;
use App\Models\Kelas\MahasiswaKehadiran;
use App\Models\Penilaian\NilaiMahasiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait WithSesiModal
{
    use HasErrorCount;
    use HasToast;

    public $selected_id_sesi;

    public $selected_id_mahasiswa;

    public $list_absensi_array = [];

    public $isEditingSesi = false;

    public $showEditSesi = false;

    public $showSesiModal = false;

    public $showSesiAbsen = false;

    public $showMahasiswaAbsen = false;

    public function addSesi()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditSesi == true) {
            $this->resetInputSesi();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingSesi = false;
        $this->showSesiModal = true;
        $this->showEditSesi = false;
    }

    public function absenSesi($data)
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

            $validated = validator(
                $data,
                [
                    'sesi_id' => ['required', 'exists:kelas_sesi,id'],
                    'absen' => [
                        'required',
                        'in:Hadir,Terlambat,Absen,Sakit,Izin,Dispensasi',
                    ],
                    'keterangan' => [
                        'required_if:absen,Dispensasi,Sakit,Izin',
                        'nullable',
                        'string',
                        'min:5',
                        'max:1000',
                    ],
                ],
                [
                    'sesi_id.required' => 'Sesi Kelas tidak ditemukan!',
                    'sesi_id.exists' => 'Sesi Kelas tidak valid!',

                    'absen.required' => 'Status Absensi wajib dipilih!',
                    'absen.in' => 'Status Absensi tidak valid!',

                    'keterangan.required_if' => 'Keterangan wajib diisi untuk status Izin, Sakit, & Dispensasi!',
                    'keterangan.string' => 'Keterangan harus berupa text!',
                    'keterangan.min' => 'Keterangan terlalu pendek (Minimal 5 karakter)!',
                    'keterangan.max' => 'Keterangan terlalu panjang (Maksimal 1000 karakter)!',
                ]
            )->validate();

            $sesi = KelasSesi::with(['jadwal_rel.kelas_rel'])->findOrFail($validated['sesi_id']);

            $now = now();

            $mulai = Carbon::parse($sesi->waktu_pelaksanaan);
            $berakhir = Carbon::parse($sesi->waktu_berakhir);
            $batasTerlambat = Carbon::parse($sesi->waktu_telat);
            $batasDispensasi = Carbon::parse($sesi->waktu_dispensasi);

            $statusDipilih = $validated['absen'];

            if ($now->lt($mulai) || $now->gt($batasDispensasi)) {
                $this->toast(
                    text: 'Sesi absensi sedang ditutup atau telah kedaluwarsa!',
                    variant: 'danger'
                );

                return;
            }

            if ($statusDipilih === 'Absen') {
                $validated['absen'] = 'Absen';
            } elseif ($now->betweenIncluded($mulai, $batasTerlambat)) {
                if (! in_array($statusDipilih, ['Hadir', 'Izin', 'Sakit', 'Dispensasi'])) {
                    $validated['absen'] = 'Hadir';
                }
            } elseif ($now->gt($batasTerlambat) && $now->lte($berakhir)) {
                if ($statusDipilih === 'Sakit') {
                    $validated['absen'] = 'Sakit';
                } elseif (in_array($statusDipilih, ['Hadir', 'Terlambat', 'Izin'])) {
                    $validated['absen'] = 'Terlambat';
                } else {
                    $validated['absen'] = 'Absen';
                }
            } elseif ($now->gt($mulai) && $now->lte($batasDispensasi)) {
                if ($statusDipilih === 'Dispensasi') {
                    $validated['absen'] = 'Dispensasi';
                } else {
                    $validated['absen'] = 'Absen';
                }
            }

            MahasiswaKehadiran::updateOrCreate(
                [
                    'sesi_id' => $validated['sesi_id'],
                    'mahasiswa_id' => $mahasiswa_id,
                ],
                [
                    'status' => $validated['absen'],
                    'waktu_presensi' => now(),
                    'keterangan' => $validated['keterangan'] ?? null,
                ]
            );

            $this->showSesiAbsen = false;
            $this->dispatch('refresh-data-sesi');

            $this->toast(
                message: 'Absensi berhasil dikirim',
                type: 'success'
            );

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->toast(
                text: 'Gagal Mengambil Data: '.$e->getMessage(),
                variant: 'danger'
            );
        }
    }

    // public function editAbsensi($id, $jadwal_id)
    // {
    //     if (! $this->AuthCheck('staff')) {
    //         return;
    //     }

    //     $this->resetInputSesi();
    //     $this->resetValidation();
    //     $this->resetErrorBag();

    //     // dd($this->list_absensi_array);

    //     if (empty($id)) {
    //         $this->toast(message: 'Mahasiswa', type: 'unfound', variant: 'danger');

    //         return;
    //     }

    //     if (empty($jadwal_id)) {
    //         $this->toast(message: 'Jadwal Kelas', type: 'unfound', variant: 'danger');

    //         return;
    //     }

    //     $sesis = KelasSesi::with([
    //         'kehadirans' => function ($query) use ($id) {
    //             $query->where('mahasiswa_id', $id);
    //         },
    //     ])->where('kj_id', $jadwal_id)->orderBy('pertemuan_ke', 'asc')->get();

    //     $nilaiMahasiswa = NilaiMahasiswa::where('kj_id', $jadwal_id)
    //         ->where('mahasiswa_id', $id)
    //         ->first();

    //     $nilaiArray = $nilaiMahasiswa?->nilai_array ?? [];
    //     $this->list_absensi_array = $sesis->map(function ($sesi) use ($nilaiArray) {
    //         $kehadiran = $sesi->kehadirans->first();
    //         $index = $sesi->pertemuan_ke - 1;

    //         return [
    //             'sesi_id' => $sesi->id,
    //             'pertemuan_ke' => $sesi->pertemuan_ke,

    //             'tanggal' => $sesi->tanggal,
    //             'tanggal_carbon' => $sesi->tanggal
    //                 ? Carbon::parse($sesi->tanggal)->format('d M Y')
    //                 : '---',

    //             'metode' => $sesi->metode ?? '---',
    //             'kode_scpmk' => $sesi->kode_scpmk ?? '---',

    //             'kehadiran_id' => $kehadiran?->id,
    //             'status' => $kehadiran?->status ?? 'Belum Presensi',
    //             'keterangan' => $kehadiran?->keterangan ?? '',
    //             'waktu_presensi' => $kehadiran?->waktu_presensi,

    //             'nilai' => $nilaiArray[$index] ?? null,
    //         ];
    //     })->toArray();

    //     $this->selected_id_mahasiswa = $id;
    //     $this->showMahasiswaAbsen = true;
    //     $this->dispatch('refresh-component');
    // }

    // public function updateAbsensi($data)
    // {
    //     if (! $this->AuthCheck('staff')) {
    //         return;
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 1. Merge data dari frontend dengan aman (Satu kali loop)
    //     |--------------------------------------------------------------------------
    //     */
    //     foreach ($data['list_absensi_array'] ?? [] as $index => $item) {
    //         if (! isset($this->list_absensi_array[$index])) {
    //             continue;
    //         }

    //         $merged = array_merge($this->list_absensi_array[$index], $item);
    //         if (empty(trim($merged['status'] ?? ''))) {
    //             $merged['status'] = 'Belum Presensi';
    //         }

    //         $this->list_absensi_array[$index] = $merged;
    //     }

    //     if (empty($this->selected_id_mahasiswa) || empty($this->list_absensi_array)) {
    //         $this->toast(
    //             message: 'Data mahasiswa atau sesi absensi kosong!',
    //             type: 'error',
    //             variant: 'danger'
    //         );

    //         return;
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 2. Bangun Validation Rules & Messages secara Dinamis
    //     |--------------------------------------------------------------------------
    //     */
    //     $rules = [];
    //     $messages = [];

    //     $nilaiMahasiswa = NilaiMahasiswa::where('mahasiswa_id', $this->selected_id_mahasiswa)->where('kj_id', $this->jadwal_id)->first();

    //     foreach ($this->list_absensi_array as $index => $item) {
    //         $prefix = "list_absensi_array.{$index}.";

    //         $rules[$prefix.'sesi_id'] = ['required', 'exists:kelas_sesi,id'];
    //         $rules[$prefix.'status'] = ['required', 'in:Hadir,Terlambat,Absen,Sakit,Izin,Dispensasi,Belum Presensi'];

    //         $rules[$prefix.'keterangan'] = [
    //             Rule::requiredIf(function () use ($item) {
    //                 return in_array($item['status'] ?? '', ['Dispensasi', 'Sakit', 'Izin']);
    //             }),
    //             'nullable',
    //             'string',
    //             'min:5',
    //             'max:1000',
    //         ];

    //         $rules[$prefix.'nilai'] = [
    //             'required',
    //             'numeric',
    //             'min:0',
    //             'max:100',
    //         ];

    //         $pertemuan = $item['pertemuan_ke'] ?? ($index + 1);

    //         $messages[$prefix.'sesi_id.required'] = "Pertemuan Ke-{$pertemuan}: Sesi Kelas tidak ditemukan!";
    //         $messages[$prefix.'sesi_id.exists'] = "Pertemuan Ke-{$pertemuan}: Sesi Kelas tidak valid!";
    //         $messages[$prefix.'status.required'] = "Pertemuan Ke-{$pertemuan}: Status Absensi wajib dipilih!";
    //         $messages[$prefix.'status.in'] = "Pertemuan Ke-{$pertemuan}: Status Absensi tidak valid!";
    //         $messages[$prefix.'keterangan.required_if'] = "Pertemuan Ke-{$pertemuan}: Keterangan wajib diisi untuk status Izin, Sakit, & Dispensasi!";
    //         $messages[$prefix.'keterangan.required'] = "Pertemuan Ke-{$pertemuan}: Keterangan wajib diisi untuk status Izin, Sakit, & Dispensasi!";
    //         $messages[$prefix.'keterangan.string'] = "Pertemuan Ke-{$pertemuan}: Keterangan harus berupa text!";
    //         $messages[$prefix.'keterangan.min'] = "Pertemuan Ke-{$pertemuan}: Keterangan terlalu pendek (Minimal 5 karakter)!";
    //         $messages[$prefix.'keterangan.max'] = "Pertemuan Ke-{$pertemuan}: Keterangan terlalu panjang (Maksimal 1000 karakter)!";
    //         $messages[$prefix.'nilai.required'] = "Pertemuan Ke-{$pertemuan}: Nilai wajib diisi!";
    //         $messages[$prefix.'nilai.numeric'] = "Pertemuan Ke-{$pertemuan}: Nilai harus berupa angka!";
    //         $messages[$prefix.'nilai.min'] = "Pertemuan Ke-{$pertemuan}: Nilai minimal 0!";
    //         $messages[$prefix.'nilai.max'] = "Pertemuan Ke-{$pertemuan}: Nilai maksimal 100!";
    //     }

    //     $this->resetValidation();

    //     $validator = validator(
    //         ['list_absensi_array' => $this->list_absensi_array],
    //         $rules,
    //         $messages
    //     );

    //     if ($validator->fails()) {
    //         $this->setErrorBag($validator->errors());

    //         return;
    //     }

    //     if ($nilaiMahasiswa) {

    //         $nilaiArray = $nilaiMahasiswa->nilai_array ?? [];

    //         foreach ($this->list_absensi_array as $index => $item) {

    //             $nilaiArray[$index] = $item['nilai'] ?? 0;
    //         }

    //         $nilaiMahasiswa->nilai_array = $nilaiArray;
    //         $bobotArray = $nilaiMahasiswa->bobot_array ?? [];
    //         $totalNilai = 0;
    //         foreach ($nilaiArray as $i => $nilai) {
    //             $bobot = $bobotArray[$i] ?? 0;
    //             $totalNilai += ((float) $nilai) * ((float) $bobot);
    //         }

    //         $nilaiMahasiswa->nilai = round($totalNilai, 2);
    //         $nilaiMahasiswa->save();
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 3. Eksekusi Simpan Database (Jika Lolos Validasi)
    //     |--------------------------------------------------------------------------
    //     */
    //     try {
    //         \DB::beginTransaction();

    //         foreach ($this->list_absensi_array as $index => $item) {

    //             if (($item['status'] ?? null) === 'Belum Presensi') {
    //                 if (! empty($item['kehadiran_id'])) {
    //                     MahasiswaKehadiran::destroy($item['kehadiran_id']);
    //                 }

    //                 $this->list_absensi_array[$index]['kehadiran_id'] = null;
    //                 $this->list_absensi_array[$index]['status'] = 'Belum Presensi';
    //                 $this->list_absensi_array[$index]['keterangan'] = '';
    //                 $this->list_absensi_array[$index]['waktu_presensi'] = null;

    //                 continue;
    //             }

    //             $waktuSaves = $item['waktu_presensi'] ?? now();

    //             $kehadiran = MahasiswaKehadiran::updateOrCreate(
    //                 [
    //                     'id' => $item['kehadiran_id'],
    //                 ],
    //                 [
    //                     'sesi_id' => $item['sesi_id'],
    //                     'mahasiswa_id' => $this->selected_id_mahasiswa,
    //                     'status' => $item['status'],
    //                     'keterangan' => $item['keterangan'] ?: null,
    //                     'waktu_presensi' => $waktuSaves,
    //                 ]
    //             );

    //             $this->list_absensi_array[$index]['kehadiran_id'] = $kehadiran->id;
    //             $this->list_absensi_array[$index]['status'] = $kehadiran->status;
    //             $this->list_absensi_array[$index]['keterangan'] = $kehadiran->keterangan ?? '';
    //             $this->list_absensi_array[$index]['waktu_presensi'] = $kehadiran->waktu_presensi;
    //         }

    //         if ($nilaiMahasiswa) {
    //             $nilaiArray = $nilaiMahasiswa->nilai_array ?? [];
    //             foreach ($this->list_absensi_array as $index => $item) {
    //                 $nilaiArray[$index] = (float) ($item['nilai'] ?? 0);
    //             }

    //             $nilaiMahasiswa->nilai_array = $nilaiArray;
    //             $bobotArray = $nilaiMahasiswa->bobot_array ?? [];
    //             $totalNilai = 0;

    //             foreach ($nilaiArray as $i => $nilai) {
    //                 $bobot = $bobotArray[$i] ?? 0;
    //                 $totalNilai += ((float) $nilai) * ((float) $bobot);
    //             }

    //             $nilaiMahasiswa->nilai = round($totalNilai, 2);
    //             $nilaiMahasiswa->save();
    //         }
    //         \DB::commit();

    //         // dd($this->list_absensi_array);

    //         $this->resetInputSesi();
    //         $this->dispatch('refresh-data-sesi');
    //         $this->showMahasiswaAbsen = false;

    //         $this->toast(
    //             message: 'Absensi',
    //             type: 'update'
    //         );

    //     } catch (\Exception $e) {
    //         \DB::rollBack();

    //         $this->toast(
    //             message: 'Gagal menyimpan absensi: '.$e->getMessage(),
    //             type: 'error',
    //             variant: 'danger'
    //         );
    //     }
    // }

    public function editSesi($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputSesi();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_sesi = $id;
        $this->isEditingSesi = true;
        $this->showEditSesi = true;

        $this->dispatch('refresh-component');
    }

    private function inputModalSesi($isUpdate, $data, $kelasId = null)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $kelasId = $kelasId ?? $this->selected_kelas_id ?? null;

        if ($isUpdate) {
            $sesi = KelasSesi::with(['jadwal_rel.kelas_rel.rps_rel.cpmks.scpmks'])->find($this->selected_id_sesi);
            if (! $sesi) {
                throw ValidationException::withMessages([
                    'sesi' => 'Data sesi pertemuan tidak ditemukan!',
                ]);
            }
            $scpmk = $sesi->scpmk_atr;
            $jadwalInduk = $sesi->jadwal_rel;
        } else {
            $kelas = Kelas::with(['rps_rel.cpmks.scpmks'])->find($kelasId);
            $scpmk = $kelas?->rps_rel?->cpmks?->flatMap->scpmks->firstWhere('pertemuan_ke', $data['pertemuan_ke'] ?? 1);
            $jadwalInduk = null;
        }

        // ==========================================
        // RULES & CONDITIONAL UNIQUE CHECK
        // ==========================================
        $rules = [
            'pertemuan_ke' => [
                'required',
                'integer',
                'min:1',
                'max:16',
                function ($attribute, $value, $fail) use ($isUpdate, $jadwalInduk, $data) {
                    $kjId = $jadwalInduk?->id ?? $data['kj_id'] ?? $this->selected_kj_id ?? null;

                    if ($kjId) {
                        $query = DB::table('kelas_sesi')
                            ->where('kj_id', $kjId)
                            ->where('pertemuan_ke', $value);

                        if ($isUpdate) {
                            $query->where('id', '!=', $this->selected_id_sesi);
                        }

                        if ($query->exists()) {
                            $fail("Pertemuan ke-{$value} sudah terdaftar pada jadwal kelas ini.");
                        }
                    }
                },
            ],
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_berakhir' => 'nullable|date_format:H:i|after:jam_mulai',

            'deskripsi' => 'nullable|string|min:5|max:1000',
            'materi' => 'nullable|string|min:5|max:1000',
            'metodologi' => 'nullable|string|min:5|max:1000',
            'indikator' => 'nullable|string|min:5|max:1000',

            'metode' => [
                'nullable',
                Rule::in([
                    'Teori', 'Aktivitas Partisipasif', 'Tugas', 'Mandiri',
                    'UTS', 'UAS', 'Evaluasi Awal', 'Evaluasi Akhir',
                    'Laporan Akhir', 'Hasil Proyek', 'Kuis',
                    'Skripsi', 'Kerja Praktek', 'Responsi', 'Logbook', 'Portofolio',
                ]),
            ],

            'deskripsi_tugas' => 'nullable|string|min:5|max:1000',
            'waktu_tugas' => 'nullable|integer|min:60',
            'waktu_mandiri' => 'nullable|integer|min:60',
            'bobot' => 'nullable|numeric|min:0.5|max:100',
        ];

        // ==========================================
        // VALIDATOR
        // ==========================================
        $validator = Validator::make($data, $rules, $this->validationMessagesSesi());

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        // ==========================================
        // TRANSFORM DATA & LOGIKA FILTER OVERRIDE
        // ==========================================
        $sks = isset($data['sks']) ? (int) $data['sks'] : 2;
        $defaultWaktuSks = 60 * $sks;

        $fieldsToCompare = [
            'jam_mulai' => $validated['jam_mulai'] ?? null,
            'jam_berakhir' => $validated['jam_berakhir'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'materi' => $validated['materi'] ?? null,
            'metodologi' => $validated['metodologi'] ?? null,
            'indikator' => $validated['indikator'] ?? null,
            'metode' => $validated['metode'] ?? null,
            'deskripsi_tugas' => $validated['deskripsi_tugas'] ?? null,
            'waktu_tugas' => isset($validated['waktu_tugas']) && $validated['waktu_tugas'] !== '' ? (int) $validated['waktu_tugas'] : null,
            'waktu_mandiri' => isset($validated['waktu_mandiri']) && $validated['waktu_mandiri'] !== '' ? (int) $validated['waktu_mandiri'] : null,
            'bobot' => isset($validated['bobot']) && $validated['bobot'] !== '' ? (float) $validated['bobot'] : null,
        ];

        $overridePayload = [];
        $hasOverrideContent = false;

        foreach ($fieldsToCompare as $field => $inputValue) {

            if (in_array($field, ['jam_mulai', 'jam_berakhir'])) {
                $defaultValue = $jadwalInduk ? ($jadwalInduk->{$field} ?? null) : ($data[$field] ?? null);
                if ($defaultValue) {
                    $defaultValue = date('H:i', strtotime($defaultValue));
                }
            } else {
                $defaultValue = $scpmk ? ($scpmk->{$field} ?? null) : null;
            }

            if (in_array($field, ['deskripsi', 'materi', 'metodologi', 'indikator', 'deskripsi_tugas'])) {
                $defaultValue = $this->normalizeText($defaultValue);
                if ($scpmk && ! empty($defaultValue)) {
                    $scpmk->{$field} = $defaultValue;
                }
                $inputValue = $this->normalizeText($inputValue);
            }

            if (in_array($field, ['waktu_tugas', 'waktu_mandiri']) && ! is_null($defaultValue)) {
                $defaultValue = (int) $defaultValue;
            }
            if ($field === 'bobot' && ! is_null($defaultValue)) {
                $defaultValue = (float) $defaultValue;
            }

            if (in_array($field, ['waktu_tugas', 'waktu_mandiri'])) {
                if ($inputValue === $defaultValue || $inputValue === $defaultWaktuSks) {
                    $overridePayload[$field] = null;
                } else {
                    $overridePayload[$field] = $inputValue;
                    if (! empty($inputValue)) {
                        $hasOverrideContent = true;
                    }
                }

                continue;
            }

            if (trim((string) $inputValue) === trim((string) $defaultValue) || trim((string) $inputValue) === '') {
                $overridePayload[$field] = null;
            } else {
                $overridePayload[$field] = $inputValue;
                $hasOverrideContent = true;
            }
        }

        return [
            'main_data' => [
                'pertemuan_ke' => $validated['pertemuan_ke'],
                'tanggal' => $validated['tanggal'] ?? null,
            ],
            'override_data' => $overridePayload,
            'has_override' => $hasOverrideContent,
        ];
    }

    public function saveSesi($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['kj_id'] = $data['kj_id'] ?? $this->selected_kj_id ?? null;
        $kelasId = $this->selected_kelas_id ?? null;

        try {
            $validated = $this->inputModalSesi(false, $data, $kelasId);

            DB::transaction(function () use ($validated, $data) {
                $sesi = KelasSesi::create([
                    'kj_id' => $data['kj_id'],
                    'pertemuan_ke' => $validated['main_data']['pertemuan_ke'],
                    'tanggal' => $validated['main_data']['tanggal'],
                ]);

                if ($validated['has_override']) {
                    $sesi->override()->create($validated['override_data']);
                }
            });

            $this->toast(message: "Sesi Pertemuan Ke-{$data['pertemuan_ke']} Berhasil Ditambahkan", type: 'create');

            $this->resetInputSesi();
            $this->dispatch('refresh-data-jadwal');
            $this->showSesiModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: collect($e->errors())->flatten()->first(), variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            report($e);
        }
    }

    public function updateSesi($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $validated = $this->inputModalSesi(true, $data);

            DB::transaction(function () use ($validated) {
                $sesi = KelasSesi::findOrFail($this->selected_id_sesi);
                $sesi->update($validated['main_data']);

                if ($validated['has_override']) {
                    $sesi->override()->updateOrCreate(
                        ['sesi_id' => $sesi->id],
                        $validated['override_data']
                    );
                } else {
                    $sesi->override()->delete();
                }
            });

            $this->resetInputSesi();
            $this->dispatch('refresh-data-sesi');
            $this->showSesiModal = false;

            $this->toast(message: "Sesi Pertemuan Ke-{$data['pertemuan_ke']}", type: 'update');

        } catch (ValidationException $e) {
            $this->toast(text: collect($e->errors())->flatten()->first(), variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            report($e);
        }
    }

    private function validationMessagesSesi()
    {
        $messages = [
            'tanggal.required' => 'Tanggal Sesi Kelas pertemuan wajib diisi!',
            'tanggal.date' => 'Format tanggal Sesi Kelas tidak valid!',

            'jam_mulai.date_format' => 'Format jam mulai tidak valid!',
            'jam_mulai.date_format' => 'Format jam mulai harus berupa HH:MM (contoh: 08:00)!',
            'jam_berakhir.date_format' => 'Format jam berakhir harus berupa HH:MM (contoh: 09:40)!',
            'jam_berakhir.after' => 'Jam berakhir harus setelah jam mulai!',

            // Deskripsi & Status
            'deskripsi.string' => 'Deskripsi Sub-CPMK harus berupa text!',
            'deskripsi.min' => 'Deskripsi Sub-CPMK terlalu pendek (Minimal 5 karakter)!',
            'deskripsi.max' => 'Deskripsi Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',

            'materi.string' => 'Materi Sub-CPMK harus berupa text!',
            'materi.min' => 'Materi Sub-CPMK terlalu pendek (Minimal 5 karakter)!',
            'materi.max' => 'Materi Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',

            'metodologi.string' => 'Metodologi Sub-CPMK harus berupa text!',
            'metodologi.min' => 'Metodologi Sub-CPMK terlalu pendek (Minimal 5 karakter)!',
            'metodologi.max' => 'Metodologi Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',

            'indikator.string' => 'Indikator Sub-CPMK harus berupa text!',
            'indikator.min' => 'Indikator Sub-CPMK terlalu pendek (Minimal 5 karakter)!',
            'indikator.max' => 'Indikator Sub-CPMK terlalu panjang (Maksimal 1000 karakter)!',

            'deskripsi_tugas.string' => 'Deskripsi Tugas harus berupa text!',
            'deskripsi_tugas.min' => 'Deskripsi Tugas terlalu pendek (Minimal 5 karakter)!',
            'deskripsi_tugas.max' => 'Deskripsi Tugas terlalu panjang (Maksimal 1000 karakter)!',

            'metode.in' => 'Pilih Metode yang telah tersedia!',

            'waktu_tugas.integer' => 'Waktu tugas harus berupa angka!',
            'waktu_tugas.min' => 'Waktu tugas minimal 60 menit!',

            'waktu_mandiri.integer' => 'Waktu mandiri harus berupa angka!',
            'waktu_mandiri.min' => 'Waktu mandiri minimal 60 menit!',

            'bobot.numeric' => 'Bobot harus berupa angka desimal!',
            'bobot.min' => 'Bobot minimal bernilai 0.5!',
            'bobot.max' => 'Bobot minimal bernilai 100!',
        ];

        return $messages;
    }

    public function getSesiErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'kode_sesi',
                'nama_sesi',
                'deskripsi',
            ]),
            2 => $this->getErrorCount([
            ]),
            3 => $this->getErrorCount([
            ]),
        ];
    }

    public function getAbsenErrorSections()
    {
        return [
            1 => $this->getAbsenErrorCountByIndexes(0, 7),
            2 => $this->getAbsenErrorCountByIndexes(8, 100),
            3 => 0,
        ];
    }

    private function getAbsenErrorCountByIndexes($start, $end)
    {
        $errors = $this->getErrorBag()->messages();
        $count = 0;

        for ($i = $start; $i <= $end; $i++) {
            $prefix = "list_absensi_array.{$i}.";

            if (isset($errors[$prefix.'status'])) {
                $count += count($errors[$prefix.'status']);
            }
            if (isset($errors[$prefix.'keterangan'])) {
                $count += count($errors[$prefix.'keterangan']);
            }
            if (isset($errors[$prefix.'nilai'])) {
                $count += count($errors[$prefix.'nilai']);
            }
            if (isset($errors[$prefix.'sesi_id'])) {
                $count += count($errors[$prefix.'sesi_id']);
            }
        }

        return $count;
    }

    private function resetInputSesi()
    {
        $fields = [
            'selected_id_sesi',
            'selected_id_mahasiswa',
        ];

        $this->list_absensi_array = [];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
