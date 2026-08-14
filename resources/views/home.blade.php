@extends('layouts.app')

@section('title', 'Yayasan Sabah Conservation Booking — Heart of Borneo')

@section('content')

{{-- Hero Section --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-forest-950 pt-16">
    {{-- Animated background overlay --}}
    <div class="absolute inset-0 bg-gradient-to-br from-forest-950 via-forest-900 to-forest-800 opacity-90"></div>

    {{-- Decorative elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-forest-700/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-earth-700/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/4 w-64 h-64 bg-forest-600/10 rounded-full blur-2xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20">
        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 bg-forest-800/60 border border-forest-700 text-forest-300 px-4 py-1.5 rounded-full text-sm font-medium mb-8 backdrop-blur-sm">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            Yayasan Sabah — Conservation Division
        </div>

        <h1 class="text-5xl md:text-7xl font-display font-bold text-white leading-tight mb-6">
            Explore the
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-forest-400 to-earth-400">Heart of Borneo</span>
        </h1>

        <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto mb-10 leading-relaxed">
            Book your immersive conservation experience across 7 extraordinary wilderness areas in Sabah. Discover pristine rainforests, rare wildlife, and contribute to nature conservation.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('areas.index') }}" class="btn-primary text-base px-8 py-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Explore Areas
            </a>
            <a href="{{ route('booking.create') }}" class="btn-earth text-base px-8 py-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Book a Visit
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-8 max-w-lg mx-auto mt-16 pt-12 border-t border-forest-800">
            <div>
                <div class="text-3xl font-bold text-white font-display">7</div>
                <div class="text-forest-400 text-sm mt-1">Conservation Areas</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-white font-display">200K+</div>
                <div class="text-forest-400 text-sm mt-1">Hectares Protected</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-white font-display">500+</div>
                <div class="text-forest-400 text-sm mt-1">Wildlife Species</div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/40 animate-bounce">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </div>
</section>

{{-- Conservation Areas Grid --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 text-forest-600 text-sm font-semibold uppercase tracking-wider mb-3">
                <span class="w-8 h-px bg-forest-600"></span>
                Our Conservation Areas
                <span class="w-8 h-px bg-forest-600"></span>
            </div>
            <h2 class="section-title text-forest-950">Discover 7 Extraordinary Wilderness Areas</h2>
            <p class="text-gray-500 mt-4 max-w-xl mx-auto">Each area offers a unique ecosystem and unforgettable wildlife experiences in the pristine rainforests of Sabah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($areas as $area)
            <a href="{{ route('areas.show', $area->slug) }}"
               class="group card relative overflow-hidden">
                {{-- Gradient placeholder image --}}
                @php
                    $gradients = [
                        'DVCA'    => 'from-green-800 to-emerald-600',
                        'MBCA'    => 'from-teal-800 to-cyan-600',
                        'ICCA'    => 'from-forest-800 to-forest-600',
                        'SCCA'    => 'from-blue-800 to-cyan-500',
                        'TRCA'    => 'from-emerald-700 to-teal-500',
                        'INFAPRO' => 'from-lime-800 to-green-500',
                        'INIKEA'  => 'from-green-700 to-lime-600',
                    ];
                    $grad = $gradients[$area->code] ?? 'from-forest-800 to-forest-600';
                @endphp
                <div class="h-48 bg-gradient-to-br {{ $grad }} relative overflow-hidden">
                    {{-- Pattern overlay --}}
                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/30" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 19.34A1 1 0 004.72 21C8.59 17.87 12 16 17 16v4l5-5-5-5v4z"/></svg>
                    </div>
                    <div class="absolute top-3 left-3">
                        <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-full">
                            {{ $area->code }}
                        </span>
                    </div>
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/20 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full capitalize">
                            {{ $area->difficulty_level }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <h3 class="font-display font-bold text-lg text-gray-900 group-hover:text-forest-700 transition-colors leading-snug">
                        {{ $area->short_name }}
                    </h3>
                    <p class="text-gray-500 text-sm mt-1 mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4 text-forest-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $area->location }}
                    </p>
                    <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $area->description }}</p>

                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-400">
                            @if($area->area_hectares)
                                {{ number_format($area->area_hectares) }} ha
                            @endif
                        </div>
                        <span class="text-forest-700 text-sm font-semibold group-hover:underline flex items-center gap-1">
                            Explore
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('areas.index') }}" class="btn-primary">View All Conservation Areas</a>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-20 bg-forest-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 text-forest-400 text-sm font-semibold uppercase tracking-wider mb-3">
                <span class="w-8 h-px bg-forest-400"></span>
                How It Works
                <span class="w-8 h-px bg-forest-400"></span>
            </div>
            <h2 class="section-title text-white">Book Your Conservation Experience</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach([
                ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'step' => '01', 'title' => 'Choose an Area', 'desc' => 'Browse our 7 conservation areas and find your perfect wilderness destination.'],
                ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'step' => '02', 'title' => 'Select a Package', 'desc' => 'Choose from curated conservation packages or accommodation-only options.'],
                ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'step' => '03', 'title' => 'Make Reservation', 'desc' => 'Fill in your details, select dates, and submit your booking request.'],
                ['icon' => 'M5 13l4 4L19 7', 'step' => '04', 'title' => 'Receive Confirmation', 'desc' => 'Our team reviews and confirms your booking with all details via email.'],
            ] as $step)
            <div class="text-center group">
                <div class="w-16 h-16 bg-forest-800 border-2 border-forest-700 group-hover:border-forest-500 rounded-2xl flex items-center justify-center mx-auto mb-4 transition-colors">
                    <svg class="w-7 h-7 text-forest-400 group-hover:text-forest-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <div class="text-forest-600 font-bold text-3xl font-display mb-2">{{ $step['step'] }}</div>
                <h3 class="text-white font-semibold text-lg mb-2">{{ $step['title'] }}</h3>
                <p class="text-gray-400 text-sm">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('booking.create') }}" class="btn-earth text-base px-8 py-4">
                Start Booking Now
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Why Visit --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center gap-2 text-forest-600 text-sm font-semibold uppercase tracking-wider mb-4">
                    <span class="w-8 h-px bg-forest-600"></span>
                    Why Visit Us
                </div>
                <h2 class="section-title text-forest-950 mb-6">Protecting Borneo's Natural Heritage</h2>
                <p class="text-gray-600 leading-relaxed mb-8">
                    Yayasan Sabah's Conservation Division manages some of the most biodiverse habitats on Earth. Your visit directly supports conservation efforts, local communities, and the long-term preservation of Borneo's irreplaceable natural heritage.
                </p>
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'UNESCO-recognised biodiversity', 'desc' => 'Home to rare and endemic species found nowhere else on Earth.'],
                        ['icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'title' => 'Expert-guided experiences', 'desc' => 'Professional naturalist guides with decades of local knowledge.'],
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Revenue funds conservation', 'desc' => 'Every booking contributes directly to protecting these wild places.'],
                    ] as $feat)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 bg-forest-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $feat['title'] }}</div>
                            <div class="text-gray-500 text-sm">{{ $feat['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-4">
                    <div class="h-40 bg-gradient-to-br from-forest-700 to-forest-500 rounded-2xl flex items-center justify-center">
                        <svg class="w-14 h-14 text-white/40" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 19.34A1 1 0 004.72 21C8.59 17.87 12 16 17 16v4l5-5-5-5v4z"/></svg>
                    </div>
                    <div class="h-56 bg-gradient-to-br from-earth-700 to-earth-500 rounded-2xl flex items-center justify-center">
                        <svg class="w-14 h-14 text-white/40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    </div>
                </div>
                <div class="space-y-4 mt-8">
                    <div class="h-56 bg-gradient-to-br from-teal-700 to-teal-500 rounded-2xl flex items-center justify-center">
                        <svg class="w-14 h-14 text-white/40" fill="currentColor" viewBox="0 0 24 24"><path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.11 0 2-.89 2-2V5c0-1.11-.89-2-2-2zm0 5h-2V5h2v3z"/></svg>
                    </div>
                    <div class="h-40 bg-gradient-to-br from-forest-800 to-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-14 h-14 text-white/40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22q-2.075 0-3.9-.788-1.825-.787-3.175-2.137-1.35-1.35-2.137-3.175Q2 14.075 2 12t.788-3.9q.787-1.825 2.137-3.175Q6.275 3.575 8.1 2.788 9.925 2 12 2t3.9.788q1.825.787 3.175 2.137 1.35 1.35 2.137 3.175Q22 9.925 22 12t-.788 3.9q-.787 1.825-2.137 3.175-1.35 1.35-3.175 2.137Q14.075 22 12 22z"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-gradient-to-r from-forest-800 to-forest-700">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">
            Ready for Your Borneo Adventure?
        </h2>
        <p class="text-forest-200 text-lg mb-8">
            Track existing bookings or start planning your next conservation experience today.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('booking.create') }}" class="bg-white text-forest-800 font-semibold px-8 py-4 rounded-xl hover:bg-forest-50 transition-colors shadow-lg">
                Book Now
            </a>
            <a href="{{ route('booking.track') }}" class="bg-forest-700/50 text-white border border-forest-600 font-semibold px-8 py-4 rounded-xl hover:bg-forest-700 transition-colors">
                Track Booking
            </a>
        </div>
    </div>
</section>

@endsection
