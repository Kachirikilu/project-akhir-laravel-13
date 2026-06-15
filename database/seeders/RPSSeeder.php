<?php

namespace Database\Seeders;

use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\Dosen;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RPSSeeder extends Seeder
{
    public function run(): void
    {
        $targetRps = 128;
        $batchSize = 256;

        // =========================
        // 1. MASTER CPL (LEBIH BANYAK & VARIATIF)
        // =========================
        $cpls = [];
        $cplTemplates = [
            // --- KELOMPOK 1: PENGETAHUAN & TEORI DASAR (KOGNITIF) ---
            'Mampu menerapkan konsep dasar ilmu %s dalam penyelesaian masalah rekayasa',
            'Mampu mengidentifikasi, memformulasi, dan menyelesaikan masalah rekayasa kompleks pada bidang %s',
            'Mampu menguasai prinsip-prinsip teoritis dan matematika terapan dalam sistem %s',
            'Mampu memodelkan dan mensimulasikan fenomena fisik atau matematis pada teknologi %s',
            'Mampu melakukan studi literatur dan menelaah perkembangan riset mutakhir di bidang %s',

            // --- KELOMPOK 2: PERANCANGAN & PENGEMBANGAN (DESAIN) ---
            'Mampu merancang sistem %s yang efektif dan efisien',
            'Mampu mengembangkan solusi inovatif berbasis %s dengan mempertimbangkan aspek keselamatan dan lingkungan',
            'Mampu merancang komponen, proses, atau sistem spesifik dari %s untuk memenuhi kebutuhan publik',
            'Mampu membuat prototipe dan arsitektur sistem %s berskala laboratorium maupun industri',
            'Mampu mengintegrasikan berbagai platform perangkat keras dan perangkat lunak dalam ekosistem %s',

            // --- KELOMPOK 3: ANALISIS, EVALUASI & OPTIMASI (ANALITIK) ---
            'Mampu menganalisis permasalahan %s secara kritis dan sistematis',
            'Mampu mengevaluasi kinerja sistem %s secara terukur menggunakan standar internasional',
            'Mampu mengoptimalkan performa sistem %s berbasis data dan algoritma cerdas',
            'Mampu melakukan pengujian, *troubleshooting*, dan validasi pada infrastruktur %s',
            'Mampu mendiagnosis kegagalan fungsi serta melakukan pemeliharaan (*maintenance*) pada sistem %s',

            // --- KELOMPOK 4: TEKNOLOGI TERKINI & METODOLOGI (TERAPAN) ---
            'Mampu memanfaatkan teknologi terkini dalam bidang %s',
            'Mampu mengimplementasikan metode %s dalam skala industri',
            'Mampu mengadopsi perangkat bantu modern berbasis kecerdasan buatan (*AI tools*) untuk rekayasa %s',
            'Mampu mengelola siklus hidup pengembangan (*lifecycle management*) pada teknologi %s',
            'Mampu menerapkan standar regulasi, keamanan siber, dan kode etik profesi pada implementasi %s',

            // --- KELOMPOK 5: KETERAMPILAN UMUM & MANAJERIAL (SOFT SKILLS) ---
            'Mampu bekerja dalam tim multidisiplin pada bidang %s',
            'Mampu berkomunikasi secara profesional dalam konteks %s',
            'Mampu memimpin dan mengelola proyek implementasi %s dengan prinsip manajemen modern',
            'Mampu mengambil keputusan taktis dan strategis berdasarkan analisis teknis di bidang %s',
            'Mampu melakukan pembelajaran sepanjang hayat untuk beradaptasi dengan disrupsi teknologi %s',
        ];

        $bidangs = [
            // Rumpun Utama & Konsentrasi Elektro
            'teknik elektro',
            'sistem tenaga listrik',
            'elektronika terapan',
            'telekomunikasi dan sinyal',
            'kendali otomatis',
            'instrumentasi medik dan industri',
            'energi terbarukan',
            'instalasi listrik industri',

            // Rumpun Komputer, Informatika & Digital
            'informatika',
            'embedded system',
            'jaringan komputer',
            'internet of things (IoT)',
            'kecerdasan buatan dan robotika',
            'pemrosesan sinyal digital',
            'keamanan siber dan jaringan',
            'komputasi awan (*cloud computing*)',
            'sistem siber-fisik (*cyber-physical systems*)',
        ];

        // generate kombinasi lebih besar
        $kombinasi = [];

        foreach ($cplTemplates as $template) {
            foreach ($bidangs as $bidang) {
                $kombinasi[] = sprintf($template, $bidang);
            }
        }

        shuffle($kombinasi);

        $totalCPL = min(1024, count($kombinasi));
        $this->command->info("Generating $totalCPL CPL records...");
        for ($i = 1; $i <= $totalCPL; $i++) {
            $cpls[] = CPL::updateOrCreate(
                [
                    'kode_cpl' => sprintf('CPL%02d', $i),
                ],
                [
                    'deskripsi' => $kombinasi[$i - 1],
                    'level_cpl' => fake()->randomElement([1, 1, 1, 2, 2, 3, 4]),
                ]
            );
        }

        $this->attachCplsToProdi($cpls);

        // =========================
        // DATA AWAL
        // =========================
        $mks = MataKuliah::take(64)->get();

        $tahunAkademik = [
            '2011/2012', '2012/2013', '2013/2014',
            '2014/2015', '2015/2016', '2016/2017',
            '2017/2018', '2018/2019', '2019/2020',
            '2020/2021', '2021/2022', '2022/2023',
            '2023/2024', '2024/2025', '2025/2026',
        ];

        $rpsCreated = 0;

        // 🔥 pindahkan ke luar closure (INI PENTING)

        $this->command->info("Seeding $targetRps RPS records in batches of $batchSize...");

        $mks = MataKuliah::with('prodis.cpls')
            ->get()
            ->filter(function ($mk) {
                return $mk->prodis
                    ->flatMap(fn ($prodi) => $prodi->cpls)
                    ->isNotEmpty();
            })
            ->values();

        if ($mks->isEmpty()) {
            $this->command->warn('Tidak ada MataKuliah yang memiliki CPL pada prodi. Seeder RPS dilewati.');

            return;
        }

        while ($rpsCreated < $targetRps) {

            DB::transaction(function () use (
                &$rpsCreated,
                $batchSize,
                $targetRps,
                $mks,
                $tahunAkademik,
            ) {

                $limit = min($batchSize, $targetRps - $rpsCreated);

                for ($i = 0; $i < $limit; $i++) {

                    if ($mks->isEmpty()) {
                        break;
                    }

                    $mk = $mks->random();
                    $waktu = now()->subYears(rand(0, 3));

                    $mkProdiIds = $mk->prodis
                        ->pluck('id')
                        ->unique();

                    $availableCpls = CPL::with('prodis')
                        ->whereHas('prodis', function ($q) use ($mkProdiIds) {
                            $q->whereIn('prodis.id', $mkProdiIds);
                        })
                        ->get()
                        ->filter(function ($cpl) use ($mkProdiIds) {

                            $cplProdiIds = $cpl->prodis
                                ->pluck('id')
                                ->unique();

                            return $cplProdiIds
                                ->intersect($mkProdiIds)
                                ->isNotEmpty();
                        })
                        ->values();

                    if ($availableCpls->count() < 1) {
                        continue;
                    }

                    $rps = RPS::create([
                        'mk_id' => $mk->id,
                        'deskripsi' => "RPS {$mk->nama_mk}",
                        'akademik' => $tahunAkademik[array_rand($tahunAkademik)],
                        'is_draf' => rand(0, 1),
                        'revisi' => $waktu,
                    ]);

                    // =========================
                    // REFERENSI (3–6)
                    // =========================
                    $refIds = [];

                    $penerbits = [
                        'IEEE Press', 'Springer', 'Elsevier',
                        'McGraw-Hill', 'Pearson', 'UNSRI Press',
                    ];

                    $penulisList = [
                        'J. Smith', 'A. Kumar', 'Budi Santoso',
                        'Siti Rahma', 'Michael Johnson', 'Ahmad Fauzi',
                    ];

                    $jumlahRef = rand(3, 6);

                    for ($r = 1; $r <= $jumlahRef; $r++) {

                        $ref = Referensi::create([
                            'kode_ref' => $this->generateUniqueKode(Referensi::class, 'kode_ref'),
                            'judul' => "Studi {$mk->nama_mk} dan Aplikasinya",
                            'penulis' => $penulisList[array_rand($penulisList)],
                            'tahun' => rand(2000, 2026),
                            'penerbit' => $penerbits[array_rand($penerbits)],
                        ]);

                        $rps->refs()->attach($ref->id);
                        $refIds[] = $ref->id;
                    }

                    // =========================
                    // ISI RPS
                    // =========================
                    $this->seedRealisticRPS(
                        $rps,
                        $availableCpls,
                        $mk,
                        $waktu,
                        $refIds
                    );

                    $rpsCreated++;
                }
            });

            $this->command->info("Created $rpsCreated/$targetRps RPS records...");

            // jeda kecil
            usleep(200000);
        }

        $this->command->info('RPSSeeder finished successfully.');
    }

    private function getMkProdiIds(MataKuliah $mk): array
    {
        return $mk->prodis
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    public function attachCplsToProdi(array $cpls): void
    {
        $prodis = Prodi::with('dp_rel.fk_rel')->get();

        if ($prodis->isEmpty()) {
            return;
        }

        foreach ($cpls as $cpl) {

            switch ((int) $cpl->level_cpl) {

                // ======================
                // LEVEL 1
                // 1 PRODI SAJA
                // ======================
                case 1:

                    $prodi = $prodis->random();

                    $cpl->prodis()->syncWithoutDetaching([
                        $prodi->id => [
                            'sort_order' => 0,
                        ],
                    ]);

                    break;

                    // ======================
                    // LEVEL 2
                    // SATU DEPARTEMEN
                    // ======================
                case 2:

                    $prodiAwal = $prodis->random();

                    $targetProdis = $prodis
                        ->where(
                            'dp_id',
                            $prodiAwal->dp_id
                        );

                    $attachIds = $targetProdis
                        ->pluck('id')
                        ->all();

                    // $cpl->prodis()->syncWithoutDetaching($attachIds);
                    $cpl->prodis()->detach();
                    $cpl->prodis()->attach($attachIds);

                    break;

                    // ======================
                    // LEVEL 3
                    // SATU FAKULTAS
                    // ======================
                case 3:

                    $prodiAwal = $prodis->random();

                    $fakultasId =
                        $prodiAwal->dp_rel->fk_id;

                    $attachIds = $prodis
                        ->filter(function ($prodi) use ($fakultasId) {
                            return
                                $prodi->dp_rel->fk_id
                                == $fakultasId;
                        })
                        ->pluck('id')
                        ->all();

                    $cpl->prodis()->syncWithoutDetaching($attachIds);

                    break;

                    // ======================
                    // LEVEL 4
                    // SEMUA PRODI
                    // ======================
                case 4:

                    $cpl->prodis()->syncWithoutDetaching(
                        $prodis->pluck('id')->all()
                    );

                    break;
            }
        }
    }

    private function generateKode($prefixMin = 3, $prefixMax = 4, $numMin = 2, $numMax = 6)
    {
        $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, rand($prefixMin, $prefixMax)));

        $min = pow(10, $numMin - 1);
        $max = pow(10, $numMax) - 1;

        $numbers = rand($min, $max);

        return $letters.$numbers;
    }

    private function generateUniqueKode($model, $column)
    {
        do {
            $kode = $this->generateKode();
        } while ($model::where($column, $kode)->exists());

        return $kode;
    }

    private function seedRealisticRPS(
        $rps,
        $availableCpls,
        $mk,
        $waktu,
        $refIds
    ) {
        // =========================
        // CPMK (2–4)
        // =========================
        $jumlahCpmk = rand(2, 4);
        $cpmks = [];

        for ($i = 1; $i <= $jumlahCpmk; $i++) {

            // 🔥 RANDOM: sebagian CPMK tanpa deskripsi
            $deskripsi = rand(0, 1)
                ? "Kemampuan ke-$i {$mk->nama_mk}"
                : null;

            $cpmk = CPMK::create([
                'kode_cpmk' => $this->generateUniqueKode(CPMK::class, 'kode_cpmk'),
                'deskripsi' => $deskripsi,
                'created_at' => $waktu,
            ]);

            $rps->cpmks()->attach($cpmk->id, ['sort_order' => $i]);

            // CPL 1–2
            $randomCpls = $availableCpls
                ->pluck('id')
                ->shuffle()
                ->take(
                    min(
                        rand(1, 2),
                        $availableCpls->count()
                    )
                );

            foreach (collect($randomCpls)->values() as $idx => $cplId) {
                $cpmk->cpls()->attach($cplId, ['sort_order' => $idx]);
            }

            $cpmks[] = $cpmk;
        }

        // =========================
        // TIPE 14 / 15 / 16
        // =========================
        $tipe = collect([14, 15, 16])->random();

        $subs = [];
        $totalBobot = 0;

        // simpan mapping CPMK → jumlah SCPMK
        $cpmkAssignments = [];

        // -----------------------------------
        // 1. Pastikan semua CPMK kebagian dulu
        // -----------------------------------
        foreach ($cpmks as $index => $cpmk) {

            $i = $index + 1;

            if ($i > $tipe) {
                break;
            }

            $isUTS = ($tipe >= 15 && $i == 8);
            $isUAS = ($tipe == 16 && $i == 16);

            $metode = $isUTS
                ? 'UTS'
                : ($isUAS ? 'UAS' : 'Teori');

            $bobot = rand(3, 10);

            $sub = SubCPMK::create([
                'kode_scpmk' => $this->generateUniqueKode(SubCPMK::class, 'kode_scpmk'),
                'deskripsi' => "Pertemuan $i",
                'materi' => "Materi $i",
                'metodologi' => 'Problem Based Learning',
                'indikator' => 'Pemahaman',
                'metode' => $metode,
                'bobot' => $bobot,
                'created_at' => $waktu,
            ]);

            $sub->cpmks()->attach($cpmk->id, [
                'sort_order' => $i,
            ]);

            $subs[] = $sub;
            $totalBobot += $bobot;
        }

        // -----------------------------------
        // 2. Sisanya random
        // -----------------------------------
        for ($i = count($subs) + 1; $i <= $tipe; $i++) {

            $isUTS = ($tipe >= 15 && $i == 8);
            $isUAS = ($tipe == 16 && $i == 16);

            $metode = $isUTS
                ? 'UTS'
                : ($isUAS ? 'UAS' : 'Teori');

            $bobot = rand(3, 10);

            $sub = SubCPMK::create([
                'kode_scpmk' => $this->generateUniqueKode(SubCPMK::class, 'kode_scpmk'),
                'deskripsi' => "Pertemuan $i",
                'materi' => "Materi $i",
                'metodologi' => 'Problem Based Learning',
                'indikator' => 'Pemahaman',
                'metode' => $metode,
                'bobot' => $bobot,
                'created_at' => $waktu,
            ]);

            $sub->cpmks()->attach(
                collect($cpmks)->random()->id,
                ['sort_order' => $i]
            );

            $subs[] = $sub;
            $totalBobot += $bobot;
        }

        // =========================
        // SUB-CPMK ↔ REFERENSI (MINIMAL 1)
        // =========================
        foreach ($subs as $sub) {
            $selectedRefs = collect($refIds)->random(min(rand(1, 3), count($refIds)));
            foreach ($selectedRefs as $refId) {
                $sub->refs()->attach($refId);
            }
        }

        // =========================
        // UTS / UAS
        // =========================
        if ($tipe == 14) {
            $rps->bobot_uts = rand(10, 30);
            $rps->bobot_uas = rand(10, 30);
        } elseif ($tipe == 15) {
            if (rand(0, 1)) {
                $rps->bobot_uts = rand(10, 30);
                $rps->bobot_uas = null;
            } else {
                $rps->bobot_uts = null;
                $rps->bobot_uas = rand(10, 30);
            }
        } else {
            $rps->bobot_uts = null;
            $rps->bobot_uas = null;
        }

        $totalBobot += ($rps->bobot_uts ?? 0);
        $totalBobot += ($rps->bobot_uas ?? 0);

        // =========================
        // VALIDASI
        // =========================
        if ($totalBobot < 70 || $totalBobot > 200) {

            foreach ($subs as $sub) {
                $sub->delete();
            }
            foreach ($cpmks as $cpmk) {
                $cpmk->delete();
            }

            return $this->seedRealisticRPS(
                $rps,
                $availableCpls,
                $mk,
                $waktu,
                $refIds
            );
        }

        $rps->save();

        // =========================
        // DOSEN
        // =========================
        $dosens = Dosen::inRandomOrder()->take(rand(1, 2))->get();

        foreach ($dosens as $i => $dsn) {
            $rps->dosens()->attach($dsn->id, [
                'peran' => $i == 0 ? 'Koordinator' : 'Pengajar',
                'is_ketua' => $i == 0,
            ]);
        }

        // =========================
        // DOSEN ↔ SCPMK
        // =========================
        if ($dosens->count() == 2 && rand(0, 1)) {

            foreach ($subs as $i => $sub) {

                $dsn = $i < 8 ? $dosens[0] : $dosens[1];

                DB::table('dosen_pivot_scpmk')->insert([
                    'rps_id' => $rps->id,
                    'dosen_id' => $dsn->id,
                    'scpmk_id' => $sub->id,
                    'sort_order' => $i + 1,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
