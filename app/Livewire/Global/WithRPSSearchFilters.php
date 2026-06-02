<?php

namespace App\Livewire\Global;

use App\Models\Akademik\RPS;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithRPSSearchFilters
{
    use WithPagination;

    public $rpsSearchQuery = '';

    public $rpsSearchResults = [];

    public $modeRPS = '';

    public $rps_id;

    public $rps_name = '';

    public $rps_items;

    public $rpsNameSearch = '';

    public $rpsResults = [];

    public $selectedRPSId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $rps_id_array = [];

    public $rps_items_array = [];

    private function mapRPS($collection)
    {
        if ($collection instanceof AbstractPaginator) {
            $collection = $collection->getCollection();
        }

        return $collection->map(fn ($r) => [
            'id' => $r->id,
            'mk_id' => $r->mk_id,
            'kode_mk' => $r->kode_mk,
            'kode_blok' => $r->kode_blok,
            'kode' => $r->kode,
            'rps' => $r->rps,
            'rps_with_kode' => $r->rps_with_kode,
            'mk' => $r->mk,
            'deskripsi' => $r->deskripsi,
            'akademik' => $r->akademik,
            'draf' => $r->draf,
            'draf_text' => $r->draf_text,
            'draf_full' => $r->draf_full,
            'revisi' => $r->revisi,
            'count_cpmk' => $r->count_cpmk,
            'count_scpmk' => $r->count_scpmk,
            'wajib' => $r->wajib,
            'wajib_text' => $r->wajib_text,
            'sks' => $r->sks,
            'sks_text' => $r->sks_text,
            'sks_full' => $r->sks_full,
            'bobot_uts' => $r->bobot_uts,
            'bobot_uas' => $r->bobot_uas,
            'total_bobot' => $r->total_bobot,
        ])->toArray();
    }

    private function mapRPSSearch($collection)
    {
        if ($collection instanceof AbstractPaginator) {
            $collection = $collection->getCollection();
        }

        return $collection->map(fn ($r) => [
            'id' => $r->id,
            'kode' => $r->kode,
            'rps' => $r->rps,
            'rps_with_kode' => $r->rps_with_kode,
            'draf_text' => $r->draf_text,
            'draf_full' => $r->draf_full,
            'wajib_text' => $r->wajib_text,
            'sks_full' => $r->sks_full,
        ])->toArray();
    }

    private function rpsQuery()
    {
        return RPS::query()->with(['mk_rel', 'cpmks', 'cpmks.scpmks']);
    }

    private function itemsRPS($r)
    {
        if (! $r) {
            return null;
        }

        return [
            'id' => $r->id,
            'kode' => $r->kode,
            'slot1' => $r->rps,
            'slot2' => $r->sks_full,
            'slot3' => $r->wajib_text,
            'slot4' => $r->draf_full,
        ];
    }

    public function inputRPSFilter()
    {
        $search = trim($this->rpsSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->rps_name) {
            $this->rpsSearchResults = $this->mapRPSSearch(
                $this->rpsQuery()->searchRPS($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->rps_name) {
            $this->rpsSearchResults = $this->getRPSbyUser('search');
        } else {
            $this->rpsSearchResults = [];
        }
    }

    public function resetRPSFilter()
    {
        $this->reset(['selectedRPSId', 'rpsSearchQuery', 'rps_name', 'rps_items']);
        $this->resetPage();
    }

    public function selectRPSForFilter($id)
    {
        $data = $this->rpsQuery()->with(['mk_rel'])->find($id);

        if ($data) {
            $this->selectedRPSId = $id;
            $this->rps_name = $data->rps;
            $this->rpsSearchQuery = $data->rps;
            $this->rps_items = $this->itemsRPS($data);
            $this->rpsSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRPSNameSearch($value)
    {
        $this->rps_id = null;
        $this->rps_items = null;
        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);

        $query = $this->rpsQuery()->select('rps.*');

        $this->haveRPSParent($query);

        if (trim(strlen($value)) > 0) {
            $results = $query->searchRPS($value)->limit(12)->get();
            $this->rpsResults = $this->mapRPS($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($r) use ($value, $normalizedValue) {
                $normalizedRPSKode = str_replace(['-', ' '], '', strtolower($r->kode));

                return strtolower($r->rps) === strtolower($value)
                    || strtolower($r->mk) === strtolower($value)
                    || $normalizedRPSKode === $normalizedValue;
            });

            if ($exactMatch) {
                if ($this->modeRPS == 'single') {
                    $this->rpsNameSearch = $exactMatch->rps;
                    $this->rps_id = $exactMatch->id;
                    $this->rps_items = $this->itemsRPS($exactMatch);
                    $this->rpsResults = [];
                } else {
                    $this->rpsNameSearch = '';
                    $this->rps_id_array[] = $exactMatch->id;
                    $this->rps_items_array[] = $this->itemsRPS($exactMatch);
                }
                $this->rpsResults = $this->getRPSbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->rpsResults = $this->getRPSbyUser();
            } else {
                $this->rpsResults = $this->mapRPS(
                    $query->orderBy('rps.mk_rel.nama_mk')->limit(12)->get()
                );
            }
        }
    }

    public function getRPSbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->rpsQuery();

        if (! $prodiId) {
            $defaultRPS = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapRPSSearch($defaultRPS)
                : $this->mapRPS($defaultRPS);
        }

        $this->haveRPSParent($query);

        $mainResults = $query
            ->whereHas('mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->rpsQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapRPSSearch($mainResults)
            : $this->mapRPS($mainResults);
    }

    public function fetchRPS($query = '', $mode = 'single')
    {
        $this->modeRPS = $mode;
        if (empty($query) || $this->rps_id) {
            $this->rpsResults = $this->getRPSbyUser();
        }

    }

    public function selectRPS($id, $rpsName)
    {
        $this->rps_id = $id;
        $this->rpsNameSearch = $rpsName;
        $this->rpsResults = $this->getRPSbyUser();

        $data = $this->rpsQuery()->find($id);
        if ($data) {
            $this->rps_items = $this->itemsRPS($data);
        }

        if (method_exists($this, 'fetchRPS')) {
            $this->fetchRPS('');
        }

        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);
    }

    public function selectRPSArray($id)
    {
        $data = $this->rpsQuery()->find($id);
        if ($data && ! in_array($id, $this->rps_id_array)) {
            $this->rps_id_array[] = $id;
            $this->rps_items_array[] = $this->itemsRPS($data);
        }
    }

    public function resetRPSInput()
    {
        $this->reset(['rps_id', 'rps_items', 'rpsNameSearch']);
        $this->rpsResults = $this->getRPSbyUser();
    }

    public function resetRPSArray()
    {
        $this->rps_id_array = [];
        $this->rps_items_array = [];
        $this->rpsNameSearch = '';
    }

    public function haveRPSParent($query)
    {
        if (property_exists($this, 'showKelasModal')) {
            if ($this->showKelasModal == true && filled($this->pr_id)) {
                $query->whereHas('mk_rel.prodis', function ($q) {
                    $q->where('prodis.id', $this->pr_id);
                });
            }
        }

        return $query;
    }
}
