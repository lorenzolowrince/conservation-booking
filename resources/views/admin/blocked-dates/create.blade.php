@extends('layouts.admin')

@section('title', 'Block Dates')
@section('page_title', 'Block Dates')

@section('content')

<div class="max-w-2xl" x-data="{ target: '{{ old('target', 'package') }}' }">
    <form action="{{ route('admin.blocked-dates.store') }}" method="POST" class="space-y-5">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div>
                <label class="form-label">What are you blocking? <span class="text-red-500">*</span></label>
                <select name="target" x-model="target" class="form-input" required>
                    <option value="package">A Package</option>
                    <option value="accommodation">An Accommodation Type</option>
                </select>
            </div>

            <div x-show="target === 'package'">
                <label class="form-label">Package <span class="text-red-500">*</span></label>
                <select name="package_id" class="form-input">
                    <option value="">— Select package —</option>
                    @foreach($packages as $pkg)
                    <option value="{{ $pkg->id }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }}>
                        {{ $pkg->conservationArea->short_name }} — {{ $pkg->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div x-show="target === 'accommodation'">
                <label class="form-label">Accommodation Type <span class="text-red-500">*</span></label>
                <select name="accommodation_type_id" class="form-input">
                    <option value="">— Select accommodation —</option>
                    @foreach($accommodationTypes as $acc)
                    <option value="{{ $acc->id }}" {{ old('accommodation_type_id') == $acc->id ? 'selected' : '' }}>
                        {{ $acc->conservationArea->short_name }} — {{ $acc->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" class="form-input" value="{{ old('start_date') }}" required>
                </div>
                <div>
                    <label class="form-label">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" class="form-input" value="{{ old('end_date') }}" required>
                </div>
            </div>
            <p class="text-xs text-gray-400 -mt-2">Both dates are inclusive — blocking 20&ndash;25 Aug closes both the 20th and the 25th.</p>

            <div>
                <label class="form-label">Reason <span class="text-red-500">*</span></label>
                <input type="text" name="reason" class="form-input" value="{{ old('reason') }}" placeholder="e.g. Facility maintenance" required>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Block Dates</button>
            <a href="{{ route('admin.blocked-dates.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
