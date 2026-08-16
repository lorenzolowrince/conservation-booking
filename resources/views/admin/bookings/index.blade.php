@extends('layouts.admin')

@section('title', 'Bookings')
@section('page_title', 'Manage Bookings')

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-input py-2 text-sm" placeholder="Ref, name, email...">
        </div>
        <div>
            <label class="form-label text-xs">Status</label>
            <select name="status" class="form-input py-2 text-sm">
                <option value="">All Statuses</option>
                @foreach(['pending', 'confirmed', 'cancelled', 'completed'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary py-2 px-4 text-sm">Filter</button>
        <a href="{{ route('admin.bookings.index') }}" class="btn-secondary py-2 px-4 text-sm">Clear</a>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.bookings.import.form') }}" class="btn-secondary py-2 px-4 text-sm">
                Import from Excel
            </a>
            <a href="{{ route('admin.bookings.create') }}" class="btn-primary py-2 px-4 text-sm">
                + New Booking
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Ref</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Contact</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Area</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Dates</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Amount</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Payment</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Assigned</th>
                    <th class="text-left px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ $booking->booking_ref }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $booking->contact_name }}</div>
                        <div class="text-gray-400 text-xs">{{ $booking->contact_email }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $booking->conservationArea->short_name }}</td>
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                        {{ $booking->check_in_date->format('d M Y') }}<br>
                        to {{ $booking->check_out_date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">RM {{ number_format($booking->total_amount, 2) }}</td>
                    <td class="px-4 py-3"><span class="status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ $booking->assignedTo?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-forest-600 hover:text-forest-800 font-medium text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400">No bookings found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>

@endsection
