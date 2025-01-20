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
        Schema::create('accurate_log_new', function (Blueprint $table) {
            $table->id();
            $table->string('kd_database');
            $table->string('scheduler');
            $table->string('rowCount')->nullable()->default(null);
            $table->string('updateRowCount')->nullable()->default(null);
            $table->timestamp('startTime')->nullable()->default(null);
            $table->timestamp('endTime')->nullable()->default(null);
            $table->string('duration')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_log_new');
    }
};
