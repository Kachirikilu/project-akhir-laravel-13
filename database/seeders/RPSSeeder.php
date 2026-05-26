<?php

namespace Database\Seeders;

use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RPSSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // 1. MASTER CPL (LEBIH BANYAK & VARIATIF)
        // =========================
        $cpls = [];

        $cplTemplates = [
            'Mampu menerapkan konsep dasar ilmu %s dalam penyelesaian masalah rekayasa',
            'Mampu merancang sistem %s yang efektif dan efisien',
            'Mampu menganalisis permasalahan %s secara kritis dan sistematis',
            'Mampu mengembangkan solusi inovatif berbasis %s',
            'Mampu bekerja dalam tim multidisiplin pada bidang %s',
            'Mampu berkomunikasi secara profesional dalam konteks %s',
            'Mampu memanfaatkan teknologi terkini dalam bidang %s',
            'Mampu mengevaluasi kinerja sistem %s secara terukur',
            'Mampu mengimplementasikan metode %s dalam skala industri',
            'Mampu mengoptimalkan performa sistem %s berbasis data',
        ];

        $bidangs = [
            'teknik elektro',
            'sistem tenaga',
            'elektronika',
            'telekomunikasi',
            'informatika',
            'embedded system',
            'jaringan komputer',
            'kendali otomatis',
        ];

        // generate kombinasi lebih besar
        $kombinasi = [];

        foreach ($cplTemplates as $template) {
            foreach ($bidangs as $bidang) {
                $kombinasi[] = sprintf($template, $bidang);
            }
        }

        shuffle($kombinasi);

        // 🔥 BUKAN 24 LAGI, tapi minimal 64 CPL
        $totalCPL = min(300, count($kombinasi));
        $this->command->info("Generating $totalCPL CPL records...");
        for ($i = 1; $i <= $totalCPL; $i++) {
            $cpls[] = CPL::updateOrCreate([
                'kode_cpl' => sprintf('CPL%02d', $i),
            ], [
                'deskripsi' => $kombinasi[$i - 1],
            ]);
        }

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

        $targetRps = 256;
        $batchSize = 256;

        $rpsCreated = 0;

        // 🔥 pindahkan ke luar closure (INI PENTING)
        $cplUsage = [];

        $this->command->info("Seeding $targetRps RPS records in batches of $batchSize...");

        while ($rpsCreated < $targetRps) {

            DB::transaction(function () use (
                &$rpsCreated,
                $batchSize,
                $targetRps,
                $mks,
                $cpls,
                $tahunAkademik,
                &$cplUsage
            ) {

                $limit = min($batchSize, $targetRps - $rpsCreated);

                for ($i = 0; $i < $limit; $i++) {

                    $mk = $mks->random();
                    $waktu = now()->subYears(rand(0, 3));

                    $rps = RPS::create([
                        'mk_id' => $mk->id,
                        'deskripsi' => "RPS {$mk->nama_mk}",
                        'akademik' => $tahunAkademik[array_rand($tahunAkademik)],
                        'is_draf' => rand(0, 1),
                        'revisi' => $waktu,
                    ]);

                    // =========================
                    // RPS ↔ CPL (1–5 MERATA)
                    // =========================
                    $selectedCpls = collect($cpls)
                        ->sortBy(fn ($cpl) => $cplUsage[$cpl->id] ?? 0)
                        ->take(rand(1, 5));

                    foreach ($selectedCpls->values() as $idx => $cpl) {
                        $rps->cpls()->attach($cpl->id, [
                            'sort_order' => $idx,
                        ]);

                        $cplUsage[$cpl->id] = ($cplUsage[$cpl->id] ?? 0) + 1;
                    }

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
                    $this->seedRealisticRPS($rps, $cpls, $mk, $waktu, $refIds);

                    $rpsCreated++;
                }
            });

            $this->command->info("Created $rpsCreated/$targetRps RPS records...");

            // jeda kecil
            usleep(200000);
        }

        $this->command->info("RPSSeeder finished successfully.");
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

    private function seedRealisticRPS($rps, $cpls, $mk, $waktu, $refIds)
    {
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
            $randomCpls = collect($cpls)->pluck('id')->random(rand(1, 2));

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

        for ($i = 1; $i <= $tipe; $i++) {

            $isUTS = ($tipe >= 15 && $i == 8);
            $isUAS = ($tipe == 16 && $i == 16);

            $metode = $isUTS ? 'UTS' : ($isUAS ? 'UAS' : 'Teori');
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

            return $this->seedRealisticRPS($rps, $cpls, $mk, $waktu, $refIds);
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
