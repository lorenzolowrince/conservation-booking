<nav x-data="{ open: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
     :class="scrolled ? 'bg-forest-900/95 backdrop-blur-md shadow-lg' : 'bg-forest-900'"
     class="fixed w-full z-50 top-0 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 bg-forest-500 rounded-lg flex items-center justify-center group-hover:bg-forest-400 transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <div class="hidden sm:block">
                    <div class="text-white font-display font-bold text-sm leading-tight">Yayasan Sabah</div>
                    <div class="text-forest-300 text-xs">Conservation Booking</div>
                </div>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="nav-link px-3 py-2 rounded-md hover:bg-forest-800">Home</a>
                <a href="{{ route('areas.index') }}" class="nav-link px-3 py-2 rounded-md hover:bg-forest-800">Conservation Areas</a>
                <a href="{{ route('booking.create') }}" class="nav-link px-3 py-2 rounded-md hover:bg-forest-800">Book Now</a>
                <a href="{{ route('booking.track') }}" class="nav-link px-3 py-2 rounded-md hover:bg-forest-800">Track Booking</a>
                <a href="{{ route('about') }}" class="nav-link px-3 py-2 rounded-md hover:bg-forest-800">About</a>
            </div>

            {{-- Auth buttons --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @if(auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link px-3 py-1.5 bg-earth-600 rounded-lg hover:bg-earth-700 text-white text-xs font-semibold">
                            Admin Portal
                        </a>
                    @endif
                    <div x-data="{ userMenu: false }" class="relative">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 text-white/80 hover:text-white text-sm font-medium">
                            <div class="w-8 h-8 bg-forest-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden lg:block">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="userMenu" @click.away="userMenu = false" x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                            <a href="{{ route('booking.my-bookings') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-forest-50 hover:text-forest-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Bookings
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-forest-50 hover:text-forest-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profile
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link px-3 py-2">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-earth-600 text-white text-sm font-semibold rounded-lg hover:bg-earth-700 transition-colors">
                        Register
                    </a>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <button @click="open = !open" class="md:hidden text-white/80 hover:text-white p-2">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition class="md:hidden bg-forest-900 border-t border-forest-800">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block nav-link py-2">Home</a>
            <a href="{{ route('areas.index') }}" class="block nav-link py-2">Conservation Areas</a>
            <a href="{{ route('booking.create') }}" class="block nav-link py-2">Book Now</a>
            <a href="{{ route('booking.track') }}" class="block nav-link py-2">Track Booking</a>
            <a href="{{ route('about') }}" class="block nav-link py-2">About</a>
            <div class="border-t border-forest-800 pt-3 mt-3 flex gap-3">
                @auth
                    <a href="{{ route('booking.my-bookings') }}" class="btn-secondary py-2 px-4 text-sm">My Bookings</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="text-red-400 text-sm font-medium py-2">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary py-2 px-4 text-sm">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary py-2 px-4 text-sm">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
