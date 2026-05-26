<?php

namespace App\Livewire\Global;

use App\Models\Akademik\CPMK;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithCPMKSearchFilters
{
    use WithPagination;

    public $cpmkSearchQuery = '';

    public $cpmkSearchResults = [];

    public $modeCPMK = '';

    public $cpmk_id;

    public $cpmk_name = '';

    public $cpmk_items;

    public $cpmkNameSearch = '';

    public $cpmkResults = [];

    public $selectedCPMKId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $cpmk_id_array = [];

    public $cpmk_items_array = [];

    public $cpmk_sub_items_array = [];

    // public $deskripsi_cpmk;

    private function mapCPMK($collection)
    {
        return $collection->map(function ($c) {
            $allScpmkRefIds = $c->scpmks->flatMap(fn ($s) => $s->refs->pluck('id'))->unique()->toArray();
            $cpmkUniqueRefs = $c->refs->filter(fn ($r) => ! in_array($r->id, $allScpmkRefIds));

            return [
                'id' => $c->id,
                'kode' => $c->kode,
                'deskripsi' => $c->deskripsi_cpl,
                'scpmk' => $this->mapSCPMK($c->scpmks),
                'count_scpmk' => $c->count_scpmk,
                'ref' => $this->mapRef($cpmkUniqueRefs),
                'cpl' => $this->mapCPL($c->cpls),
                'total_bobot' => $c->scpmks->sum('bobot') ?? 0,
            ];
        })->toArray();
    }

    private function mapCPMKSearch($collection)
    {
        return $collection->map(function ($c) {
            $allScpmkRefIds = $c->scpmks->flatMap(fn ($s) => $s->refs->pluck('id'))->unique()->toArray();
            $cpmkUniqueRefs = $c->refs->filter(fn ($r) => ! in_array($r->id, $allScpmkRefIds));

            return [
                'id' => $c->id,
                'kode' => $c->kode,
                'deskripsi' => $c->deskripsi_cpl,
                'total_pertemuan' => $c->count_scpmk.' Pertemuan',
                'total_bobot_text' => ($c->scpmks->sum('bobot') ?? 0).'% Bobot',
            ];
        })->toArray();
    }

    private function cpmkQuery()
    {
        return CPMK::query()->with(['rps', 'scpmks']);
    }

    private function itemsCPMK($c)
    {
        if (! $c) {
            return null;
        }

        return [
            'id' => $c->id,
            'kode' => $c->kode,
            'slot1' => $c->deskripsi_cpl,
            'slot2' => $c->count_scpmk,
            'slot3' => $c->total_bobot ?? ($c->scpmks ? $c->scpmks->sum('bobot') : 0),
        ];
    }

    private function pushToCPMKItems($mappedResults)
    {
        $mappedData = $mappedResults[0] ?? null;

        if ($mappedData) {
            $this->cpmk_sub_items_array[] = [
                'scpmk' => $mappedData['scpmk'] ?? [],
                'ref' => $mappedData['ref'] ?? [],
                'cpl' => $mappedData['cpl'] ?? [],
            ];
        }
    }

    public function inputCPMKFilter()
    {
        $search = trim($this->cpmkSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->cpmk_name) {
            $this->cpmkSearchResults = $this->mapCPMKSearch(
                $this->cpmkQuery()->searchCPMK($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->cpmk_name) {
            $this->cpmkSearchResults = $this->getCPMKbyUser('search');
        } else {
            $this->cpmkSearchResults = [];
        }
    }

    public function resetCPMKFilter()
    {
        $this->reset(['selectedCPMKId', 'cpmkSearchQuery', 'cpmk_name', 'cpmk_items']);
        $this->resetPage();
    }

    public function selectCPMKForFilter($id)
    {
        $data = $this->cpmkQuery()->find($id);

        if ($data) {
            $this->selectedCPMKId = $id;
            $this->cpmk_name = $data->deskripsi_cpl;
            $this->cpmkSearchQuery = $data->deskripsi_cpl;
            $this->cpmk_items = $this->itemsCPMK($data);
            $this->cpmkSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedCPMKNameSearch($value)
    {
        $this->cpmk_id = null;
        $this->cpmk_items = null;
        $this->resetErrorBag(['cpmk_id', 'cpmkNameSearch']);

        $query = $this->cpmkQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchCPMK($value)->limit(12)->get();
            $this->cpmkResults = $this->mapCPMK($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($c) use ($value, $normalizedValue) {
                $normalizedMkKode = str_replace(['-', ' '], '', strtolower($c->kode));

                return strtolower($c->deskripsi) === strtolower($value)
                    || strtolower($c->deskripsi_cpl) === strtolower($value)
                    || $normalizedMkKode === $normalizedValue;
            });

            if ($exactMatch) {
                if ($this->modeCPMK == 'single') {
                    $this->cpmkNameSearch = $exactMatch->deskripsi_cpl;
                    $this->cpmk_id = $exactMatch->id;
                    $this->cpmk_items = $this->itemsCPMK($exactMatch);
                    $mappedResults = $this->mapCPMK(collect([$exactMatch]));
                    $mappedData = $mappedResults[0] ?? null;
                    if ($mappedData) {
                        $this->cpmk_sub_items_array[] = [
                            'scpmk' => $mappedData['scpmk'] ?? [],
                            'ref' => $mappedData['ref'] ?? [],
                            'cpl' => $mappedData['cpl'] ?? [],
                        ];
                    }
                    $this->cpmkResults = [];
                } else {
                    $this->cpmkNameSearch = '';
                    $this->cpmk_id_array[] = $exactMatch->id;
                    $this->cpmk_items_array[] = $this->itemsCPMK($exactMatch);
                }
                $mappedResults = $this->mapCPMK(collect([$exactMatch]));
                $this->pushToCPMKItems($mappedResults);
                $this->cpmkResults = $this->getCPMKbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->cpmkResults = $this->getCPMKbyUser();
            } else {
                $this->cpmkResults = $this->mapCPMK(
                    $query->orderBy('cpmks.id', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getCPMKbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->cpmkQuery();

        if (! $prodiId) {
            $defaultCPMK = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapCPMKSearch($defaultCPMK)
                : $this->mapCPMK($defaultCPMK);
        }

        $mainResults = $query
            ->whereHas('rps.mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->cpmkQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')->with(['scpmks'])
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapCPMKSearch($mainResults)
            : $this->mapCPMK($mainResults);
    }

    public function fetchCPMK($query = '', $mode = 'single')
    {
        $this->modeCPMK = $mode;
        if (empty($query) || $this->cpmk_id) {
            $this->cpmkResults = $this->getCPMKbyUser();
        }

    }

    public function selectCPMK($id, $cpmkName)
    {
        $this->cpmk_id = $id;
        $this->cpmkNameSearch = $cpmkName;
        $this->cpmkResults = $this->getCPMKbyUser();

        $data = $this->cpmkQuery()->find($id);
        if ($data) {
            $this->cpmk_items = $this->itemsCPMK($data);
            $mappedResults = $this->mapCPMK(collect([$data]));
            $this->pushToCPMKItems($mappedResults);
        }

        if (method_exists($this, 'fetchCPMK')) {
            $this->fetchCPMK('');
        }

        $this->resetErrorBag(['cpmk_id', 'cpmkNameSearch']);
    }

    public function selectCPMKArray($id)
    {
        $data = $this->cpmkQuery()->find($id);
        if ($data && ! in_array($id, $this->cpmk_id_array)) {
            $this->cpmk_id_array[] = $id;
            $this->cpmk_items_array[] = $this->itemsCPMK($data);

            $mappedResults = $this->mapCPMK(collect([$data]));
            $this->pushToCPMKItems($mappedResults);
        }
    }

    public function resetCPMKInput()
    {
        $this->reset(['cpmk_id', 'cpmk_items', 'cpmkNameSearch']);
        $this->cpmkResults = $this->getCPMKbyUser();
    }

    public function resetCPMKArray()
    {
        $this->cpmk_id_array = [];
        $this->cpmk_items_array = [];
        $this->cpmk_sub_items_array = [];
        $this->cpmkNameSearch = '';
    }
}
