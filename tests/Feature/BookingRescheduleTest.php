<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRescheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_reschedule_to_available_dates_succeeds_and_logs(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create(['conservation_area_id' => $area->id, 'total_units' => 1]);

        $booking = Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-04-01',
            'check_out_date' => '2027-04-03',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($staff)->patch(route('admin.bookings.reschedule', $booking), [
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-04-10',
            'check_out_date' => '2027-04-12',
            'num_adults' => $booking->num_adults,
            'num_children' => $booking->num_children,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $booking->refresh();
        $this->assertSame('2027-04-10', $booking->check_in_date->format('Y-m-d'));
        $this->assertSame(1, ActivityLog::where('action', 'booking.rescheduled')->count());
    }

    public function test_reschedule_to_unavailable_dates_is_rejected(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create(['conservation_area_id' => $area->id, 'total_units' => 1]);

        // The booking we're about to reschedule.
        $booking = Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-05-01',
            'check_out_date' => '2027-05-03',
            'status' => 'confirmed',
        ]);

        // Someone else already occupies the target dates.
        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-05-10',
            'check_out_date' => '2027-05-12',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($staff)->patch(route('admin.bookings.reschedule', $booking), [
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-05-10',
            'check_out_date' => '2027-05-12',
            'num_adults' => $booking->num_adults,
            'num_children' => $booking->num_children,
        ]);

        $response->assertSessionHasErrors('availability');
        $this->assertSame('2027-05-01', $booking->fresh()->check_in_date->format('Y-m-d'));
    }

    public function test_rescheduling_to_the_same_dates_the_booking_already_occupies_succeeds(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create(['conservation_area_id' => $area->id, 'total_units' => 1]);

        $booking = Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-06-01',
            'check_out_date' => '2027-06-03',
            'status' => 'confirmed',
        ]);

        // "Reschedule" to the exact dates it already has -- must not be
        // rejected as if it conflicted with itself.
        $response = $this->actingAs($staff)->patch(route('admin.bookings.reschedule', $booking), [
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-06-01',
            'check_out_date' => '2027-06-03',
            'num_adults' => $booking->num_adults,
            'num_children' => $booking->num_children,
        ]);

        $response->assertSessionDoesntHaveErrors();
    }
}
