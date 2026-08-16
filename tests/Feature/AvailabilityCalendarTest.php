<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\AvailabilityBlock;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_shows_correct_used_units_for_a_known_booking(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create([
            'conservation_area_id' => $area->id,
            'name' => 'Grid Test Chalet',
            'total_units' => 3,
            'capacity' => 2,
        ]);

        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-09-05',
            'check_out_date' => '2027-09-08',
            'num_adults' => 2,
            'num_children' => 0,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($staff)->get(route('admin.availability-calendar', [
            'view' => 'accommodations',
            'start' => '2027-09-05',
            'days' => 7,
        ]));

        $response->assertOk();
        // 1 unit used out of 3 on the covered nights.
        $response->assertSee('1/3');
        $response->assertSee('Grid Test Chalet');
    }

    public function test_calendar_shows_blocked_marker_for_blocked_dates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = ConservationArea::factory()->create();
        $acc = AccommodationType::factory()->create(['conservation_area_id' => $area->id, 'name' => 'Blocked Chalet']);

        AvailabilityBlock::create([
            'accommodation_type_id' => $acc->id,
            'start_date' => '2027-10-01',
            'end_date' => '2027-10-03',
            'reason' => 'Maintenance',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.availability-calendar', [
            'view' => 'accommodations',
            'start' => '2027-10-01',
            'days' => 7,
        ]));

        $response->assertOk();
        $response->assertSee('Blocked Chalet');
        $response->assertSee('title="Blocked"', false);
    }
}
