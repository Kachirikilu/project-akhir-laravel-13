<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->enum('level_mk', [1, 2, 3, 4])->default(1);
            $table->string('kode_mk')->nullable();
            $table->char('digit_semester', 2);
            $table->char('digit_mk', 2)->nullable();
            $table->string('nama_mk');
            $table->integer('semester');
            $table->integer('sks_kuliah')->default(1);
            $table->enum('tipe_sks', [1, 2, 3, 4])->default(1);
            $table->boolean('is_wajib')->default(true);
            $table->text('deskripsi')->nullable();
            $table->text('bahan_kajian')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('prodi_pivot_mk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_id')->constrained('prodis')->onDelete('cascade');
            $table->foreignId('mk_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodi_pivot_mk');
        Schema::dropIfExists('mata_kuliahs');
    }
};