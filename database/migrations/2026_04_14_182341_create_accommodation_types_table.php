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
        Schema::create('accommodation_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conservation_area_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // chalet, hostel, camping, research_lodge, etc.
            $table->text('description')->nullable();
            $table->integer('capacity'); // max persons
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('price_per_night_foreigner', 10, 2)->nullable();
            $table->json('amenities')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_types');
    }
};
