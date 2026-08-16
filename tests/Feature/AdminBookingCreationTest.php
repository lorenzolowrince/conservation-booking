<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingCreationTest extends TestCase
{
    use RefreshDatabase;

    private function fullUnitAccommodation(): AccommodationType
    {
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create([
            'conservation_area_id' => $area->id,
            'total_units' => 1,
            'capacity' => 10,
        ]);

        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-02-10',
            'check_out_date' => '2027-02-15',
            'status' => 'confirmed',
        ]);

        return $acc;
    }

    private function payload(AccommodationType $acc, array $overrides = []): array
    {
        return array_merge([
            'conservation_area_id' => $acc->conservation_area_id,
            'accommodation_type_id' => $acc->id,
            'booking_type' => 'accommodation_only',
            'contact_name' => 'Jane Tan',
            'contact_email' => 'jane@example.com',
            'contact_phone' => '+60123456789',
            'contact_nationality' => 'Malaysian',
            'check_in_date' => '2027-02-11',
            'check_out_date' => '2027-02-13',
            'num_adults' => 2,
        ], $overrides);
    }

    public function test_staff_can_create_a_manual_booking(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create(['conservation_area_id' => $area->id, 'total_units' => 3]);

        $response = $this->actingAs($staff)->post(route('admin.bookings.store'), $this->payload($acc, [
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-03-01',
            'check_out_date' => '2027-03-03',
        ]));

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(1, Booking::count());
    }

    public function test_manual_booking_rejected_under_same_capacity_rule_as_public_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $acc = $this->fullUnitAccommodation();

        $response = $this->actingAs($admin)->post(route('admin.bookings.store'), $this->payload($acc));

        $response->assertSessionHasErrors('availability');
        $this->assertSame(1, Booking::count()); // only the pre-seeded one
    }

    public function test_staff_cannot_use_override_even_if_submitted_directly(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $acc = $this->fullUnitAccommodation();

        $response = $this->actingAs($staff)->post(route('admin.bookings.store'), $this->payload($acc, [
            'override' => '1',
            'override_reason' => 'trying to bypass',
        ]));

        $response->assertForbidden();
        $this->assertSame(1, Booking::count());
    }

    public function test_admin_override_creates_booking_and_logs_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $acc = $this->fullUnitAccommodation();

        $response = $this->actingAs($admin)->post(route('admin.bookings.store'), $this->payload($acc, [
            'override' => '1',
            'override_reason' => 'VIP arrangement confirmed by phone',
        ]));

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(2, Booking::count());

        $log = ActivityLog::where('action', 'booking.override')->first();
        $this->assertNotNull($log);
        $this->assertSame('VIP arrangement confirmed by phone', $log->reason);
    }

    public function test_admin_without_override_flag_still_gets_rejected_normally(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $acc = $this->fullUnitAccommodation();

        $response = $this->actingAs($admin)->post(route('admin.bookings.store'), $this->payload($acc));

        $response->assertSessionHasErrors('availability');
        $this->assertSame(1, Booking::count());
        $this->assertSame(0, ActivityLog::where('action', 'booking.override')->count());
    }
}
