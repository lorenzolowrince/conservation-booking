<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — YS Conservation Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-gray-100 font-sans" x-data="{ sidebarOpen: false }">

    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside class="w-64 bg-forest-950 text-white flex-col hidden lg:flex fixed inset-y-0 left-0 z-50">
            {{-- Logo --}}
            <div class="h-16 flex items-center gap-3 px-5 border-b border-forest-900">
                <div class="w-8 h-8 bg-forest-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <div>
                    <div class="text-white font-semibold text-sm leading-tight">YS Conservation</div>
                    <div class="text-forest-400 text-xs">Admin Portal</div>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['route' => 'admin.bookings.index', 'label' => 'Bookings', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['route' => 'admin.areas.index', 'label' => 'Conservation Areas', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                        ['route' => 'admin.packages.index', 'label' => 'Packages', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['route' => 'admin.accommodation-types.index', 'label' => 'Accommodations', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3v-6a1 1 0 011-1h2a1 1 0 011 1v6h3a1 1 0 001-1V10m-9 3h4'],
                        ['route' => 'admin.blocked-dates.index', 'label' => 'Blocked Dates', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ];

                    if (auth()->user()->isAdmin()) {
                        $navItems[] = ['route' => 'admin.backup.index', 'label' => 'Backup', 'icon' => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4'];
                    }

                    if (auth()->user()->isSuperAdmin()) {
                        $navItems[] = ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-8.13a4 4 0 110 8 4 4 0 010-8zm6 8a4 4 0 10-8 0'];
                    }

                    if (auth()->user()->isAdmin()) {
                        $navItems[] = ['route' => 'admin.access-matrix', 'label' => 'Access Matrix', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
                    }
                @endphp

                @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs($item['route'] . '*') ? 'bg-forest-800 text-white' : 'text-forest-300 hover:bg-forest-900 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
                @endforeach

                <div class="pt-4 border-t border-forest-900 mt-4">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-forest-300 hover:bg-forest-900 hover:text-white transition-colors">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        View Public Site
                    </a>
                </div>
            </nav>

            {{-- User info --}}
            <div class="p-4 border-t border-forest-900">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-forest-700 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-forest-400 text-xs capitalize">{{ auth()->user()->role }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log out" class="text-forest-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
            {{-- Top bar --}}
            <header class="h-16 min-h-[4rem] max-h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-40 overflow-hidden">
                <div class="flex items-center gap-3 min-w-0 flex-1 mr-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900 truncate">@yield('page_title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-500 shrink-0">
                    <span class="hidden sm:inline whitespace-nowrap">{{ now()->format('d M Y') }}</span>

                    {{-- Quick Backup Button — only on Backup Management page --}}
                    @if(request()->routeIs('admin.backup.*'))
                    <form action="{{ route('admin.backup.create') }}" method="POST"
                          x-data="{ loading: false }"
                          @submit="loading = true">
                        @csrf
                        <button type="submit"
                                :disabled="loading"
                                title="Create a manual backup now"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-forest-600 hover:bg-forest-700 disabled:opacity-60 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap">
                            <svg x-show="!loading" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <svg x-show="loading" class="w-3.5 h-3.5 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="loading ? 'Backing up…' : 'Backup Now'"></span>
                        </button>
                    </form>
                    @endif
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0 1 1 0 01-2 0zm1-9a1 1 0 011 1v5a1 1 0 11-2 0V5a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
            @endif

            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    @stack('scripts')
</body>
</html>
