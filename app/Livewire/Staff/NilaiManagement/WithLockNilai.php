<?php

namespace App\Livewire\Staff\NilaiManagement;

use App\Livewire\Global\HasToast;
use App\Models\Penilaian\LockNilai;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithLockNilai
{
    use HasToast;

    public $showLockNilaiModal;

    public $nilai_input = [
        // 'ganjil_genap' => '',
        // 'akademik' => '',
        // 'akademik_1' => '',
        // 'akademik_2' => '',
        // 'tanggal_unlock' => '',
        'tanggal_ganjil' => '',
        'tanggal_genap' => '',
        'bulan_ganjil' => '',
        'bulan_genap' => '',
    ];

    public function editLockNilai()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->showLockNilaiModal = true;
        $this->resetInputNilai();
        $this->resetValidation();

        try {
            $data = LockNilai::where('pr_id', Auth::user()->pr_id)
                ->first();

            if ($data) {
                if ($data->ganjil_unlock) {
                    $pecahGanjil = explode('-', $data->ganjil_unlock);
                    $this->nilai_input['bulan_ganjil'] = $pecahGanjil[0] ?? '';
                    $this->nilai_input['tanggal_ganjil'] = ltrim($pecahGanjil[1] ?? '', '0');
                } else {
                    $this->nilai_input['bulan_ganjil'] = '';
                    $this->nilai_input['tanggal_ganjil'] = '';
                }

                if ($data->genap_unlock) {
                    $pecahGenap = explode('-', $data->genap_unlock);
                    $this->nilai_input['bulan_genap'] = $pecahGenap[0] ?? '';
                    $this->nilai_input['tanggal_genap'] = ltrim($pecahGenap[1] ?? '', '0');
                } else {
                    $this->nilai_input['bulan_genap'] = '';
                    $this->nilai_input['tanggal_genap'] = '';
                }
            }
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalLockNilai($data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $getMaxDays = function($month) {
            if (in_array($month, ['04', '06', '09', '11'])) return 30;
            if ($month === '02') return 28;
            return 31;
        };
        if (!empty($data['bulan_ganjil']) && !empty($data['tanggal_ganjil'])) {
            $maxDaysGanjil = $getMaxDays($data['bulan_ganjil']);
            if ((int)$data['tanggal_ganjil'] > $maxDaysGanjil) {
                $data['tanggal_ganjil'] = $maxDaysGanjil;
            } elseif ((int)$data['tanggal_ganjil'] < 1) {
                $data['tanggal_ganjil'] = 1;
            }
        }
        if (!empty($data['bulan_genap']) && !empty($data['tanggal_genap'])) {
            $maxDaysGenap = $getMaxDays($data['bulan_genap']);
            if ((int)$data['tanggal_genap'] > $maxDaysGenap) {
                $data['tanggal_genap'] = $maxDaysGenap;
            } elseif ((int)$data['tanggal_genap'] < 1) {
                $data['tanggal_genap'] = 1;
            }
        }

        $ganjilUnlock = (!empty($data['bulan_ganjil']) && !empty($data['tanggal_ganjil'])) 
            ? $data['bulan_ganjil'] . '-' . str_pad($data['tanggal_ganjil'], 2, '0', STR_PAD_LEFT) 
            : null;

        $genapUnlock = (!empty($data['bulan_genap']) && !empty($data['tanggal_genap'])) 
            ? $data['bulan_genap'] . '-' . str_pad($data['tanggal_genap'], 2, '0', STR_PAD_LEFT) 
            : null;

        $data['ganjil_unlock'] = $ganjilUnlock;
        $data['genap_unlock'] = $genapUnlock;

        $rules = [
            'ganjil_unlock' => ['required', 'regex:/^(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/'],
            'genap_unlock' => ['required', 'regex:/^(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/'],
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesLockNilai());

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        return [
            'ganjil_unlock' => $validated['ganjil_unlock'],
            'genap_unlock' => $validated['genap_unlock'],
        ];
    }
    
    public function updateLockNilai($dataAlpine)
    {
        $data = array_merge($this->nilai_input, $dataAlpine);

        try {
           $validated = $this->inputModalLockNilai($data);

            DB::transaction(function () use ($validated) {
                LockNilai::updateOrCreate(
                    ['pr_id' => Auth::user()->pr_id],
                    [
                        'ganjil_unlock' => $validated['ganjil_unlock'],
                        'genap_unlock' => $validated['genap_unlock'],
                    ]
                );
            });

            $prodi = Auth::user()->prodi_pr;
            $text = "Pengaturan tanggal unlock nilai untuk prodi $prodi berhasil disimpan!";

            $this->toast(text: $text, type: 'update');
            $this->showLockNilaiModal = false;

            $this->resetInputNilai();
            $this->dispatch('refresh-data-nilai');
        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
            // $firstError = collect($e->errors())->flatten()->first() ?? 'Terjadi kesalahan validasi!';
            // $this->toast(text: 'Validasi Gagal: '.$firstError, variant: 'danger');
            // throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-nilai');
            $this->showLockNilaiModal = false;
        }
    }

private function validationMessagesLockNilai()
    {
        return [
            'ganjil_unlock.required' => 'Tanggal & Bulan Unlock Ganjil wajib diisi lengkap!',
            'ganjil_unlock.regex' => 'Format Tanggal & Bulan Unlock Ganjil tidak valid!',
            'genap_unlock.required' => 'Tanggal & Bulan Unlock Genap wajib diisi lengkap!',
            'genap_unlock.regex' => 'Format Tanggal & Bulan Unlock Genap tidak valid!',
            'akademik.required' => 'Tahun Akademik wajib diisi!',
            'akademik.regex' => 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!',
            'akademik_1.required' => 'Tahun awal (input kiri) wajib diisi!',
            'akademik_1.min' => 'Tahun awal minimal adalah 1970!',
            'akademik_2.required' => 'Tahun akhir (input kanan) wajib diisi!',
            'akademik_2.min' => 'Tahun akhir minimal adalah 1971!',
        ];
    }

    public function resetInputNilai()
    {
        $fields = [
            'nilai_input',
        ];
        $this->reset($fields);
        $this->resetErrorBag();
    }
}
