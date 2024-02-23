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
        Schema::create('accurate_page', function (Blueprint $table) {
            //
            $table->id();
            $table->integer('batch');
            $table->integer('startPage');
            $table->integer('endPage');
            $table->integer('totalBatches');
            $table->integer('rowCount');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('accurate_page', function (Blueprint $table) {
            //
            $table->dropColumn('page');
            $table->dropColumn('pageSize');
            $table->dropColumn('pageCount');
            $table->dropColumn('rowCount');
            $table->dropColumn('start');
            $table->dropColumn('sort');
            $table->dropColumn('limit');
        });
    }
};
