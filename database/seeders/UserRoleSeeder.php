<?php

namespace Database\Seeders;

use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\Membership;
use App\Models\Auth\Pendidikan;
use App\Models\Auth\Team;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Prodi;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $defaultPw = Hash::make('12345678');

        $totalUsers = 1000;
        $batchSize = 512;

        $prodiIds = Prodi::pluck('id')->toArray();
        if (empty($prodiIds)) {
            throw new \Exception('Tabel prodis kosong! Jalankan SilsilahSeeder terlebih dahulu!');
        }

        // --- 1. AKUN UTAMA (Diproses sekali) ---
        DB::transaction(function () use ($faker, $defaultPw, $prodiIds) {
            // Admin Utama
            $adminUser = User::create(['email' => 'muttaqien.wildan12@gmail.com', 'password' => $defaultPw]);
            $this->createAdminProfile($adminUser, 'Wildan Athif Muttaqien (Admin)', $faker, $prodiIds[0]);
            $this->createPersonalTeamForUser($adminUser, 'Wildan Athif Muttaqien (Admin)');

            // Dosen Utama
            $dosenUser = User::create(['email' => 'muttaqien.wildan13@gmail.com', 'password' => $defaultPw]);
            $this->createDosenProfile($dosenUser, 'Wildan Athif Muttaqien (Dosen)', $faker, $prodiIds[0]);
            $this->createPersonalTeamForUser($dosenUser, 'Wildan Athif Muttaqien (Dosen)');

            // Mahasiswa Utama
            $mhsUser = User::create(['email' => 'muttaqien.wildan14@gmail.com', 'password' => $defaultPw]);
            $this->createMahasiswaProfile($mhsUser, 'Wildan Athif Muttaqien (Mahasiswa)', $faker, $prodiIds[0]);
            $this->createPersonalTeamForUser($mhsUser, 'Wildan Athif Muttaqien (Mahasiswa)');
        });

        // --- 2. DATA DUMMY (Distribusi 10/30/60) ---
        $countAdmin = (int) ($totalUsers * 0.10) - 1;
        $countDosen = (int) ($totalUsers * 0.30) - 1;
        $countMhs = $totalUsers - $countAdmin - $countDosen - 3;

        $this->command->info("Seeding $totalUsers users with Teams in batches of $batchSize...");

        $this->seedInBatches($countAdmin, $batchSize, 'Admin', function () use ($faker, $defaultPw, $prodiIds) {
            $name = $faker->name;
            $user = User::create(['email' => $faker->unique()->safeEmail, 'password' => $defaultPw]);
            $this->createAdminProfile($user, $name, $faker, $faker->randomElement($prodiIds));
            $this->createPersonalTeamForUser($user, $name);
        });

        // Seed Dosens
        $this->seedInBatches($countDosen, $batchSize, 'Dosen', function () use ($faker, $defaultPw, $prodiIds) {
            $name = $faker->name;
            $user = User::create(['email' => $faker->unique()->safeEmail, 'password' => $defaultPw]);
            $this->createDosenProfile($user, $name, $faker, $faker->randomElement($prodiIds));
            $this->createPersonalTeamForUser($user, $name);
        });

        // Seed Mahasiswas
        $this->seedInBatches($countMhs, $batchSize, 'Mahasiswa', function () use ($faker, $defaultPw, $prodiIds) {
            $name = $faker->name;
            $user = User::create(['email' => $faker->unique()->safeEmail, 'password' => $defaultPw]);
            $this->createMahasiswaProfile($user, $name, $faker, $faker->randomElement($prodiIds));
            $this->createPersonalTeamForUser($user, $name);
        });
    }

    private function seedInBatches($total, $batchSize, $label, $callback)
    {
        $created = 0;
        while ($created < $total) {
            $currentBatch = min($batchSize, $total - $created);

            DB::transaction(function () use ($currentBatch, $callback) {
                for ($i = 0; $i < $currentBatch; $i++) {
                    $callback();
                }
            });

            $created += $currentBatch;
            $this->command->info("Created $created/$total $label users with teams...");
        }
    }

    /**
     * Helper untuk membuat Tim Personal bawaan Jetstream secara langsung
     */
    /**
     * Helper untuk membuat Tim Personal berdasarkan struktur tabel kustom
     */
    private function createPersonalTeamForUser(User $user, string $name): void
    {
        $teamName = explode(' ', $name)[0]."'s Team";

        $team = Team::create([
            'name' => $teamName,
            'slug' => Str::slug($teamName).'-'.$user->id,
            'is_personal' => true,
        ]);

        Membership::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->forceFill([
            'current_team_id' => $team->id,
        ])->save();
    }

    private function createAdminProfile($user, $name, $faker, $prodiId)
    {
        Admin::create([
            'user_id' => $user->id,
            'pr_id' => $prodiId,
            'kode_wilayah' => $faker->randomElement(['IDL', 'PLG']),
            'nip' => $faker->unique()->numerify('19#########'),
            'nitk' => $faker->unique()->numerify('88#########'),
            'nik' => $faker->unique()->numerify('################'),
            'name' => $name,
            'tempat_lahir' => $faker->city,
            'tanggal_lahir' => $faker->date('Y-m-d', '2000-01-01'),
            'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
            'agama' => $faker->randomElement(['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik']),
            'no_hp' => $faker->phoneNumber,
            'pangkat' => 'Penata Muda',
            'golongan_awal' => 'III/a',
            'golongan_akhir' => 'III/b',
            'tmt_cp_blu' => $faker->date('Y-m-d', '2023-01-01'),
            'tmt_blu' => $faker->date('Y-m-d', '2024-01-01'),
            'status' => 'Aktif',
        ]);

        $this->seedEducation($user, $faker, ['S1', 'S2']);
    }

    private function createDosenProfile($user, $name, $faker, $prodiId)
    {
        Dosen::create([
            'user_id' => $user->id,
            'pr_id' => $prodiId,
            'name' => $name,
            'nip' => $faker->unique()->numerify('19#########'),
            'nidn' => $faker->unique()->numerify('00########'),
            'nidk' => $faker->unique()->numerify('88########'),
            'nik' => $faker->unique()->numerify('################'),
            'tempat_lahir' => $faker->city,
            'tanggal_lahir' => $faker->date('Y-m-d', '1995-01-01'),
            'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
            'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik']),
            'no_hp' => $faker->phoneNumber,
            'no_karpeg' => $faker->unique()->numerify('N#########'),
            'pangkat_terakhir' => 'Lektor',
            'golongan_terakhir' => 'III/c',
            'tmt_golongan' => $faker->date('Y-m-d', '2020-01-01'),
            'jabatan_fungsional' => 'Dosen Tetap',
            'tmt_jabatan' => $faker->date('Y-m-d', '2021-01-01'),
            'status' => 'Aktif',
        ]);

        $this->seedEducation($user, $faker, ['S1', 'S2', 'S3']);
    }

    private function createMahasiswaProfile($user, $name, $faker, $prodiId)
    {
        Mahasiswa::create([
            'user_id' => $user->id,
            'pr_id' => $prodiId,
            'kode_wilayah' => $faker->randomElement(['IDL', 'PLG']),
            'name' => $name,
            'nim' => $faker->unique()->numerify('061112817#####'),
            'nik' => $faker->unique()->numerify('################'),
            'tempat_lahir' => $faker->city,
            'tanggal_lahir' => $faker->date('Y-m-d', '2005-01-01'),
            'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
            'agama' => 'Islam',
            'no_hp' => $faker->phoneNumber,
            'angkatan' => $faker->numberBetween(2018, 2024),
            'status' => 'Aktif',
        ]);

        $this->seedEducation($user, $faker, ['SMA']);
    }

    private function seedEducation($user, $faker, $levels)
    {
        foreach ($levels as $level) {
            Pendidikan::create([
                'user_id' => $user->id,
                'institusi' => $faker->company.' '.$faker->city,
                'negara' => 'Indonesia',
                'tahun_lulus' => $faker->year,
                'jenjang_pendidikan' => $level,
                'bidang_ilmu' => $faker->sentence(2),
                'gelar' => $faker->suffix,
            ]);
        }
    }
}
