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

        $totalUsers = 64;
        $batchSize = 512;

        $prodiIds = Prodi::pluck('id')->toArray();
        if (empty($prodiIds)) {
            throw new \Exception('Tabel prodis kosong! Jalankan SilsilahSeeder terlebih dahulu!');
        }

        // --- 1. AKUN UTAMA (Diproses sekali) ---
        DB::transaction(function () use ($faker, $defaultPw, $prodiIds) {
            // Admin Utama
            $adminUser = User::create(['email' => 'muttaqien.wildan12@gmail.com', 'password' => $defaultPw]);
            $this->createAdminProfile($adminUser, 'Wildan Athif Muttaqien (Admin)', '03041282227066', '628985655826', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 100);
            $this->createPersonalTeamForUser($adminUser, 'Wildan Athif Muttaqien (Admin)');

            // Dosen Utama
            $dosenUser = User::create(['email' => 'muttaqien.wildan13@gmail.com', 'password' => $defaultPw]);
            $this->createDosenProfile($dosenUser, 'Wildan Athif Muttaqien (Dosen)', '03041282227064', '628985655826', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 100);
            $this->createPersonalTeamForUser($dosenUser, 'Wildan Athif Muttaqien (Dosen)');

            // Mahasiswa Utama
            $mhsUser = User::create(['email' => 'muttaqien.wildan14@gmail.com', 'password' => $defaultPw]);
            $this->createMahasiswaProfile($mhsUser, 'Wildan Athif Muttaqien (Mahasiswa)', '03041282227063', '628985655826', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 50);
            $this->createPersonalTeamForUser($mhsUser, 'Wildan Athif Muttaqien (Mahasiswa)');

            $mhsUser2 = User::create(['email' => 'iqbal.apriza@gmail.com', 'password' => $defaultPw]);
            $this->createMahasiswaProfile($mhsUser2, 'Muhammad Iqbal Apriza', '03041282227043', '6281271069292', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 50);
            $this->createPersonalTeamForUser($mhsUser2, 'Muhammad Iqbal Apriza');

            $mhsUser3 = User::create(['email' => 'andi.kautsar@gmail.com', 'password' => $defaultPw]);
            $this->createMahasiswaProfile($mhsUser3, 'Andi Muhammad Kautsar', '03041282227065', '6282379370929', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 50);
            $this->createPersonalTeamForUser($mhsUser3, 'Andi Muhammad Kautsar');

            $mhsUser4 = User::create(['email' => 'ghuzam.ganteng@gmail.com', 'password' => $defaultPw]);
            $this->createMahasiswaProfile($mhsUser4, 'Muhammad Ghuzammir Valcruysen Mizanno', '03041282227096', '6285788756988', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 50);
            $this->createPersonalTeamForUser($mhsUser4, 'Muhammad Ghuzammir Valcruysen Mizanno');

            $mhsUser5 = User::create(['email' => 'dzakiudin07@gmail.com', 'password' => $defaultPw]);
            $this->createMahasiswaProfile($mhsUser5, 'Dzaki Udin', '03041282227062', '6285707091624', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 50);
            $this->createPersonalTeamForUser($mhsUser5, 'Dzaki Udin');

            $mhsUser6 = User::create(['email' => 'aisyah@gmail.com', 'password' => $defaultPw]);
            $this->createDosenProfile($mhsUser6, 'Aisyah Nada Khalilah', '03041282227061', '6282118716848', 'Perempuan', 'Islam', $faker, $prodiIds[0], 1, 100);
            $this->createPersonalTeamForUser($mhsUser6, 'Aisyah Nada Khalilah');

            $mhsUser7 = User::create(['email' => 'afif@gmail.com', 'password' => $defaultPw]);
            $this->createDosenProfile($mhsUser7, 'Afif Budiani', '03011382126114', '6289506506639', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 100);
            $this->createPersonalTeamForUser($mhsUser7, 'Afif Budiani');

            $mhsUser5 = User::create(['email' => 'mustofa.ihsan@gmail.com', 'password' => $defaultPw]);
            $this->createMahasiswaProfile($mhsUser5, 'Mustofa Ihsan', '2230803106', '6283143337282', 'Laki-laki', 'Islam', $faker, $prodiIds[0], 1, 50);
            $this->createPersonalTeamForUser($mhsUser5, 'Mustofa Ihsan');
        });

        // --- 2. DATA DUMMY (Distribusi 10/30/60) ---
        $countAdmin = (int) ($totalUsers * 0.10) - 1;
        $countDosen = (int) ($totalUsers * 0.30) - 1;
        $countMhs = $totalUsers - $countAdmin - $countDosen - 3;

        $this->command->info("Seeding $totalUsers users with Teams in batches of $batchSize...");

        $this->seedInBatches($countAdmin, $batchSize, 'Admin', function () use ($faker, $defaultPw, $prodiIds) {
            $name = $faker->name;
            $nip = $faker->unique()->numerify('19#########');
            $telpon = '628985655826';
            $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
            $agama = $faker->randomElement(['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik']);
            $user = User::create(['email' => $faker->unique()->safeEmail, 'password' => $defaultPw]);
            $this->createAdminProfile($user, $name, $nip, $telpon, $gender, $agama, $faker, $faker->randomElement($prodiIds));
            $this->createPersonalTeamForUser($user, $name);
        });

        // Seed Dosens
        $this->seedInBatches($countDosen, $batchSize, 'Dosen', function () use ($faker, $defaultPw, $prodiIds) {
            $name = $faker->name;
            $nip = $faker->unique()->numerify('19#########');
            $telpon = '628985655826';
            $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
            $agama = $faker->randomElement(['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik']);
            $user = User::create(['email' => $faker->unique()->safeEmail, 'password' => $defaultPw]);
            $this->createDosenProfile($user, $name, $nip, $telpon, $gender, $agama, $faker, $faker->randomElement($prodiIds));
            $this->createPersonalTeamForUser($user, $name);
        });

        // Seed Mahasiswas
        $this->seedInBatches($countMhs, $batchSize, 'Mahasiswa', function () use ($faker, $defaultPw, $prodiIds) {
            $name = $faker->name;
            $nim = $faker->unique()->numerify('03041282######');
            $telpon = '628985655826';
            $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
            $agama = $faker->randomElement(['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik']);
            $user = User::create(['email' => $faker->unique()->safeEmail, 'password' => $defaultPw]);
            $this->createMahasiswaProfile($user, $name, $nim, $telpon, $gender, $agama, $faker, $faker->randomElement($prodiIds));
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

    private function createAdminProfile($user, $name, $nip, $telpon, $gender, $agama, $faker, $prodiId, $wa = 0, $token = 0)
    {
        Admin::create([
            'user_id' => $user->id,
            'pr_id' => $prodiId,
            'kode_wilayah' => $faker->randomElement(['IDL', 'PLG']),
            'nip' => $nip,
            'nitk' => $faker->unique()->numerify('88#########'),
            'nik' => $faker->unique()->numerify('################'),
            'name' => $name,
            'tempat_lahir' => $faker->city,
            'tanggal_lahir' => $faker->date('Y-m-d', '2000-01-01'),
            'jenis_kelamin' => $gender,
            'agama' => $agama,
            'no_hp' => $telpon,
            'is_wa_active' => $wa,
            'wa_limit' => $token,
            // 'no_hp' => $faker->phoneNumber,
            'pangkat' => 'Penata Muda',
            'golongan_awal' => 'III/a',
            'golongan_akhir' => 'III/b',
            'tmt_cp_blu' => $faker->date('Y-m-d', '2023-01-01'),
            'tmt_blu' => $faker->date('Y-m-d', '2024-01-01'),
            'status' => 'Aktif',
        ]);

        $this->seedEducation($user, $faker, ['S1', 'S2']);
    }

    private function createDosenProfile($user, $name, $nip, $telpon, $gender, $agama, $faker, $prodiId, $wa = 0, $token = 0)
    {
        Dosen::create([
            'user_id' => $user->id,
            'pr_id' => $prodiId,
            'name' => $name,
            'nip' => $nip,
            'nidn' => $faker->unique()->numerify('00########'),
            'nidk' => $faker->unique()->numerify('88########'),
            'nik' => $faker->unique()->numerify('################'),
            'tempat_lahir' => $faker->city,
            'tanggal_lahir' => $faker->date('Y-m-d', '1995-01-01'),
            'jenis_kelamin' => $gender,
            'agama' => $agama,
            'no_hp' => $telpon,
            'is_wa_active' => $wa,
            'wa_limit' => $token,
            // 'no_hp' => $faker->phoneNumber,
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

    private function createMahasiswaProfile($user, $name, $nim, $telpon, $gender, $agama, $faker, $prodiId, $wa = 0, $token = 0)
    {
        Mahasiswa::create([
            'user_id' => $user->id,
            'pr_id' => $prodiId,
            'kode_wilayah' => $faker->randomElement(['IDL', 'PLG']),
            'name' => $name,
            'nim' => $nim,
            'nik' => $faker->unique()->numerify('################'),
            'tempat_lahir' => $faker->city,
            'tanggal_lahir' => $faker->date('Y-m-d', '2005-01-01'),
            'jenis_kelamin' => $gender,
            'agama' => $agama,
            'no_hp' => $telpon,
            'is_wa_active' => $wa,
            'wa_limit' => $token,
            // 'no_hp' => $faker->phoneNumber,
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
