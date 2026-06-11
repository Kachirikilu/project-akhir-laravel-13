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

        // normalize spasi
        $target = preg_replace('/\s+/', ' ', $target);
        $search = preg_replace('/\s+/', ' ', $search);

        // 🔥 normalize untuk match fleksibel
        $targetClean = preg_replace('/[^a-z0-9]/', '', $target);
        $searchClean = preg_replace('/[^a-z0-9]/', '', $search);

        // 1. exact match
        if ($targetClean === $searchClean) {
            return true;
        }

        // 2. substring anywhere (INI YANG PALING PENTING)
        if (str_contains($targetClean, $searchClean)) {
            return true;
        }

        // 3. word-level contains (lebih aman dari typo kecil)
        $targetWords = explode(' ', $target);
        $searchWords = explode(' ', $search);

        foreach ($searchWords as $word) {
            $found = false;

            foreach ($targetWords as $tWord) {

                $tClean = preg_replace('/[^a-z0-9]/', '', $tWord);
                $wClean = preg_replace('/[^a-z0-9]/', '', $word);

                if ($wClean !== '' && str_contains($tClean, $wClean)) {
                    $found = true;
                    break;
                }
            }

            if (! $found) {
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

        if (str_contains($target, $search)) {
            return true;
        }

        $targetWords = explode(' ', $target);
        $searchWords = explode(' ', $search);

        foreach ($searchWords as $sWord) {

            foreach ($targetWords as $tWord) {
                if (strlen($sWord) < 3 || strlen($tWord) < 3) {
                    continue;
                }
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

    protected function matchMetode(?string $target, ?string $search): bool
    {
        $target = strtolower(trim($target ?? ''));
        $search = strtolower(trim($search ?? ''));

        if ($target === '' || $search === '') {
            return false;
        }

        // 1. Ambil data alias dari ENV
        $utsFields = array_map('trim', explode(',', strtolower(env('UTS_FIELDS', 'UTS,EVALUASI AWAL'))));
        $uasFields = array_map('trim', explode(',', strtolower(env('UAS_FIELDS', 'UAS,EVALUASI AKHIR,LAPORAN AKHIR,HASIL PROYEK,HASIL PROJEK'))));

        // Normalize target dan search spasi agar rapi
        $targetNormalized = preg_replace('/\s+/', ' ', $target);
        $searchNormalized = preg_replace('/\s+/', ' ', $search);

        // 🔥 Bersihkan karakter non-alphanumeric untuk pencarian kata kunci 'uts' atau 'uas' yang fleksibel
        $searchClean = preg_replace('/[^a-z0-9\s]/', '', $searchNormalized);
        $searchWords = explode(' ', $searchClean);

        // 2. LOGIKA INTERSEPTOR DENGAN KATA TAMBAHAN (Menggunakan Cek Word/Kata)
        if (in_array('uts', $searchWords)) {
            // Jika input mengandung kata 'uts' (misal: "metode uts", "uts rps"), cocokkan ke $utsFields
            foreach ($utsFields as $field) {
                if (str_contains($targetNormalized, $field) || str_contains(preg_replace('/[^a-z0-9]/', '', $targetNormalized), preg_replace('/[^a-z0-9]/', '', $field))) {
                    return true;
                }
            }
        }

        if (in_array('uas', $searchWords)) {
            // Jika input mengandung kata 'uas' (misal: "metode uas baru"), cocokkan ke $uasFields
            foreach ($uasFields as $field) {
                if (str_contains($targetNormalized, $field) || str_contains(preg_replace('/[^a-z0-9]/', '', $targetNormalized), preg_replace('/[^a-z0-9]/', '', $field))) {
                    return true;
                }
            }
        }

        // 3. JALUR PENCARIAN REGULER (Jika tidak masuk kondisi interseptor di atas atau mencari kata lain)
        $targetClean = preg_replace('/[^a-z0-9]/', '', $targetNormalized);
        $searchCleanReguler = preg_replace('/[^a-z0-9]/', '', $searchNormalized);

        // exact match
        if ($targetClean === $searchCleanReguler) {
            return true;
        }

        // substring anywhere
        if (str_contains($targetClean, $searchCleanReguler)) {
            return true;
        }

        // word-level contains
        $targetWords = explode(' ', $targetNormalized);
        $searchWordsReguler = explode(' ', $searchNormalized);

        foreach ($searchWordsReguler as $word) {
            $found = false;

            foreach ($targetWords as $tWord) {
                $tClean = preg_replace('/[^a-z0-9]/', '', $tWord);
                $wClean = preg_replace('/[^a-z0-9]/', '', $word);

                if ($wClean !== '' && str_contains($tClean, $wClean)) {
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                return false;
            }
        }

        return true;
    }

    protected function matchCount(
        mixed $value,
        string $search,
        array $keywords = []
    ): bool {
        $search = strtolower($search);

        preg_match('/\d+/', $search, $m);
        $number = $m[0] ?? null;

        if ($number === null) {
            return false;
        }

        $cleanSearch = preg_replace('/[^a-z0-9]/', '', $search);
        foreach ($keywords as $keyword) {
            $cleanKeyword = preg_replace('/[^a-z0-9]/', '', strtolower($keyword));
            // dd($cleanSearch, $cleanKeyword);
            if (str_contains($cleanSearch, $cleanKeyword)) {
                return (int) $value === (int) $number;
            }
        }

        return false;
    }

    protected function matchOnlyCount(
        mixed $value,
        string $search,
        array $keywords = []
    ): bool {
        $search = strtolower(trim($search));
        preg_match('/\d+/', $search, $m);
        $number = $m[0] ?? null;

        if ($number === null) {
            return false;
        }

        if (preg_match('/^\d+$/', $search)) {
            return str_starts_with((string) $value, (string) $number);
        }

        $cleanSearch = preg_replace('/[^a-z0-9]/', '', $search);
        foreach ($keywords as $keyword) {
            $cleanKeyword = preg_replace(
                '/[^a-z0-9]/',
                '',
                strtolower($keyword)
            );
            if (
                str_contains($cleanSearch, $cleanKeyword)
            ) {
                return str_starts_with((string) $value, (string) $number);
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
        if (preg_match('/^\d{4}\/\d{4}$/', $search)) {
            return $akademik === $search;
        }
        if (! preg_match('/\d{4}/', $search, $sm)) {
            return false;
        }
        $queryYear = $sm[0];
        if (str_starts_with($search, '/')) {
            return $queryYear === $tahun2;
        }
        if (str_contains($search, '/')) {
            return $queryYear === $tahun1;
        }
        return $queryYear === $tahun1;
    }

    protected function matchDateField($date, string $search, array $keywords): bool
    {
        if ($this->matchDate($date, $search)) {
            return true;
        }

        foreach ($keywords as $keyword) {
            if ($this->containsStrict($keyword, $search)) {
                return $this->matchDate($date, $search);
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

            // Mapping nama bulan ke nomor bulan
            $monthNameMap = [
                'jan' => 1, 'januari' => 1, 'january' => 1,
                'feb' => 2, 'februari' => 2, 'february' => 2,
                'mar' => 3, 'maret' => 3, 'march' => 3,
                'apr' => 4, 'april' => 4,
                'mei' => 5, 'may' => 5,
                'jun' => 6, 'juni' => 6, 'june' => 6,
                'jul' => 7, 'juli' => 7, 'july' => 7,
                'agu' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
                'sep' => 9, 'september' => 9,
                'okt' => 10, 'oktober' => 10, 'oct' => 10, 'october' => 10,
                'nov' => 11, 'november' => 11,
                'des' => 12, 'desember' => 12, 'dec' => 12, 'december' => 12,
            ];

            // Cek apakah input mengandung nama bulan dan validasi month cocok
            $searchMonthNum = null;
            foreach ($monthNameMap as $monthName => $monthNum) {
                if (str_contains($search, $monthName)) {
                    $searchMonthNum = $monthNum;
                    break;
                }
            }

            // Jika ada nama bulan, validasi bulan data harus cocok + salah satu day/year cocok
            if ($searchMonthNum !== null) {
                if ($month !== $searchMonthNum) {
                    return false;
                }

                foreach ($nums as $n) {
                    if ($n === $day || $n === $year) {
                        return true;
                    }
                }

                return false;
            }

            // Jika input punya 3+ angka, cek ketiganya harus match (format numeric ketat)
            if (count($nums) >= 3) {
                return in_array($day, $nums)
                    && in_array($month, $nums)
                    && in_array($year, $nums);
            }

            // Jika < 3 angka, cek lenient (salah satu cocok)
            foreach ($nums as $n) {
                if (
                    $n === $day ||
                    $n === $month ||
                    $n === $year
                ) {
                    return true;
                }
            }

            return false;

        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function matchNilaiAkhir(
        mixed $nilai,
        string $search
    ): bool {
        if (
            ! preg_match(
                '/nilai\s*(>=|<=|>|<|=)?\s*([\d.]+)/i',
                $search,
                $matches
            )
        ) {
            return false;
        }

        return $this->compareNumber(
            (float) $nilai,
            ($matches[1] ?? '=').($matches[2] ?? '')
        );

    }

    protected function matchNilaiIndex(
        mixed $index,
        string $search
    ): bool {
        if (
            ! preg_match(
                '/(index|indeks|ip)\s*(>=|<=|>|<|=)?\s*([\d.]+)/i',
                $search,
                $matches
            )
        ) {
            return false;
        }

        return $this->compareNumber(
            (float) $index,
            ($matches[2] ?? '=').($matches[3] ?? '')

        );

    }

    protected function matchNilaiHuruf(
        ?string $huruf,
        string $search
    ): bool {

        $huruf = strtoupper(trim($huruf ?? ''));
        $search = strtolower(trim($search));

        if (! str_starts_with($search, 'nilai ')) {
            return false;
        }

        $targetHuruf = strtoupper(
            trim(substr($search, 6))
        );

        return $huruf === $targetHuruf;
    }

    protected function detectSearchMode(string $search): ?string
    {
        $search = strtolower(trim($search));

        if (preg_match('/\b(id)\b/i', $search)) {
            return 'id';
        }

        if (preg_match('/\b(no|nomor|number)\b/i', $search)) {
            return 'nomor';
        }

        if (
            str_contains($search, 'nilai ')
            || str_contains($search, 'nilai=')
            || str_contains($search, 'nilai>')
            || str_contains($search, 'nilai<')
        ) {
            return 'nilai';
        }

        if (
            str_contains($search, 'metode')
            || preg_match('/\b(uts|uas)\b/i', $search)
        ) {
            return 'metode';
        }

        if (
            str_contains($search, 'index ')
            || str_contains($search, 'ip ')
            || str_contains($search, 'indeks ')
        ) {
            return 'index';
        }

        if (
            str_contains($search, 'huruf ')
            || str_contains($search, 'grade ')
        ) {
            return 'huruf';
        }

        if (
            str_contains($search, 'sks') ||
            in_array($search, ['tm', 'tp', 'pr', 'pl', 'sm'])
        ) {
            return 'sks';
        }

        if (
            str_contains($search, 'sem') ||
            str_contains($search, 'semester') ||
            str_contains($search, 'ganjil') ||
            str_contains($search, 'genap')
        ) {
            return 'semester';
        }

        /*
        |------------------------------------------------
        | 🔥 PENTING: SCMPK HARUS DI ATAS CPMK
        |------------------------------------------------
        */
        if (
            str_contains($search, 'scpmk') ||
            str_contains($search, 'sub-cpmk') ||
            str_contains($search, 'subcpmk') ||
            str_contains($search, 'pertemuan')
        ) {
            return 'scpmk';
        }

        if (
            str_contains($search, 'cpmk') ||
            str_contains($search, 'cpl')
        ) {
            return 'cpmk';
        }

        if (
            str_contains($search, '%') ||
            str_contains($search, 'bobot') ||
            str_contains($search, 'persen')
        ) {
            return 'bobot';
        }

        if (
            $this->typoContains('aktif', $search) ||
            $this->typoContains('draf', $search) ||
            $this->typoContains('draft', $search) ||
            $this->typoContains('publish', $search) ||
            $this->typoContains('published', $search) ||
            $this->typoContains('konsep', $search) ||
            $this->typoContains('approved', $search)
        ) {
            return 'status';
        }

        if (
            $this->typoContains('wajib', $search) ||
            $this->typoContains('pilihan', $search) ||
            $this->typoContains('mandatory', $search) ||
            $this->typoContains('optional', $search) ||
            $this->typoContains('elective', $search) ||
            $this->typoContains('opsional', $search)
        ) {
            return 'wajib';
        }

        return null;
    }
}
