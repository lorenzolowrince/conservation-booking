<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodation_types', function (Blueprint $table) {
            // Conservative default: existing rows get 1 unit so the availability
            // engine can never over-book them. Review and correct the real
            // physical unit count per accommodation type after this deploys.
            $table->unsignedInteger('total_units')->default(1)->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_types', function (Blueprint $table) {
            $table->dropColumn('total_units');
        });
    }
};
