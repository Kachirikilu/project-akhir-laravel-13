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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelas')->unique();
            $table->foreignId('rps_id')->nullable()->constrained('rps')->onDelete('set null');
            $table->foreignId('pr_id')->nullable()->constrained('prodis')->onDelete('set null');

            $table->string('nama_kelas');
            $table->text('deskripsi')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('kelas_jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            
            $table->string('password')->nullable();

            $table->string('label_kelas');
            $table->enum('kode_wilayah', ['IDL', 'PLG']);
            
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();

            $table->enum('hari_pelaksanaan', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_berakhir')->nullable();

            $table->integer('kapasitas')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['kelas_id', 'label_kelas', 'kode_wilayah']);
        });

        Schema::create('kelas_sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kj_id')->constrained('kelas_jadwals')->onDelete('cascade');

            $table->integer('pertemuan_ke');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->boolean('reminder_sent')->default(false);

            $table->softDeletes();
            $table->timestamps();
            $table->unique(['kj_id', 'pertemuan_ke']);
        });

        Schema::create('kelas_sesi_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('kelas_sesi')->onDelete('cascade');

            $table->time('jam_mulai')->nullable();
            $table->time('jam_berakhir')->nullable();

            $table->text('deskripsi')->nullable();
            $table->text('materi')->nullable();
            $table->text('metodologi')->nullable();
            $table->text('indikator')->nullable();

            $table->enum('metode', [
                'Teori', 'Aktivitas Partisipasif', 'Tugas', 'Mandiri',
                'UTS', 'UAS', 'Evaluasi Awal', 'Evaluasi Akhir',
                'Laporan Akhir', 'Hasil Proyek', 'Kuis',
                'Skripsi', 'Kerja Praktek', 'Responsi', 'Logbook', 'Portofolio',
            ])->nullable();

            $table->text('deskripsi_tugas')->nullable();
            $table->integer('waktu_tugas')->nullable();
            $table->integer('waktu_mandiri')->nullable();
            $table->decimal('bobot', 5, 2)->nullable();

            $table->timestamps();
        });

        Schema::create('mahasiswa_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('kj_id')->constrained('kelas_jadwals')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('mahasiswa_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('kelas_sesi')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');

            $table->enum('status', [
                'Hadir',
                'Terlambat',
                'Absen',
                'Sakit',
                'Izin',
                'Dispensasi',
            ])->default('Absen');

            $table->dateTime('waktu_presensi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });


        Schema::create('sesi_pivot_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('kelas_sesi')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->enum('peran', ['Koordinator', 'Pengajar', 'Asisten'])->default('Pengajar');
            $table->boolean('is_ketua')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('sesi_pivot_ref', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('kelas_sesi')->onDelete('cascade');
            $table->foreignId('ref_id')->constrained('referensis')->onDelete('cascade');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_kehadiran');
        Schema::dropIfExists('mahasiswa_kelas');
        Schema::dropIfExists('sesi_pivot_ref');
        Schema::dropIfExists('sesi_pivot_dosen');
        Schema::dropIfExists('kelas_sesi_overrides');
        Schema::dropIfExists('kelas_sesi');
        Schema::dropIfExists('kelas_jadwals');
        Schema::dropIfExists('kelas');
    }
};
