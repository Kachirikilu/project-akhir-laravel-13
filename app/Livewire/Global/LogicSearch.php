<?php

namespace App\Livewire\Global;

use Carbon\Carbon;

trait LogicSearch
{
    protected function matchID(
        mixed $id,
        string $search
    ): bool {

        if (
            ! preg_match(
                '/(?:id\s*(\d+)|(\d+)\s*id)/i',
                $search,
                $matches
            )
        ) {
            return false;
        }

        $targetId = (int) max(
            $matches[1] ?? 0,
            $matches[2] ?? 0
        );

        return (int) $id === $targetId;
    }

    protected function matchKode(
        ?string $kode,
        ?string $search
    ): bool {

        $kode = strtolower(trim($kode ?? ''));
        $search = strtolower(trim($search ?? ''));

        if ($kode === '' || $search === '') {
            return false;
        }

        $kodeClean = preg_replace('/[^a-z0-9]/', '', $kode);
        $searchClean = preg_replace('/[^a-z0-9]/', '', $search);

        if ($kodeClean === '' || $searchClean === '') {
            return false;
        }

        $triggers = ['kode', 'id', 'identitas', 'code'];

        $hasTrigger = false;

        foreach ($triggers as $t) {
            if (str_contains($search, $t)) {
                $hasTrigger = true;
                break;
            }
        }

        if ($hasTrigger) {

            $cleanSearch = str_replace($triggers, '', $search);
            $cleanSearch = preg_replace('/\s+/', '', $cleanSearch);

            return $cleanSearch !== ''
                && str_contains($kodeClean, $cleanSearch);
        }

        return str_contains($kodeClean, $searchClean);
    }

    protected function containsStrict(?string $target, ?string $search): bool
    {
        $target = strtolower(trim($target ?? ''));
        $search = strtolower(trim($search ?? ''));

        if ($target === '' || $search === '') {
            return false;
        }
        $target = preg_replace('/\s+/', ' ', $target);
        $search = preg_replace('/\s+/', ' ', $search);

        if ($target === $search) {
            return true;
        }
        if (str_starts_with($target, $search)) {
            return true;
        }
        $targetWords = explode(' ', $target);
        $searchWords = explode(' ', $search);

        foreach ($searchWords as $i => $word) {
            if (! isset($targetWords[$i])) {
                return false;
            }

            if ($targetWords[$i] !== $word) {
                return false;
            }
        }

        return true;
    }

