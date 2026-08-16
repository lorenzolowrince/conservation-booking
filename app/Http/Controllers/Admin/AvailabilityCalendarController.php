<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccommodationType;
use App\Models\AvailabilityBlock;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\Package;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityCalendarController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'accommodations') === 'packages' ? 'packages' : 'accommodations';
        $start = Carbon::parse($request->input('start') ?: now()->toDateString())->startOfDay();
        $days = min(21, max(7, (int) $request->input('days', 14)));
        $dates = collect(range(0, $days - 1))->map(fn ($i) => $start->copy()->addDays($i));

        $areas = ConservationArea::active()->get();
        $areaId = $request->input('area_id');

        $rows = $view === 'packages'
            ? $this->packageRows($dates, $start, $days, $areaId)
            : $this->accommodationRows($dates, $start, $days, $areaId);

        return view('admin.availability-calendar.index', [
            'view' => $view,
            'dates' => $dates,
            'rows' => $rows,
            'areas' => $areas,
            'areaId' => $areaId,
            'start' => $start,
            'days' => $days,
        ]);
    }

    private function accommodationRows($dates, Carbon $start, int $days, ?string $areaId): array
    {
        $accommodations = AccommodationType::with('conservationArea')
            ->where('is_active', true)
            ->when($areaId, fn ($q) => $q->where('conservation_area_id', $areaId))
            ->orderBy('conservation_area_id')->orderBy('name')
            ->get();

        $rangeEnd = $start->copy()->addDays($days);

        $bookings = Booking::whereIn('accommodation_type_id', $accommodations->pluck('id'))
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->whereDate('check_out_date', '>', $start)
            ->whereDate('check_in_date', '<', $rangeEnd)
            ->get(['accommodation_type_id', 'check_in_date', 'check_out_date', 'num_adults', 'num_children']);

        $blocks = AvailabilityBlock::whereIn('accommodation_type_id', $accommodations->pluck('id'))
            ->whereDate('start_date', '<', $rangeEnd)
            ->whereDate('end_date', '>=', $start)
            ->get(['accommodation_type_id', 'start_date', 'end_date']);

        return $accommodations->map(function (AccommodationType $acc) use ($dates, $bookings, $blocks) {
            $perUnit = max(1, $acc->capacity);
            $accBookings = $bookings->where('accommodation_type_id', $acc->id);
            $accBlocks = $blocks->where('accommodation_type_id', $acc->id);

            $cells = $dates->map(function (Carbon $date) use ($acc, $perUnit, $accBookings, $accBlocks) {
                $blocked = $accBlocks->contains(fn ($b) => $b->start_date->lte($date) && $b->end_date->gte($date));

                $used = $accBookings
                    ->filter(fn ($b) => $b->check_in_date->lte($date) && $b->check_out_date->gt($date))
                    ->sum(fn ($b) => AvailabilityService::unitsRequiredFor($b->num_adults + $b->num_children, $perUnit));

                return [
                    'date' => $date,
                    'blocked' => $blocked,
                    'used' => $used,
                    'total' => $acc->total_units,
                    'remaining' => max(0, $acc->total_units - $used),
                ];
            });

            return ['resource' => $acc, 'cells' => $cells];
        })->all();
    }

    private function packageRows($dates, Carbon $start, int $days, ?string $areaId): array
    {
        $packages = Package::with('conservationArea')
            ->where('is_active', true)
            ->when($areaId, fn ($q) => $q->where('conservation_area_id', $areaId))
            ->orderBy('conservation_area_id')->orderBy('name')
            ->get();

        $rangeEnd = $start->copy()->addDays($days);

        $bookings = Booking::whereIn('package_id', $packages->pluck('id'))
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->whereDate('check_out_date', '>', $start)
            ->whereDate('check_in_date', '<', $rangeEnd)
            ->get(['package_id', 'check_in_date', 'check_out_date', 'num_adults', 'num_children']);

        $blocks = AvailabilityBlock::whereIn('package_id', $packages->pluck('id'))
            ->whereDate('start_date', '<', $rangeEnd)
            ->whereDate('end_date', '>=', $start)
            ->get(['package_id', 'start_date', 'end_date']);

        return $packages->map(function (Package $pkg) use ($dates, $bookings, $blocks) {
            $pkgBookings = $bookings->where('package_id', $pkg->id);
            $pkgBlocks = $blocks->where('package_id', $pkg->id);

            $cells = $dates->map(function (Carbon $date) use ($pkg, $pkgBookings, $pkgBlocks) {
                $blocked = $pkgBlocks->contains(fn ($b) => $b->start_date->lte($date) && $b->end_date->gte($date));

                $used = $pkgBookings
                    ->filter(fn ($b) => $b->check_in_date->lte($date) && $b->check_out_date->gt($date))
                    ->sum(fn ($b) => $b->num_adults + $b->num_children);

                return [
                    'date' => $date,
                    'blocked' => $blocked,
                    'used' => $used,
                    'total' => $pkg->daily_capacity,
                    'remaining' => $pkg->daily_capacity === null ? null : max(0, $pkg->daily_capacity - $used),
                ];
            });

            return ['resource' => $pkg, 'cells' => $cells];
        })->all();
    }
}
