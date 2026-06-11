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
        // Dosen Pengajar (RPS - Dosen)
        Schema::create('rps_pivot_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->enum('peran', [
                'Koordinator',
                'Pengajar',
                'Asisten'
            ])->default('Pengajar');
            $table->boolean('is_ketua')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // RPS - CPMK
        Schema::create('rps_pivot_cpmk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
            $table->foreignId('cpmk_id')->constrained('cpmks')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // RPS - CPL
        // Schema::create('rps_pivot_cpl', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
        //     $table->foreignId('cpl_id')->constrained('cpls')->onDelete('cascade');
        //     $table->integer('sort_order')->default(0);
        //     $table->timestamps();
        // });

        Schema::create('dosen_pivot_scpmk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->foreignId('scpmk_id')->constrained('sub_cpmks')->onDelete('cascade');

            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // CPMK - Sub-CPMK
        Schema::create('cpmk_pivot_scpmk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpmk_id')->constrained('cpmks')->onDelete('cascade');
            $table->foreignId('scpmk_id')->constrained('sub_cpmks')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // CPMK - CPL
        Schema::create('cpmk_pivot_cpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpl_id')->constrained('cpls')->onDelete('cascade');
            $table->foreignId('cpmk_id')->constrained('cpmks')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });

        // CPMK - Referensi
        Schema::create('cpmk_pivot_ref', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpmk_id')->constrained('cpmks')->onDelete('cascade');
            $table->foreignId('ref_id')->constrained('referensis')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });

        // Sub-CPMK - Referensi
        Schema::create('scpmk_pivot_ref', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scpmk_id')->constrained('sub_cpmks')->onDelete('cascade');
            $table->foreignId('ref_id')->constrained('referensis')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });

        // Prodis - CPL (Ownership)
        Schema::create('prodi_pivot_cpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_id')->constrained('prodis')->onDelete('cascade');
            $table->foreignId('cpl_id')->constrained('cpls')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });

        // RPS - Referensi
        Schema::create('rps_pivot_ref', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
            $table->foreignId('ref_id')->constrained('referensis')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rps_pivot_ref');
        // Schema::dropIfExists('rps_pivot_cpl');
        Schema::dropIfExists('rps_pivot_dosen');
        Schema::dropIfExists('rps_pivot_cpmk');
        Schema::dropIfExists('cpmk_pivot_scpmk');
        Schema::dropIfExists('cpmk_pivot_cpl');
        Schema::dropIfExists('cpmk_pivot_ref');
        Schema::dropIfExists('scpmk_pivot_ref');
        Schema::dropIfExists('prodi_pivot_cpl');


    }
};