    protected function typoContains(?string $target, ?string $search): bool
    {
        $target = strtolower(trim($target ?? ''));
        $search = strtolower(trim($search ?? ''));

        if ($target === '' || $search === '') {
            return false;
        }

        // 1. exact / contains
        if (str_contains($target, $search)) {
            return true;
        }

        $targetWords = explode(' ', $target);
        $searchWords = explode(' ', $search);

        foreach ($searchWords as $sWord) {

            foreach ($targetWords as $tWord) {

                // skip kata terlalu pendek biar gak noise
                if (strlen($sWord) < 3 || strlen($tWord) < 3) {
                    continue;
                }

                // fuzzy ringan saja
                if (levenshtein($sWord, $tWord) <= 1) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function matchNo(
        int|string|null $number,
        ?string $search
    ): bool {

        $search = strtolower(trim($search ?? ''));

        if (
            ! str_contains($search, 'no')
            && ! str_contains($search, 'nomor')
            && ! str_contains($search, 'number')
        ) {
            return false;
        }

        preg_match('/\d+/', $search, $matches);

        if (empty($matches[0])) {
            return false;
        }

        return (string) $number === $matches[0];
    }

    protected function matchCount(
        mixed $value,
        string $search,
        array $keywords = []
    ): bool {

        $number = preg_replace('/[^0-9]/', '', $search);

        if (! is_numeric($number)) {
            return false;
        }

        $search = strtolower($search);

        foreach ($keywords as $keyword) {

            if ($this->typoContains($keyword, $search)) {
                return (int) $value === (int) $number;
            }
        }

        return false;
    }

    protected function compareNumber(
        float|int $value,
        string $search
    ): bool {

        [
            'operator' => $operator,
            'number' => $number,
        ] = $this->parseNumericSearch($search);

        switch ($operator) {

            case '>':
                return $value > $number;

            case '<':
                return $value < $number;

            case '>=':
                return $value >= $number;

            case '<=':
                return $value <= $number;

            default:
                return abs($value - $number) < 0.01;
        }
    }

    protected function parseNumericSearch(
        string $search
    ): array {

        $operator = '=';

        if (
            preg_match(
                '/^\s*(>=|<=|>|<)/',
                $search,
                $matches
            )
        ) {
            $operator = $matches[1];
        }

        $number = preg_replace(
            '/[^0-9.]/',
            '',
            $search
        );

        return [
            'operator' => $operator,
            'number' => (float) $number,
        ];
    }

    protected function matchDraf(
        ?string $drafText,
        ?string $search
    ): bool {

        $drafText = strtolower(trim($drafText ?? ''));
        $search = strtolower(trim($search ?? ''));

        if (empty($drafText) || empty($search)) {
            return false;
        }

        $groups = [
            'aktif' => [
                'aktif',
                'active',
                'publish',
                'published',
                'siap',
                'final',
                'approved',
            ],

            'draf' => [
                'draf',
                'draft',
                'konsep',
                'sementara',
                'belum',
            ],
        ];

        foreach ($groups as $status => $keywords) {

            if (! str_contains($drafText, $status)) {
                continue;
            }

            foreach ($keywords as $keyword) {

                if ($this->typoContains($keyword, $search)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function matchSemesterJenis(?int $semester, ?string $search): bool
    {
        if ($semester === null || $search === null) {
            return false;
        }

        $search = strtolower(trim($search));

        if ($search === '') {
            return false;
        }

        $isGanjil = ($semester % 2) !== 0;
        $isGenap = ! $isGanjil;

        $keywords = preg_split('/[\s\-_,.]+/', $search);

        foreach ($keywords as $word) {
            $word = strtolower($word);
            if ($word === '') {
                continue;
            }

            if (str_starts_with($word, 'gan') || $word === 'ganjil' || $word === 'odd') {
                return $isGanjil;
            }
            if (str_starts_with($word, 'gen') || $word === 'genap' || $word === 'even') {
                return $isGenap;
            }
        }

        return false;
    }

    protected function matchWajib(
        ?string $wajibText,
        ?string $search
    ): bool {

        $wajibText = strtolower(trim($wajibText ?? ''));
        $search = strtolower(trim($search ?? ''));

        if (empty($wajibText) || empty($search)) {
            return false;
        }

        $groups = [
            'wajib' => [
                'wajib',
                'must',
                'mandatory',
                'inti',
                'core',
            ],

            'pilihan' => [
                'pilihan',
                'optional',
                'option',
                'elective',
                'opsional',
            ],
        ];

        foreach ($groups as $jenis => $keywords) {

            if (! str_contains($wajibText, $jenis)) {
                continue;
            }

            foreach ($keywords as $keyword) {

                if ($this->typoContains($keyword, $search)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function matchSKSText(
        ?string $sksText,
        ?string $search
    ): bool {

        $sksText = strtolower(trim($sksText ?? ''));
        $search = strtolower(trim($search ?? ''));

        if (empty($sksText) || empty($search)) {
            return false;
        }
        if (
            strlen($search) >= 3
            && $this->typoContains($sksText, $search)
        ) {
            return true;
        }

        $aliases = [
            'tm' => 'tatap muka',
            'tp' => 'tatap muka',
            'pr' => 'praktikum',
            'pl' => 'praktek lapangan',
            'sm' => 'simulasi',
            'sks tm' => 'tatap muka',
            'sks tp' => 'tatap muka',
            'sks pr' => 'praktikum',
            'sks pl' => 'praktek lapangan',
            'sks sm' => 'simulasi',
        ];

        $keywords = preg_split(
            '/[\s,._-]+/',
            $search
        );

        foreach ($keywords as $keyword) {
            if (
                isset($aliases[$keyword])
                && str_contains(
                    $sksText,
                    $aliases[$keyword]
                )
            ) {
                return true;
            }
        }

        return false;
    }

    protected function matchAkademik(?string $akademik, ?string $search): bool
    {
        $akademik = trim($akademik ?? '');
        $search = trim($search ?? '');

        if ($akademik === '' || $search === '') {
            return false;
        }
        if (! preg_match('/^(\d{4})\/(\d{4})$/', $akademik, $m)) {
            return false;
        }
        [$full, $tahun1, $tahun2] = $m;
        if (! preg_match('/\d{4}/', $search, $sm)) {
            return false;
        }

        $queryYear = $sm[0];
        if (str_starts_with($search, '/')) {
            return $queryYear === $tahun2;
        }
        if (str_contains($search, '/')) {
            return $queryYear === $tahun1 || $queryYear === $tahun2;
        }

        return $queryYear === $tahun1;
    }

    protected function matchDateField(
        $date,
        string $search,
        array $keywords
    ): bool {

        foreach ($keywords as $keyword) {

            if (
                $this->typoContains(
                    $keyword,
                    $search
                )
            ) {
                return $this->matchDate(
                    $date,
                    $search
                );
            }
        }

        return false;
    }

    protected function matchDate($date, string $search): bool
    {
        if (empty($date) || empty($search)) {
            return false;
        }

        try {
            $dt = Carbon::parse($date);
            $search = strtolower(trim($search));
            preg_match_all('/\d+/', $search, $m);
            $nums = $m[0] ?? [];

            if (empty($nums)) {
                return false;
            }

            $day = (int) $dt->day;
            $month = (int) $dt->month;
            $year = (int) $dt->year;
            $nums = array_map('intval', $nums);

            if (count($nums) >= 3) {

                return in_array($day, $nums)
                    && in_array($month, $nums)
                    && in_array($year, $nums);
            }

            foreach ($nums as $n) {

                if (
                    $n === $day ||
                    $n === $month ||
                    $n === $year
                ) {
                    return true;
                }
            }

            $monthNames = [
                1 => ['jan', 'januari', 'january'],
                2 => ['feb', 'februari', 'february'],
                3 => ['mar', 'maret', 'march'],
                4 => ['apr', 'april'],
                5 => ['mei', 'may'],
                6 => ['jun', 'juni', 'june'],
                7 => ['jul', 'juli', 'july'],
                8 => ['agu', 'agustus', 'aug', 'august'],
                9 => ['sep', 'september'],
                10 => ['okt', 'oktober', 'oct', 'october'],
                11 => ['nov', 'november'],
                12 => ['des', 'desember', 'dec', 'december'],
            ];

            foreach ($monthNames as $mNum => $aliases) {
                foreach ($aliases as $alias) {
                    if (str_contains($search, $alias) && $month === $mNum) {
                        return true;
                    }
                }
            }

            return false;

        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function detectSearchMode(string $search): ?string
    {
        $search = strtolower(trim($search));

        if (
            preg_match('/\b(id)\b/i', $search)
        ) {
            return 'id';
        }

        if (
            preg_match('/\b(no|nomor|number)\b/i', $search)
        ) {
            return 'nomor';
        }

        if (
            str_contains($search, 'sks')
            || in_array($search, ['tm', 'tp', 'pr', 'pl', 'sm'])
        ) {
            return 'sks';
        }

        if (
            str_contains($search, 'sem')
            || str_contains($search, 'semester')
            || str_contains($search, 'ganjil')
            || str_contains($search, 'genap')
        ) {
            return 'semester';
        }

        if (
            str_contains($search, 'cpmk')
            || str_contains($search, 'cpl')
        ) {
            return 'cpmk';
        }

        if (
            str_contains($search, 'scpmk')
            || str_contains($search, 'sub-cpmk')
            || str_contains($search, 'subcpmk')
            || str_contains($search, 'pertemuan')
        ) {
            return 'scpmk';
        }

        if (
            str_contains($search, '%')
            || str_contains($search, 'bobot')
            || str_contains($search, 'persen')
        ) {
            return 'bobot';
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */
        if (
            $this->typoContains('aktif', $search)
            || $this->typoContains('draf', $search)
            || $this->typoContains('draft', $search)
            || $this->typoContains('publish', $search)
            || $this->typoContains('published', $search)
            || $this->typoContains('konsep', $search)
            || $this->typoContains('approved', $search)
        ) {
            return 'status';
        }

        /*
        |--------------------------------------------------------------------------
        | WAJIB / PILIHAN
        |--------------------------------------------------------------------------
        */
        if (
            $this->typoContains('wajib', $search)
            || $this->typoContains('pilihan', $search)
            || $this->typoContains('mandatory', $search)
            || $this->typoContains('optional', $search)
            || $this->typoContains('elective', $search)
            || $this->typoContains('opsional', $search)
        ) {
            return 'wajib';
        }

        return null;
    }
}
