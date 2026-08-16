@extends('layouts.admin')

@section('title', 'Edit User')
@section('page_title', 'Edit User')

@section('content')

<div class="max-w-xl">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div>
                <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            </div>
            <div>
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
            </div>
            <div>
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-input" minlength="8">
                <p class="text-xs text-gray-400 mt-1">Leave blank to keep the current password.</p>
            </div>
            <div>
                <label class="form-label">Role <span class="text-red-500">*</span></label>
                <select name="role" class="form-input" required {{ $user->id === auth()->id() && $user->isSuperAdmin() ? 'disabled' : '' }}>
                    @foreach(\App\Models\User::ROLES as $r)
                    <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                    @endforeach
                </select>
                @if($user->id === auth()->id() && $user->isSuperAdmin())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="text-xs text-gray-400 mt-1">You can't change your own Super Admin role. Have another Super Admin do it if needed.</p>
                @else
                    <p class="text-xs text-gray-400 mt-1">See the <a href="{{ route('admin.access-matrix') }}" class="text-forest-600 hover:underline">access matrix</a> for what each role can do.</p>
                @endif
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
