<?php

namespace App\Services;

use App\Exceptions\AvailabilityException;
use App\Models\AccommodationType;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The one place a Booking row gets created. Used by both the public booking
 * form and admin/staff manual booking creation so the two paths can never
 * drift apart on pricing or availability enforcement.
 */
class BookingCreationService
{
    public function __construct(private AvailabilityService $availability)
    {
    }

    /**
     * Create a booking, enforcing availability. Throws AvailabilityException
     * if the requested package/accommodation/dates aren't available.
     */
    public function create(array $data): Booking
    {
        $availabilityParams = $this->availabilityParams($data);

        $precheck = $this->availability->checkAvailability($availabilityParams);
        if (! $precheck->available) {
            throw new AvailabilityException($precheck->message);
        }

        return $this->persist($data, $availabilityParams);
    }

    /**
     * Admin-only escape hatch: creates the booking even if it's unavailable,
     * recording exactly what was overridden and why. Callers MUST already
     * have verified the acting user is authorized (role:admin) -- this
     * method does not check permissions itself.
     */
    public function createWithOverride(array $data, string $overrideReason): Booking
    {
        $availabilityParams = $this->availabilityParams($data);
        $precheck = $this->availability->checkAvailability($availabilityParams);

        $booking = $this->persist($data, $availabilityParams, forceCreate: true);

        if (! $precheck->available) {
            ActivityLog::record(
                ActivityLog::ACTION_BOOKING_OVERRIDE,
                $booking,
                "Booking {$booking->booking_ref} created despite unavailable inventory (overridden).",
                changes: ['availability_at_override' => $precheck->toArray()],
                reason: $overrideReason,
            );
        }

        return $booking;
    }

    private function persist(array $data, array $availabilityParams, bool $forceCreate = false): Booking
    {
        $area = ConservationArea::findOrFail($data['conservation_area_id']);
        [$subtotal, $tax, $total] = $this->price($data);

        return DB::transaction(function () use ($data, $availabilityParams, $area, $subtotal, $tax, $total, $forceCreate) {
            // Authoritative recheck: locks the package/accommodation-type
            // row(s) first, so a concurrent submission for the same resource
            // is forced to wait and then sees our committed booking.
            $recheck = $this->availability->checkAvailabilityForUpdate($availabilityParams);
            if (! $recheck->available && ! $forceCreate) {
                throw new AvailabilityException($recheck->message);
            }

            $booking = Booking::create([
                'booking_ref' => Booking::generateRef($area->code),
                'user_id' => $data['user_id'] ?? auth()->id(),
                'assigned_to' => $data['assigned_to'] ?? null,
                'conservation_area_id' => $data['conservation_area_id'],
                'package_id' => $data['package_id'] ?? null,
                'accommodation_type_id' => $data['accommodation_type_id'] ?? null,
                'contact_name' => $data['contact_name'],
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'contact_nationality' => $data['contact_nationality'],
                'booking_type' => $data['booking_type'],
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'num_adults' => $data['num_adults'],
                'num_children' => $data['num_children'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $total,
                'status' => $data['status'] ?? 'pending',
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'special_requests' => $data['special_requests'] ?? null,
            ]);

            ActivityLog::record(
                ActivityLog::ACTION_BOOKING_CREATED,
                $booking,
                "Booking {$booking->booking_ref} created."
            );

            return $booking;
        });
    }

    private function price(array $data): array
    {
        $nights = Carbon::parse($data['check_in_date'])->diffInDays(Carbon::parse($data['check_out_date']));
        $subtotal = 0;

        if ($data['package_id'] ?? null) {
            $package = Package::findOrFail($data['package_id']);
            $isForeigner = strtolower($data['contact_nationality']) !== 'malaysian';
            $pricePerPerson = $isForeigner && $package->price_per_person_foreigner
                ? $package->price_per_person_foreigner
                : $package->price_per_person;
            $subtotal = $pricePerPerson * $data['num_adults'];
        } elseif ($data['accommodation_type_id'] ?? null) {
            $accType = AccommodationType::findOrFail($data['accommodation_type_id']);
            $isForeigner = strtolower($data['contact_nationality']) !== 'malaysian';
            $pricePerNight = $isForeigner && $accType->price_per_night_foreigner
                ? $accType->price_per_night_foreigner
                : $accType->price_per_night;
            $subtotal = $pricePerNight * max(1, $nights);
        }

        $tax = $subtotal * 0.06; // 6% SST
        $total = $subtotal + $tax;

        return [$subtotal, $tax, $total];
    }

    private function availabilityParams(array $data): array
    {
        return [
            'package_id' => $data['package_id'] ?? null,
            'accommodation_type_id' => $data['accommodation_type_id'] ?? null,
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'num_adults' => $data['num_adults'],
            'num_children' => $data['num_children'],
        ];
    }
}
