@extends('layouts.app')

@section('title', $area->name . ' — Yayasan Sabah Conservation')
@section('meta_description', $area->description)

@section('content')

@php
    $gradients = [
        'DVCA'    => 'from-green-900 via-forest-800 to-emerald-700',
        'MBCA'    => 'from-teal-900 via-teal-800 to-cyan-700',
        'ICCA'    => 'from-forest-900 via-forest-800 to-forest-700',
        'SCCA'    => 'from-blue-900 via-blue-800 to-cyan-700',
        'TRCA'    => 'from-emerald-900 via-emerald-800 to-teal-700',
        'INFAPRO' => 'from-lime-900 via-lime-800 to-green-700',
        'INIKEA'  => 'from-green-900 via-green-800 to-lime-700',
    ];
    $grad = $gradients[$area->code] ?? 'from-forest-900 via-forest-800 to-forest-700';
@endphp

{{-- Hero --}}
<div class="bg-gradient-to-br {{ $grad }} pt-28 pb-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px;"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="mb-4">
            <a href="{{ route('areas.index') }}" class="text-white/60 hover:text-white text-sm flex items-center gap-1 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                All Conservation Areas
            </a>
        </div>
        <span class="inline-block bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full mb-3">{{ $area->code }}</span>
        <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-3 max-w-3xl">{{ $area->name }}</h1>
        <div class="flex flex-wrap gap-4 text-white/70 text-sm mb-6">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                {{ $area->location }}
            </span>
            @if($area->area_hectares)
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                {{ number_format($area->area_hectares) }} hectares
            </span>
            @endif
            @if($area->best_time_to_visit)
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Best: {{ $area->best_time_to_visit }}
            </span>
            @endif
        </div>
        <a href="{{ route('booking.create', ['area' => $area->slug]) }}" class="btn-earth">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Book This Area
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-10">

            {{-- About --}}
            <div>
                <h2 class="text-2xl font-display font-bold text-gray-900 mb-4">About {{ $area->short_name }}</h2>
                <div class="text-gray-600 leading-relaxed">
                    {{ $area->about ?? $area->description }}
                </div>
            </div>

            {{-- Highlights --}}
            @if($area->highlights)
            <div>
                <h2 class="text-2xl font-display font-bold text-gray-900 mb-4">Key Highlights</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($area->highlights as $highlight)
                    <div class="flex items-center gap-2 bg-forest-50 border border-forest-100 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-forest-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-forest-800 text-sm font-medium">{{ $highlight }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Wildlife --}}
            @if($area->wildlife)
            <div>
                <h2 class="text-2xl font-display font-bold text-gray-900 mb-4">Wildlife You May Encounter</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($area->wildlife as $animal)
                    <span class="bg-earth-50 border border-earth-200 text-earth-800 text-sm px-3 py-1.5 rounded-full font-medium">
                        {{ $animal }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Packages --}}
            @if($area->packages->count() > 0)
            <div>
                <h2 class="text-2xl font-display font-bold text-gray-900 mb-6">Available Packages</h2>
                <div class="space-y-4">
                    @foreach($area->packages as $package)
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-forest-300 hover:shadow-md transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-lg text-gray-900">{{ $package->name }}</h3>
                                    <span class="bg-forest-100 text-forest-700 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $package->duration_days }} day{{ $package->duration_days > 1 ? 's' : '' }}</span>
                                </div>
                                <p class="text-gray-500 text-sm mb-3">{{ $package->description }}</p>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    <span>Min {{ $package->min_pax }} pax{{ $package->max_pax ? ' — Max ' . $package->max_pax : '' }}</span>
                                </div>
                                @if($package->inclusions)
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach(array_slice($package->inclusions, 0, 4) as $inc)
                                    <span class="bg-green-50 text-green-700 text-xs px-2 py-0.5 rounded-full">&#10003; {{ $inc }}</span>
                                    @endforeach
                                    @if(count($package->inclusions) > 4)
                                    <span class="text-gray-400 text-xs py-0.5">+{{ count($package->inclusions) - 4 }} more</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-2xl font-bold text-forest-700 font-display">RM {{ number_format($package->price_per_person, 0) }}</div>
                                <div class="text-gray-400 text-xs">per person (Malaysian)</div>
                                @if($package->price_per_person_foreigner)
                                <div class="text-gray-500 text-sm mt-0.5">RM {{ number_format($package->price_per_person_foreigner, 0) }} <span class="text-xs text-gray-400">(Foreigner)</span></div>
                                @endif
                                <a href="{{ route('booking.create', ['area' => $area->slug, 'package' => $package->id]) }}"
                                   class="mt-3 btn-primary text-sm px-4 py-2 inline-flex">
                                    Book Package
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick book card --}}
            <div class="bg-forest-950 rounded-2xl p-6 text-white sticky top-24">
                <h3 class="font-display font-bold text-xl mb-2">Plan Your Visit</h3>
                <p class="text-forest-300 text-sm mb-5">Book your {{ $area->short_name }} experience today.</p>
                <a href="{{ route('booking.create', ['area' => $area->slug]) }}" class="btn-earth w-full justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Book Now
                </a>
                <div class="mt-6 space-y-3 text-sm divide-y divide-forest-800">
                    @if($area->difficulty_level)
                    <div class="flex justify-between text-forest-300 pb-3">
                        <span>Difficulty</span>
                        <span class="text-white capitalize font-medium">{{ $area->difficulty_level }}</span>
                    </div>
                    @endif
                    @if($area->area_hectares)
                    <div class="flex justify-between text-forest-300 pt-3 pb-3">
                        <span>Area</span>
                        <span class="text-white font-medium">{{ number_format($area->area_hectares) }} ha</span>
                    </div>
                    @endif
                    @if($area->best_time_to_visit)
                    <div class="flex justify-between text-forest-300 pt-3 pb-3">
                        <span>Best Season</span>
                        <span class="text-white font-medium text-right">{{ $area->best_time_to_visit }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-forest-300 pt-3">
                        <span>Packages</span>
                        <span class="text-white font-medium">{{ $area->packages->count() }} available</span>
                    </div>
                </div>
            </div>

            {{-- Accommodations --}}
            @if($area->accommodationTypes->count() > 0)
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <h3 class="font-display font-bold text-lg text-gray-900 mb-4">Accommodations</h3>
                <div class="space-y-3">
                    @foreach($area->accommodationTypes as $acc)
                    <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                        <div class="font-medium text-gray-900 text-sm">{{ $acc->name }}</div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-gray-400 text-xs capitalize">{{ $acc->type }} &middot; max {{ $acc->capacity }} pax</span>
                            <span class="text-forest-700 font-semibold text-sm">RM {{ number_format($acc->price_per_night, 0) }}<span class="text-gray-400 font-normal text-xs">/night</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
