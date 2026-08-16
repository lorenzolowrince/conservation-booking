<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingReactivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reactivating_a_cancelled_booking_is_rejected_if_slot_now_taken(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create(['conservation_area_id' => $area->id, 'total_units' => 1]);

        $cancelled = Booking::factory()->cancelled()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-07-01',
            'check_out_date' => '2027-07-03',
        ]);

        // Someone else took the slot after the first booking was cancelled.
        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-07-01',
            'check_out_date' => '2027-07-03',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.bookings.status', $cancelled), [
            'status' => 'confirmed',
        ]);

        $response->assertSessionHas('error');
        $this->assertSame('cancelled', $cancelled->fresh()->status);
    }

    public function test_reactivating_a_cancelled_booking_succeeds_if_slot_still_free(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create(['conservation_area_id' => $area->id, 'total_units' => 1]);

        $cancelled = Booking::factory()->cancelled()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-08-01',
            'check_out_date' => '2027-08-03',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.bookings.status', $cancelled), [
            'status' => 'confirmed',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame('confirmed', $cancelled->fresh()->status);
    }

    public function test_cancelling_requires_a_reason_and_stores_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $booking = Booking::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->patch(route('admin.bookings.status', $booking), [
            'status' => 'cancelled',
            'reason' => 'Customer requested refund',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $booking->refresh();
        $this->assertSame('cancelled', $booking->status);
        $this->assertSame('Customer requested refund', $booking->cancellation_reason);
    }
}
