@extends('layouts.app')

@section('title', 'Conservation Areas — Yayasan Sabah')

@section('content')

{{-- Page header --}}
<div class="bg-forest-950 pt-28 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-forest-400 text-sm font-semibold uppercase tracking-wider mb-4">
            <span class="w-8 h-px bg-forest-400"></span>
            Sabah, Malaysia
            <span class="w-8 h-px bg-forest-400"></span>
        </div>
        <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-4">Our Conservation Areas</h1>
        <p class="text-gray-300 max-w-2xl mx-auto text-lg">
            Seven distinct wilderness destinations, each protecting irreplaceable Bornean ecosystems and offering extraordinary wildlife encounters.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
        @endphp

        @foreach($areas as $area)
        <a href="{{ route('areas.show', $area->slug) }}"
           class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col md:flex-row">
            <div class="md:w-48 h-48 md:h-auto bg-gradient-to-br {{ $gradients[$area->code] ?? 'from-forest-800 to-forest-600' }} relative shrink-0">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 16px 16px;"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-white/40 font-display font-bold text-4xl">{{ $area->code }}</span>
                </div>
            </div>
            <div class="p-6 flex flex-col justify-between flex-1">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h2 class="font-display font-bold text-xl text-gray-900 group-hover:text-forest-700 transition-colors leading-tight">
                            {{ $area->name }}
                        </h2>
                        <span class="badge bg-forest-100 text-forest-800 shrink-0 capitalize">{{ $area->difficulty_level }}</span>
                    </div>
                    <p class="text-forest-600 text-sm mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $area->location }}
                        @if($area->area_hectares)
                            <span class="text-gray-400 ml-2">· {{ number_format($area->area_hectares) }} ha</span>
                        @endif
                    </p>
                    <p class="text-gray-600 text-sm line-clamp-2">{{ $area->description }}</p>
                </div>

                @if($area->highlights)
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach(array_slice($area->highlights, 0, 3) as $highlight)
                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full">{{ $highlight }}</span>
                    @endforeach
                    @if(count($area->highlights) > 3)
                    <span class="bg-gray-100 text-gray-500 text-xs px-2.5 py-1 rounded-full">+{{ count($area->highlights) - 3 }} more</span>
                    @endif
                </div>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    @if($area->best_time_to_visit)
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Best: {{ $area->best_time_to_visit }}
                    </span>
                    @endif
                    <span class="text-forest-700 text-sm font-semibold group-hover:underline flex items-center gap-1 ml-auto">
                        View Details
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

@endsection
