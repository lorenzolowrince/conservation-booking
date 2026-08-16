@extends('layouts.admin')

@section('title', 'Packages')
@section('page_title', 'Manage Packages')

@section('content')

<div class="flex justify-between items-center mb-5">
    <p class="text-gray-500 text-sm">{{ $packages->total() }} packages across all conservation areas.</p>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.packages.create') }}" class="btn-primary text-sm py-2 px-4">+ Add Package</a>
    @endif
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Package</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Area</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Duration</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Price (MY)</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Pax</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($packages as $pkg)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $pkg->name }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $pkg->conservationArea->short_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $pkg->duration_days }} day(s)</td>
                    <td class="px-4 py-3 font-semibold text-forest-700">RM {{ number_format($pkg->price_per_person, 0) }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $pkg->min_pax }}{{ $pkg->max_pax ? '–'.$pkg->max_pax : '+' }}</td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $pkg->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $pkg->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.packages.edit', $pkg) }}" class="text-forest-600 text-xs hover:underline font-medium">Edit</a>
                        <form action="{{ route('admin.packages.destroy', $pkg) }}" method="POST"
                              onsubmit="return confirm('Delete this package?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 text-xs hover:underline font-medium">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No packages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($packages->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $packages->links() }}</div>
    @endif
</div>

@endsection
