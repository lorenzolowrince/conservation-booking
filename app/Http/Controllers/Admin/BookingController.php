<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\BookingsImport;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

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

    public function importForm()
    {
        return view('admin.bookings.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new BookingsImport();
        Excel::import($import, $request->file('file'));

        return redirect()->route('admin.bookings.import.form')->with('import_result', [
            'imported' => $import->imported,
            'errors' => $import->errors,
        ]);
    }

    public function downloadTemplate()
    {
        $headers = [
            'booking_ref', 'conservation_area', 'package', 'accommodation_type',
            'contact_name', 'contact_email', 'contact_phone', 'contact_nationality',
            'booking_type', 'check_in_date', 'check_out_date', 'num_adults', 'num_children',
            'subtotal', 'tax', 'total_amount', 'status', 'payment_status',
            'payment_method', 'special_requests',
        ];

        $example = [
            '', 'DVCA', '', '', 'Jane Tan', 'jane@example.com', '+60123456789', 'Malaysian',
            'package', '2026-09-01', '2026-09-03', 2, 0,
            450, 0, 450, 'pending', 'unpaid', '', '',
        ];

        $csv = implode(',', $headers) . "\n" . implode(',', array_map(
            fn ($v) => str_contains((string) $v, ',') ? '"' . $v . '"' : $v,
            $example
        )) . "\n";

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-import-template.csv"',
        ]);
    }
}
