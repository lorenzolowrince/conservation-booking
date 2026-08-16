<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pending Booking Hold Window
    |--------------------------------------------------------------------------
    |
    | How long (in minutes) a "pending" booking holds its inventory before
    | the bookings:expire-stale scheduled command auto-cancels it.
    |
    */

    'pending_hold_minutes' => (int) env('BOOKING_PENDING_HOLD_MINUTES', 60),

];
