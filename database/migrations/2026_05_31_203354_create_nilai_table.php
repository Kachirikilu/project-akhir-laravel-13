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
        Schema::create('nilai_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('kj_id')->constrained('kelas_jadwals')->onDelete('cascade');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['mahasiswa_id', 'kj_id']);
        });

        Schema::create('nilai_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilai_mahasiswa')->onDelete('cascade');
            $table->foreignId('sesi_id')->nullable()->constrained('kelas_sesi')->nullOnDelete();

            $table->decimal('nilai', 5, 2)->nullable();
            $table->decimal('bobot', 5, 2)->nullable();
            $table->decimal('nilai_bobot', 8, 2)->nullable();
            $table->boolean('is_generated')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rekap_cpl_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilai_mahasiswa')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->decimal('persentase', 8, 2);
            $table->integer('jumlah_pertemuan')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['nilai_id', 'cpl_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
        Schema::dropIfExists('nilai_detail');
        Schema::dropIfExists('rekap_cpl_mahasiswa');
    }
};
