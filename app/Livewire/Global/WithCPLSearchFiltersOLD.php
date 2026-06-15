<?php

namespace App\Livewire\Global;

use App\Models\Akademik\CPL;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithCPLSearchFilters
{
    use WithPagination;

    public $cplSearchQuery = '';
    public $cplSearchResults = [];
    public $modeCPL = '';
    public $cpl_id;
    public $cpl_name = '';
    public $cpl_items = [];
    public $cplNameSearch = '';
    public $cplResults = [];
    public $selectedCPLId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $cpl_id_array = [];
    public $cpl_name_array = [];
    public $cpl_items_array = [];

    /**
     * Helper untuk mapping hasil agar seragam
     */
    private function mapCPL($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'deskripsi' => $c->deskripsi,
        ])->toArray();
    }

    private function cplQuery()
    {
        return CPL::query()->with('cpmks.rps', 'cpmks');
    }

    private function itemsCPL($c)
    {
        if (! $c) {
            return null;
        }
        return [
            'id' => $c->id,
            'kode' => $c->kode,
            'slot1' => $c->deskripsi,
        ];
    }

    public function inputCPLFilter()
    {
        $search = trim($this->cplSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->cpl_name) {
            $this->cplSearchResults = $this->mapCPL(
                $this->cplQuery()->searchCPL($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->cpl_name) {
            $this->cplSearchResults = $this->getCPLbyUser();
        } else {
            $this->cplSearchResults = [];
        }
    }

    public function resetCPLFilter()
    {
        $this->reset(['selectedCPLId', 'cplSearchQuery', 'cpl_name', 'cpl_items']);
        $this->resetPage();
    }

    public function selectCPLForFilter($id)
    {
        $data = $this->cplQuery()->find($id);

        if ($data) {
            $this->selectedCPLId = $id;
            $this->cpl_name = $data->deskripsi;
            $this->cplSearchQuery = $data->deskripsi;
            $this->cpl_items = $this->itemsCPL($data);
            $this->cplSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedCPLNameSearch($value)
    {
        $this->cpl_id = null;
        $this->cpl_items = null;
        $this->resetErrorBag(['cpl_id', 'cplNameSearch']);

        $query = $this->cplQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchCPL($value)->limit(12)->get();
            $this->cplResults = $this->mapCPL($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($cpl) use ($value, $normalizedValue) {
                $normalizedMkKode = str_replace(['-', ' '], '', strtolower($cpl->kode));
                
                return strtolower($cpl->deskripsi) === strtolower($value) 
                    || $normalizedMkKode === $normalizedValue;
            });

            if ($exactMatch) {
                if ($this->modeCPL == 'single') {
                    $this->cplNameSearch = $exactMatch->deskripsi;
                    $this->cpl_id = $exactMatch->id;
                    $this->cpl_items = $this->itemsCPL($exactMatch);
                    $this->cplResults = [];
                } else {
                    $this->cplNameSearch = '';
                    $this->cpl_id_array[] = $exactMatch->id;
                    $this->cpl_items_array[] = $this->itemsCPL($exactMatch);
                }
                $this->cplResults = $this->getCPLbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->cplResults = $this->getCPLbyUser();
            } else {
                $this->cplResults = $this->mapCPL(
                    $query->orderBy('cpls.deskripsi')->limit(12)->get()
                );
            }
        }
    }

    public function getCPLbyUser()
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->cplQuery();
        
        if (!$prodiId) {
            $defaultCPL = $query
                ->latest()
                ->limit(12)
                ->get();
            return $this->mapCPL($defaultCPL);
        }

        $mainResults = $query
            ->whereHas('cpmks.rps.mk_rel.prodis', function($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->cplQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapCPL($mainResults);
    }

    public function fetchCPL($query = '', $mode = 'single')
    {
        $this->modeCPL = $mode;
        if (empty($query) || $this->cpl_id) {
            $this->cplResults = $this->getCPLbyUser();
        }

        return;
    }


    public function selectCPL($id, $cplName)
    {
        $this->cpl_id = $id;
        $this->cplNameSearch = $cplName;
        $this->cplResults = $this->getCPLbyUser();

        $data = $this->cplQuery()->find($id);
        if ($data) {
            $this->cpl_items = $this->itemsCPL($data);
        }

        if (method_exists($this, 'fetchCPL')) {
            $this->fetchCPL('');
        }

        $this->resetErrorBag(['cpl_id', 'cplNameSearch']);
    }
    public function selectCPLArray($id)
    {
        $data = $this->cplQuery()->find($id);
        if ($data && ! in_array($id, $this->cpl_id_array)) {
            $this->cpl_id_array[] = $id;
            $this->cpl_name_array[] = $data->deskripsi;
            $this->cpl_items_array[] = $data->kode;
        }
    }

    public function resetCPLInput()
    {
        $this->reset(['cpl_id', 'cpl_items', 'cplNameSearch']);
        $this->cplResults = $this->getCPLbyUser();
    }

    public function resetCPLArray()
    {
        $this->cpl_id_array = [];
        $this->cpl_name_array = [];
        $this->cpl_items_array = [];
        $this->cplNameSearch = '';
    }
}