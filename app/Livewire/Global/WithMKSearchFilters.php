<?php

namespace App\Livewire\Global;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithMKSearchFilters
{
    use WithPagination;

    public $mkSearchQuery = '';

    public $mkSearchResults = [];

    public $modeMK = '';

    public $mk_id;

    public $mk_name = '';

    public $mk_items = [];

    public $mkNameSearch = '';

    public $mkResults = [];

    public $selectedMKId = null;

    public $mk_id_array = [];

    public $mk_items_array = [];

    // public $skipMkNameSearchUpdate = false;

    private function mapMK($collection)
    {
        return $collection->map(fn ($m) => [
            'id' => $m->id,
            'kode' => $m->kode,
            'mk' => $m->mk,
            'semester' => $m->semester,
            'kode_semester' => $m->kode_semester,
            'sks' => $m->sks,
            'sks_text' => $m->sks_text,
            'sks_full' => $m->sks_full,
            'wajib_text' => $m->wajib_text,
            'level_mk' => $m->level_mk,
        ])->toArray();
    }

    private function mapMKSearch($collection)
    {
        return $collection->map(fn ($m) => [
            'id' => $m->id,
            'kode' => $m->kode,
            'mk' => $m->mk,
            'semester_text' => $m->semester_text,
            'sks_full' => $m->sks_full,
            'wajib_text' => $m->wajib_text,
        ])->toArray();
    }

    private function mkQuery()
    {
        return MataKuliah::query()->with('prodis');
    }

    private function itemsMK($m)
    {
        if (! $m) {
            return null;
        }

        return [
            'id' => $m->id,
            'kode' => $m->kode,
            'slot1' => $m->mk,
            'slot2' => $m->kode_semester,
        ];
    }

    public function inputMKFilter()
    {
        $search = trim($this->mkSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->mk_name) {
            $this->mkSearchResults = $this->mapMKSearch(
                $this->mkQuery()->searchMK($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->mk_name) {
            $this->mkSearchResults = $this->getMKbyUser('search');
        } else {
            $this->mkSearchResults = [];
        }
    }

    public function resetMKFilter()
    {
        $this->reset(['selectedMKId', 'mkSearchQuery', 'mk_name', 'mk_items']);
        $this->resetPage();
    }

    public function selectMKForFilter($id)
    {
        $data = $this->mkQuery()->find($id);

        if ($data) {
            $this->selectedMKId = $id;
            $this->mk_name = $data->mk;
            $this->mkSearchQuery = $data->mk;
            $this->mk_items = $this->itemsMK($data);
            $this->mkSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedMKNameSearch($value)
    {
        // if ($this->skipMkNameSearchUpdate) {
        //     return;
        // }

        $this->mk_id = null;
        $this->mk_items = null;
        $this->resetErrorBag(['mk_id', 'mkNameSearch']);

        $query = $this->mkQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchMK($value)->limit(12)->get();
            $this->mkResults = $this->mapMK($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($mk) use ($value, $normalizedValue) {
                $normalizedMKKode = str_replace(['-', ' '], '', strtolower($mk->kode));

                return strtolower($mk->mk) === strtolower($value)
                    || $normalizedMKKode === $normalizedValue;
            });

            if ($exactMatch) {
                if ($this->modeMK == 'single') {
                    $this->mkNameSearch = $exactMatch->mk;
                    $this->mk_id = $exactMatch->id;
                    $this->mk_items = $this->itemsMK($exactMatch);
                    $this->mkResults = [];
                } else {
                    $this->mkNameSearch = '';
                    $this->mk_id_array[] = $exactMatch->id;
                    $this->mk_items_array[] = $this->itemsMK($exactMatch);
                }
                $this->mkResults = $this->getMKbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->mkResults = $this->getMKbyUser();
            } else {
                $this->mkResults = $this->mapMK(
                    $query->orderBy('mata_kuliahs.nama_mk')->limit(12)->get()
                );
            }
        }
    }

    public function getMKbyUser($mode = 'full')
    {
        // if ($this->skipMkNameSearchUpdate) {
        //     return;
        // }

        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->mkQuery();

        if (! $prodiId) {
            $defaultMK = $query
                ->orderBy('nama_mk', 'asc')
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapMKSearch($defaultMK)
                : $this->mapMK($defaultMK);
        }

        $mainResults = $query
            ->whereHas('prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->orderBy('nama_mk', 'asc')
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->mkQuery()
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('nama_mk', 'asc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapMKSearch($mainResults)
            : $this->mapMK($mainResults);
    }

    public function fetchMK($query = '', $mode = 'single')
    {
        $this->modeMK = $mode;

        if ($this->mk_id && empty($this->mk_items)) {
            $mk = MataKuliah::find($this->mk_id);
            if ($mk) {
                $this->mk_items = $this->itemsMK($mk);
            }
        }

        if (empty($query) || $this->mk_id) {
            $this->mkResults = $this->getMKbyUser();

            return;
        }
    }

    public function selectMK($id, $mkName)
    {
        $this->mk_id = $id;
        $this->mkNameSearch = $mkName;

        $data = $this->mkQuery()->find($id);
        if ($data) {
            $this->mk_items = $this->itemsMK($data);
        }

        // if (method_exists($this, 'fetchMK')) {
        //     $this->fetchMK('');
        // }

        $this->mkResults = $this->getMKbyUser();
        $this->resetErrorBag(['mk_id', 'mkNameSearch']);
    }

    public function selectMKArray($id)
    {
        $data = $this->mkQuery()->find($id);

        if ($data && ! in_array($id, $this->mk_id_array)) {
            $this->mk_id_array[] = $id;
            $this->mk_items_array[] = $this->itemsMK($data);
        }
    }

    public function resetMKInput()
    {
        $this->mk_id = null;
        $this->mk_items = null;
        $this->mkNameSearch = '';

        $this->updatedMKNameSearch('');
        $this->resetErrorBag(['mk_id', 'mkNameSearch']);
    }

    public function resetMKArray()
    {
        $this->mk_id_array = [];
        $this->mk_items_array = [];
        $this->mkNameSearch = '';
    }
}
