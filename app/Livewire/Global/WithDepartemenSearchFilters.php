<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Departemen;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithDepartemenSearchFilters
{
    use WithPagination;

    public $dpSearchQuery = '';

    public $dpSearchResults = [];

    public $dp_id;

    public $dp_name = '';

    public $dp_items;

    public $dpNameSearch = '';

    public $dpResults = [];

    public $selectedDpId = null;

    private function mapDp($collection)
    {
        return $collection->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'departemen' => $j->departemenDp,
            'fakultas' => $j->fakultasFk,
        ])->toArray();
    }

    private function mapDpSearch($collection)
    {
        return $collection->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'kode_text' => 'Kode: '.$j->kode,
            'departemen' => $j->departemenDp,
            'fakultas' => $j->fakultasFk,
        ])->toArray();
    }

    private function dpQuery()
    {
        return Departemen::query()->with('fk_rel');
    }

    private function itemsDp($j)
    {
        if (! $j) {
            return null;
        }

        return [
            'id' => $j->id,
            'kode' => $j->kode,
            'slot1' => $j->departemenDp,
            'slot2' => $j->fakultasFk,
        ];
    }

    public function inputDpFilter()
    {
        $search = trim($this->dpSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->dp_name) {
            $this->dpSearchResults = $this->mapDpSearch(
                $this->dpQuery()
                    ->searchDepartemen($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->dp_name) {
            $this->dpSearchResults = $this->getDpbyUser('search');
        } else {
            $this->dpSearchResults = [];
        }
    }

    public function resetDpFilter()
    {
        $this->reset(['selectedDpId', 'dpSearchQuery', 'dp_name', 'dp_items']);
        $this->resetPage();
    }

    public function selectDpForFilter($id)
    {
        $data = $this->dpQuery()->find($id);

        if ($data) {
            $this->selectedDpId = $id;
            $this->dp_name = $data->departemenDp;
            $this->dpSearchQuery = $data->departemenDp;
            $this->dp_items = $this->itemsDp($data);
            $this->dpSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedDpNameSearch($value)
    {
        $this->dp_id = null;
        $this->dp_items = null;
        $this->resetErrorBag(['dp_id', 'dpNameSearch']);

        $query = $this->dpQuery()->select('departemens.*');

        if (trim(strlen($value)) > 0) {

            $results = $query->searchDepartemen($value)->limit(12)->get();
            $this->dpResults = $this->mapDp($results);

            $exactMatch = $results->first(function ($departemen) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($departemen->departemen)->lower();
                $kode = str($departemen->kode)->lower();

                return $input->is([
                    $nama,
                    "departemen $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->dp_id = $exactMatch->id;
                $this->dp_items = $this->itemsDp($exactMatch);
                $this->dpNameSearch = $exactMatch->departemenDp;
                $this->dpResults = [];
            }

        } else {
            if (Auth::user()->dp_id) {
                $this->dpResults = $this->getDpbyUser();
            } else {
                $this->dpResults = $this->mapDp(
                    $query->orderBy('departemens.nama_dp')->limit(12)->get()
                );
            }
        }
    }

    public function getDpbyUser($type = 'full')
    {
        $user = Auth::user();
        $departemenId = $user->dp_id ?? null;
        $fakultasId = $user->fk_id ?? null;

        $query = $this->dpQuery();

        if (! $departemenId) {
            $defaultDepartemens = $this->dpQuery()
                ->orderBy('nama_dp', 'asc')
                ->limit(12)
                ->get();

            return $type === 'search'
                ? $this->mapDpSearch($defaultDepartemens)
                : $this->mapDp($defaultDepartemens);
        }

        $mainResults = $query
            ->where('fk_id', $fakultasId)
            ->get()
            ->sortBy(fn ($j) => $j->id === $departemenId ? 0 : 1)
            ->take(12);

        if ($mainResults->count() < 12) {
            $extra = $this->dpQuery()
                ->whereHas('fk_rel', fn ($q) => $q->where('id', '!=', $fakultasId))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
            $mainResults = $mainResults->concat($extra);
        }

        return $type === 'search'
            ? $this->mapDpSearch($mainResults)
            : $this->mapDp($mainResults);
    }

    public function fetchDp($query = '')
    {
        if (empty($query) || $this->dp_id) {
            $this->dpResults = $this->getDpbyUser();

            return;
        }
    }

    public function selectDp($id, $departemenName)
    {
        $this->dp_id = $id;
        $this->dpNameSearch = $departemenName;
        $this->dpResults = $this->getDpbyUser();

        $data = $this->dpQuery()->find($id);
        if ($data) {
            $this->dp_items = $this->itemsDp($data);
        }

        $this->haveDpChild();

        if (method_exists($this, 'fetchDp')) {
            $this->fetchDp('');
        }

        $this->resetErrorBag(['dp_id', 'dpNameSearch']);
    }

    public function resetDpInput()
    {
        $this->dp_id = null;
        $this->dp_items = null;
        $this->dpNameSearch = '';

        $this->haveDpChild();

        $this->updatedDpNameSearch('');
        $this->resetErrorBag(['dp_id', 'dpNameSearch']);
    }

    public function haveDpChild()
    {
        if (property_exists($this, 'showMKModal') && property_exists($this, 'pr_id_array') && property_exists($this, 'mkType')) {
            if ($this->showMKModal == true && $this->mkType == 2) {
                $this->resetPrArray();
            }
        }
    }
}
