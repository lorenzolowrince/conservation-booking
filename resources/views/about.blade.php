@extends('layouts.app')

@section('title', 'About — Yayasan Sabah Conservation Division')

@section('content')

<div class="bg-forest-950 pt-28 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-4">About Yayasan Sabah</h1>
        <p class="text-gray-300 text-lg">Conservation Division — Protecting the Heart of Borneo</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-14">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <div>
            <div class="inline-flex items-center gap-2 text-forest-600 text-sm font-semibold uppercase tracking-wider mb-4">
                <span class="w-8 h-px bg-forest-600"></span>
                Our Mission
            </div>
            <h2 class="section-title text-forest-950 mb-4">Guardians of Sabah's Forests</h2>
            <p class="text-gray-600 leading-relaxed">
                Yayasan Sabah's Conservation Division is entrusted with the management and protection of some of the most ecologically significant forested areas in Borneo. Established to balance conservation with sustainable development, we oversee seven unique conservation areas covering over 200,000 hectares of pristine rainforest, coastal ecosystems, and river corridors.
            </p>
        </div>
        <div class="bg-forest-900 rounded-2xl p-8 text-white">
            <div class="grid grid-cols-2 gap-6">
                @foreach([['200K+', 'Hectares Protected'], ['7', 'Conservation Areas'], ['500+', 'Wildlife Species'], ['30+', 'Years of Conservation']] as [$n, $l])
                <div class="text-center">
                    <div class="text-3xl font-bold font-display text-forest-300">{{ $n }}</div>
                    <div class="text-gray-400 text-xs mt-1">{{ $l }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900 mb-6">Our Conservation Areas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach([
                ['DVCA', 'Danum Valley Conservation Area', 'The crown jewel — 43,800 ha of ancient lowland dipterocarp forest.'],
                ['MBCA', 'Maliau Basin Conservation Area', 'Sabah\'s Lost World — 58,840 ha of virtually untouched wilderness.'],
                ['ICCA', 'Imbak Canyon Conservation Area', '30,000 ha of spectacular canyon and forest ecosystems.'],
                ['SCCA', 'Silam Coast Conservation Area', 'Coastal and marine habitats along the iconic Darvel Bay.'],
                ['TRCA', 'Taliwas River Conservation Area', 'Pristine riverine forest corridor in the Kinabatangan region.'],
                ['INFAPRO', 'Innoprise-FACE Foundation Project', '25,000 ha forest rehabilitation with native species.'],
                ['INIKEA', 'Innoprise-IKEA Forest Project', 'Corporate sustainability partnership restoring tropical forest.'],
            ] as [$code, $name, $desc])
            <div class="flex gap-3 p-4 bg-forest-50 rounded-xl border border-forest-100">
                <span class="text-forest-700 font-bold text-sm bg-forest-100 px-2 py-0.5 rounded h-fit shrink-0">{{ $code }}</span>
                <div>
                    <div class="font-semibold text-gray-900 text-sm">{{ $name }}</div>
                    <div class="text-gray-500 text-xs mt-0.5">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-forest-950 rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-display font-bold text-white mb-3">Ready to Explore?</h2>
        <p class="text-gray-300 mb-6">Book your conservation experience and contribute to protecting Borneo's natural heritage.</p>
        <a href="{{ route('booking.create') }}" class="btn-earth">Book a Conservation Visit</a>
    </div>
</div>

@endsection
