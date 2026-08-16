@extends('layouts.admin')

@section('title', 'New Booking')
@section('page_title', 'Create Booking')

@section('content')

<div class="max-w-3xl" x-data="adminBookingForm()">
    <form action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-6">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-display font-bold text-gray-900 mb-2">Booking Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Conservation Area <span class="text-red-500">*</span></label>
                    <select name="conservation_area_id" x-model="selectedAreaId" class="form-input" required>
                        <option value="">— Select an area —</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('conservation_area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Booking Type <span class="text-red-500">*</span></label>
                    <select name="booking_type" x-model="bookingType" class="form-input" required>
                        <option value="package">Package Tour</option>
                        <option value="accommodation_only">Accommodation Only</option>
                        <option value="day_trip">Day Trip</option>
                    </select>
                </div>
            </div>

            <div x-show="bookingType === 'package'">
                <label class="form-label">Package</label>
                <select name="package_id" class="form-input">
                    <option value="">— Select a package —</option>
                    @foreach($areas as $area)
                        @foreach($area->packages as $pkg)
                        <option value="{{ $pkg->id }}" data-area="{{ $area->id }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }}>
                            {{ $area->short_name }} — {{ $pkg->name }}
                        </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div x-show="bookingType !== 'package'">
                <label class="form-label">Accommodation</label>
                <select name="accommodation_type_id" class="form-input">
                    <option value="">— Select accommodation —</option>
                    @foreach($areas as $area)
                        @foreach($area->accommodationTypes as $acc)
                        <option value="{{ $acc->id }}" data-area="{{ $area->id }}" {{ old('accommodation_type_id') == $acc->id ? 'selected' : '' }}>
                            {{ $area->short_name }} — {{ $acc->name }}
                        </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Check-in <span class="text-red-500">*</span></label>
                    <input type="date" name="check_in_date" class="form-input" value="{{ old('check_in_date') }}" required>
                </div>
                <div>
                    <label class="form-label">Check-out <span class="text-red-500">*</span></label>
                    <input type="date" name="check_out_date" class="form-input" value="{{ old('check_out_date') }}" required>
                </div>
                <div>
                    <label class="form-label">Adults <span class="text-red-500">*</span></label>
                    <input type="number" name="num_adults" class="form-input" min="1" max="20" value="{{ old('num_adults', 1) }}" required>
                </div>
                <div>
                    <label class="form-label">Children</label>
                    <input type="number" name="num_children" class="form-input" min="0" max="20" value="{{ old('num_children', 0) }}">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-display font-bold text-gray-900 mb-2">Customer Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_name" class="form-input" value="{{ old('contact_name') }}" required>
                </div>
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email') }}" required>
                </div>
                <div>
                    <label class="form-label">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone') }}" required>
                </div>
                <div>
                    <label class="form-label">Nationality <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_nationality" class="form-input" value="{{ old('contact_nationality', 'Malaysian') }}" required>
                </div>
                <div>
                    <label class="form-label">Assign to Staff</label>
                    <select name="assigned_to" class="form-input">
                        <option value="">— Unassigned —</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}" {{ old('assigned_to', auth()->id()) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Special Requests</label>
                    <textarea name="special_requests" rows="2" class="form-input">{{ old('special_requests') }}</textarea>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <label class="flex items-start gap-3">
                <input type="hidden" name="override" value="0">
                <input type="checkbox" name="override" value="1" x-model="override" class="mt-1 w-4 h-4 text-amber-600 rounded">
                <div>
                    <span class="font-semibold text-amber-900 text-sm">Override availability check</span>
                    <p class="text-xs text-amber-700 mt-0.5">Only use this for a genuine operational reason (e.g. a confirmed arrangement outside the system). This is logged in the audit trail with your reason.</p>
                </div>
            </label>
            <div x-show="override" class="mt-3">
                <label class="form-label text-xs">Reason for override <span class="text-red-500">*</span></label>
                <input type="text" name="override_reason" class="form-input" value="{{ old('override_reason') }}" placeholder="Why is this booking being forced through?">
            </div>
        </div>
        @endif

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Create Booking</button>
            <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function adminBookingForm() {
    return {
        selectedAreaId: '{{ old('conservation_area_id') }}',
        bookingType: '{{ old('booking_type', 'package') }}',
        override: {{ old('override') ? 'true' : 'false' }},
    }
}
</script>
@endpush

@endsection
