<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\Package;
use App\Models\AccommodationType;
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

        $area = ConservationArea::findOrFail($validated['conservation_area_id']);

        // Calculate total
        $nights = \Carbon\Carbon::parse($validated['check_in_date'])
            ->diffInDays(\Carbon\Carbon::parse($validated['check_out_date']));
        $totalPax = $validated['num_adults'] + $validated['num_children'];
        $subtotal = 0;

        if ($validated['package_id']) {
            $package = Package::findOrFail($validated['package_id']);
            $isForeigner = strtolower($validated['contact_nationality']) !== 'malaysian';
            $pricePerPerson = $isForeigner && $package->price_per_person_foreigner
                ? $package->price_per_person_foreigner
                : $package->price_per_person;
            $subtotal = $pricePerPerson * $validated['num_adults'];
        } elseif ($validated['accommodation_type_id']) {
            $accType = AccommodationType::findOrFail($validated['accommodation_type_id']);
            $isForeigner = strtolower($validated['contact_nationality']) !== 'malaysian';
            $pricePerNight = $isForeigner && $accType->price_per_night_foreigner
                ? $accType->price_per_night_foreigner
                : $accType->price_per_night;
            $subtotal = $pricePerNight * max(1, $nights);
        }

        $tax = $subtotal * 0.06; // 6% SST
        $total = $subtotal + $tax;

        $booking = Booking::create([
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

        return redirect()->route('booking.confirmation', $booking->booking_ref)
            ->with('success', 'Booking submitted successfully!');
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
