@extends('layouts.admin')

@section('title', 'Add Package')
@section('page_title', 'Add New Package')

@section('content')

<div class="max-w-2xl">
    <form action="{{ route('admin.packages.store') }}" method="POST" class="space-y-5">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div>
                <label class="form-label">Conservation Area <span class="text-red-500">*</span></label>
                <select name="conservation_area_id" class="form-input" required>
                    <option value="">— Select area —</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ old('conservation_area_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Package Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="form-label">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="3" class="form-input">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Duration (days) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_days" class="form-input" min="1" value="{{ old('duration_days', 1) }}" required>
                </div>
                <div>
                    <label class="form-label">Min Pax</label>
                    <input type="number" name="min_pax" class="form-input" min="1" value="{{ old('min_pax', 1) }}">
                </div>
                <div>
                    <label class="form-label">Max Pax</label>
                    <input type="number" name="max_pax" class="form-input" min="1" value="{{ old('max_pax') }}">
                </div>
                <div></div>
                <div>
                    <label class="form-label">Price/Person (Malaysian) <span class="text-red-500">*</span></label>
                    <input type="number" name="price_per_person" class="form-input" step="0.01" min="0" value="{{ old('price_per_person') }}" required>
                </div>
                <div>
                    <label class="form-label">Price/Person (Foreigner)</label>
                    <input type="number" name="price_per_person_foreigner" class="form-input" step="0.01" min="0" value="{{ old('price_per_person_foreigner') }}">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-forest-600 rounded" {{ old('is_active', '1') ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700 font-medium">Package is active</label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Create Package</button>
            <a href="{{ route('admin.packages.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
