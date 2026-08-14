<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'total_areas' => ConservationArea::count(),
            'total_users' => User::where('role', 'user')->count(),
        ];

        $recentBookings = Booking::with(['conservationArea'])
            ->latest()
            ->take(8)
            ->get();

        $bookingsByArea = ConservationArea::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->get();

        $lastBackup = Backup::where('status', 'completed')->latest()->first();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'bookingsByArea', 'lastBackup'));
    }
}
