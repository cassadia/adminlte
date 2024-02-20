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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kd_produk')->unique();
            $table->string('nm_produk');
            $table->decimal('harga_jual');
            $table->string('database');
            $table->enum('status', ['Aktif', 'Tidak Aktif']);
            $table->timestamps();
            // $table->dateTime('created_at')->useCurrent();
            // $table->dateTime('updated_at')->useCurrent();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
