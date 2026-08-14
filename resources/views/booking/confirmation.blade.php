@extends('layouts.app')

@section('title', 'Booking Confirmed — ' . $booking->booking_ref)

@section('content')

<div class="min-h-screen bg-gray-50 pt-24 pb-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Success Banner --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-gray-900">Booking Submitted!</h1>
            <p class="text-gray-500 mt-2">Your booking request has been received. We will review and confirm shortly.</p>
        </div>

        {{-- Booking Card --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
            <div class="bg-forest-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-forest-200 text-xs font-medium uppercase tracking-wider">Booking Reference</div>
                        <div class="text-white text-2xl font-bold font-display">{{ $booking->booking_ref }}</div>
                    </div>
                    <span class="status-pending text-sm px-3 py-1">{{ ucfirst($booking->status) }}</span>
                </div>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4 pb-5 border-b border-gray-100">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Conservation Area</div>
                        <div class="font-semibold text-gray-900">{{ $booking->conservationArea->short_name }}</div>
                    </div>
                    @if($booking->package)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Package</div>
                        <div class="font-semibold text-gray-900">{{ $booking->package->name }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Check-in</div>
                        <div class="font-semibold text-gray-900">{{ $booking->check_in_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Check-out</div>
                        <div class="font-semibold text-gray-900">{{ $booking->check_out_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Guests</div>
                        <div class="font-semibold text-gray-900">{{ $booking->num_adults }} adult(s){{ $booking->num_children > 0 ? ', ' . $booking->num_children . ' child(ren)' : '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Contact</div>
                        <div class="font-semibold text-gray-900">{{ $booking->contact_name }}</div>
                        <div class="text-gray-500 text-sm">{{ $booking->contact_email }}</div>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="space-y-2">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>RM {{ number_format($booking->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>SST (6%)</span>
                        <span>RM {{ number_format($booking->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg text-gray-900 pt-2 border-t border-gray-200">
                        <span>Total Amount</span>
                        <span class="text-forest-700">RM {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>

                @if($booking->special_requests)
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Special Requests</div>
                    <div class="text-gray-700 text-sm">{{ $booking->special_requests }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Next steps --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-5">
            <div class="font-semibold text-blue-800 mb-2">What happens next?</div>
            <ol class="text-blue-700 text-sm space-y-1.5">
                <li class="flex items-start gap-2"><span class="font-bold shrink-0">1.</span> Our team will review your booking request within 1–2 business days.</li>
                <li class="flex items-start gap-2"><span class="font-bold shrink-0">2.</span> You will receive a confirmation email at <strong>{{ $booking->contact_email }}</strong> with payment instructions.</li>
                <li class="flex items-start gap-2"><span class="font-bold shrink-0">3.</span> Complete payment to finalise your reservation.</li>
            </ol>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 mt-8">
            <a href="{{ route('booking.track', ['ref' => $booking->booking_ref]) }}" class="btn-secondary flex-1 justify-center">
                Track This Booking
            </a>
            <a href="{{ route('home') }}" class="btn-primary flex-1 justify-center">
                Back to Home
            </a>
        </div>
    </div>
</div>

@endsection
