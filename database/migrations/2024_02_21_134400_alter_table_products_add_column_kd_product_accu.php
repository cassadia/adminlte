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
        Schema::table('prducts', function (Blueprint $table) {
           $table->string('kd_produk_accu')->nullable()->after('kd_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prducts', function (Blueprint $table) {
            $table->dropColumn('kd_produk_accu');
        });
    }
};
