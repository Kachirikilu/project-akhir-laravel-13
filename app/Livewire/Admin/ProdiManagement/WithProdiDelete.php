<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Livewire\Global\HasToast;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Kelas\Kelas;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;

trait WithProdiDelete
{
    use HasToast;

    public $showProdiDelete = false;

    public $prodiIdToDelete;

    public $prodiNamaToDelete;

    public $prodiForDelete;

    public $notFoundText;

    public $isPermanentDelete = false;

    public $typeProdi;

    private function getModels()
    {
        return [
            'prodi' => Prodi::class,
            'departemen' => Departemen::class,
            'fakultas' => Fakultas::class,
        ];
    }

    public function deleteProdi($id, $type, $isTrashed = false)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $this->typeProdi = $type;
        $modelClass = $this->getModels()[$type] ?? null;

        $data = $isTrashed
            ? $modelClass::withTrashed()->find($id)
            : $modelClass::find($id);

        if (! $data) {
            $this->toast(type: 'unfound', variant: 'warning');

            return;
        }

        $this->prodiIdToDelete = $id;
        $this->prodiNamaToDelete = $data->prodi_pr ?? $data->departemen_dp ?? $data->fakultas_fk;
        $this->prodiForDelete = $type;
        $this->isPermanentDelete = $isTrashed;
        $this->showProdiDelete = true;
    }

    public function destroyProdi()
    {
        if (! $this->AuthCheck()) {
            return;
        }
        if (! $this->prodiIdToDelete) {
            return;
        }

        $type = 'delete';

        try {
            $modelClass = $this->getModels()[$this->prodiForDelete] ?? null;
            $data = $modelClass::withTrashed()->findOrFail($this->prodiIdToDelete);


            // if ($this->typeProdi == '' || $this->typeProdi == 'prodi') {
            //     $compositeKey = $data->kode;
            //     $prId = $this->prodiIdToDelete;
            // }

            if ($this->isPermanentDelete) {
                $this->checkSafety($data, $this->prodiForDelete);
                $type = 'permanent';
                $data->forceDelete();
            } else {
                $data->delete();
            }

            // if ($this->typeProdi == '' || $this->typeProdi == 'prodi') {
            //     foreach (['prodi.history', 'capaian.history'] as $key) {
            //         $history = session($key, []);
            //         $historyBefore = count($history);
            //         $history = array_filter($history, function ($item) use ($prId) {
            //             return data_get($item, 'pr_id') != $prId;
            //         });
            //         if (count($history) !== $historyBefore) {
            //             session([$key => $history]);
            //         }
            //     }
            //     // $this->dispatch('refresh-layout-sidebar');
            // }

            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(message: $this->prodiNamaToDelete, type: $type);
            $this->cleanupDeleteStateProdi();

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function checkSafety($data, $type)
    {
        if ($type === 'fakultas') {
            if ($data->departemens()->exists()) {
                throw new \Exception('Gagal hapus permanen: Fakultas masih memiliki Departemen!');
            }
        }

        if ($type === 'departemen') {
            if ($data->prodis()->exists()) {
                throw new \Exception('Gagal hapus permanen: Departemen masih memiliki Program Studi!');
            }
        }

        if ($type === '' || $type === 'prodi') {
            $hasUsers = Admin::where('pr_id', $data->id)->exists() ||
                       Dosen::where('pr_id', $data->id)->exists() ||
                       Mahasiswa::where('pr_id', $data->id)->exists();

            if ($hasUsers) {
                throw new \Exception('Gagal hapus permanen: Program Studi masih memiliki User (Admin/Dosen/Mahasiswa)!');
            }

            if ($data->mata_kuliahs()->exists()) {
                throw new \Exception('Gagal hapus permanen: Program Studi masih memiliki Mata Kuliah!');
            }

            if (Kelas::where('pr_id', $data->id)->exists()) {
                throw new \Exception('Gagal hapus permanen: Program Studi masih memiliki Kelas!');
            }
        }
    }

    public function restoreProdi($id, $type)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        try {
            $modelClass = $this->getModels()[$type] ?? null;
            $data = $modelClass::withTrashed()->findOrFail($id);
            $message = $data->prodi_pr ?? $data->departemen_dp ?? $data->fakultas_fk;
            $data->restore();

            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(message: $message, type: 'recycle');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateProdi()
    {
        $this->prodiIdToDelete = null;
        $this->prodiNamaToDelete = null;
        $this->showProdiDelete = false;
    }
}
