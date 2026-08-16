<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpireStalePendingBookings extends Command
{
    protected $signature = 'bookings:expire-stale {--dry-run : List what would be cancelled without changing anything}';
    protected $description = 'Auto-cancel pending bookings that have exceeded the configured hold window, releasing their inventory';

    public function handle(): int
    {
        $holdMinutes = config('booking.pending_hold_minutes');
        $cutoff = now()->subMinutes($holdMinutes);

        $stale = Booking::where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($stale->isEmpty()) {
            $this->info('No stale pending bookings found.');
            return self::SUCCESS;
        }

        foreach ($stale as $booking) {
            $this->line("- {$booking->booking_ref} (pending since {$booking->created_at})");

            if (! $this->option('dry-run')) {
                $booking->status = 'cancelled';
                $booking->cancelled_at = now();
                $booking->admin_notes = trim(
                    ($booking->admin_notes ? $booking->admin_notes . "\n\n" : '')
                    . "Auto-cancelled by system: pending booking exceeded the {$holdMinutes}-minute hold window."
                );
                $booking->save();
            }
        }

        $prefix = $this->option('dry-run') ? '[dry-run] Would cancel ' : 'Cancelled ';
        $this->info($prefix . $stale->count() . ' stale pending booking(s).');

        return self::SUCCESS;
    }
}
