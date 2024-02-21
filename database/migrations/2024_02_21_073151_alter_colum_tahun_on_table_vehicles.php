<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Menambahkan kolom baru
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('tahun_dari')->after('tahun');
        });

        // Menyalin data dari kolom lama ke kolom baru
        DB::statement('UPDATE vehicles SET tahun_dari = tahun');

        // Menghapus kolom lama
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menambahkan kolom lama
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('tahun')->after('tahun_dari');
        });

        // Menyalin data dari kolom baru ke kolom lama
        DB::statement('UPDATE vehicles SET tahun = tahun_dari');

        // Menghapus kolom baru
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('tahun_dari');
        });
    }
};