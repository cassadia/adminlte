<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToBrowserInfosTable extends Migration
{
    public function up()
    {
        Schema::table('browser_infos', function (Blueprint $table) {
            $table->float('latitude')->nullable();  // Tambahkan kolom latitude
            $table->float('longitude')->nullable(); // Tambahkan kolom longitude
        });
    }

    public function down()
    {
        Schema::table('browser_infos', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']); // Hapus kolom saat rollback
        });
    }
}
