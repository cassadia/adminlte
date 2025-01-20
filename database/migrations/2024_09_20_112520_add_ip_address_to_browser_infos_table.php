<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpAddressToBrowserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('browser_infos', function (Blueprint $table) {
            $table->string('ip_address')->nullable(); // Tambahkan kolom IP address
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('browser_infos', function (Blueprint $table) {
            $table->dropColumn('ip_address'); // Hapus kolom IP address saat rollback
        });
    }
}
