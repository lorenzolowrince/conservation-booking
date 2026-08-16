@extends('layouts.admin')

@section('title', 'Add User')
@section('page_title', 'Add New User')

@section('content')

<div class="max-w-xl">
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div>
                <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
            </div>
            <div>
                <label class="form-label">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" class="form-input" minlength="8" required>
                <p class="text-xs text-gray-400 mt-1">At least 8 characters.</p>
            </div>
            <div>
                <label class="form-label">Role <span class="text-red-500">*</span></label>
                <select name="role" class="form-input" required>
                    <option value="">— Select role —</option>
                    @foreach(\App\Models\User::ROLES as $r)
                    <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">See the <a href="{{ route('admin.access-matrix') }}" class="text-forest-600 hover:underline">access matrix</a> for what each role can do.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Create User</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
