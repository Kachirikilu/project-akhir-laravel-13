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
        Schema::create('rps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mk_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->index('mk_id');
            $table->text('deskripsi');
            $table->string('akademik', 9);
            $table->string('bobot_uts', 2)->nullable();
            $table->string('bobot_uas', 2)->nullable();
            $table->boolean('is_draf')->default(true);
            $table->date('revisi')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tim_dosens', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tim_dosen')->unique();
            $table->foreignId('pr_id')->constrained('prodis')->onDelete('cascade');
            $table->index('pr_id');
            $table->text('nama_tim');
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cpmks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cpmk')->unique();
            $table->string('deskripsi')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sub_cpmks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_scpmk')->unique();
            $table->text('deskripsi');
            $table->text('materi');
            $table->text('metodologi');
            $table->text('indikator');
            $table->enum('metode', [
                // --- Evaluasi OBE/Projek (Tatap Muka/Tugas) ---
                'Teori',
                'Aktivitas Partisipasif',
                'Tugas',
                'Mandiri',

                // --- Evaluasi Formal (Umum) ---
                'UTS', 'UAS', 
                'Evaluasi Awal', // Setara UTS
                'Evaluasi Akhir', 'Laporan Akhir', 'Hasil Proyek', // Setara UAS
                'Kuis',

                // --- Evaluasi Berbasis Kinerja (Praktikum/Lapangan/Simulasi) ---
                'Skripsi',
                'Kerja Praktek',
                'Responsi',
                'Logbook',
                'Portofolio',
            ])->default('Teori');
            $table->text('deskripsi_tugas')->nullable();
            $table->integer('waktu_tugas')->nullable();
            $table->integer('waktu_mandiri')->nullable();
            $table->decimal('bobot', 5, 2);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cpls', function (Blueprint $table) {
            $table->id();
            $table->enum('level_cpl', [1, 2, 3, 4])->default(1);
            $table->string('kode_cpl');
            $table->string('deskripsi')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('referensis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ref')->unique();
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->year('tahun');
            $table->string('link')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rps');
        Schema::dropIfExists('tim_dosens');
        Schema::dropIfExists('cpls');
        Schema::dropIfExists('cpmks');
        Schema::dropIfExists('sub_cpmks');
        Schema::dropIfExists('referensis');
    }
};
