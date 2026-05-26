<?php

namespace App\Livewire\Global;

use App\Models\Akademik\SubCPMK;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithSubCPMKSearchFilters
{
    use WithPagination;

    public $scpmkSearchQuery = '';

    public $scpmkSearchResults = [];

    public $modeSCPMK = '';

    public $scpmk_id;

    public $scpmk_name = '';

    public $scpmk_items;

    public $scpmkNameSearch = '';

    public $scpmkResults = [];

    public $selectedSCPMKId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $scpmk_id_array = [];

    public $scpmk_items_array = [];

    public $scpmk_sub_items_array = [];

    private function mapSCPMK($collection)
    {
        return $collection->map(fn ($s) => [
            'id' => $s->id,
            'kode' => $s->kode,
            'deskripsi' => $s->deskripsi,
            'materi' => $s->materi,
            'metodologi' => $s->metodologi,
            'indikator' => $s->indikator,
            'metode' => $s->metode,
            'tugas' => $s->tugas,
            'w_tugas' => $s->w_tugas,
            'w_mandiri' => $s->w_mandiri,
            'waktu_tugas' => $s->waktu_tugas,
            'waktu_mandiri' => $s->waktu_mandiri,
            'bobot' => $s->bobot ?? 0,
            'bobot' => rtrim(rtrim(number_format($s->bobot ?? 0, 2, '.', ''), '0'), '.'),
            'bobot_text' => rtrim(rtrim(number_format($s->bobot ?? 0, 2, '.', ''), '0'), '.') . '% Bobot',
            'ref' => $this->mapRef($s->refs),
            'dosen' => $this->mapDosen($s->dosens)
        ])->toArray();
    }

    private function mapSCPMKSearch($collection)
    {
        return $collection->map(fn ($s) => [
            'id' => $s->id,
            'kode' => $s->kode,
            'deskripsi' => $s->deskripsi,
            'metode' => $s->metode,
            'bobot_text' => rtrim(rtrim(number_format($s->bobot ?? 0, 2, '.', ''), '0'), '.') . '% Bobot',
        ])->toArray();
    }

    private function scpmkQuery()
    {
        return SubCPMK::query()->with('cpmks.rps', 'cpmks', 'refs');
    }

    private function itemsSCPMK($s)
    {
        if (! $s) {
            return null;
        }

        return [
            'id' => $s->id,
            'kode' => $s->kode,
            'slot1' => $s->deskripsi,
        ];
    }

    private function pushToSCPMKItems($mappedResults)
    {
        $mappedData = $mappedResults[0] ?? null;

        if ($mappedData) {
            $this->scpmk_sub_items_array[] = [
                'scpmk' => [$mappedData]
            ];
        }
    }

    public function inputSCPMKFilter()
    {
        $search = trim($this->scpmkSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->scpmk_name) {
            $this->scpmkSearchResults = $this->mapSCPMKSearch(
                $this->scpmkQuery()->searchSCPMK($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->scpmk_name) {
            $this->scpmkSearchResults = $this->getSCPMKbyUser('search');
        } else {
            $this->scpmkSearchResults = [];
        }
    }

    public function resetSCPMKFilter()
    {
        $this->reset(['selectedSCPMKId', 'scpmkSearchQuery', 'scpmk_name', 'scpmk_items']);
        $this->resetPage();
    }

    public function selectSCPMKForFilter($id)
    {
        $data = $this->scpmkQuery()->find($id);

        if ($data) {
            $this->selectedSCPMKId = $id;
            $this->scpmk_name = $data->deskripsi;
            $this->scpmkSearchQuery = $data->deskripsi;
            $this->scpmk_items = $this->itemsSCPMK($data);
            $this->scpmkSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedSCPMKNameSearch($value)
    {
        $this->scpmk_id = null;
        $this->scpmk_items = null;
        $this->resetErrorBag(['scpmk_id', 'scpmkNameSearch']);

        $query = $this->scpmkQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchSCPMK($value)->limit(12)->get();
            $this->scpmkResults = $this->mapSCPMK($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($sc) use ($value, $normalizedValue) {
                $normalizedMkKode = str_replace(['-', ' '], '', strtolower($sc->kode));

                return strtolower($sc->deskripsi) === strtolower($value)
                    || $normalizedMkKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->scpmk_id = $exactMatch->id;
                $this->scpmk_items = $this->itemsSCPMK($exactMatch);
                $this->scpmkNameSearch = $exactMatch->deskripsi;
                $this->scpmkResults = [];
            }
            if ($exactMatch) {
                if ($this->modeSCPMK == 'single') {
                    $this->scpmkNameSearch = $exactMatch->deskripsi;
                    $this->scpmk_id = $exactMatch->id;
                    $this->scpmk_items = $this->itemsSCPMK($exactMatch);
                    $this->scpmkResults = [];
                } else {
                    $this->scpmkNameSearch = '';
                    $this->scpmk_id_array[] = $exactMatch->id;
                    $this->scpmk_items_array[] = $this->itemsSCPMK($exactMatch);
                    $mappedResults = $this->mapSCPMK(collect([$exactMatch]));
                }
                $mappedResults = $this->mapSCPMK(collect([$exactMatch]));
                $this->pushToSCPMKItems($mappedResults);
                $this->scpmkResults = $this->getSCPMKbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->scpmkResults = $this->getSCPMKbyUser();
            } else {
                $this->scpmkResults = $this->mapSCPMK(
                    $query->orderBy('sub_cpmks.deskripsi', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getSCPMKbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->scpmkQuery();

        if (! $prodiId) {
            $defaultSCPMK = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapSCPMKSearch($defaultSCPMK)
                : $this->mapSCPMK($defaultSCPMK);
        }

        $mainResults = $query
            ->whereHas('cpmks.rps.mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = SubCPMK::whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapSCPMKSearch($mainResults)
            : $this->mapSCPMK($mainResults);
    }

    public function fetchSCPMK($query = '', $mode = 'single')
    {
        $this->modeSCPMK = $mode;
        if (empty($query) || $this->scpmk_id) {
            $this->scpmkResults = $this->getSCPMKbyUser();
        }

    }

    public function selectSCPMK($id, $scpmkName)
    {
        $this->scpmk_id = $id;
        $this->scpmkNameSearch = $scpmkName;
        $this->scpmkResults = $this->getSCPMKbyUser();

        $data = $this->scpmkQuery()->find($id);
        if ($data) {
            $this->scpmk_items = $this->itemsSCPMK($data);
            $mappedResults = $this->mapSCPMK(collect([$data]));
            $this->pushToSCPMKItems($mappedResults);
        }

        if (method_exists($this, 'fetchSCPMK')) {
            $this->fetchSCPMK('');
        }

        $this->resetErrorBag(['scpmk_id', 'scpmkNameSearch']);
    }

    public function selectSCPMKArray($id)
    {
        $data = $this->scpmkQuery()->find($id);
        if ($data && ! in_array($id, $this->scpmk_id_array)) {
            $this->scpmk_id_array[] = $id;
            $this->scpmk_items_array[] = $this->itemsSCPMK($data);
            $mappedResults = $this->mapSCPMK(collect([$data]));
            $this->pushToSCPMKItems($mappedResults);
        }
    }

    public function resetSCPMKInput()
    {
        $this->reset(['scpmk_id', 'scpmk_items', 'scpmkNameSearch']);
        $this->scpmkResults = $this->getSCPMKbyUser();
    }

    public function resetSCPMKArray()
    {
        $this->scpmk_id_array = [];
        $this->scpmk_items_array = [];
        $this->scpmkNameSearch = '';
    }
}
