@extends('layouts.admin')

@section('title', 'Reschedule Booking')
@section('page_title', 'Reschedule ' . $booking->booking_ref)

@section('content')

<div class="max-w-2xl" x-data="{ bookingType: '{{ old('accommodation_type_id', $booking->accommodation_type_id) ? 'accommodation' : 'package' }}' }">
    <div class="mb-5 text-sm text-gray-500">
        Currently: {{ $booking->package?->name ?? $booking->accommodationType?->name ?? 'No resource' }}
        &middot; {{ $booking->check_in_date->format('d M Y') }} &ndash; {{ $booking->check_out_date->format('d M Y') }}
        &middot; {{ $booking->num_adults }} adult(s), {{ $booking->num_children }} child(ren)
    </div>

    <form action="{{ route('admin.bookings.reschedule', $booking) }}" method="POST" class="space-y-5">
        @csrf @method('PATCH')

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div>
                <label class="form-label">Resource type</label>
                <select x-model="bookingType" class="form-input">
                    <option value="package">Package</option>
                    <option value="accommodation">Accommodation</option>
                </select>
            </div>

            <div x-show="bookingType === 'package'">
                <label class="form-label">Package</label>
                <select name="package_id" class="form-input">
                    <option value="">— None —</option>
                    @foreach($areas as $area)
                        @foreach($area->packages as $pkg)
                        <option value="{{ $pkg->id }}" {{ old('package_id', $booking->package_id) == $pkg->id ? 'selected' : '' }}>
                            {{ $area->short_name }} — {{ $pkg->name }}
                        </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div x-show="bookingType === 'accommodation'">
                <label class="form-label">Accommodation</label>
                <select name="accommodation_type_id" class="form-input">
                    <option value="">— None —</option>
                    @foreach($areas as $area)
                        @foreach($area->accommodationTypes as $acc)
                        <option value="{{ $acc->id }}" {{ old('accommodation_type_id', $booking->accommodation_type_id) == $acc->id ? 'selected' : '' }}>
                            {{ $area->short_name }} — {{ $acc->name }}
                        </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Check-in <span class="text-red-500">*</span></label>
                    <input type="date" name="check_in_date" class="form-input" value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Check-out <span class="text-red-500">*</span></label>
                    <input type="date" name="check_out_date" class="form-input" value="{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Adults <span class="text-red-500">*</span></label>
                    <input type="number" name="num_adults" class="form-input" min="1" max="20" value="{{ old('num_adults', $booking->num_adults) }}" required>
                </div>
                <div>
                    <label class="form-label">Children</label>
                    <input type="number" name="num_children" class="form-input" min="0" max="20" value="{{ old('num_children', $booking->num_children) }}">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Save New Dates</button>
            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
