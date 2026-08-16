<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\Booking;
use App\Models\ConservationArea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingStoreAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_when_accommodation_over_capacity(): void
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
            'check_in_date' => '2026-09-10',
            'check_out_date' => '2026-09-15',
            'status' => 'confirmed',
        ]);

        $response = $this->post(route('booking.store'), [
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'booking_type' => 'accommodation_only',
            'contact_name' => 'Jane Tan',
            'contact_email' => 'jane@example.com',
            'contact_phone' => '+60123456789',
            'contact_nationality' => 'Malaysian',
            'check_in_date' => '2026-09-11',
            'check_out_date' => '2026-09-13',
            'num_adults' => 2,
            'num_children' => 0,
        ]);

        $response->assertSessionHasErrors('availability');
        $this->assertSame(1, Booking::count()); // only the pre-seeded one
    }

    public function test_store_succeeds_when_available(): void
    {
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create([
            'conservation_area_id' => $area->id,
            'total_units' => 2,
            'capacity' => 10,
        ]);

        $response = $this->post(route('booking.store'), [
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'booking_type' => 'accommodation_only',
            'contact_name' => 'Jane Tan',
            'contact_email' => 'jane@example.com',
            'contact_phone' => '+60123456789',
            'contact_nationality' => 'Malaysian',
            'check_in_date' => '2026-09-11',
            'check_out_date' => '2026-09-13',
            'num_adults' => 2,
            'num_children' => 0,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(1, Booking::count());
    }
}
