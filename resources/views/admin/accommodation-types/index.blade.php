@extends('layouts.admin')

@section('title', 'Accommodations')
@section('page_title', 'Manage Accommodations')

@section('content')

<div class="flex justify-between items-center mb-5">
    <p class="text-gray-500 text-sm">{{ $accommodationTypes->total() }} accommodation types across all conservation areas.</p>
    <a href="{{ route('admin.accommodation-types.create') }}" class="btn-primary text-sm py-2 px-4">+ Add Accommodation</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Name</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Area</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Type</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Capacity</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Price/Night (MY)</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($accommodationTypes as $acc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $acc->name }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $acc->conservationArea->short_name }}</td>
                    <td class="px-4 py-3 text-gray-600 capitalize">{{ $acc->type }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $acc->capacity }} pax</td>
                    <td class="px-4 py-3 font-semibold text-forest-700">RM {{ number_format($acc->price_per_night, 0) }}</td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $acc->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $acc->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.accommodation-types.edit', $acc) }}" class="text-forest-600 text-xs hover:underline font-medium">Edit</a>
                        <form action="{{ route('admin.accommodation-types.destroy', $acc) }}" method="POST"
                              onsubmit="return confirm('Delete this accommodation type?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 text-xs hover:underline font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No accommodation types found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($accommodationTypes->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $accommodationTypes->links() }}</div>
    @endif
</div>

@endsection
