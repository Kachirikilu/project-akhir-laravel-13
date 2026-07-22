<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithProdiSearchFilters
{
    use WithPagination;

    public $prSearchQuery = '';
    public $prSearchResults = [];
    public $modePr = 'single';
    public $pr_id;
    public $pr_name;
    public $pr_items;
    public $prNameSearch = '';
    public $prResults = [];
    public $selectedPrId = null;
    public $mkType = '';
    public $showMKModal = false;

    public $pr_id_array = [];
    public $pr_items_array = [];


    private function mapPr($collection)
    {
        return $collection->map(fn ($p) => [
            'id' => $p->id,
            'kode' => $p->kode,
            'prodi' => $p->prodi,
            'departemen' => $p->departemenDp,
            'fakultas' => $p->fakultasFk,
            'strata' => $p->strata,
        ])->toArray();
    }

    private function prQuery()
    {
        return Prodi::query()->with(['dp_rel', 'dp_rel.fk_rel']);
    }

    private function itemsPr($p)
    {
        if (! $p) {
            return null;
        }

        return [
            'id' => $p->id,
            'kode' => $p->kode,
            'slot1' => $p->prodi,
            'slot2' => $p->departemenDp,
            'slot3' => $p->fakultasFk,
        ];
    }

    public function inputPrFilter()
    {
        $search = trim($this->prSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->pr_name) {
            $this->prSearchResults = $this->mapPr(
                $this->prQuery()
                    ->searchProdi($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->pr_name) {
            $this->prSearchResults = $this->getPrbyUser();
        } else {
            $this->prSearchResults = [];
        }
    }

    public function resetPrFilter()
    {
        $this->reset(['selectedPrId', 'prSearchQuery', 'pr_name', 'pr_items']);
        $this->resetPage();
    }

    public function selectPrForFilter($id)
    {
        $data = $this->prQuery()->find($id);

        if ($data) {
            $this->selectedPrId = $id;
            $this->pr_name = $data->prodi;
            $this->prSearchQuery = $data->prodi;
            $this->pr_items = $this->itemsPr($data);
            $this->prSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedPrNameSearch($value)
    {
        $this->pr_id = null;
        $this->pr_items = null;
        $this->resetErrorBag(['pr_id', 'prNameSearch']);

        $input = str($value)->lower()->trim();
        if (empty($input->toString())) {
            $this->prResults = $this->getPrbyUser();

            return;
        }

        $query = $this->prQuery()->select('prodis.*');

        // --- TAMBAHKAN LOGIKA FILTER DI SINI ---
        if (($this->mkType == 2) && filled($this->dp_id) && $this->showMKModal) {
            $query->where('dp_id', $this->dp_id);
        } elseif (($this->mkType == 3) && filled($this->fk_id) && $this->showMKModal) {
            $query->whereHas('dp_rel', fn ($q) => $q->where('fk_id', $this->fk_id));
        }

        // 1. Logika shortcut 'uni' untuk mkType 4
        if ($this->modePr !== 'single' && $input->toString() === 'uni' && $this->mkType == 4) {
            $allProdis = $query->get();
            foreach ($allProdis as $p) {
                if (! in_array($p->id, $this->pr_id_array)) {
                    $this->pr_id_array[] = $p->id;
                    $this->pr_items_array[] = $this->itemsPr($p);
                }
            }
            $this->prNameSearch = '';
            $this->prResults = $this->getPrbyUser();

            return;
        }

        // 2. Jalankan Query Pencarian Biasa (untuk filter dropdown)
        $results = $query->searchProdi($value)->limit(12)->get();
        $this->prResults = $this->mapPr($results);

        // 3. Pencocokan "Exact Match" yang Diperluas (Leveling)
        $matches = $results->filter(function ($prodi) use ($input) {
            $namaProdi = str($prodi->prodi)->lower()->trim();
            $kodeProdi = str($prodi->kode)->lower()->trim();

            $kodeDepartemen = $kodeProdi;
            $kodeFakultas = $kodeProdi;

            if ($this->mkType >= 2) {
                $kodeDepartemen = str($prodi->dp_rel?->kode ?? '')->lower()->trim();
            }
            if ($this->mkType >= 3) {
                $kodeFakultas = str($prodi->dp_rel?->fk_rel?->kode ?? '')->lower()->trim();
            }

            $namaStrata = str($prodi->strata)->lower()->trim();
            $inisialStrata = match ($namaStrata->toString()) {
                'sarjana' => 's1', 'magister' => 's2', 'doktor' => 's3', default => ''
            };

            $possibilities = [
                $namaProdi->toString(),
                $kodeProdi->toString(),
                $kodeDepartemen->toString(),
                $kodeFakultas->toString(),
                "$inisialStrata $namaProdi",
                "$namaStrata $namaProdi",
                "$inisialStrata$namaProdi",
            ];

            return in_array($input->toString(), $possibilities);
        });

        // 4. Eksekusi Hasil Match
        if ($matches->isNotEmpty()) {
            if ($this->modePr == 'single') {
                $exactMatch = $matches->first();
                $this->prNameSearch = $exactMatch->prodi;
                $this->pr_id = $exactMatch->id;
                $this->pr_items = $this->itemsPr($exactMatch);
            } else {
                foreach ($matches as $match) {
                    if (! in_array($match->id, $this->pr_id_array)) {
                        $this->pr_id_array[] = $match->id;
                        $this->pr_items_array[] = $this->itemsPr($match);
                    }
                }
                $this->prNameSearch = '';
            }
            $this->prResults = $this->getPrbyUser();
        }
    }

    public function getPrbyUser()
    {
        $user = Auth::user();
        $prodiId = $user?->pr_id;
        $departemenId = $user->dp_id ?? null;
        $fakultasId = $user->fk_id ?? null;

        $query = $this->prQuery();

        if (! $prodiId) {
            $defaultProdis = $query
                ->orderBy('nama_pr', 'asc')
                ->limit(12)
                ->get();

            return $this->mapPr($defaultProdis);
        }

        if (($this->mkType == 2) && filled($this->dp_id) && $this->showMKModal) {
            $query->where('dp_id', $this->dp_id);
        } elseif (($this->mkType == 3) && filled($this->fk_id) && $this->showMKModal) {
            $query->whereHas('dp_rel', fn ($q) => $q->where('fk_id', $this->fk_id));
        } else {
            $query->whereHas('dp_rel', fn ($q) => $q->where('fk_id', $fakultasId));
        }

        $mainResults = $query->get()->sortBy(function ($p) use ($prodiId, $departemenId, $fakultasId) {
            if ($p->id === $prodiId) {
                return 0;
            }
            if ($p->dp_id === $departemenId) {
                return 1;
            }
            if ($p->fk_id === $fakultasId) {
                return 2;
            }

            return 3;
        })->take(12);

        if ($mainResults->count() < 12) {
            $extra = $this->prQuery()
                ->whereHas('dp_rel', fn ($q) => $q->where('fk_id', '!=', $fakultasId))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapPr($mainResults);
    }

    public function fetchPr($query = '', $mode = 'single')
    {
        $this->modePr = $mode;

        if ($this->pr_id && empty($this->pr_items)) {
            $prodi = Prodi::find($this->pr_id);
            if ($prodi) {
                $this->pr_items = $this->itemsPr($prodi);
            }
        }

        if (empty($query) || $this->pr_id) {
            $this->prResults = $this->getPrbyUser();

            return;
        }
    }

    public function selectPr($id, $prodiName)
    {
        $this->pr_id = $id;
        $this->prNameSearch = $prodiName;

        $data = $this->prQuery()->find($id);
        if ($data) {
            $this->pr_items = $this->itemsPr($data);
        }

        $this->prResults = $this->getPrbyUser();
        $this->resetErrorBag(['pr_id', 'prNameSearch']);
    }

    public function selectPrArray($id)
    {
        $data = $this->prQuery()->find($id);

        if ($data && ! in_array($id, (array) $this->pr_id_array)) {
            $this->pr_id_array[] = $id;
            $this->pr_items_array[] = $this->itemsPr($data);
        }
    }

    public function resetPrInput()
    {
        $this->pr_id = null;
        $this->pr_items = null;
        $this->prNameSearch = '';

        $this->updatedPrNameSearch('');
        $this->resetErrorBag(['pr_id', 'prNameSearch']);
    }

    public function resetPrArray()
    {
        $this->pr_id_array = [];
        $this->pr_items_array = [];
        $this->prNameSearch = '';
    }
}
