<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Models\Kelas\KelasSesi;
use App\Models\Kelas\MahasiswaKehadiran;
use App\Models\Penilaian\NilaiMahasiswa;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait WithAbsenModal
{
    use HasErrorCount;
    use HasToast;

    public $selected_id_sesi;

    public $selected_id_mahasiswa;

    public $selected_id_jadwal;

    public $selected_id_rps;

    public $list_absensi_array = [];

    public $isEditingSesi = false;

    public $showEditSesi = false;

    public $showMahasiswaAbsen = false;

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

            $validated = Validator::make($data, [
                'sesi_id' => ['required', 'exists:kelas_sesi,id'],
                'absen' => [
                    'required',
                    'in:Hadir,Terlambat,Absen,Sakit,Izin,Dispensasi',
                ],
                'keterangan' => [
                    'required_if:absen,Dispensasi,Terlambat,Sakit,Izin,Absen',
                    'nullable',
                    'string',
                    'min:5',
                    'max:1000',
                ],
            ], [
                'sesi_id.required' => 'Sesi Kelas tidak ditemukan!',
                'sesi_id.exists' => 'Sesi Kelas tidak valid!',

                'absen.required' => 'Status Absensi wajib dipilih!',
                'absen.in' => 'Status Absensi tidak valid!',

                'keterangan.required_if' => 'Keterangan wajib diisi untuk status Terlambat, Izin, Sakit, Dispensasi, & Absen!',
                'keterangan.string' => 'Keterangan harus berupa text!',
                'keterangan.min' => 'Keterangan terlalu pendek (Minimal 5 karakter)!',
                'keterangan.max' => 'Keterangan terlalu panjang (Maksimal 1000 karakter)!',
            ])->validate();

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
                if (! in_array($statusDipilih, ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Dispensasi'])) {
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
                message: 'Absensi berhasil dikirim!',
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

    public function editAbsensi($id, $jadwal_id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputAbsen();
        $this->resetValidation();
        $this->resetErrorBag();

        if (empty($id)) {
            $this->toast(message: 'Mahasiswa', type: 'unfound', variant: 'danger');

            return;
        }

        if (empty($jadwal_id)) {
            $this->toast(message: 'Jadwal Kelas', type: 'unfound', variant: 'danger');

            return;
        }

        // 1. Ambil semua data sesi kelas terlebih dahulu
        $sesis = KelasSesi::with([
            'kehadirans' => function ($query) use ($id) {
                $query->where('mahasiswa_id', $id);
            },
        ])->where('kj_id', $jadwal_id)->orderBy('pertemuan_ke', 'asc')->get();

        $this->selected_id_jadwal = $jadwal_id;
        $this->selected_id_rps = $this->kelas->rps_id;

        // 2. Cari data NilaiMahasiswa
        $nilai_mahasiswa = NilaiMahasiswa::where('mahasiswa_id', $id)
            ->where('rps_id', $this->rps_id_url)
            ->where('ganjil_genap', (string) $this->jadwal->ganjil_genap)
            ->where('tahun_akademik', (string) $this->jadwal->tahun_akademik)
            ->first();

        // 3. JIKA NULL, BUAT DATA BARU SECARA OTOMATIS
        if (! $nilai_mahasiswa) {
            $newNilaiArray = [];
            $newBobotArray = [];

            foreach ($sesis as $sesi) {
                $index = $sesi->pertemuan_ke - 1;
                $newNilaiArray[$index] = 0; // Nilai default diganti menjadi 0 sesuai request

                // Ambil nilai dari accessor bobotNormalisasi (dibagi 100 karena accessor menghasilkan angka * 100)
                $bobotNorm = $sesi->bobot_normalisasi ?? 0;
                $newBobotArray[$index] = $bobotNorm > 0 ? round($bobotNorm / 100, 4) : 0;
            }

            // Daftarkan baris record baru ke database
            $nilai_mahasiswa = NilaiMahasiswa::create([
                'mahasiswa_id' => $id,
                'rps_id' => $this->rps_id_url,
                'ganjil_genap' => (string) $this->jadwal->ganjil_genap,
                'tahun_akademik' => (string) $this->jadwal->tahun_akademik,
                'nilai_array' => $newNilaiArray,
                'bobot_array' => $newBobotArray,
                // tambahkan field mandatory lain jika ada, contoh: 'nilai_akhir' => 0
            ]);
        }

        // 4. Proses array dari data yang dipastikan valid/ada
        $nilaiArray = $nilai_mahasiswa->nilai_array ?? [];
        $bobotArray = $nilai_mahasiswa->bobot_array ?? [];

        $this->list_absensi_array = $sesis->map(function ($sesi) use ($nilaiArray, $bobotArray) {
            $kehadiran = $sesi->kehadirans->first();
            $index = $sesi->pertemuan_ke - 1;

            $nilaiRaw = $nilaiArray[$index] ?? null;
            $nilaiFinal = ($nilaiRaw === null || $nilaiRaw === '') ? 0 : (float) $nilaiRaw;

            // Mengambil data bobot desimal dari database untuk dikalikan 100% kembali pada tampilan
            $rawBobot = $bobotArray[$index] ?? null;
            $tampilanBobot = ($rawBobot !== null) ? round((float) $rawBobot * 100, 2).'%' : '0%';

            return [
                'sesi_id' => $sesi->id,
                'pertemuan_ke' => $sesi->pertemuan_ke,

                'tanggal' => $sesi->tanggal,
                'tanggal_carbon' => $sesi->tanggal
                    ? Carbon::parse($sesi->tanggal)
                        ->locale('id')
                        ->translatedFormat('l, d M Y')
                    : '---',
                'metode' => $sesi->metode ?? '---',
                'kode_scpmk' => $sesi->kode_scpmk ?? '---',
                'kode_cpmk' => $sesi->kode_cpmk ?? '---',

                'kehadiran_id' => $kehadiran?->id,
                'status' => $kehadiran?->status ?? 'Belum Presensi',
                'keterangan' => $kehadiran?->keterangan ?? '',
                'waktu_presensi' => $kehadiran?->waktu_presensi,

                'nilai' => $nilaiFinal,
                'bobot' => $tampilanBobot,
            ];
        })->toArray();

        $this->selected_id_mahasiswa = $id;
        $this->showMahasiswaAbsen = true;
        $this->dispatch('refresh-component');
    }

    public function updateAbsensi($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. Merge data dari frontend dengan aman (Satu kali loop)
        |--------------------------------------------------------------------------
        */
        foreach ($data['list_absensi_array'] ?? [] as $index => $item) {
            if (! isset($this->list_absensi_array[$index])) {
                continue;
            }

            $merged = array_merge($this->list_absensi_array[$index], $item);
            if (empty(trim($merged['status'] ?? ''))) {
                $merged['status'] = 'Belum Presensi';
            }

            $this->list_absensi_array[$index] = $merged;
        }

        if (empty($this->selected_id_mahasiswa) || empty($this->list_absensi_array)) {
            $this->toast(
                message: 'Data mahasiswa atau sesi absensi kosong!',
                type: 'error',
                variant: 'danger'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Ambil atau Instansiasi Record NilaiMahasiswa
        |--------------------------------------------------------------------------
        */
        $nilai_mahasiswa = NilaiMahasiswa::where('mahasiswa_id', $this->selected_id_mahasiswa)
            ->where('rps_id', $this->rps_id_url)
            ->where('ganjil_genap', (string) $this->jadwal->ganjil_genap)
            ->where('tahun_akademik', (string) $this->jadwal->tahun_akademik)
            ->first();

        if (! $nilai_mahasiswa) {
            $initNilaiArray = [];
            $initBobotArray = [];

            foreach ($this->list_absensi_array as $index => $item) {
                $initNilaiArray[$index] = 0;

                $sesiModel = KelasSesi::find($item['sesi_id']);
                $bobotNorm = $sesiModel?->bobot_normalisasi ?? 0;
                $initBobotArray[$index] = $bobotNorm > 0 ? round($bobotNorm / 100, 4) : 0;
            }

            $nilai_mahasiswa = new NilaiMahasiswa;
            $nilai_mahasiswa->mahasiswa_id = $this->selected_id_mahasiswa;
            $nilai_mahasiswa->ganjil_genap = (string) $this->jadwal->ganjil_genap;
            $nilai_mahasiswa->tahun_akademik = (string) $this->jadwal->tahun_akademik;
            $nilai_mahasiswa->nilai_array = $initNilaiArray;
            $nilai_mahasiswa->bobot_array = $initBobotArray;
        }

        $nilai_mahasiswa->kj_id = $this->jadwal->id;
        $nilai_mahasiswa->rps_id = $this->rps_id_url;

        /*
        |--------------------------------------------------------------------------
        | 3. Bangun Validation Rules & Messages secara Dinamis
        |--------------------------------------------------------------------------
        */
        $rules = [];
        $messages = [];

        foreach ($this->list_absensi_array as $index => $item) {
            $prefix = "list_absensi_array.{$index}.";

            $rules[$prefix.'sesi_id'] = ['required', 'exists:kelas_sesi,id'];
            $rules[$prefix.'status'] = ['required', 'in:Hadir,Terlambat,Absen,Sakit,Izin,Dispensasi,Belum Presensi'];

            $rules[$prefix.'keterangan'] = [
                Rule::requiredIf(function () use ($item) {
                    return in_array($item['status'] ?? '', ['Dispensasi', 'Sakit', 'Izin']);
                }),
                'nullable',
                'string',
                'min:5',
                'max:1000',
            ];

            $rules[$prefix.'nilai'] = [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ];

            $pertemuan = $item['pertemuan_ke'] ?? ($index + 1);

            $messages[$prefix.'sesi_id.required'] = "Pertemuan Ke-{$pertemuan}: Sesi Kelas tidak ditemukan!";
            $messages[$prefix.'sesi_id.exists'] = "Pertemuan Ke-{$pertemuan}: Sesi Kelas tidak valid!";
            $messages[$prefix.'status.required'] = "Pertemuan Ke-{$pertemuan}: Status Absensi wajib dipilih!";
            $messages[$prefix.'status.in'] = "Pertemuan Ke-{$pertemuan}: Status Absensi tidak valid!";
            $messages[$prefix.'keterangan.required_if'] = "Pertemuan Ke-{$pertemuan}: Keterangan wajib diisi untuk status Izin, Sakit, & Dispensasi!";
            $messages[$prefix.'keterangan.required'] = "Pertemuan Ke-{$pertemuan}: Keterangan wajib diisi untuk status Izin, Sakit, & Dispensasi!";
            $messages[$prefix.'keterangan.string'] = "Pertemuan Ke-{$pertemuan}: Keterangan harus berupa text!";
            $messages[$prefix.'keterangan.min'] = "Pertemuan Ke-{$pertemuan}: Keterangan terlalu pendek (Minimal 5 karakter)!";
            $messages[$prefix.'keterangan.max'] = "Pertemuan Ke-{$pertemuan}: Keterangan terlalu panjang (Maksimal 1000 karakter)!";
            $messages[$prefix.'nilai.required'] = "Pertemuan Ke-{$pertemuan}: Nilai wajib diisi!";
            $messages[$prefix.'nilai.numeric'] = "Pertemuan Ke-{$pertemuan}: Nilai harus berupa angka!";
            $messages[$prefix.'nilai.min'] = "Pertemuan Ke-{$pertemuan}: Nilai minimal 0!";
            $messages[$prefix.'nilai.max'] = "Pertemuan Ke-{$pertemuan}: Nilai maksimal 100!";
        }

        $this->resetValidation();

        $validator = validator(
            ['list_absensi_array' => $this->list_absensi_array],
            $rules,
            $messages
        );

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Hitung Akumulasi Nilai Akhir OBE
        |--------------------------------------------------------------------------
        */
        $nilaiArray = $nilai_mahasiswa->nilai_array ?? [];
        foreach ($this->list_absensi_array as $index => $item) {
            $nilaiArray[$index] = (float) ($item['nilai'] ?? 0);
        }

        $nilai_mahasiswa->nilai_array = $nilaiArray;
        $bobotArray = $nilai_mahasiswa->bobot_array ?? [];
        $totalNilai = 0;

        foreach ($nilaiArray as $i => $nilai) {
            $bobot = $bobotArray[$i] ?? 0;
            $totalNilai += ((float) $nilai) * ((float) $bobot);
        }

        $nilai_mahasiswa->nilai = round($totalNilai, 2);

        /*
        |--------------------------------------------------------------------------
        | 5. Eksekusi Simpan Database
        |--------------------------------------------------------------------------
        */
        try {
            \DB::beginTransaction();

            foreach ($this->list_absensi_array as $index => $item) {

                if (($item['status'] ?? null) === 'Belum Presensi') {
                    if (! empty($item['kehadiran_id'])) {
                        MahasiswaKehadiran::destroy($item['kehadiran_id']);
                    }

                    $this->list_absensi_array[$index]['kehadiran_id'] = null;
                    $this->list_absensi_array[$index]['status'] = 'Belum Presensi';
                    $this->list_absensi_array[$index]['keterangan'] = '';
                    $this->list_absensi_array[$index]['waktu_presensi'] = null;

                    continue;
                }

                $waktuSaves = $item['waktu_presensi'] ?? now();

                $kehadiran = MahasiswaKehadiran::updateOrCreate(
                    [
                        'id' => $item['kehadiran_id'] ?: null, // Gunakan fallback null agar jika kosong system memicu insert baru
                    ],
                    [
                        'sesi_id' => $item['sesi_id'],
                        'mahasiswa_id' => $this->selected_id_mahasiswa,
                        'status' => $item['status'],
                        'keterangan' => $item['keterangan'] ?: null,
                        'waktu_presensi' => $waktuSaves,
                    ]
                );

                $this->list_absensi_array[$index]['kehadiran_id'] = $kehadiran->id;
                $this->list_absensi_array[$index]['status'] = $kehadiran->status;
                $this->list_absensi_array[$index]['keterangan'] = $kehadiran->keterangan ?? '';
                $this->list_absensi_array[$index]['waktu_presensi'] = $kehadiran->waktu_presensi;
            }

            // Simpan model NilaiMahasiswa (Otomatis INSERT jika record baru / UPDATE jika record lama)
            $nilai_mahasiswa->save();

            \DB::commit();

            $this->resetInputAbsen();
            $this->dispatch('refresh-data-sesi');
            $this->showMahasiswaAbsen = false;
            $this->showEditSesi = false;

            $this->toast(
                message: 'Absensi berhasil diperbarui!',
                type: 'update'
            );

        } catch (\Exception $e) {
            \DB::rollBack();

            $this->toast(
                message: 'Gagal menyimpan absensi: '.$e->getMessage(),
                type: 'error',
                variant: 'danger'
            );
        }
    }

    public function getAbsenErrorSections()
    {
        return [
            1 => $this->getAbsenErrorCountByIndexes(0, 3),
            2 => $this->getAbsenErrorCountByIndexes(4, 7),
            3 => $this->getAbsenErrorCountByIndexes(8, 11),
            4 => $this->getAbsenErrorCountByIndexes(12, 99),
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

    private function resetInputAbsen()
    {
        $fields = [
            'selected_id_sesi',
            'selected_id_mahasiswa',
            'selected_id_jadwal',
            'selected_id_rps',
        ];

        $this->list_absensi_array = [];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
