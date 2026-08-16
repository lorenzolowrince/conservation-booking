@extends('layouts.admin')

@section('title', 'Availability Calendar')
@section('page_title', 'Availability Calendar')

@section('content')

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">View</label>
            <select name="view" class="form-input py-2 text-sm" onchange="this.form.submit()">
                <option value="accommodations" {{ $view === 'accommodations' ? 'selected' : '' }}>Accommodations</option>
                <option value="packages" {{ $view === 'packages' ? 'selected' : '' }}>Packages</option>
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Area</label>
            <select name="area_id" class="form-input py-2 text-sm" onchange="this.form.submit()">
                <option value="">All Areas</option>
                @foreach($areas as $area)
                <option value="{{ $area->id }}" {{ (string) $areaId === (string) $area->id ? 'selected' : '' }}>{{ $area->short_name }}</option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="days" value="{{ $days }}">
        <div class="flex gap-2 ml-auto">
            <a href="{{ request()->fullUrlWithQuery(['start' => $start->copy()->subDays($days)->toDateString()]) }}" class="btn-secondary py-2 px-3 text-sm">&larr; Previous {{ $days }} days</a>
            <a href="{{ request()->fullUrlWithQuery(['start' => now()->toDateString()]) }}" class="btn-secondary py-2 px-3 text-sm">Today</a>
            <a href="{{ request()->fullUrlWithQuery(['start' => $start->copy()->addDays($days)->toDateString()]) }}" class="btn-secondary py-2 px-3 text-sm">Next {{ $days }} days &rarr;</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 uppercase tracking-wide sticky left-0 bg-gray-50 z-10 min-w-[12rem]">
                        {{ $view === 'packages' ? 'Package' : 'Accommodation' }}
                    </th>
                    @foreach($dates as $date)
                    <th class="text-center px-2 py-3 font-semibold text-gray-500 min-w-[3rem] {{ $date->isToday() ? 'bg-forest-50' : '' }}">
                        {{ $date->format('d') }}<br><span class="font-normal normal-case">{{ $date->format('D') }}</span>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rows as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 sticky left-0 bg-white font-medium text-gray-800 whitespace-nowrap">
                        {{ $row['resource']->conservationArea->short_name }} — {{ $row['resource']->name }}
                    </td>
                    @foreach($row['cells'] as $cell)
                    <td class="text-center px-1 py-2 {{ $cell['date']->isToday() ? 'bg-forest-50/40' : '' }}">
                        @if($cell['blocked'])
                        <span class="inline-flex items-center justify-center w-9 h-6 rounded bg-gray-200 text-gray-500 font-semibold" title="Blocked">B</span>
                        @elseif($cell['total'] === null)
                        <span class="inline-flex items-center justify-center w-9 h-6 rounded bg-forest-50 text-forest-600 font-semibold" title="Uncapped">&infin;</span>
                        @elseif($cell['remaining'] === 0)
                        <span class="inline-flex items-center justify-center w-9 h-6 rounded bg-red-100 text-red-700 font-semibold" title="Full">{{ $cell['used'] }}/{{ $cell['total'] }}</span>
                        @elseif($cell['remaining'] <= max(1, (int) ($cell['total'] * 0.25)))
                        <span class="inline-flex items-center justify-center w-9 h-6 rounded bg-amber-100 text-amber-700 font-semibold" title="Limited">{{ $cell['used'] }}/{{ $cell['total'] }}</span>
                        @else
                        <span class="inline-flex items-center justify-center w-9 h-6 rounded bg-green-100 text-green-700 font-semibold" title="Available">{{ $cell['used'] }}/{{ $cell['total'] }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @empty
                <tr><td colspan="{{ $days + 1 }}" class="px-4 py-10 text-center text-gray-400">No active {{ $view }} found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500">
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-100 border border-green-200"></span> Available</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-100 border border-amber-200"></span> Limited</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-100 border border-red-200"></span> Full</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-200 border border-gray-300"></span> Blocked</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-forest-50 border border-forest-100"></span> Uncapped</span>
</div>

@endsection
