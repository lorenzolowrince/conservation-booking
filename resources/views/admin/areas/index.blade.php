@extends('layouts.admin')

@section('title', 'Conservation Areas')
@section('page_title', 'Conservation Areas')

@section('content')

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">{{ session('error') }}</div>
@endif

@if(auth()->user()->isAdmin())
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.areas.create') }}" class="btn-primary">+ Add Conservation Area</a>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($areas as $area)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
        <div class="h-3 bg-gradient-to-r {{ match($area->code) {
            'DVCA' => 'from-green-500 to-emerald-400',
            'MBCA' => 'from-teal-500 to-cyan-400',
            'ICCA' => 'from-green-700 to-green-500',
            'SCCA' => 'from-blue-500 to-cyan-400',
            'TRCA' => 'from-emerald-500 to-teal-400',
            'INFAPRO' => 'from-lime-500 to-green-400',
            'INIKEA' => 'from-green-500 to-lime-400',
            default => 'from-gray-400 to-gray-300'
        } }}"></div>
        <div class="p-5">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $area->code }}</span>
                    <h3 class="font-display font-bold text-gray-900 mt-0.5">{{ $area->short_name }}</h3>
                </div>
                <span class="badge {{ $area->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $area->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $area->description }}</p>
            <div class="grid grid-cols-3 gap-3 text-center border-t border-gray-100 pt-4 mb-4">
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $area->bookings_count }}</div>
                    <div class="text-xs text-gray-400">Bookings</div>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $area->packages_count }}</div>
                    <div class="text-xs text-gray-400">Packages</div>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $area->accommodation_types_count }}</div>
                    <div class="text-xs text-gray-400">Accomm.</div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.areas.show', $area) }}" class="flex-1 text-center py-2 text-sm font-medium bg-forest-50 text-forest-700 rounded-lg hover:bg-forest-100 transition-colors">View</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.areas.edit', $area) }}" class="flex-1 text-center py-2 text-sm font-medium bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">Edit</a>
                @endif
                <a href="{{ route('areas.show', $area->slug) }}" target="_blank" class="py-2 px-3 text-sm font-medium bg-gray-50 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors" title="View public page">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                @if(auth()->user()->isAdmin())
                <form action="{{ route('admin.areas.destroy', $area) }}" method="POST"
                      onsubmit="return confirm('Delete {{ addslashes($area->short_name) }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="py-2 px-3 text-sm font-medium bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-colors" title="Delete area">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection
