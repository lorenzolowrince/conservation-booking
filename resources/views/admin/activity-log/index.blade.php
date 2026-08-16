@extends('layouts.admin')

@section('title', 'Audit Log')
@section('page_title', 'Audit Log')

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">Action</label>
            <select name="action" class="form-input py-2 text-sm">
                <option value="">All Actions</option>
                @foreach(\App\Models\ActivityLog::ACTIONS as $a)
                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst(str_replace(['.', '_'], [' — ', ' '], $a)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label text-xs">User</label>
            <select name="user_id" class="form-input py-2 text-sm">
                <option value="">All Users</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label text-xs">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-input py-2 text-sm">
        </div>
        <div>
            <label class="form-label text-xs">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-input py-2 text-sm">
        </div>
        <button type="submit" class="btn-primary py-2 px-4 text-sm">Filter</button>
        <a href="{{ route('admin.activity-log.index') }}" class="btn-secondary py-2 px-4 text-sm">Clear</a>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">When</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Action</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Subject</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Description</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <span class="badge bg-gray-100 text-gray-700">{{ $log->action }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($log->subject_type === \App\Models\Booking::class && $log->subject)
                        <a href="{{ route('admin.bookings.show', $log->subject_id) }}" class="text-forest-600 hover:underline font-medium">{{ $log->subject->booking_ref }}</a>
                        @elseif($log->subject_type)
                        <span class="text-gray-500 text-xs">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}{{ $log->subject ? '' : ' (deleted)' }}</span>
                        @else
                        <span class="text-gray-300">&mdash;</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ $log->description }}
                        @if($log->reason)
                        <div class="text-xs text-gray-400 mt-0.5">Reason: {{ $log->reason }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->user?->name ?? 'System' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No activity recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
