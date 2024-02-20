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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('kd_motor'); // Ubah kolom menjadi unique
            $table->string('nm_motor');
            $table->integer('tahun');
            $table->string('no_seri_mesin')->nullable(); // Tambahkan unique constraint
            $table->string('no_seri_rangka')->nullable(); // Tambahkan unique constraint
            $table->enum('status', ['Aktif', 'Tidak Aktif']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
