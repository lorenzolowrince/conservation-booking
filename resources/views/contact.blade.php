@extends('layouts.app')

@section('title', 'Contact — Yayasan Sabah Conservation')

@section('content')

<div class="bg-forest-950 pt-28 pb-16">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-display font-bold text-white mb-3">Contact Us</h1>
        <p class="text-gray-300">Get in touch with Yayasan Sabah's Conservation Division.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-2 gap-10">

    <div class="space-y-6">
        <h2 class="text-2xl font-display font-bold text-gray-900">Get in Touch</h2>

        @foreach([
            ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Phone', 'value' => '+60 87-870 000'],
            ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => 'conservation@yayasansabah.org'],
            ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'label' => 'Address', 'value' => 'Wisma Yayasan Sabah, Kota Kinabalu, Sabah, Malaysia'],
        ] as $contact)
        <div class="flex gap-4">
            <div class="w-10 h-10 bg-forest-100 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $contact['icon'] }}"/></svg>
            </div>
            <div>
                <div class="font-semibold text-gray-900">{{ $contact['label'] }}</div>
                <div class="text-gray-600 text-sm">{{ $contact['value'] }}</div>
            </div>
        </div>
        @endforeach

        <div class="bg-forest-50 border border-forest-200 rounded-xl p-5">
            <div class="font-semibold text-forest-800 mb-2">Booking Enquiries</div>
            <p class="text-forest-700 text-sm">For booking-related enquiries, please use our <a href="{{ route('booking.track') }}" class="underline font-medium">booking tracker</a> or email us directly with your booking reference number.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="font-display font-bold text-lg text-gray-900 mb-5">Send a Message</h3>
        <form class="space-y-4">
            <div>
                <label class="form-label">Name</label>
                <input type="text" class="form-input" placeholder="Your full name">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" class="form-input" placeholder="your@email.com">
            </div>
            <div>
                <label class="form-label">Subject</label>
                <input type="text" class="form-input" placeholder="Booking enquiry, general info...">
            </div>
            <div>
                <label class="form-label">Message</label>
                <textarea class="form-input" rows="4" placeholder="Your message..."></textarea>
            </div>
            <button type="button" class="btn-primary w-full justify-center">Send Message</button>
            <p class="text-xs text-gray-400 text-center">Contact form coming soon. Please email us directly in the meantime.</p>
        </form>
    </div>
</div>

@endsection
