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
        Schema::create('accurate_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->string('session');
            $table->string('admin');
            $table->string('data_version');
            $table->string('accessible_until');
            $table->string('license_end');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_sessions');
    }
};
