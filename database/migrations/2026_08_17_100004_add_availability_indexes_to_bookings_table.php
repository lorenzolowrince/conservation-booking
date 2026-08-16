<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['package_id', 'check_in_date', 'check_out_date', 'status'], 'bookings_package_avail_idx');
            $table->index(['accommodation_type_id', 'check_in_date', 'check_out_date', 'status'], 'bookings_accommodation_avail_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_package_avail_idx');
            $table->dropIndex('bookings_accommodation_avail_idx');
        });
    }
};
