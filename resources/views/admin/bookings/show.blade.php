@extends('layouts.admin')

@section('title', 'Booking ' . $booking->booking_ref)
@section('page_title', 'Booking: ' . $booking->booking_ref)

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Details --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Booking Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-display font-bold text-gray-900">Booking Details</h2>
                <div class="flex items-center gap-2">
                    <span class="status-{{ $booking->status }} text-sm px-3 py-1">{{ ucfirst($booking->status) }}</span>
                    @if(auth()->user()->isStaff() && in_array($booking->status, ['pending', 'confirmed']))
                    <a href="{{ route('admin.bookings.reschedule.edit', $booking) }}" class="btn-secondary py-1.5 px-3 text-xs">Reschedule</a>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Booking Ref</div>
                    <div class="font-semibold">{{ $booking->booking_ref }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Booking Type</div>
                    <div class="font-semibold capitalize">{{ str_replace('_', ' ', $booking->booking_type) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Conservation Area</div>
                    <div class="font-semibold">{{ $booking->conservationArea->name }}</div>
                </div>
                @if($booking->package)
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Package</div>
                    <div class="font-semibold">{{ $booking->package->name }}</div>
                </div>
                @endif
                @if($booking->accommodationType)
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Accommodation</div>
                    <div class="font-semibold">{{ $booking->accommodationType->name }}</div>
                </div>
                @endif
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Check-in</div>
                    <div class="font-semibold">{{ $booking->check_in_date->format('D, d M Y') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Check-out</div>
                    <div class="font-semibold">{{ $booking->check_out_date->format('D, d M Y') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Adults</div>
                    <div class="font-semibold">{{ $booking->num_adults }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Children</div>
                    <div class="font-semibold">{{ $booking->num_children }}</div>
                </div>
            </div>

            @if($booking->special_requests)
            <div class="mt-5 pt-5 border-t border-gray-100">
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Special Requests</div>
                <div class="text-gray-700 text-sm bg-amber-50 rounded-lg p-3">{{ $booking->special_requests }}</div>
            </div>
            @endif
        </div>

        {{-- Contact --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-display font-bold text-gray-900 mb-4">Contact Information</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Full Name</div>
                    <div class="font-semibold">{{ $booking->contact_name }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Email</div>
                    <div class="font-semibold">{{ $booking->contact_email }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Phone</div>
                    <div class="font-semibold">{{ $booking->contact_phone }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Nationality</div>
                    <div class="font-semibold">{{ $booking->contact_nationality }}</div>
                </div>
            </div>
        </div>

        {{-- Admin Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-display font-bold text-gray-900 mb-4">Admin Notes</h2>
            <form action="{{ route('admin.bookings.note', $booking) }}" method="POST">
                @csrf
                <textarea name="admin_notes" rows="3" class="form-input mb-3"
                          placeholder="Add internal notes about this booking...">{{ $booking->admin_notes }}</textarea>
                <button type="submit" class="btn-primary text-sm py-2 px-4">Save Note</button>
            </form>
        </div>

        {{-- Booking Timeline --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-display font-bold text-gray-900 mb-4">Booking Timeline</h2>
            @forelse($timeline as $event)
            <div class="flex gap-3 py-3 border-b border-gray-50 last:border-0">
                <div class="w-1.5 h-1.5 rounded-full bg-forest-500 mt-2 shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-gray-800">{{ $event->description }}</div>
                    @if($event->reason)
                    <div class="text-xs text-gray-400 mt-0.5">Reason: {{ $event->reason }}</div>
                    @endif
                    <div class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->format('d M Y H:i') }} &middot; {{ $event->user?->name ?? 'System' }}</div>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm">No activity recorded yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Sidebar: Actions & Finance --}}
    <div class="space-y-5">

        {{-- Financial Summary --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-display font-bold text-gray-900 mb-4">Financial Summary</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span><span>RM {{ number_format($booking->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>SST (6%)</span><span>RM {{ number_format($booking->tax, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-base text-gray-900 pt-2 border-t border-gray-100">
                    <span>Total</span><span class="text-forest-700">RM {{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-xs text-gray-400 mb-2">Payment Status</div>
                @if(auth()->user()->isAdmin())
                <form action="{{ route('admin.bookings.payment', $booking) }}" method="POST" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="payment_status" class="form-input flex-1 py-1.5 text-sm">
                        @foreach(['unpaid', 'paid', 'refunded'] as $s)
                        <option value="{{ $s }}" {{ $booking->payment_status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-700 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-gray-800 transition-colors">Save</button>
                </form>
                @else
                <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($booking->payment_status) }}
                </span>
                @endif
            </div>
        </div>

        {{-- Update Status --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ showCancelReason: false }">
            <h3 class="font-display font-bold text-gray-900 mb-4">Update Status</h3>
            @if(auth()->user()->isAdmin())
            <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['pending', 'confirmed', 'completed'] as $s)
                    <button type="submit" name="status" value="{{ $s }}"
                            class="py-2 px-3 rounded-lg text-sm font-medium border transition-colors
                                   {{ $booking->status === $s
                                      ? 'bg-forest-700 text-white border-forest-700'
                                      : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                        {{ ucfirst($s) }}
                    </button>
                    @endforeach
                    <button type="button" @click="showCancelReason = true"
                            class="py-2 px-3 rounded-lg text-sm font-medium border transition-colors
                                   {{ $booking->status === 'cancelled'
                                      ? 'bg-red-600 text-white border-red-600'
                                      : 'bg-gray-50 text-red-600 border-gray-200 hover:bg-red-50' }}">
                        Cancelled
                    </button>
                </div>
                <div x-show="showCancelReason" x-transition class="pt-3 mt-1 border-t border-gray-100">
                    <label class="form-label text-xs">Cancellation reason <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" class="form-input text-sm" placeholder="Why is this booking being cancelled?" required>
                    <button type="submit" name="status" value="cancelled" class="btn-primary text-xs py-2 px-3 mt-2">Confirm Cancellation</button>
                </div>
            </form>
            @else
            <span class="status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
            <p class="text-xs text-gray-400 mt-2">Only Admin and above can change booking status.</p>
            @endif
            @if($booking->confirmed_at)
            <div class="mt-3 text-xs text-gray-400">Confirmed: {{ $booking->confirmed_at->format('d M Y H:i') }}</div>
            @endif
            @if($booking->status === 'cancelled' && $booking->cancellation_reason)
            <div class="mt-3 text-xs text-gray-400">Cancellation reason: {{ $booking->cancellation_reason }}</div>
            @endif
        </div>

        {{-- Assigned Staff --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-display font-bold text-gray-900 mb-3">Assigned Staff</h3>
            @if(auth()->user()->isAdmin())
            <form action="{{ route('admin.bookings.assign', $booking) }}" method="POST" class="flex gap-2">
                @csrf @method('PATCH')
                <select name="assigned_to" class="form-input flex-1 py-1.5 text-sm">
                    <option value="">— Unassigned —</option>
                    @foreach($staff as $s)
                    <option value="{{ $s->id }}" {{ $booking->assigned_to === $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-700 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-gray-800 transition-colors">Save</button>
            </form>
            @else
            <span class="text-gray-700 text-sm">{{ $booking->assignedTo?->name ?? 'Unassigned' }}</span>
            @endif
        </div>

        {{-- Booking Meta --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-sm">
            <h3 class="font-display font-bold text-gray-900 mb-3">Booking Meta</h3>
            <div class="space-y-2 text-gray-600">
                <div class="flex justify-between">
                    <span class="text-gray-400">Created</span>
                    <span>{{ $booking->created_at->format('d M Y H:i') }}</span>
                </div>
                @if($booking->user)
                <div class="flex justify-between">
                    <span class="text-gray-400">User Account</span>
                    <span>{{ $booking->user->name }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-400">Payment Method</span>
                    <span>{{ $booking->payment_method ?? 'TBD' }}</span>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.bookings.index') }}" class="btn-secondary w-full justify-center text-sm py-2.5">
            &larr; Back to Bookings
        </a>
    </div>
</div>

@endsection
