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
            $table->foreignId('rps_id')->nullable()->constrained('rps')->nullOnDelete();
            $table->foreignId('kj_id')->nullable()->constrained('kelas_jadwals')->nullOnDelete();

            $table->index('mahasiswa_id');
            $table->index('rps_id');
            $table->index('kj_id');

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

        Schema::create('rekap_nilai_mahasiswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswas')
                ->cascadeOnDelete();

            $table->index('mahasiswa_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->decimal('nilai_ipk', 3, 2)->nullable();
            $table->unsignedInteger('count_rps')->default(0);
            $table->unsignedInteger('total_sks')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['mahasiswa_id']);
        });

        Schema::create('rekap_rps_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->cascadeOnDelete();
            $table->foreignId('pr_id')->nullable()->constrained('prodis')->nullOnDelete();
            $table->index('rps_id');
            $table->index('pr_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['rps_id', 'pr_id']);
        });

        Schema::create('rekap_cpl_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->foreignId('pr_id')->nullable()->constrained('prodis')->nullOnDelete();

            $table->index('cpl_id');
            $table->index('pr_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cpl_id', 'pr_id']);
        });

        Schema::create('rekap_cpmk_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpmk_id')->constrained('cpmks')->cascadeOnDelete();
            $table->foreignId('pr_id')->nullable()->constrained('prodis')->nullOnDelete();

            $table->index('cpmk_id');
            $table->index('pr_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cpmk_id', 'pr_id']);
        });

        Schema::create('rekap_scpmk_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scpmk_id')->constrained('sub_cpmks')->cascadeOnDelete();
            // $table->foreignId('cpl_id')->nullable()->constrained('cpls')->nullOnDelete();
            $table->foreignId('pr_id')->nullable()->constrained('prodis')->nullOnDelete();

            $table->index('scpmk_id');
            $table->index('pr_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['scpmk_id', 'pr_id']);
        });

        Schema::create('rekap_cpl_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpl_id')->constrained('cpls')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();

            $table->index('cpl_id');
            $table->index('mahasiswa_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cpl_id', 'mahasiswa_id']);
        });

        Schema::create('rekap_cpmk_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpmk_id')->constrained('cpmks')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->index('cpmk_id');
            $table->index('mahasiswa_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cpmk_id', 'mahasiswa_id']);
        });

        Schema::create('rekap_scpmk_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scpmk_id')->constrained('sub_cpmks')->cascadeOnDelete();
            // $table->foreignId('cpl_id')->nullable()->constrained('cpls')->nullOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();

            $table->index('scpmk_id');
            $table->index('mahasiswa_id');

            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['scpmk_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');

        Schema::dropIfExists('rekap_rps_prodi');

        Schema::dropIfExists('rekap_cpl_prodi');
        Schema::dropIfExists('rekap_cpmk_prodi');
        Schema::dropIfExists('rekap_scpmk_prodi');

        Schema::dropIfExists('rekap_cpl_mahasiswa');
        Schema::dropIfExists('rekap_cpmk_mahasiswa');
        Schema::dropIfExists('rekap_scpmk_mahasiswa');
    }
};
