@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @php
    $statCards = [
        ['label' => 'Total Bookings', 'value' => number_format($stats['total_bookings']), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'bg-blue-100 text-blue-700'],
        ['label' => 'Pending', 'value' => number_format($stats['pending_bookings']), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-yellow-100 text-yellow-700'],
        ['label' => 'Confirmed', 'value' => number_format($stats['confirmed_bookings']), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-green-100 text-green-700'],
        ['label' => 'Revenue (Paid)', 'value' => 'RM ' . number_format($stats['total_revenue'], 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-forest-100 text-forest-700'],
        ['label' => 'Areas', 'value' => $stats['total_areas'], 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'color' => 'bg-teal-100 text-teal-700'],
        ['label' => 'Users', 'value' => number_format($stats['total_users']), 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'bg-purple-100 text-purple-700'],
    ];
    @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 {{ $card['color'] }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 font-display">{{ $card['value'] }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Backup Quick Action --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-forest-100 rounded-lg flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
        </div>
        <div>
            <div class="font-semibold text-gray-900 text-sm">Application Backup</div>
            @if($lastBackup)
            <div class="text-xs text-gray-500 mt-0.5">
                Last backup: <span class="font-medium text-gray-700">{{ $lastBackup->created_at->format('d M Y, H:i') }}</span>
                &middot; {{ $lastBackup->formatted_size }}
                &middot; <span class="capitalize">{{ $lastBackup->type }}</span>
                &middot; <span class="text-gray-400">{{ $lastBackup->created_at->diffForHumans() }}</span>
            </div>
            @else
            <div class="text-xs text-amber-600 mt-0.5 font-medium">No backups yet — create one now.</div>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        <div class="hidden sm:flex items-center gap-1.5 text-xs text-gray-400">
            <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>
            Auto-backup: daily at midnight
        </div>
        <form action="{{ route('admin.backup.create') }}" method="POST"
              x-data="{ loading: false }"
              @submit="loading = true">
            @csrf
            <button type="submit"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 hover:bg-forest-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg transition-colors">
                <svg x-show="!loading" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="loading ? 'Creating backup…' : 'Backup Now'"></span>
            </button>
        </form>
        <a href="{{ route('admin.backup.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            View All
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent Bookings --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-display font-bold text-gray-900">Recent Bookings</h2>
            <a href="{{ route('admin.bookings.index') }}" class="text-forest-600 hover:text-forest-700 text-sm font-medium">View all</a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recentBookings as $booking)
            <a href="{{ route('admin.bookings.show', $booking) }}"
               class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="font-semibold text-sm text-gray-900">{{ $booking->booking_ref }}</span>
                        <span class="status-{{ $booking->status }} text-xs">{{ ucfirst($booking->status) }}</span>
                    </div>
                    <div class="text-gray-500 text-xs">{{ $booking->contact_name }} &middot; {{ $booking->conservationArea->short_name }}</div>
                </div>
                <div class="text-right shrink-0 ml-3">
                    <div class="font-semibold text-gray-900 text-sm">RM {{ number_format($booking->total_amount, 0) }}</div>
                    <div class="text-gray-400 text-xs">{{ $booking->created_at->diffForHumans() }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Bookings by Area --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h2 class="font-display font-bold text-gray-900">Bookings by Area</h2>
        </div>
        <div class="p-4 space-y-3">
            @php $maxCount = $bookingsByArea->max('bookings_count') ?: 1; @endphp
            @foreach($bookingsByArea as $area)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700 truncate">{{ $area->short_name }}</span>
                    <span class="text-gray-500 shrink-0">{{ $area->bookings_count }}</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-forest-600 rounded-full transition-all"
                         style="width: {{ $maxCount > 0 ? ($area->bookings_count / $maxCount * 100) : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-4 border-t border-gray-100">
            <a href="{{ route('admin.areas.index') }}" class="text-forest-600 hover:text-forest-700 text-sm font-medium">Manage areas &rarr;</a>
        </div>
    </div>
</div>

@endsection
