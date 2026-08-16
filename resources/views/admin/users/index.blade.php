@extends('layouts.admin')

@section('title', 'Users')
@section('page_title', 'Manage Users')

@section('content')

<div class="flex justify-between items-center mb-5">
    <p class="text-gray-500 text-sm">{{ $users->total() }} user account(s). <a href="{{ route('admin.access-matrix') }}" class="text-forest-600 hover:underline">View access matrix &rarr;</a></p>
    <a href="{{ route('admin.users.create') }}" class="btn-primary text-sm py-2 px-4">+ Add User</a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-input py-2 text-sm" placeholder="Name or email...">
        </div>
        <div>
            <label class="form-label text-xs">Role</label>
            <select name="role" class="form-input py-2 text-sm">
                <option value="">All Roles</option>
                @foreach(\App\Models\User::ROLES as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary py-2 px-4 text-sm">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary py-2 px-4 text-sm">Clear</a>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Name</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Email</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Role</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Joined</th>
                    <th class="text-left px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $u->name }}
                        @if($u->id === auth()->id())<span class="text-xs text-gray-400">(you)</span>@endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $u->email }}</td>
                    <td class="px-4 py-3">
                        <span class="badge {{ match($u->role) {
                            'super_admin' => 'bg-purple-100 text-purple-700',
                            'admin' => 'bg-forest-100 text-forest-700',
                            'staff' => 'bg-blue-100 text-blue-700',
                            default => 'bg-gray-100 text-gray-600',
                        } }}">{{ $u->roleLabel() }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $u->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.users.edit', $u) }}" class="text-forest-600 text-xs hover:underline font-medium">Edit</a>
                        @if($u->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST"
                              onsubmit="return confirm('Delete {{ addslashes($u->name) }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 text-xs hover:underline font-medium">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>

@endsection
