@extends('layouts.admin')

@section('title', 'Reports')
@section('page_title', 'Management Reporting')

@section('content')

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">From</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-input py-2 text-sm">
        </div>
        <div>
            <label class="form-label text-xs">To</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-input py-2 text-sm">
        </div>
        <button type="submit" class="btn-primary py-2 px-4 text-sm">Apply</button>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn-secondary py-2 px-4 text-sm ml-auto">Export CSV</a>
    </form>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Bookings', 'value' => $summary['total'], 'color' => 'text-gray-900'],
        ['label' => 'Pending', 'value' => $summary['pending'], 'color' => 'text-yellow-600'],
        ['label' => 'Confirmed', 'value' => $summary['confirmed'], 'color' => 'text-green-600'],
        ['label' => 'Cancelled', 'value' => $summary['cancelled'], 'color' => 'text-red-600'],
        ['label' => 'Completed', 'value' => $summary['completed'], 'color' => 'text-blue-600'],
    ] as $card)
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</div>
        <div class="text-xs text-gray-400 mt-1">{{ $card['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Accommodation utilization --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="font-display font-bold text-gray-900">Accommodation Utilization</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Accommodation</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Total Units</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Booked Nights</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Occupancy</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($accommodationUtilization as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $row['resource']->conservationArea->short_name }} — {{ $row['resource']->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['total_units'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['booked_nights'] }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-20 h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full bg-forest-500" style="width: {{ min(100, $row['occupancy']) }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600">{{ $row['occupancy'] }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No accommodations found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Package utilization --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="font-display font-bold text-gray-900">Package Utilization</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Package</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Bookings</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Total Pax</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Daily Capacity</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Avg Utilization</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($packageUtilization as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $row['resource']->conservationArea->short_name }} — {{ $row['resource']->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['bookings'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['total_pax'] }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $row['capacity'] ?? 'Uncapped' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row['avg_utilization'] !== null ? $row['avg_utilization'] . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No packages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
