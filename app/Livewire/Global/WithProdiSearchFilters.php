<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithProdiSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $prSearchQuery = '';

    public $prSearchResults = [];

    public $modePr = '';

    public $pr_id;

    public $pr_name;

    public $pr_items = [];

    public $prNameSearch = '';

    public $prResults = [];

    public $selectedPrId = null;

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

        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->pr_name)) {
            $this->prSearchResults = $this->mapPr(
                $this->prQuery()->searchProdi($search)->limit(12)->get()
                // $this->searchOutputPr($this->prQuery(), $search, 12)
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

        $this->havePrParent($query);

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
        // $results = $this->searchOutputPr($query, $value, 12);
        $this->prResults = $this->mapPr($results);

        // 3. Pencocokan "Exact Match" yang Diperluas (Leveling)
        $matches = $results->filter(function ($prodi) use ($input) {
            $namaProdi = str($prodi->prodi)->lower()->trim();
            $kodeProdi = str($prodi->kode)->lower()->trim();

            $kodeDepartemen = $kodeProdi;
            $kodeFakultas = $kodeProdi;

            if (property_exists($this, 'mkType')) {
                if ($this->mkType >= 2) {
                    $kodeDepartemen = str($prodi->dp_rel?->kode ?? '')->lower()->trim();
                }
                if ($this->mkType >= 3) {
                    $kodeFakultas = str($prodi->dp_rel?->fk_rel?->kode ?? '')->lower()->trim();
                }
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
                $this->prNameSearch = '';
                foreach ($matches as $match) {
                    if (! in_array($match->id, $this->pr_id_array)) {
                        $this->pr_id_array[] = $match->id;
                        $this->pr_items_array[] = $this->itemsPr($match);
                    }
                }
                $this->pr_id_array = collect($this->pr_id_array)
                    ->unique()
                    ->values()
                    ->all();
                $this->pr_items_array = collect($this->pr_items_array)
                    ->unique('id')
                    ->values()
                    ->all();
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

        $this->havePrParent($query);

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

        $this->havePrChild();

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

        $this->havePrChild();
    }

    public function resetPrInput()
    {
        $this->pr_id = null;
        $this->pr_items = null;
        $this->prNameSearch = '';

        $this->havePrChild();

        $this->updatedPrNameSearch('');
        $this->resetErrorBag(['pr_id', 'prNameSearch']);
    }

    public function resetPrArray()
    {
        $this->pr_id_array = [];
        $this->pr_items_array = [];
        $this->prNameSearch = '';

        $this->havePrChild();
    }

    public function havePrChild()
    {
        if (property_exists($this, 'showKelasModal') && property_exists($this, 'rps_id')) {
            if ($this->showKelasModal == true) {
                $this->resetRPSArray();
            }
        }
    }

    public function havePrParent($query)
    {
        if (property_exists($this, 'showMKModal') && property_exists($this, 'mkType')) {
            if ($this->showMKModal == true && $this->mkType == 2 && filled($this->dp_id)) {
                $query->where('dp_id', $this->dp_id);
            } elseif ($this->showMKModal == true && $this->mkType == 3 && filled($this->fk_id)) {
                $query->whereHas('dp_rel', fn ($q) => $q->where('fk_id', $this->fk_id));
            }
            // else {
            //     $query->whereHas('dp_rel', fn ($q) => $q->where('fk_id', $fakultasId));
            // }
        }

        return $query;
    }

    public function searchOutputPr($queryPr, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allPr = (clone $queryPr)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allPr = $allPr->filter(function ($pr) use ($searchLower, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $pr->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE SEARCH MATCHING LOGIC
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $pr->kode,
                        $searchLower
                    );
                    $matchKodeDp = $this->matchKode(
                        $pr->kode_dp,
                        $searchLower
                    );
                    $matchKodeFk = $this->matchKode(
                        $pr->kode_fk,
                        $searchLower
                    );

                    $basePr = [
                        $pr->prodi,
                        $pr->prodi_pr,
                        $pr->prodi_strata,
                    ];
                    $matchPr = false;
                    foreach ($basePr as $pro) {
                        $candidates = [
                            $pro.' '.$pr->kode_pr,
                            $pro.' ('.$pr->kode_pr.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchPr = true;
                                break 2;
                            }
                        }
                    }

                    $baseDp = [
                        $pr->departemen,
                        $pr->departemen_dp,
                    ];
                    $matchDp = false;
                    foreach ($baseDp as $dp) {
                        $candidates = [
                            $dp.' '.$pr->kode_dp,
                            $dp.' ('.$pr->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchDp = true;
                                break 2;
                            }
                        }
                    }

                    $baseFk = [
                        $pr->fakultas,
                        $pr->fakultas_fk,
                    ];
                    $matchFk = false;
                    foreach ($baseFk as $fk) {
                        $candidates = [
                            $fk.' '.$pr->kode_fk,
                            $fk.' ('.$pr->kode_fk.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchFk = true;
                                break 2;
                            }
                        }
                    }

                    $matchNilaiAkhir = $this->matchNilaiAkhir(
                        $pr->rekap_pr ?? $pr->rekap_dp ?? $pr->rekap_fk ?? 0,
                        $searchLower
                    );

                    $matchNilaiIndex = $this->matchNilaiIndex(
                        $pr->index_pr ?? $pr->index_dp ?? $pr->index_fk ?? 0,
                        $searchLower
                    );

                    $matchNilaiHuruf = $this->matchNilaiHuruf(
                        $pr->akreditas_pr ?? $pr->akreditas_dp ?? $pr->akreditas_fk ?? 'E',
                        $searchLower
                    );

                    $matchCreatedAt = $this->matchDateField(
                        $pr->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $pr->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                    }

                    return
                        $matchID
                        || $matchKode
                        || $matchKodeDp
                        || $matchKodeFk

                        || $matchPr
                        || $matchDp
                        || $matchFk

                        || $matchNilaiAkhir
                        || $matchNilaiIndex
                        || $matchNilaiHuruf

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($pr) => [
                    $pr->kode_short ?? $pr->kode,
                    $pr->strata_s ?? null,
                ],
                'prodi', 'program_studi' => fn ($pr) => $pr->prodi,
                'departemen' => fn ($pr) => $pr->departemen,
                'fakultas' => fn ($pr) => $pr->fakultas,
                'strata' => fn ($pr) => $pr->strata,

                'rekap_pr',
                'rekap_dp',
                'rekap_fk',
                'index_pr',
                'index_dp',
                'index_fk',
                'akreditas_pr',
                'akreditas_dp',
                'akreditas_fk' => fn ($pr) => (float) ($pr->rekap_pr ?? $pr->rekap_dp ?? $pr->rekap_fk ?? 0),

                'created_at' => fn ($pr) => $pr->created_at,
                'updated_at' => fn ($pr) => $pr->updated_at,

                default => fn ($pr) => $pr->id,
            };

            $allPr = $sortDirection === 'asc'
                ? $allPr->sortBy($sortValue)
                : $allPr->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allPr->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allPr->forPage($currentPage, $perPage)->values(),
                $allPr->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryPr;
        }

        return $queryPr->paginate($perPage);
    }
}
