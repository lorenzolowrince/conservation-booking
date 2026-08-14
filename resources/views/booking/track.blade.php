@extends('layouts.app')

@section('title', 'Track Booking — Yayasan Sabah')

@section('content')

<div class="min-h-screen bg-gray-50 pt-24 pb-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-10">
            <h1 class="text-3xl font-display font-bold text-gray-900 mb-2">Track Your Booking</h1>
            <p class="text-gray-500">Enter your booking reference number to check the status.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('booking.track') }}" class="flex gap-3">
                <input type="text" name="ref" value="{{ request('ref') }}"
                       class="form-input flex-1" placeholder="e.g. DVCA-20260401-0001" required>
                <button type="submit" class="btn-primary px-6 py-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Track
                </button>
            </form>
        </div>

        @if(request('ref') && !$booking)
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center text-red-700">
            <svg class="w-8 h-8 mx-auto mb-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="font-semibold">Booking not found</div>
            <div class="text-sm text-red-600 mt-1">No booking found with reference <strong>{{ request('ref') }}</strong>. Please check and try again.</div>
        </div>
        @endif

        @if($booking)
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
            <div class="bg-forest-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-forest-300 text-xs uppercase tracking-wider">Booking Reference</div>
                        <div class="text-white text-xl font-bold font-display">{{ $booking->booking_ref }}</div>
                    </div>
                    <span class="status-{{ $booking->status }} text-sm px-3 py-1">{{ ucfirst($booking->status) }}</span>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Area</div>
                        <div class="font-semibold text-gray-900">{{ $booking->conservationArea->short_name }}</div>
                    </div>
                    @if($booking->package)
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Package</div>
                        <div class="font-semibold text-gray-900">{{ $booking->package->name }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Check-in</div>
                        <div class="font-semibold">{{ $booking->check_in_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Check-out</div>
                        <div class="font-semibold">{{ $booking->check_out_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Total Amount</div>
                        <div class="font-bold text-forest-700 text-lg">RM {{ number_format($booking->total_amount, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Payment</div>
                        <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                </div>

                {{-- Status timeline --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="text-sm font-medium text-gray-700 mb-3">Booking Status</div>
                    <div class="flex items-center gap-0">
                        @foreach(['pending', 'confirmed', 'completed'] as $s)
                        @php
                            $statuses = ['pending', 'confirmed', 'completed'];
                            $currentIdx = array_search($booking->status, $statuses);
                            $sIdx = array_search($s, $statuses);
                            $isActive = $sIdx <= $currentIdx && $booking->status !== 'cancelled';
                            $isCancelled = $booking->status === 'cancelled';
                        @endphp
                        <div class="flex items-center flex-1 {{ !$loop->last ? '' : '' }}">
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                    {{ $isCancelled ? 'bg-gray-200 text-gray-400' : ($isActive ? 'bg-forest-600 text-white' : 'bg-gray-200 text-gray-400') }}">
                                    @if($isCancelled && $s === 'pending')
                                        ✕
                                    @else
                                        {{ $sIdx + 1 }}
                                    @endif
                                </div>
                                <div class="text-xs mt-1 capitalize {{ $isActive && !$isCancelled ? 'text-forest-700 font-semibold' : 'text-gray-400' }}">
                                    {{ $s }}
                                </div>
                            </div>
                            @if(!$loop->last)
                            <div class="flex-1 h-0.5 mb-5 {{ $sIdx < ($currentIdx ?? -1) && !$isCancelled ? 'bg-forest-500' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                        @endforeach
                        @if($booking->status === 'cancelled')
                        <div class="ml-2 text-red-500 text-xs font-semibold">CANCELLED</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
