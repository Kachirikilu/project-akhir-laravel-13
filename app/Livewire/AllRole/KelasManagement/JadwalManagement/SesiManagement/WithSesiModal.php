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

    public $isEditingSesi = false;

    public $showEditSesi = false;

    public $showSesiModal = false;

    public $showSesiAbsen = false;

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

        $sentValue = isset($data['sent']) ? (int) $data['sent'] : 0;

        if (!empty($validated['tanggal'])) {
            $jamMulai = !empty($validated['jam_mulai']) ? $validated['jam_mulai'] : '00:00';
            $waktuSesi = \Carbon\Carbon::parse($validated['tanggal'] . ' ' . $jamMulai);
            if ($waktuSesi->isPast()) {
                $sentValue = 1;
            }
        }

        return [
            'main_data' => [
                'pertemuan_ke' => $validated['pertemuan_ke'],
                'tanggal' => $validated['tanggal'] ?? null,
                'reminder_sent' => $sentValue
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
            $this->dispatch('refresh-stats-kelas');
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
            'tanggal.date' => 'Format Tanggal Sesi Kelas tidak valid!',

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

    private function resetInputSesi()
    {
        $fields = [
            'selected_id_sesi',
            'selected_id_mahasiswa',
        ];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
