@extends('layouts.admin')

@section('title', 'Blocked Dates')
@section('page_title', 'Manage Blocked Dates')

@section('content')

<div class="flex justify-between items-center mb-5">
    <p class="text-gray-500 text-sm">{{ $blocks->total() }} block(s) configured. Blocked dates are respected by the availability engine regardless of remaining capacity.</p>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.blocked-dates.create') }}" class="btn-primary text-sm py-2 px-4">+ Block Dates</a>
    @endif
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Target</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Area</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Dates</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Reason</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Blocked By</th>
                    <th class="text-left px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($blocks as $block)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="badge {{ $block->package_id ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $block->package_id ? 'Package' : 'Accommodation' }}
                        </span>
                        <div class="font-medium text-gray-900 mt-1">{{ $block->package?->name ?? $block->accommodationType?->name }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ ($block->package ?? $block->accommodationType)?->conservationArea?->short_name }}</td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                        {{ $block->start_date->format('d M Y') }} &ndash; {{ $block->end_date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $block->reason }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $block->creator?->name }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.blocked-dates.edit', $block) }}" class="text-forest-600 text-xs hover:underline font-medium">Edit</a>
                        <form action="{{ route('admin.blocked-dates.destroy', $block) }}" method="POST"
                              onsubmit="return confirm('Remove this block? The dates will become bookable again.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 text-xs hover:underline font-medium">Remove</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No blocked dates.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($blocks->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $blocks->links() }}</div>
    @endif
</div>

@endsection
