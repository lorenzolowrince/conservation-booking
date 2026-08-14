<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['conservationArea', 'package'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('area')) {
            $query->where('conservation_area_id', $request->area);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_ref', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(20)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['conservationArea', 'package', 'accommodationType', 'guests', 'user']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);

        $booking->status = $request->status;
        if ($request->status === 'confirmed') {
            $booking->confirmed_at = now();
        } elseif ($request->status === 'cancelled') {
            $booking->cancelled_at = now();
        }
        $booking->save();

        return back()->with('success', "Booking #{$booking->booking_ref} status updated to {$request->status}.");
    }

    public function updatePayment(Request $request, Booking $booking)
    {
        $request->validate(['payment_status' => 'required|in:unpaid,paid,refunded']);

        $booking->payment_status = $request->payment_status;
        $booking->save();

        return back()->with('success', "Payment status updated.");
    }

    public function addNote(Request $request, Booking $booking)
    {
        $request->validate(['admin_notes' => 'required|string|max:2000']);
        $booking->admin_notes = $request->admin_notes;
        $booking->save();

        return back()->with('success', 'Note saved.');
    }
}
