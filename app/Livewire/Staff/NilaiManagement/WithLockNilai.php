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
        'ganjil_genap' => '',
        'akademik' => '',
        'akademik_1' => '',
        'akademik_2' => '',
        'tanggal_unlock' => '',
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
                $this->nilai_input['ganjil_genap'] = $data->ganjil_genap;
                $this->nilai_input['akademik'] = $data->akademik;
                $this->nilai_input['tanggal_unlock'] = $data->tanggal_unlock->format('Y-m-d');

                $pecahAkademik = explode('/', $data->akademik);

                $this->nilai_input['akademik_1'] = $pecahAkademik[0] ?? '';
                $this->nilai_input['akademik_2'] = $pecahAkademik[1] ?? '';
            } else {
                $bulan = (int) date('n');
                $this->nilai_input['ganjil_genap'] = ($bulan >= 1 && $bulan <= 6) ? 'Genap' : 'Ganjil';
                $this->nilai_input['akademik'] = '';
                $this->nilai_input['akademik_1'] = '';
                $this->nilai_input['akademik_2'] = '';
                $this->nilai_input['tanggal_unlock'] = date('Y-m-d');
            }

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalLockNilai($data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'ganjil_genap' => 'required|in:Ganjil,Genap',
            'akademik' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'akademik_1' => 'required|integer|min:1970',
            'akademik_2' => 'required|integer|min:1971',
            'tanggal_unlock' => 'required|date',
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesLockNilai());

        if ($validator->fails()) {
            $pesanFormatSama = 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!';
            $isThnEmpty = empty($data['akademik']) && empty($data['akademik_1']) && empty($data['akademik_2']);
            $formattedErrors = [];

            foreach ($validator->errors()->toArray() as $key => $messages) {
                if (in_array($key, ['akademik', 'akademik_1', 'akademik_2'])) {
                    if (! $hasDuplicateError && ! isset($formattedErrors['akademik'])) {
                        $formattedErrors['akademik'][] = $isThnEmpty ? 'Tahun Akademik wajib diisi!' : $pesanFormatSama;
                    }
                }
            }
            throw ValidationException::withMessages($formattedErrors);
        }

        $validated = $validator->validated();

        return $validated;
    }

    public function updateLockNilai($data)
    {
        try {
            $validated = $this->inputModalLockNilai($data);

            DB::transaction(function () use ($validated) {
                LockNilai::updateOrCreate(
                    ['pr_id' => Auth::user()->pr_id],
                    [
                        'ganjil_genap' => $validated['ganjil_genap'],
                        'akademik' => $validated['akademik'],
                        'tanggal_unlock' => $validated['tanggal_unlock'],
                    ]
                );
            });

            $prodi = Auth::user()->prodi_pr;
            $gg = $validated['ganjil_genap'];
            $aka = $validated['akademik'];
            $tgl = Carbon::parse($validated['tanggal_unlock'])->translatedFormat('d F Y');

            $text = "Nilai {$gg} {$aka} akan dibuka pada tanggal $tgl, untuk $prodi";

            $this->toast(text: $text, type: 'update');
            $this->showLockNilaiModal = false;

            $this->resetInputNilai();
            $this->dispatch('refresh-data-nilai');
        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-nilai');
            $this->showLockNilaiModal = false;
        }
    }

    private function validationMessagesLockNilai()
    {
        return [
            'ganjil_genap.required' => 'Pilih Ganjil atau Genap!',
            'ganjil_genap.in' => "Pilih antara 'Ganjil' atau 'Genap'!",
            'akademik.required' => 'Tahun Akademik wajib diisi!',
            'akademik.regex' => 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!',
            'akademik_1.required' => 'Tahun awal (input kiri) wajib diisi!',
            'akademik_1.min' => 'Tahun awal minimal adalah 1970!',
            'akademik_2.required' => 'Tahun akhir (input kanan) wajib diisi!',
            'akademik_2.min' => 'Tahun akhir minimal adalah 1971!',
            'tanggal_unlock.required' => 'Tanggal Nilai Dibuka pertemuan wajib diisi!',
            'tanggal_unlock.date' => 'Format Tanggal Nilai Dibuka tidak valid!',
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
