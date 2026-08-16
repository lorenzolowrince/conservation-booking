@extends('layouts.admin')

@section('title', 'Add Accommodation')
@section('page_title', 'Add New Accommodation')

@section('content')

<div class="max-w-2xl">
    <form action="{{ route('admin.accommodation-types.store') }}" method="POST" class="space-y-5">
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
                    <option value="{{ $area->id }}" {{ old('conservation_area_id', request('area')) == $area->id ? 'selected' : '' }}>
                        {{ $area->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Accommodation Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="e.g. Borneo Rainforest Lodge Chalet" required>
            </div>
            <div>
                <label class="form-label">Type <span class="text-red-500">*</span></label>
                <select name="type" class="form-input" required>
                    <option value="">— Select type —</option>
                    @foreach($types as $type)
                    <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Capacity (pax) <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" class="form-input" min="1" value="{{ old('capacity', 2) }}" required>
                </div>
                <div></div>
                <div>
                    <label class="form-label">Price/Night (Malaysian) <span class="text-red-500">*</span></label>
                    <input type="number" name="price_per_night" class="form-input" step="0.01" min="0" value="{{ old('price_per_night') }}" required>
                </div>
                <div>
                    <label class="form-label">Price/Night (Foreigner)</label>
                    <input type="number" name="price_per_night_foreigner" class="form-input" step="0.01" min="0" value="{{ old('price_per_night_foreigner') }}">
                </div>
            </div>
            <div>
                <label class="form-label">Amenities</label>
                <textarea name="amenities" rows="4" class="form-input" placeholder="One per line, e.g.&#10;En-suite bathroom&#10;Air conditioning&#10;Forest view">{{ old('amenities') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">One amenity per line.</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-forest-600 rounded" {{ old('is_active', '1') ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700 font-medium">Accommodation is active</label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Create Accommodation</button>
            <a href="{{ route('admin.accommodation-types.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
