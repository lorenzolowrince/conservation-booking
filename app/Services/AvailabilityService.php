<?php

namespace App\Services;

use App\Models\AccommodationType;
use App\Models\AvailabilityBlock;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for "can this booking be accepted?" Used by both
 * the public booking form (pre-check + live endpoint) and, inside a DB
 * transaction, the authoritative recheck immediately before a booking row
 * is created.
 */
class AvailabilityService
{
    /**
     * Read-only check. No locking — safe to call outside a transaction for
     * UX purposes (pre-check, live availability endpoint). NOT authoritative
     * on its own; see checkAvailabilityForUpdate().
     */
    public function checkAvailability(array $params): AvailabilityResult
    {
        return $this->evaluate($params);
    }

    /**
     * Authoritative check. MUST be called inside an open DB::transaction().
     * Locks the parent package/accommodation-type row(s) first so that two
     * concurrent transactions competing for the same resource are forced to
     * serialize — the second one only proceeds after the first commits, and
     * then sees accurate, committed booking counts.
     */
    public function checkAvailabilityForUpdate(array $params): AvailabilityResult
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('checkAvailabilityForUpdate() must be called inside an open DB::transaction().');
        }

        $this->lockResources($params['package_id'] ?? null, $params['accommodation_type_id'] ?? null);

        return $this->evaluate($params);
    }

    /**
     * Acquire a mutex on the parent resource row(s) via a no-op UPDATE. This
     * is a real row-level exclusive lock on Postgres, and forces SQLite to
     * take its write-lock immediately instead of deferring to commit — one
     * mutex primitive that works the same way on both engines. Package is
     * always locked before accommodation type (fixed order) to avoid
     * deadlocking against a transaction that needs both in the same booking.
     */
    private function lockResources(?int $packageId, ?int $accommodationTypeId): void
    {
        if ($packageId) {
            DB::statement('UPDATE packages SET id = id WHERE id = ?', [$packageId]);
        }

        if ($accommodationTypeId) {
            DB::statement('UPDATE accommodation_types SET id = id WHERE id = ?', [$accommodationTypeId]);
        }
    }

    private function evaluate(array $params): AvailabilityResult
    {
        $packageId = $params['package_id'] ?? null;
        $accommodationTypeId = $params['accommodation_type_id'] ?? null;
        $checkIn = $params['check_in_date'];
        $checkOut = $params['check_out_date'];
        $requestedPax = (int) ($params['num_adults'] ?? 0) + (int) ($params['num_children'] ?? 0);

        if ($this->isBlocked($packageId, $accommodationTypeId, $checkIn, $checkOut)) {
            return new AvailabilityResult(
                available: false,
                message: 'These dates are not available for booking.',
                blocked: true,
            );
        }

        $packageBreakdown = null;
        $accommodationBreakdown = null;

        if ($packageId) {
            $packageBreakdown = $this->packageCapacity($packageId, $checkIn, $checkOut, $requestedPax);
        }

        if ($accommodationTypeId) {
            $accommodationBreakdown = $this->accommodationCapacity($accommodationTypeId, $checkIn, $checkOut, $requestedPax);
        }

        $available = ($packageBreakdown === null || $packageBreakdown['available'])
            && ($accommodationBreakdown === null || $accommodationBreakdown['available']);

        $message = null;
        if (! $available) {
            $message = match (true) {
                $packageBreakdown !== null && ! $packageBreakdown['available']
                    => 'This package is not available for the selected date — only ' . $packageBreakdown['remaining'] . ' place(s) remaining.',
                $accommodationBreakdown !== null && ! $accommodationBreakdown['available']
                    => 'This accommodation is not available for the selected dates — only ' . $accommodationBreakdown['remaining_units'] . ' unit(s) remaining.',
                default => 'This booking is not available for the selected dates.',
            };
        }

        return new AvailabilityResult(
            available: $available,
            message: $message,
            package: $packageBreakdown,
            accommodation: $accommodationBreakdown,
        );
    }

    private function isBlocked(?int $packageId, ?int $accommodationTypeId, $checkIn, $checkOut): bool
    {
        if (! $packageId && ! $accommodationTypeId) {
            return false;
        }

        return AvailabilityBlock::query()
            ->where(function ($query) use ($packageId, $accommodationTypeId) {
                if ($packageId) {
                    $query->orWhere('package_id', $packageId);
                }
                if ($accommodationTypeId) {
                    $query->orWhere('accommodation_type_id', $accommodationTypeId);
                }
            })
            // block dates are inclusive; booking dates are half-open [check_in, check_out).
            // whereDate(), not where() -- see the comment on Booking::scopeActiveOverlap().
            ->whereDate('start_date', '<', $checkOut)
            ->whereDate('end_date', '>=', $checkIn)
            ->exists();
    }

    private function packageCapacity(int $packageId, $checkIn, $checkOut, int $requestedPax): array
    {
        $package = Package::findOrFail($packageId);

        if ($package->daily_capacity === null) {
            return ['capacity' => null, 'used' => 0, 'remaining' => null, 'available' => true];
        }

        $used = (int) Booking::query()
            ->activeOverlap($checkIn, $checkOut)
            ->where('package_id', $packageId)
            ->selectRaw('COALESCE(SUM(num_adults + num_children), 0) as total')
            ->value('total');

        $remaining = max(0, $package->daily_capacity - $used);

        return [
            'capacity' => $package->daily_capacity,
            'used' => $used,
            'remaining' => $remaining,
            'available' => ($used + $requestedPax) <= $package->daily_capacity,
        ];
    }

    private function accommodationCapacity(int $accommodationTypeId, $checkIn, $checkOut, int $requestedPax): array
    {
        $accommodationType = AccommodationType::findOrFail($accommodationTypeId);
        $perUnitCapacity = max(1, $accommodationType->capacity);

        $unitsRequired = (int) ceil($requestedPax / $perUnitCapacity);

        $usedUnits = Booking::query()
            ->activeOverlap($checkIn, $checkOut)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->get(['num_adults', 'num_children'])
            ->sum(fn (Booking $booking) => (int) ceil(($booking->num_adults + $booking->num_children) / $perUnitCapacity));

        $remainingUnits = max(0, $accommodationType->total_units - $usedUnits);

        return [
            'total_units' => $accommodationType->total_units,
            'used_units' => $usedUnits,
            'units_required' => $unitsRequired,
            'remaining_units' => $remainingUnits,
            'available' => ($usedUnits + $unitsRequired) <= $accommodationType->total_units,
        ];
    }
}
