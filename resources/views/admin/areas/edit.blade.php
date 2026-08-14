@extends('layouts.admin')

@section('title', 'Edit ' . $area->short_name)
@section('page_title', 'Edit: ' . $area->short_name)

@section('content')

<div class="max-w-2xl">
    <form action="{{ route('admin.areas.update', $area) }}" method="POST" class="space-y-5">
        @csrf @method('PATCH')

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div>
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $area->name) }}" required>
            </div>
            <div>
                <label class="form-label">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="3" class="form-input">{{ old('description', $area->description) }}</textarea>
            </div>
            <div>
                <label class="form-label">About (Full)</label>
                <textarea name="about" rows="5" class="form-input">{{ old('about', $area->about) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-input" value="{{ old('location', $area->location) }}" required>
                </div>
                <div>
                    <label class="form-label">Area (hectares)</label>
                    <input type="number" name="area_hectares" class="form-input" value="{{ old('area_hectares', $area->area_hectares) }}">
                </div>
                <div>
                    <label class="form-label">Difficulty</label>
                    <select name="difficulty_level" class="form-input" required>
                        @foreach(['easy', 'moderate', 'challenging'] as $d)
                        <option value="{{ $d }}" {{ old('difficulty_level', $area->difficulty_level) === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Best Time to Visit</label>
                    <input type="text" name="best_time_to_visit" class="form-input" value="{{ old('best_time_to_visit', $area->best_time_to_visit) }}">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-forest-600 rounded"
                       {{ old('is_active', $area->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700 font-medium">Area is active (visible to public)</label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.areas.show', $area) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
