@extends('layouts.admin')

@section('title', 'Access Matrix')
@section('page_title', 'User Access Matrix')

@section('content')

<div class="max-w-4xl">
    <p class="text-gray-500 text-sm mb-6">What each role can see and do across the admin panel. Roles are shown most-privileged first.</p>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Area</th>
                        <th class="text-center px-4 py-3 font-semibold text-purple-700 text-xs uppercase tracking-wide">Super Admin</th>
                        <th class="text-center px-4 py-3 font-semibold text-forest-700 text-xs uppercase tracking-wide">Admin</th>
                        <th class="text-center px-4 py-3 font-semibold text-blue-700 text-xs uppercase tracking-wide">Staff</th>
                    </tr>
                </thead>
                @php
                    $yes = '<span class="text-forest-600 font-bold">&check;</span>';
                    $no = '<span class="text-gray-300 font-bold">&times;</span>';
                    $rows = [
                        ['Dashboard', $yes, $yes, $yes],
                        ['Bookings — view, add notes, import from Excel', $yes, $yes, $yes],
                        ['Bookings — change status / payment', $yes, $yes, $no],
                        ['Conservation Areas — view', $yes, $yes, $yes],
                        ['Conservation Areas — create / edit / delete', $yes, $yes, $no],
                        ['Packages — view', $yes, $yes, $yes],
                        ['Packages — create / edit / delete', $yes, $yes, $no],
                        ['Accommodations — view', $yes, $yes, $yes],
                        ['Accommodations — create / edit / delete', $yes, $yes, $no],
                        ['Database Backups', $yes, $yes, $no],
                        ['User Management (this page\'s neighbors)', $yes, $no, $no],
                    ];
                @endphp
                <tbody class="divide-y divide-gray-50">
                    @foreach($rows as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">{{ $row[0] }}</td>
                        <td class="px-4 py-3 text-center">{!! $row[1] !!}</td>
                        <td class="px-4 py-3 text-center">{!! $row[2] !!}</td>
                        <td class="px-4 py-3 text-center">{!! $row[3] !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <span class="badge bg-purple-100 text-purple-700 mb-3">Super Admin</span>
            <p class="text-sm text-gray-600">Full access, including creating and deleting other users and changing anyone's role. There must always be at least one Super Admin — the system won't let the last one be demoted or deleted.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <span class="badge bg-forest-100 text-forest-700 mb-3">Admin</span>
            <p class="text-sm text-gray-600">Runs day-to-day operations — bookings, conservation areas, packages, accommodations, and backups. Cannot manage other users or change roles; only a Super Admin can do that.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <span class="badge bg-blue-100 text-blue-700 mb-3">Staff</span>
            <p class="text-sm text-gray-600">Front-line access — can view everything and handle bookings (including importing from Excel and adding notes), but can't confirm/cancel bookings, change payment status, edit listings, or touch backups.</p>
        </div>
    </div>
</div>

@endsection
