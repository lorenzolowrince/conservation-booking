@extends('layouts.admin')

@section('title', $area->name)
@section('page_title', $area->short_name)

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">

        {{-- Area Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">{{ $area->code }}</span>
                    <h2 class="font-display font-bold text-gray-900">{{ $area->name }}</h2>
                </div>
                <div class="flex gap-2 items-center">
                    <span class="badge {{ $area->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $area->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.areas.edit', $area) }}" class="btn-secondary py-1.5 px-3 text-xs">Edit</a>
                    <form action="{{ route('admin.areas.destroy', $area) }}" method="POST"
                          onsubmit="return confirm('Delete {{ addslashes($area->short_name) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="py-1.5 px-3 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors border border-red-200">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            <p class="text-gray-600 text-sm">{{ $area->description }}</p>
            <div class="grid grid-cols-3 gap-4 mt-5 pt-5 border-t border-gray-100 text-sm">
                <div><span class="text-gray-400">Location:</span> <span class="font-medium">{{ $area->location }}</span></div>
                @if($area->area_hectares)<div><span class="text-gray-400">Area:</span> <span class="font-medium">{{ number_format($area->area_hectares) }} ha</span></div>@endif
                <div><span class="text-gray-400">Difficulty:</span> <span class="font-medium capitalize">{{ $area->difficulty_level }}</span></div>
            </div>
        </div>

        {{-- Packages --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-gray-900">Packages ({{ $area->packages->count() }})</h3>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.packages.create') }}" class="btn-primary py-1.5 px-3 text-xs">+ Add Package</a>
                @endif
            </div>
            @forelse($area->packages as $pkg)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div>
                    <div class="font-medium text-sm text-gray-900">{{ $pkg->name }}</div>
                    <div class="text-xs text-gray-400">{{ $pkg->duration_days }} days · RM {{ number_format($pkg->price_per_person, 0) }}/pax</div>
                </div>
                <div class="flex gap-2">
                    <span class="badge {{ $pkg->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} text-xs">
                        {{ $pkg->is_active ? 'Active' : 'Off' }}
                    </span>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.packages.edit', $pkg) }}" class="text-forest-600 text-xs hover:underline">Edit</a>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm">No packages yet.</p>
            @endforelse
        </div>

        {{-- Accommodations --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-gray-900">Accommodations ({{ $area->accommodationTypes->count() }})</h3>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.accommodation-types.create', ['area' => $area->id]) }}" class="btn-primary py-1.5 px-3 text-xs">+ Add Accommodation</a>
                @endif
            </div>
            @forelse($area->accommodationTypes as $acc)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div>
                    <div class="font-medium text-sm text-gray-900">{{ $acc->name }}</div>
                    <div class="text-xs text-gray-400 capitalize">{{ $acc->type }} · {{ $acc->capacity }} pax · RM {{ number_format($acc->price_per_night, 0) }}/night</div>
                </div>
                <div class="flex gap-2 items-center">
                    <span class="badge {{ $acc->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} text-xs">
                        {{ $acc->is_active ? 'Active' : 'Off' }}
                    </span>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.accommodation-types.edit', $acc) }}" class="text-forest-600 text-xs hover:underline">Edit</a>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm">No accommodations configured.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Bookings --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-gray-900">Recent Bookings</h3>
            <a href="{{ route('admin.bookings.index', ['area' => $area->id]) }}" class="text-forest-600 text-xs hover:underline">View all</a>
        </div>
        @forelse($area->bookings as $b)
        <a href="{{ route('admin.bookings.show', $b) }}" class="block py-3 border-b border-gray-50 hover:bg-gray-50 -mx-2 px-2 rounded transition-colors last:border-0">
            <div class="font-semibold text-sm text-gray-900">{{ $b->booking_ref }}</div>
            <div class="text-xs text-gray-400">{{ $b->contact_name }} · {{ $b->created_at->diffForHumans() }}</div>
            <span class="status-{{ $b->status }} text-xs mt-1 inline-block">{{ ucfirst($b->status) }}</span>
        </a>
        @empty
        <p class="text-gray-400 text-sm">No bookings yet.</p>
        @endforelse
        <div class="mt-4">
            <a href="{{ route('areas.show', $area->slug) }}" target="_blank"
               class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-forest-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Public Page
            </a>
        </div>
    </div>
</div>

@endsection
