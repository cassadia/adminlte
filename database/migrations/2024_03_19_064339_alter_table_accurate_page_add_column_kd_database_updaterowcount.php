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
        Schema::table('accurate_page', function (Blueprint $table) {
            $table->integer('updateRowCount')->after('rowCount')->nullable();
            $table->string('kd_database')->after('updateRowCount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accurate_page', function (Blueprint $table) {
            $table->dropColumn('kd_database');
            $table->dropColumn('updateRowCount');
        });
    }
};
