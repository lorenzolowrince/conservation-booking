@extends('layouts.admin')

@section('title', 'Add Conservation Area')
@section('page_title', 'Add Conservation Area')

@section('content')

<div class="max-w-2xl">
    <form action="{{ route('admin.areas.store') }}" method="POST" class="space-y-5">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" class="form-input uppercase" value="{{ old('code') }}" placeholder="e.g. DVCA" required maxlength="10">
                    <p class="text-xs text-gray-400 mt-1">Short unique identifier (max 10 chars)</p>
                </div>
                <div>
                    <label class="form-label">Short Name <span class="text-red-500">*</span></label>
                    <input type="text" name="short_name" class="form-input" value="{{ old('short_name') }}" placeholder="e.g. Danum Valley" required>
                </div>
            </div>
            <div>
                <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="e.g. Danum Valley Conservation Area" required>
                <p class="text-xs text-gray-400 mt-1">The URL slug will be auto-generated from this name.</p>
            </div>
            <div>
                <label class="form-label">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="3" class="form-input" placeholder="Short public-facing description…">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="form-label">About (Full)</label>
                <textarea name="about" rows="5" class="form-input" placeholder="Longer detailed content for the public area page…">{{ old('about') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Location <span class="text-red-500">*</span></label>
                    <input type="text" name="location" class="form-input" value="{{ old('location') }}" placeholder="e.g. Lahad Datu, Sabah" required>
                </div>
                <div>
                    <label class="form-label">Area (hectares)</label>
                    <input type="number" name="area_hectares" class="form-input" value="{{ old('area_hectares') }}" min="0">
                </div>
                <div>
                    <label class="form-label">Difficulty <span class="text-red-500">*</span></label>
                    <select name="difficulty_level" class="form-input" required>
                        @foreach(['easy', 'moderate', 'challenging'] as $d)
                        <option value="{{ $d }}" {{ old('difficulty_level', 'moderate') === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Best Time to Visit</label>
                    <input type="text" name="best_time_to_visit" class="form-input" value="{{ old('best_time_to_visit') }}" placeholder="e.g. March – October">
                </div>
                <div>
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-input" value="{{ old('sort_order', 0) }}" min="0">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-forest-600 rounded"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700 font-medium">Area is active (visible to public)</label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Create Area</button>
            <a href="{{ route('admin.areas.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
