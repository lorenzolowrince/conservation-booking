<?php

namespace App\Http\Controllers;

use App\Exceptions\AvailabilityException;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\Package;
use App\Models\AccommodationType;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $areas = ConservationArea::active()->get();
        $selectedArea = null;
        $selectedPackage = null;

        if ($request->has('area')) {
            $selectedArea = ConservationArea::where('slug', $request->area)
                ->with(['packages' => fn($q) => $q->where('is_active', true),
                        'accommodationTypes' => fn($q) => $q->where('is_active', true)])
                ->first();
        }

        if ($request->has('package')) {
            $selectedPackage = Package::find($request->package);
        }

        return view('booking.create', compact('areas', 'selectedArea', 'selectedPackage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conservation_area_id' => 'required|exists:conservation_areas,id',
            'package_id' => 'nullable|exists:packages,id',
            'accommodation_type_id' => 'nullable|exists:accommodation_types,id',
            'booking_type' => 'required|in:package,accommodation_only,day_trip',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:30',
            'contact_nationality' => 'required|string|max:100',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_adults' => 'required|integer|min:1|max:20',
            'num_children' => 'required|integer|min:0|max:20',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $area = ConservationArea::findOrFail($validated['conservation_area_id']);

        $availabilityParams = [
            'package_id' => $validated['package_id'] ?? null,
            'accommodation_type_id' => $validated['accommodation_type_id'] ?? null,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'num_adults' => $validated['num_adults'],
            'num_children' => $validated['num_children'],
        ];

        $availabilityService = app(AvailabilityService::class);

        // Fast, unlocked pre-check: fail fast with a friendly message before
        // doing any pricing work. Not authoritative — see the in-transaction
        // recheck below, which is what actually prevents double-booking.
        $precheck = $availabilityService->checkAvailability($availabilityParams);
        if (! $precheck->available) {
            return back()->withInput()->withErrors(['availability' => $precheck->message]);
        }

        // Calculate total
        $nights = \Carbon\Carbon::parse($validated['check_in_date'])
            ->diffInDays(\Carbon\Carbon::parse($validated['check_out_date']));
        $totalPax = $validated['num_adults'] + $validated['num_children'];
        $subtotal = 0;

        if ($validated['package_id'] ?? null) {
            $package = Package::findOrFail($validated['package_id']);
            $isForeigner = strtolower($validated['contact_nationality']) !== 'malaysian';
            $pricePerPerson = $isForeigner && $package->price_per_person_foreigner
                ? $package->price_per_person_foreigner
                : $package->price_per_person;
            $subtotal = $pricePerPerson * $validated['num_adults'];
        } elseif ($validated['accommodation_type_id'] ?? null) {
            $accType = AccommodationType::findOrFail($validated['accommodation_type_id']);
            $isForeigner = strtolower($validated['contact_nationality']) !== 'malaysian';
            $pricePerNight = $isForeigner && $accType->price_per_night_foreigner
                ? $accType->price_per_night_foreigner
                : $accType->price_per_night;
            $subtotal = $pricePerNight * max(1, $nights);
        }

        $tax = $subtotal * 0.06; // 6% SST
        $total = $subtotal + $tax;

        try {
            $booking = DB::transaction(function () use ($availabilityService, $availabilityParams, $area, $validated, $subtotal, $tax, $total) {
                // Authoritative recheck: locks the package/accommodation-type
                // row(s) first, so a concurrent submission for the same
                // resource is forced to wait and then sees our committed
                // booking. This is what actually closes the race window.
                $recheck = $availabilityService->checkAvailabilityForUpdate($availabilityParams);
                if (! $recheck->available) {
                    throw new AvailabilityException($recheck->message);
                }

                return Booking::create([
                    'booking_ref' => Booking::generateRef($area->code),
                    'user_id' => auth()->id(),
                    'conservation_area_id' => $validated['conservation_area_id'],
                    'package_id' => $validated['package_id'] ?? null,
                    'accommodation_type_id' => $validated['accommodation_type_id'] ?? null,
                    'contact_name' => $validated['contact_name'],
                    'contact_email' => $validated['contact_email'],
                    'contact_phone' => $validated['contact_phone'],
                    'contact_nationality' => $validated['contact_nationality'],
                    'booking_type' => $validated['booking_type'],
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'num_adults' => $validated['num_adults'],
                    'num_children' => $validated['num_children'],
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total_amount' => $total,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'special_requests' => $validated['special_requests'] ?? null,
                ]);
            });
        } catch (AvailabilityException $e) {
            return back()->withInput()->withErrors(['availability' => $e->getMessage()]);
        }

        return redirect()->route('booking.confirmation', $booking->booking_ref)
            ->with('success', 'Booking submitted successfully!');
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'nullable|exists:packages,id',
            'accommodation_type_id' => 'nullable|exists:accommodation_types,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_adults' => 'required|integer|min:1',
            'num_children' => 'nullable|integer|min:0',
        ]);

        $result = app(AvailabilityService::class)->checkAvailability($validated);

        return response()->json($result->toArray());
    }

    public function confirmation(string $ref)
    {
        $booking = Booking::where('booking_ref', $ref)
            ->with(['conservationArea', 'package', 'accommodationType'])
            ->firstOrFail();

        return view('booking.confirmation', compact('booking'));
    }

    public function track(Request $request)
    {
        $booking = null;
        if ($request->filled('ref')) {
            $booking = Booking::where('booking_ref', $request->ref)
                ->with(['conservationArea', 'package', 'accommodationType'])
                ->first();
        }
        return view('booking.track', compact('booking'));
    }

    public function myBookings()
    {
        $bookings = auth()->user()->bookings()
            ->with('conservationArea')
            ->latest()
            ->paginate(10);
        return view('booking.my-bookings', compact('bookings'));
    }
}
