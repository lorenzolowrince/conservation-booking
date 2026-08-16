<?php

namespace App\Http\Controllers;

use App\Exceptions\AvailabilityException;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\Package;
use App\Services\AvailabilityService;
use App\Services\BookingCreationService;
use Illuminate\Http\Request;

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

        try {
            $booking = app(BookingCreationService::class)->create($validated);
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
