@extends('layouts.admin')

@section('title', 'Backup Management')
@section('page_title', 'Backup Management')

@section('content')

{{-- Error flash --}}
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- Info Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

    {{-- Manual Backup Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-1">Manual Backup</h3>
                <p class="text-sm text-gray-500 mb-4">Create a full backup of the application and database right now.</p>

                <form action="{{ route('admin.backup.create') }}" method="POST"
                      x-data="{ loading: false }"
                      @submit="loading = true">
                    @csrf
                    <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="loading ? 'Creating backup...' : 'Create Backup Now'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Scheduled Backup Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 bg-forest-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-1">Scheduled Backup</h3>
                <p class="text-sm text-gray-500 mb-3">Automatic backup runs every day at midnight.</p>
                <div class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>
                    Active — Daily at 12:00 AM
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    Next run: {{ $nextScheduled->format('d M Y, 12:00 AM') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-1">Backup Stats</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Total backups</span>
                        <span class="font-semibold text-gray-900">{{ $backups->total() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Completed</span>
                        <span class="font-semibold text-green-700">{{ $backups->getCollection()->where('status', 'completed')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Failed</span>
                        <span class="font-semibold text-red-700">{{ $backups->getCollection()->where('status', 'failed')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Backup History Table --}}
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-display font-bold text-gray-900">Backup History</h2>
        <span class="text-sm text-gray-400">{{ $backups->total() }} total</span>
    </div>

    @if($backups->isEmpty())
    <div class="p-12 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        <p class="text-sm">No backups yet. Create your first backup above.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">Filename</th>
                    <th class="px-5 py-3 text-left font-medium">Type</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-left font-medium">Size</th>
                    <th class="px-5 py-3 text-left font-medium">Created</th>
                    <th class="px-5 py-3 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($backups as $backup)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-mono text-xs text-gray-700 truncate max-w-xs" title="{{ $backup->filename }}">
                                {{ $backup->filename }}
                            </span>
                        </div>
                        @if($backup->notes)
                        <p class="text-xs text-red-500 mt-0.5 ml-6">{{ Str::limit($backup->notes, 80) }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @if($backup->type === 'manual')
                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Manual
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-forest-50 text-forest-700 text-xs font-medium px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Scheduled
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @if($backup->status === 'completed')
                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Completed
                        </span>
                        @elseif($backup->status === 'failed')
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 text-xs font-medium px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            Failed
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 text-xs font-medium px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                            Pending
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-gray-500">
                        {{ $backup->status === 'completed' ? $backup->formatted_size : '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-gray-500">
                        <span title="{{ $backup->created_at->format('d M Y, H:i:s') }}">
                            {{ $backup->created_at->format('d M Y, H:i') }}
                        </span>
                        <div class="text-xs text-gray-400">{{ $backup->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($backup->status === 'completed' && $backup->fileExists())
                            <a href="{{ route('admin.backup.download', $backup) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                            @endif

                            <form action="{{ route('admin.backup.destroy', $backup) }}" method="POST"
                                  onsubmit="return confirm('Delete this backup? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($backups->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $backups->links() }}
    </div>
    @endif
    @endif
</div>

{{-- Info notice --}}
<div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex gap-3 text-sm text-amber-800">
    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <strong>Storage location:</strong> Backups are stored in <code class="bg-amber-100 px-1 rounded">storage/app/backups/</code>.
        For production, ensure this directory is included in your server backup strategy.
        The scheduled backup runs automatically at <strong>12:00 AM daily</strong> via Laravel Scheduler
        — make sure <code class="bg-amber-100 px-1 rounded">php artisan schedule:run</code> is registered in your system cron.
    </div>
</div>

@endsection
