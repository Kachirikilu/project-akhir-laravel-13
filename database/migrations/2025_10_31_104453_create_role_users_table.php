<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->unique();
            $table->foreignId('pr_id')->nullable();
            $table->index('user_id');
            $table->index('pr_id');
            $table->enum('kode_wilayah', ['IDL', 'PLG'])->nullable();

            // Data Identitas
            $table->string('name');
            $table->string('nip')->nullable()->unique();
            $table->string('nitk')->nullable()->unique();
            $table->string('nik')->unique();

            // Data Personal
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik', 'Khonghucu', 'Lainnya'])->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->boolean('is_wa_active')->default(false);
            $table->tinyInteger('wa_limit')->unsigned()->default(0);

            // Data Kepegawaian BLU
            $table->string('pangkat')->nullable();
            $table->string('golongan_awal')->nullable();
            $table->string('golongan_akhir')->nullable();
            $table->date('tmt_cp_blu')->nullable();
            $table->date('tmt_blu')->nullable();

            $table->enum('status', [
                'Aktif',                  // Hijau (Produktif)
                'Tugas Belajar',          // Kuning (Transisi/Sementara)
                'Mutasi',                 // Kuning (Transisi/Sementara)
                'Cuti Luar Tanggungan',   // Kuning (Transisi/Sementara)
                'Resign',                 // Orange (Keluar Prosedural)
                'Pensiun',                // Orange (Keluar Prosedural)
                'Diberhentikan',          // Merah (Masalah/Sanksi)
                'Meninggal Dunia',        // Merah (Permanen)
            ])->default('Aktif');
            $table->index('status');

            $table->timestamps();
        });

        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->unique();
            $table->foreignId('pr_id')->nullable();
            $table->index('user_id');
            $table->index('pr_id');

            // Data Identitas
            $table->string('name');
            $table->string('nip')->nullable()->unique();
            $table->string('nidn')->nullable()->unique();
            $table->string('nidk')->nullable()->unique();
            $table->string('nik')->unique();

            // Data Personal & Fisik (Sama seperti Admin)
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik', 'Khonghucu', 'Lainnya'])->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->boolean('is_wa_active')->default(false);
            $table->tinyInteger('wa_limit')->unsigned()->default(0);

            $table->string('no_karpeg')->nullable();
            $table->string('pangkat_terakhir')->nullable();
            $table->string('golongan_terakhir')->nullable();
            $table->date('tmt_golongan')->nullable();
            $table->string('jabatan_fungsional')->nullable();
            $table->date('tmt_jabatan')->nullable();

            $table->enum('status', [
                'Aktif',                  // Hijau (Produktif)
                'Tugas Belajar',          // Kuning (Transisi/Studi)
                'Izin Belajar',           // Kuning (Transisi/Studi)
                'Cuti Sabatika',          // Kuning (Transisi/Riset)
                'Alih Tugas',             // Orange (Perubahan Jabatan)
                'Resign',                 // Orange (Keluar Prosedural)
                'Pensiun',                // Orange (Keluar Prosedural)
                'Diberhentikan',          // Merah (Masalah/Sanksi)
                'Meninggal Dunia',        // Merah (Permanen)
            ])->default('Aktif');
            $table->index('status');

            $table->timestamps();
        });

        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->unique();
            $table->foreignId('pr_id')->nullable();
            $table->index('user_id');
            $table->index('pr_id');
            $table->enum('kode_wilayah', ['IDL', 'PLG'])->nullable();

            // Data Identitas
            $table->string('name');
            $table->string('nim')->unique();
            $table->string('nik')->unique();

            // Data Personal & Fisik
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik', 'Khonghucu', 'Lainnya'])->nullable();
            $table->string('no_hp')->nullable();
            $table->boolean('is_wa_active')->default(false);
            $table->tinyInteger('wa_limit')->unsigned()->default(0);

            // Data Akademik
            $table->year('angkatan');
            $table->date('tanggal_yudisium')->nullable();
            $table->date('tanggal_wisuda')->nullable();
            $table->enum('status', [
                'Aktif',                  // Hijau (Aktif Kuliah)
                'Lulus',                  // Biru (Output Positif)
                'Cuti',                   // Kuning (Jeda Resmi)
                'Pindah',                 // Kuning (Transisi Keluar)
                'Non-Aktif',              // Orange (Masalah Administrasi)
                'Mengundurkan Diri',      // Orange (Keluar Prosedural)
                'Drop Out',               // Merah (Masalah Akademik/Sanksi)
                'Hilang',                 // Merah (Tanpa Kabar/Ghaib)
                'Meninggal Dunia',        // Merah (Permanen)
            ])->default('Aktif');
            $table->index('status');

            $table->timestamps();
        });

        Schema::create('pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('institusi');
            $table->string('negara')->default('Indonesia');
            $table->year('tahun_lulus');

            $table->enum('jenjang_pendidikan', [
                'SMA', 'SMK', 'MAN',
                'D1', 'D2', 'D3', 'D4',
                'S1', 'S2', 'S3', 'Profesi',
                'Spesialis',
            ]);

            $table->string('bidang_ilmu')->nullable();
            $table->string('gelar')->nullable();

            $table->boolean('is_pendidikan_blu')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('dosens');
        Schema::dropIfExists('mahasiswas');

    }
};
