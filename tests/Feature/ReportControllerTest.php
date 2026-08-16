<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_accommodation_occupancy_matches_hand_computed_value(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = ConservationArea::factory()->create();
        // 2 units, 2 pax capacity each.
        $acc = AccommodationType::factory()->create([
            'conservation_area_id' => $area->id,
            'total_units' => 2,
            'capacity' => 2,
        ]);

        // A 4-night stay (Mar 1 inclusive through Mar 5 exclusive check-out
        // = 4 nights), 1 unit, fully inside the report range.
        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2027-03-01',
            'check_out_date' => '2027-03-05',
            'num_adults' => 2,
            'status' => 'confirmed',
        ]);

        // Report range: the whole of March 2027 (31 days).
        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'from' => '2027-03-01',
            'to' => '2027-03-31',
        ]));

        $response->assertOk();

        // booked_nights = 4 (nights) * 1 (unit) = 4
        // total_unit_nights = 2 units * 31 days = 62
        // occupancy = round(4/62*100, 1) = 6.5
        $response->assertSee('6.5%');
    }

    public function test_report_page_requires_admin_not_staff(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('admin.reports.index'));

        $response->assertForbidden();
    }

    public function test_csv_export_returns_csv_content_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.reports.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
