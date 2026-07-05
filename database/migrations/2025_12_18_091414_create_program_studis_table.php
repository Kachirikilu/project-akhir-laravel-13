<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fakultas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_fk')->unique();
            $table->string('nama_fk');
            $table->decimal('nilai_fk', 5, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_id')->nullable() ->constrained('fakultas')->onDelete('set null');
            $table->index('fk_id');
            $table->string('kode_dp')->nullable();
            $table->string('nama_dp');
            $table->decimal('nilai_dp', 5, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dp_id')->nullable() ->constrained('departemens')->onDelete('set null');
            $table->index('dp_id');
            $table->string('kode_pr')->nullable();
            $table->string('nama_pr');
            $table->enum('strata', ['Sarjana', 'Magister', 'Doktor'])->default('Sarjana');
            $table->tinyInteger('target_sks')->unsigned()->default(144);
            $table->decimal('nilai_pr', 5, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['strata', 'nama_pr']);
            $table->unique(['strata', 'kode_pr']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fakultas');
        Schema::dropIfExists('departemens');
        Schema::dropIfExists('prodis');
    }
};
