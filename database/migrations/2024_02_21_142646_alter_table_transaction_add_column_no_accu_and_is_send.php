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
        Schema::table('transaction', function (Blueprint $table) {
            //
            $table->string('no_accu_trans')->nullable()->after('id');
            $table->smallInteger('is_send_to_accu')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
            $table->dropColumn('no_accu_trans');
            $table->dropColumn('is_send_to_accu');
        });
    }
};
