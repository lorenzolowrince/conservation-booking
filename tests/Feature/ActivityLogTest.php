<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AvailabilityBlock;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_record_persists_with_correct_subject(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $booking = Booking::factory()->create();

        $this->actingAs($user);
        $log = ActivityLog::record('booking.created', $booking, 'Test event', ['a' => 1], 'because');

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'user_id' => $user->id,
            'action' => 'booking.created',
            'subject_type' => Booking::class,
            'subject_id' => $booking->id,
            'reason' => 'because',
        ]);
        $this->assertSame(['a' => 1], $log->fresh()->changes);
    }

    public function test_manual_booking_creation_logs_exactly_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = ConservationArea::factory()->create();

        $this->actingAs($admin)->post(route('admin.bookings.store'), [
            'conservation_area_id' => $area->id,
            'booking_type' => 'day_trip',
            'contact_name' => 'Jane',
            'contact_email' => 'jane@example.com',
            'contact_phone' => '+60123456789',
            'contact_nationality' => 'Malaysian',
            'check_in_date' => now()->addDays(5)->toDateString(),
            'check_out_date' => now()->addDays(6)->toDateString(),
            'num_adults' => 2,
        ]);

        $this->assertSame(1, ActivityLog::where('action', 'booking.created')->count());
    }

    public function test_blocking_and_unblocking_dates_each_log_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = ConservationArea::factory()->create();
        $pkg = \App\Models\Package::factory()->create(['conservation_area_id' => $area->id]);

        $this->actingAs($admin)->post(route('admin.blocked-dates.store'), [
            'target' => 'package',
            'package_id' => $pkg->id,
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
            'reason' => 'Maintenance',
        ]);

        $block = AvailabilityBlock::first();
        $this->assertNotNull($block);
        $this->assertSame(1, ActivityLog::where('action', 'availability.blocked')->count());

        $this->actingAs($admin)->delete(route('admin.blocked-dates.destroy', $block));

        $this->assertSame(1, ActivityLog::where('action', 'availability.unblocked')->count());
    }
}
