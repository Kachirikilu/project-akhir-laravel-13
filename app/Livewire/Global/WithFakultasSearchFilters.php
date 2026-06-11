<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Fakultas;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithFakultasSearchFilters
{
    use WithPagination;

    public $fkSearchQuery = '';

    public $fkSearchResults = [];

    public $fk_id;

    public $fk_name = '';

    public $fk_items;

    public $fkNameSearch = '';

    public $fkResults = [];

    public $selectedFkId = null;

    private function mapFk($collection)
    {
        return $collection->map(fn ($f) => [
            'id' => $f->id,
            'kode' => $f->kode,
            'fakultas' => $f->fakultasFk
        ])->toArray();
    }

    private function mapFkSearch($collection)
    {
        return $collection->map(fn ($f) => [
            'id' => $f->id,
            'kode' => $f->kode,
            'kode_text' => 'Kode: '.$f->kode,
            'fakultas' => $f->fakultasFk
        ])->toArray();
    }
    
    private function fkQuery()
    {
        return Fakultas::query();
    }

    private function itemsFk($f)
    {
        if (! $f) {
            return null;
        }
        return [
            'id' => $f->id,
            'kode' => $f->kode,
            'slot1' => $f->fakultasFk,
        ];
    }

    public function inputFkFilter()
    {
        $search = trim($this->fkSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->fk_name)) {
            $this->fkSearchResults = $this->mapFkSearch(
                $this->fkQuery()->searchFakultas($search)->limit(12)->get()
                // $this->searchOutputPr($this->fkQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->fk_name) {
            $this->fkSearchResults = $this->getFkbyUser('search');
        } else {
            $this->fkSearchResults = [];
        }
    }

    public function resetFkFilter()
    {
        $this->reset(['selectedFkId', 'fkSearchQuery', 'fk_name', 'fk_items']);
        $this->resetPage();
    }

    public function selectFkForFilter($id)
    {
        $data = $this->fkQuery()->find($id);
        if ($data) {
            $this->selectedFkId = $id;
            $this->fk_name = $data->fakultasFk;
            $this->fkSearchQuery = $data->fakultasFk;
            $this->fk_items = $this->itemsFk($data);
            $this->fkSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedFkNameSearch($value)
    {
        $this->fk_id = null;
        $this->fk_items = null;
        $this->resetErrorBag(['fk_id', 'fkNameSearch']);

        $query = $this->fkQuery()
            ->select('fakultas.*');

        if (trim(strlen($value)) > 0) {
            $results = $query->searchFakultas($value)->limit(12)->get();
            // $results = $this->searchOutputPr($query, $value, 12);

            $this->fkResults = $this->mapFk($results);

            $exactMatch = $results->first(function ($fakultas) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($fakultas->fakultas)->lower();
                $kode = str($fakultas->kode)->lower();

                return $input->is([
                    $nama,
                    "fakultas $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->fk_id = $exactMatch->id;
                $this->fk_items = $this->itemsFk($exactMatch);
                $this->fkNameSearch = $exactMatch->fakultasFk;
                $this->fkResults = [];
            }

        } else {
            if (Auth::user()->fk_id) {
                $this->fkResults = $this->getFkbyUser();
            } else {
                $this->fkResults = $this->mapFk(
                    $query->orderBy('fakultas.nama_fk')->limit(12)->get()
                );
            }
        }
    }

    public function getFkbyUser($mode = 'full')
    {
        $user = Auth::user();
        $fakultasId = $user->fk_id ?? null;

        $query = $this->fkQuery();

        if (! $fakultasId) {
            $defaultFakultas = $query
                ->orderBy('nama_fk', 'asc')
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapFkSearch($defaultFakultas)
                : $this->mapFk($defaultFakultas);
        }

        $mainResults = $query
            ->orderBy('nama_fk', 'asc')
            ->get()
            ->sortBy(fn ($f) => $f->id === $fakultasId ? 0 : 1)
            ->take(12);

        return $mode === 'search'
            ? $this->mapFkSearch($mainResults)
            : $this->mapFk($mainResults);
    }

    public function fetchFk($query = '')
    {
        if (empty($query) || $this->fk_id) {
            $this->fkResults = $this->getFkbyUser();

            return;
        }
    }

    public function selectFk($id, $fakultasName)
    {
        $this->fk_id = $id;
        $this->fkNameSearch = $fakultasName;
        $this->fkResults = $this->getFkbyUser();

        $data = $this->fkQuery()->find($id);
        if ($data) {
            $this->fk_items = $this->itemsFk($data);
        }

        $this->haveFkChild();

        $this->resetErrorBag(['fk_id', 'fkNameSearch']);
    }

    public function resetFkInput()
    {
        $this->fk_id = null;
        $this->fk_items = null;
        $this->fkNameSearch = '';

        $this->haveFkChild();

        $this->updatedFkNameSearch('');
        $this->resetErrorBag(['fk_id', 'fkNameSearch']);
    }

    public function haveFkChild()
    {
        if (property_exists($this, 'showMKModal') && property_exists($this, 'pr_id_array') && property_exists($this, 'mkType')) {
            if ($this->showMKModal == true && $this->mkType == 3) {
                $this->resetPrArray();
            }
        }
    }
}
