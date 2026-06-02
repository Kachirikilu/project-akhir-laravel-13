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
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('kj_id')->constrained('kelas_jadwals')->cascadeOnDelete();

            $table->decimal('nilai', 5, 2)->nullable();
            $table->json('nilai_array')->nullable();
            $table->json('bobot_array')->nullable();

            $table->boolean('is_locked')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'kj_id']);
        });

        Schema::create('rekap_cpl_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->decimal('persentase', 8, 2);
            $table->integer('jumlah_pertemuan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
        Schema::dropIfExists('rekap_cpl_mahasiswa');
    }
};
