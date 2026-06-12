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
            $table->foreignId('kj_id')->nullable()->constrained('kelas_jadwals')->nullOnDelete();

            $table->foreignId('rps_id')->nullable()->constrained('rps')->nullOnDelete();
            $table->enum('ganjil_genap', ['Ganjil', 'Genap'])->nullable();
            $table->string('tahun_akademik', 20)->nullable();

            $table->decimal('nilai', 5, 2)->nullable();
            $table->json('nilai_array')->nullable();
            $table->json('bobot_array')->nullable();

            $table->boolean('is_loocked')->default(false);

            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'mahasiswa_id',
                'rps_id',
                'ganjil_genap',
                'tahun_akademik',
            ], 'nm_mhs_rps_sem_ta_unique'
            );
        });

        Schema::create('rekap_cpl_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->foreignId('pr_id')->nullable()->constrained('prodis')->nullOnDelete();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
        Schema::dropIfExists('rekap_cpl_prodi');
    }
};
