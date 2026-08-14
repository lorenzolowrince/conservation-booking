@extends('layouts.app')

@section('title', 'Book a Conservation Experience — Yayasan Sabah')

@section('content')

<div class="bg-forest-950 pt-28 pb-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl font-display font-bold text-white mb-2">Book Your Experience</h1>
        <p class="text-gray-300">Fill in the details below to reserve your conservation visit.</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
     x-data="bookingForm()" x-init="init()">

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="font-semibold text-red-700 mb-1">Please fix the following errors:</div>
        <ul class="text-red-600 text-sm space-y-1">
            @foreach($errors->all() as $error)
            <li class="flex items-start gap-1.5"><span class="mt-0.5">&#8226;</span> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('booking.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Step 1: Select Area & Type --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-display font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 bg-forest-700 text-white rounded-full flex items-center justify-center text-sm font-bold shrink-0">1</span>
                Select Conservation Area & Booking Type
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Conservation Area <span class="text-red-500">*</span></label>
                    <select name="conservation_area_id" x-model="selectedAreaId" @change="onAreaChange()"
                            class="form-input" required>
                        <option value="">— Select an area —</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}" data-slug="{{ $area->slug }}"
                            {{ (old('conservation_area_id', $selectedArea?->id) == $area->id) ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('conservation_area_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Booking Type <span class="text-red-500">*</span></label>
                    <select name="booking_type" x-model="bookingType" class="form-input" required>
                        <option value="package">Package Tour</option>
                        <option value="accommodation_only">Accommodation Only</option>
                        <option value="day_trip">Day Trip</option>
                    </select>
                    @error('booking_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Package selector --}}
            <div x-show="bookingType === 'package'" x-transition class="mt-5">
                <label class="form-label">Select Package</label>
                <select name="package_id" x-model="selectedPackageId" class="form-input">
                    <option value="">— Select a package —</option>
                    @if($selectedArea)
                        @foreach($selectedArea->packages as $pkg)
                        <option value="{{ $pkg->id }}" {{ old('package_id', $selectedPackage?->id) == $pkg->id ? 'selected' : '' }}>
                            {{ $pkg->name }} ({{ $pkg->duration_days }} days) — RM {{ number_format($pkg->price_per_person, 0) }}/pax
                        </option>
                        @endforeach
                    @endif
                </select>
                <p class="text-xs text-gray-400 mt-1">Packages are loaded after selecting an area.</p>
            </div>

            {{-- Accommodation selector --}}
            <div x-show="bookingType === 'accommodation_only'" x-transition class="mt-5">
                <label class="form-label">Select Accommodation</label>
                <select name="accommodation_type_id" class="form-input">
                    <option value="">— Select accommodation —</option>
                    @if($selectedArea)
                        @foreach($selectedArea->accommodationTypes as $acc)
                        <option value="{{ $acc->id }}" {{ old('accommodation_type_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->name }} ({{ $acc->type }}) — RM {{ number_format($acc->price_per_night, 0) }}/night
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        {{-- Step 2: Dates & Guests --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-display font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 bg-forest-700 text-white rounded-full flex items-center justify-center text-sm font-bold shrink-0">2</span>
                Dates & Group Size
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="form-label">Check-in Date <span class="text-red-500">*</span></label>
                    <input type="date" name="check_in_date" class="form-input"
                           min="{{ date('Y-m-d') }}" value="{{ old('check_in_date') }}" required>
                    @error('check_in_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Check-out Date <span class="text-red-500">*</span></label>
                    <input type="date" name="check_out_date" class="form-input"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('check_out_date') }}" required>
                    @error('check_out_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Adults <span class="text-red-500">*</span></label>
                    <input type="number" name="num_adults" class="form-input" min="1" max="20"
                           value="{{ old('num_adults', 1) }}" required>
                    @error('num_adults')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Children</label>
                    <input type="number" name="num_children" class="form-input" min="0" max="20"
                           value="{{ old('num_children', 0) }}">
                    @error('num_children')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Step 3: Contact Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-display font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 bg-forest-700 text-white rounded-full flex items-center justify-center text-sm font-bold shrink-0">3</span>
                Lead Booker Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_name" class="form-input"
                           value="{{ old('contact_name', auth()->user()?->name) }}"
                           placeholder="As per IC/Passport" required>
                    @error('contact_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="contact_email" class="form-input"
                           value="{{ old('contact_email', auth()->user()?->email) }}"
                           placeholder="your@email.com" required>
                    @error('contact_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="contact_phone" class="form-input"
                           value="{{ old('contact_phone', auth()->user()?->phone) }}"
                           placeholder="+60 12-345 6789" required>
                    @error('contact_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Nationality <span class="text-red-500">*</span></label>
                    <select name="contact_nationality" class="form-input" required>
                        <option value="">— Select nationality —</option>
                        <option value="Malaysian" {{ old('contact_nationality') == 'Malaysian' ? 'selected' : '' }}>Malaysian</option>
                        <option value="Singaporean" {{ old('contact_nationality') == 'Singaporean' ? 'selected' : '' }}>Singaporean</option>
                        <option value="Australian" {{ old('contact_nationality') == 'Australian' ? 'selected' : '' }}>Australian</option>
                        <option value="British" {{ old('contact_nationality') == 'British' ? 'selected' : '' }}>British</option>
                        <option value="American" {{ old('contact_nationality') == 'American' ? 'selected' : '' }}>American</option>
                        <option value="Japanese" {{ old('contact_nationality') == 'Japanese' ? 'selected' : '' }}>Japanese</option>
                        <option value="Other" {{ old('contact_nationality') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('contact_nationality')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Special Requests / Notes</label>
                    <textarea name="special_requests" class="form-input" rows="3"
                              placeholder="Dietary requirements, accessibility needs, specific interests...">{{ old('special_requests') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Disclaimer --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
            <div class="font-semibold mb-1">Important Notes:</div>
            <ul class="space-y-1 text-amber-700">
                <li>&#8226; Bookings are subject to availability and confirmation by our team.</li>
                <li>&#8226; Payment details will be provided upon booking confirmation.</li>
                <li>&#8226; Foreign visitors are subject to higher rates as indicated.</li>
                <li>&#8226; 6% SST will be applied to the total amount.</li>
            </ul>
        </div>

        <button type="submit" class="btn-primary w-full justify-center text-base py-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Submit Booking Request
        </button>
    </form>
</div>

@push('scripts')
<script>
function bookingForm() {
    return {
        selectedAreaId: '{{ old('conservation_area_id', $selectedArea?->id) }}',
        bookingType: '{{ old('booking_type', 'package') }}',
        selectedPackageId: '{{ old('package_id', $selectedPackage?->id) }}',
        init() {},
        onAreaChange() {
            // In a full implementation, this would fetch packages/accommodations via AJAX
        }
    }
}
</script>
@endpush

@endsection
