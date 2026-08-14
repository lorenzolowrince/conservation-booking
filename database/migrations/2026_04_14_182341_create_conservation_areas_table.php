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
        Schema::create('conservation_areas', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('short_name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('about')->nullable();
            $table->string('location');
            $table->unsignedInteger('area_hectares')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('highlights')->nullable();
            $table->json('wildlife')->nullable();
            $table->string('best_time_to_visit')->nullable();
            $table->string('difficulty_level')->default('moderate'); // easy, moderate, challenging
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conservation_areas');
    }
};
