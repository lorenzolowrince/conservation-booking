<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AvailabilityException;
use App\Http\Controllers\Controller;
use App\Imports\BookingsImport;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingCreationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['conservationArea', 'package', 'assignedTo'])
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

    public function create()
    {
        $areas = ConservationArea::active()
            ->with(['packages' => fn ($q) => $q->where('is_active', true),
                    'accommodationTypes' => fn ($q) => $q->where('is_active', true)])
            ->get();
        $staff = User::whereIn('role', User::ADMIN_PANEL_ROLES)->orderBy('name')->get();

        return view('admin.bookings.create', compact('areas', 'staff'));
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
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_adults' => 'required|integer|min:1|max:20',
            'num_children' => 'nullable|integer|min:0|max:20',
            'special_requests' => 'nullable|string|max:1000',
            'assigned_to' => 'nullable|exists:users,id',
            'override' => 'nullable|boolean',
            'override_reason' => 'required_if:override,1|nullable|string|max:500',
        ]);

        $validated['num_children'] = $validated['num_children'] ?? 0;

        $wantsOverride = $request->boolean('override');
        if ($wantsOverride && ! $request->user()->isAdmin()) {
            abort(403, 'Only Admin and above can override availability.');
        }

        $service = app(BookingCreationService::class);

        try {
            $booking = $wantsOverride
                ? $service->createWithOverride($validated, $validated['override_reason'])
                : $service->create($validated);
        } catch (AvailabilityException $e) {
            return back()->withInput()->withErrors(['availability' => $e->getMessage()]);
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', "Booking {$booking->booking_ref} created.");
    }

    public function show(Booking $booking)
    {
        $booking->load(['conservationArea', 'package', 'accommodationType', 'guests', 'user', 'assignedTo']);

        $timeline = ActivityLog::where('subject_type', Booking::class)
            ->where('subject_id', $booking->id)
            ->latest('created_at')
            ->get();

        $staff = User::whereIn('role', User::ADMIN_PANEL_ROLES)->orderBy('name')->get();

        return view('admin.bookings.show', compact('booking', 'timeline', 'staff'));
    }

    public function editReschedule(Booking $booking)
    {
        $areas = ConservationArea::active()
            ->with(['packages' => fn ($q) => $q->where('is_active', true),
                    'accommodationTypes' => fn ($q) => $q->where('is_active', true)])
            ->get();

        return view('admin.bookings.reschedule', compact('booking', 'areas'));
    }

    public function reschedule(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'package_id' => 'nullable|exists:packages,id',
            'accommodation_type_id' => 'nullable|exists:accommodation_types,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_adults' => 'required|integer|min:1|max:20',
            'num_children' => 'nullable|integer|min:0|max:20',
        ]);
        $validated['num_children'] = $validated['num_children'] ?? 0;

        $availabilityParams = [
            'package_id' => $validated['package_id'] ?? null,
            'accommodation_type_id' => $validated['accommodation_type_id'] ?? null,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'num_adults' => $validated['num_adults'],
            'num_children' => $validated['num_children'],
            'exclude_booking_id' => $booking->id,
        ];

        $availability = app(AvailabilityService::class);

        $precheck = $availability->checkAvailability($availabilityParams);
        if (! $precheck->available) {
            return back()->withInput()->withErrors(['availability' => $precheck->message]);
        }

        $before = [
            'package_id' => $booking->package_id,
            'accommodation_type_id' => $booking->accommodation_type_id,
            'check_in_date' => $booking->check_in_date->format('Y-m-d'),
            'check_out_date' => $booking->check_out_date->format('Y-m-d'),
            'num_adults' => $booking->num_adults,
            'num_children' => $booking->num_children,
        ];

        try {
            DB::transaction(function () use ($availability, $availabilityParams, $booking, $validated) {
                $recheck = $availability->checkAvailabilityForUpdate($availabilityParams);
                if (! $recheck->available) {
                    throw new AvailabilityException($recheck->message);
                }

                $booking->update([
                    'package_id' => $validated['package_id'] ?? null,
                    'accommodation_type_id' => $validated['accommodation_type_id'] ?? null,
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'num_adults' => $validated['num_adults'],
                    'num_children' => $validated['num_children'],
                ]);
            });
        } catch (AvailabilityException $e) {
            return back()->withInput()->withErrors(['availability' => $e->getMessage()]);
        }

        ActivityLog::record(
            ActivityLog::ACTION_BOOKING_RESCHEDULED,
            $booking,
            "Booking {$booking->booking_ref} rescheduled.",
            changes: ['before' => $before, 'after' => $validated]
        );

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking rescheduled.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'reason' => 'required_if:status,cancelled|nullable|string|max:500',
        ]);

        $newStatus = $request->status;
        $oldStatus = $booking->status;

        $wasInactive = in_array($oldStatus, ['cancelled', 'completed']);
        $becomingActive = in_array($newStatus, Booking::ACTIVE_STATUSES);

        if ($wasInactive && $becomingActive) {
            // The slot may have been taken by someone else since this
            // booking stopped consuming inventory -- re-verify before
            // reactivating it rather than silently overbooking.
            $availability = app(AvailabilityService::class)->checkAvailability([
                'package_id' => $booking->package_id,
                'accommodation_type_id' => $booking->accommodation_type_id,
                'check_in_date' => $booking->check_in_date->format('Y-m-d'),
                'check_out_date' => $booking->check_out_date->format('Y-m-d'),
                'num_adults' => $booking->num_adults,
                'num_children' => $booking->num_children,
                'exclude_booking_id' => $booking->id,
            ]);

            if (! $availability->available) {
                return back()->with('error', "Cannot reactivate this booking — {$availability->message}");
            }
        }

        $booking->status = $newStatus;
        if ($newStatus === 'confirmed') {
            $booking->confirmed_at = now();
        } elseif ($newStatus === 'cancelled') {
            $booking->cancelled_at = now();
            $booking->cancellation_reason = $request->reason;
        }
        $booking->save();

        ActivityLog::record(
            ActivityLog::ACTION_BOOKING_STATUS_CHANGED,
            $booking,
            "Booking {$booking->booking_ref} status changed from {$oldStatus} to {$newStatus}.",
            changes: ['from' => $oldStatus, 'to' => $newStatus],
            reason: $newStatus === 'cancelled' ? $request->reason : null,
        );

        return back()->with('success', "Booking #{$booking->booking_ref} status updated to {$newStatus}.");
    }

    public function updatePayment(Request $request, Booking $booking)
    {
        $request->validate(['payment_status' => 'required|in:unpaid,paid,refunded']);

        $old = $booking->payment_status;
        $booking->payment_status = $request->payment_status;
        $booking->save();

        ActivityLog::record(
            ActivityLog::ACTION_PAYMENT_UPDATED,
            $booking,
            "Payment status for {$booking->booking_ref} changed from {$old} to {$request->payment_status}.",
            changes: ['from' => $old, 'to' => $request->payment_status]
        );

        return back()->with('success', "Payment status updated.");
    }

    public function assign(Request $request, Booking $booking)
    {
        $request->validate(['assigned_to' => 'nullable|exists:users,id']);

        $oldName = $booking->assignedTo?->name ?? 'Unassigned';
        $booking->assigned_to = $request->assigned_to;
        $booking->save();
        $newName = $booking->fresh()->assignedTo?->name ?? 'Unassigned';

        ActivityLog::record(
            ActivityLog::ACTION_BOOKING_ASSIGNED,
            $booking,
            "Booking {$booking->booking_ref} reassigned from {$oldName} to {$newName}."
        );

        return back()->with('success', 'Assignment updated.');
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
