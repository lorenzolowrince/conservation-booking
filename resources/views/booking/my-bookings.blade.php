@extends('layouts.app')

@section('title', 'My Bookings — Yayasan Sabah')

@section('content')

<div class="bg-forest-950 pt-24 pb-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-display font-bold text-white mb-1">My Bookings</h1>
        <p class="text-gray-400">Manage and track all your conservation bookings.</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    @if($bookings->count() === 0)
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <h3 class="font-display font-bold text-gray-700 text-xl mb-2">No bookings yet</h3>
        <p class="text-gray-500 mb-6">Start planning your conservation adventure today.</p>
        <a href="{{ route('booking.create') }}" class="btn-primary">Book a Visit</a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($bookings as $booking)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="font-bold text-gray-900 font-display">{{ $booking->booking_ref }}</span>
                        <span class="status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                        <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                    <div class="text-forest-700 font-semibold">{{ $booking->conservationArea->name }}</div>
                    <div class="text-gray-500 text-sm mt-1 flex flex-wrap gap-3">
                        <span>{{ $booking->check_in_date->format('d M Y') }} — {{ $booking->check_out_date->format('d M Y') }}</span>
                        <span>{{ $booking->num_adults }} adult(s){{ $booking->num_children > 0 ? ', ' . $booking->num_children . ' child' : '' }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-forest-700">RM {{ number_format($booking->total_amount, 2) }}</div>
                    <div class="text-xs text-gray-400 mb-2">Total incl. SST</div>
                    <a href="{{ route('booking.track', ['ref' => $booking->booking_ref]) }}"
                       class="text-forest-700 text-sm font-semibold hover:underline flex items-center gap-1 justify-end">
                        View Details
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
    @endif

    <div class="mt-8 text-center">
        <a href="{{ route('booking.create') }}" class="btn-primary">+ New Booking</a>
    </div>
</div>

@endsection
