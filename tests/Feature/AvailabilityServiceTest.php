<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\AvailabilityBlock;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\Package;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private AvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AvailabilityService::class);
    }

    private function accommodation(int $totalUnits = 1, int $capacityPerUnit = 10): AccommodationType
    {
        $area = ConservationArea::factory()->create();

        return AccommodationType::factory()->create([
            'conservation_area_id' => $area->id,
            'total_units' => $totalUnits,
            'capacity' => $capacityPerUnit,
        ]);
    }

    private function existingBooking(AccommodationType $accommodation, string $checkIn, string $checkOut, string $status = 'pending', int $adults = 2): Booking
    {
        return Booking::factory()->create([
            'conservation_area_id' => $accommodation->conservation_area_id,
            'accommodation_type_id' => $accommodation->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'status' => $status,
            'num_adults' => $adults,
            'num_children' => 0,
        ]);
    }

    private function check(?AccommodationType $accommodation, string $checkIn, string $checkOut, int $adults = 2, ?Package $package = null)
    {
        return $this->service->checkAvailability([
            'package_id' => $package?->id,
            'accommodation_type_id' => $accommodation?->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'num_adults' => $adults,
            'num_children' => 0,
        ]);
    }

    // --- Date overlap boundary cases (single-unit accommodation) ---

    public function test_identical_dates_conflict(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-10', '2026-08-15');

        $result = $this->check($acc, '2026-08-10', '2026-08-15');

        $this->assertFalse($result->available);
    }

    public function test_new_booking_starting_inside_existing_range_conflicts(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-10', '2026-08-15');

        $result = $this->check($acc, '2026-08-12', '2026-08-18');

        $this->assertFalse($result->available);
    }

    public function test_new_booking_ending_inside_existing_range_conflicts(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-10', '2026-08-15');

        $result = $this->check($acc, '2026-08-05', '2026-08-12');

        $this->assertFalse($result->available);
    }

    public function test_existing_range_nested_inside_new_range_conflicts(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-12', '2026-08-14');

        $result = $this->check($acc, '2026-08-10', '2026-08-16');

        $this->assertFalse($result->available);
    }

    public function test_new_range_nested_inside_existing_range_conflicts(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-10', '2026-08-18');

        $result = $this->check($acc, '2026-08-12', '2026-08-14');

        $this->assertFalse($result->available);
    }

    public function test_back_to_back_dates_do_not_conflict(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-10', '2026-08-15');

        // New check-in is exactly the existing booking's check-out day.
        $result = $this->check($acc, '2026-08-15', '2026-08-18');

        $this->assertTrue($result->available);
    }

    public function test_dates_after_existing_booking_are_available(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-10', '2026-08-15');

        $result = $this->check($acc, '2026-08-16', '2026-08-20');

        $this->assertTrue($result->available);
    }

    // --- Package capacity ---

    public function test_package_daily_capacity_exceeded_is_unavailable(): void
    {
        $area = ConservationArea::factory()->create();
        $package = Package::factory()->create(['conservation_area_id' => $area->id, 'daily_capacity' => 5]);

        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'package_id' => $package->id,
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-12',
            'status' => 'confirmed',
            'num_adults' => 4,
            'num_children' => 0,
        ]);

        $result = $this->check(null, '2026-08-10', '2026-08-12', adults: 2, package: $package);

        $this->assertFalse($result->available);
        $this->assertSame(1, $result->package['remaining']);
    }

    public function test_package_daily_capacity_null_is_uncapped(): void
    {
        $area = ConservationArea::factory()->create();
        $package = Package::factory()->create(['conservation_area_id' => $area->id, 'daily_capacity' => null]);

        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'package_id' => $package->id,
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-12',
            'status' => 'confirmed',
            'num_adults' => 500,
            'num_children' => 0,
        ]);

        $result = $this->check(null, '2026-08-10', '2026-08-12', adults: 500, package: $package);

        $this->assertTrue($result->available);
    }

    // --- Accommodation unit capacity ---

    public function test_accommodation_units_exceeded_is_unavailable(): void
    {
        $acc = $this->accommodation(totalUnits: 2, capacityPerUnit: 10);
        $this->existingBooking($acc, '2026-08-10', '2026-08-12', adults: 2);
        $this->existingBooking($acc, '2026-08-11', '2026-08-13', adults: 2);

        $result = $this->check($acc, '2026-08-11', '2026-08-12', adults: 2);

        $this->assertFalse($result->available);
        $this->assertSame(0, $result->accommodation['remaining_units']);
    }

    public function test_accommodation_units_respects_multi_unit_parties(): void
    {
        // 3 units, capacity 2 pax each -> 6 pax total capacity.
        $acc = $this->accommodation(totalUnits: 3, capacityPerUnit: 2);

        // One existing booking of 4 pax already consumes ceil(4/2) = 2 units.
        $this->existingBooking($acc, '2026-08-10', '2026-08-15', adults: 4);

        // A new party of 3 needs ceil(3/2) = 2 more units -> 2 + 2 = 4 > 3 total.
        $unavailable = $this->check($acc, '2026-08-11', '2026-08-13', adults: 3);
        $this->assertFalse($unavailable->available);

        // A new party of 2 needs 1 more unit -> 2 + 1 = 3 <= 3 total.
        $available = $this->check($acc, '2026-08-11', '2026-08-13', adults: 2);
        $this->assertTrue($available->available);
    }

    public function test_cancelled_bookings_do_not_consume_capacity(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2026-08-10', '2026-08-15', status: 'cancelled');

        $result = $this->check($acc, '2026-08-10', '2026-08-15');

        $this->assertTrue($result->available);
    }

    public function test_completed_bookings_in_the_past_do_not_block_future_dates(): void
    {
        $acc = $this->accommodation(totalUnits: 1);
        $this->existingBooking($acc, '2020-01-01', '2020-01-05', status: 'completed');

        $result = $this->check($acc, '2026-08-10', '2026-08-15');

        $this->assertTrue($result->available);
    }

    // --- Blocked dates ---

    public function test_blocked_dates_make_package_unavailable(): void
    {
        $area = ConservationArea::factory()->create();
        $package = Package::factory()->create(['conservation_area_id' => $area->id]);
        $admin = User::factory()->create(['role' => 'admin']);

        AvailabilityBlock::create([
            'package_id' => $package->id,
            'start_date' => '2026-08-20',
            'end_date' => '2026-08-25',
            'reason' => 'Maintenance',
            'created_by' => $admin->id,
        ]);

        $result = $this->check(null, '2026-08-21', '2026-08-23', package: $package);

        $this->assertFalse($result->available);
        $this->assertTrue($result->blocked);
    }

    public function test_blocked_dates_make_accommodation_unavailable(): void
    {
        $acc = $this->accommodation(totalUnits: 5);
        $admin = User::factory()->create(['role' => 'admin']);

        AvailabilityBlock::create([
            'accommodation_type_id' => $acc->id,
            'start_date' => '2026-08-20',
            'end_date' => '2026-08-25',
            'reason' => 'Maintenance',
            'created_by' => $admin->id,
        ]);

        $result = $this->check($acc, '2026-08-20', '2026-08-21');

        $this->assertFalse($result->available);
        $this->assertTrue($result->blocked);
    }

    // --- Combined package + accommodation ---

    public function test_combined_package_and_accommodation_booking_checks_both(): void
    {
        $area = ConservationArea::factory()->create();
        $package = Package::factory()->create(['conservation_area_id' => $area->id, 'daily_capacity' => null]);
        $acc = AccommodationType::factory()->create([
            'conservation_area_id' => $area->id,
            'total_units' => 1,
            'capacity' => 10,
        ]);

        // Fill the only accommodation unit, package itself has no cap.
        Booking::factory()->create([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-15',
            'status' => 'confirmed',
        ]);

        $result = $this->service->checkAvailability([
            'package_id' => $package->id,
            'accommodation_type_id' => $acc->id,
            'check_in_date' => '2026-08-11',
            'check_out_date' => '2026-08-13',
            'num_adults' => 2,
            'num_children' => 0,
        ]);

        $this->assertFalse($result->available);
        $this->assertTrue($result->package['available']);
        $this->assertFalse($result->accommodation['available']);
    }
}
