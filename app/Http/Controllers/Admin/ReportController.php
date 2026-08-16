<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccommodationType;
use App\Models\Booking;
use App\Models\Package;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $summary = [
            'total' => Booking::whereBetween('created_at', [$from, $to->copy()->endOfDay()])->count(),
            'pending' => Booking::whereBetween('created_at', [$from, $to->copy()->endOfDay()])->where('status', 'pending')->count(),
            'confirmed' => Booking::whereBetween('created_at', [$from, $to->copy()->endOfDay()])->where('status', 'confirmed')->count(),
            'cancelled' => Booking::whereBetween('created_at', [$from, $to->copy()->endOfDay()])->where('status', 'cancelled')->count(),
            'completed' => Booking::whereBetween('created_at', [$from, $to->copy()->endOfDay()])->where('status', 'completed')->count(),
        ];

        $packageUtilization = $this->packageUtilization($from, $to);
        $accommodationUtilization = $this->accommodationUtilization($from, $to);

        return view('admin.reports.index', compact('summary', 'packageUtilization', 'accommodationUtilization', 'from', 'to'));
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);
        $accommodationUtilization = $this->accommodationUtilization($from, $to);
        $packageUtilization = $this->packageUtilization($from, $to);

        $lines = ['Report period,' . $from->toDateString() . ' to ' . $to->toDateString()];
        $lines[] = '';
        $lines[] = 'Accommodation,Area,Total Units,Booked Nights,Occupancy %';
        foreach ($accommodationUtilization as $row) {
            $lines[] = implode(',', [
                '"' . $row['resource']->name . '"',
                '"' . $row['resource']->conservationArea->short_name . '"',
                $row['total_units'],
                $row['booked_nights'],
                $row['occupancy'],
            ]);
        }
        $lines[] = '';
        $lines[] = 'Package,Area,Bookings,Total Pax,Daily Capacity,Avg Daily Utilization %';
        foreach ($packageUtilization as $row) {
            $lines[] = implode(',', [
                '"' . $row['resource']->name . '"',
                '"' . $row['resource']->conservationArea->short_name . '"',
                $row['bookings'],
                $row['total_pax'],
                $row['capacity'] ?? 'Uncapped',
                $row['avg_utilization'] ?? '—',
            ]);
        }

        return Response::make(implode("\n", $lines) . "\n", 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="utilization-report-' . $from->toDateString() . '-to-' . $to->toDateString() . '.csv"',
        ]);
    }

    private function range(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to) : now()->endOfMonth();

        return [$from->startOfDay(), $to->startOfDay()];
    }

    private function packageUtilization(Carbon $from, Carbon $to): array
    {
        $packages = Package::with('conservationArea')->where('is_active', true)->orderBy('name')->get();
        $days = $from->diffInDays($to) + 1;

        $bookings = Booking::whereIn('package_id', $packages->pluck('id'))
            ->whereIn('status', array_merge(Booking::ACTIVE_STATUSES, ['completed']))
            ->whereDate('check_out_date', '>', $from)
            ->whereDate('check_in_date', '<=', $to)
            ->get(['package_id', 'check_in_date', 'check_out_date', 'num_adults', 'num_children']);

        return $packages->map(function (Package $pkg) use ($bookings, $from, $to, $days) {
            $pkgBookings = $bookings->where('package_id', $pkg->id);

            $avgUtilization = null;
            if ($pkg->daily_capacity !== null && $days > 0) {
                $dailyPct = collect(range(0, $days - 1))->map(function ($i) use ($from, $pkgBookings, $pkg) {
                    $date = $from->copy()->addDays($i);
                    $used = $pkgBookings
                        ->filter(fn ($b) => $b->check_in_date->lte($date) && $b->check_out_date->gt($date))
                        ->sum(fn ($b) => $b->num_adults + $b->num_children);

                    return min(100, ($used / $pkg->daily_capacity) * 100);
                });
                $avgUtilization = round($dailyPct->avg(), 1);
            }

            return [
                'resource' => $pkg,
                'bookings' => $pkgBookings->count(),
                'total_pax' => $pkgBookings->sum(fn ($b) => $b->num_adults + $b->num_children),
                'capacity' => $pkg->daily_capacity,
                'avg_utilization' => $avgUtilization,
            ];
        })->all();
    }

    private function accommodationUtilization(Carbon $from, Carbon $to): array
    {
        $accommodations = AccommodationType::with('conservationArea')->where('is_active', true)->orderBy('name')->get();

        $bookings = Booking::whereIn('accommodation_type_id', $accommodations->pluck('id'))
            ->whereIn('status', array_merge(Booking::ACTIVE_STATUSES, ['completed']))
            ->whereDate('check_out_date', '>', $from)
            ->whereDate('check_in_date', '<=', $to)
            ->get(['accommodation_type_id', 'check_in_date', 'check_out_date', 'num_adults', 'num_children']);

        $totalDays = $from->diffInDays($to) + 1;

        return $accommodations->map(function (AccommodationType $acc) use ($bookings, $from, $to, $totalDays) {
            $perUnit = max(1, $acc->capacity);
            $accBookings = $bookings->where('accommodation_type_id', $acc->id);

            $bookedNights = $accBookings->sum(function ($b) use ($from, $to, $perUnit) {
                $overlapStart = $b->check_in_date->greaterThan($from) ? $b->check_in_date : $from;
                $overlapEndExclusive = $b->check_out_date->lessThan($to->copy()->addDay()) ? $b->check_out_date : $to->copy()->addDay();
                $nights = max(0, $overlapStart->diffInDays($overlapEndExclusive));

                return $nights * AvailabilityService::unitsRequiredFor($b->num_adults + $b->num_children, $perUnit);
            });

            $totalUnitNights = $acc->total_units * $totalDays;
            $occupancy = $totalUnitNights > 0 ? round(($bookedNights / $totalUnitNights) * 100, 1) : 0;

            return [
                'resource' => $acc,
                'total_units' => $acc->total_units,
                'booked_nights' => $bookedNights,
                'occupancy' => $occupancy,
            ];
        })->all();
    }
}
