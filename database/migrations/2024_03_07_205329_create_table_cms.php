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
        Schema::create('cms', function (Blueprint $table) {
            $table->id();
            $table->string('title_icon')->nullable()->default(null);
            $table->string('brand_image')->nullable()->default(null);
            $table->string('brand_text')->nullable()->default(null);
            $table->string('footer_text_left')->nullable()->default(null);
            $table->string('footer_text_middle')->nullable()->default(null);
            $table->string('footer_text_right')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms');
    }
};
