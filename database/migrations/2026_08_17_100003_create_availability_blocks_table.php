<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_type_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date'); // inclusive
            $table->string('reason');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['package_id', 'start_date', 'end_date']);
            $table->index(['accommodation_type_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_blocks');
    }
};
