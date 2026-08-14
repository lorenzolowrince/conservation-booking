<footer class="bg-forest-950 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            {{-- Brand --}}
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-forest-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-display font-bold text-lg">Yayasan Sabah</div>
                        <div class="text-forest-400 text-sm">Conservation Division</div>
                    </div>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                    Protecting the Heart of Borneo's extraordinary biodiversity through sustainable conservation and responsible ecotourism across 7 remarkable conservation areas in Sabah, Malaysia.
                </p>
                <div class="flex gap-4 mt-5">
                    <a href="#" class="w-9 h-9 bg-forest-800 rounded-lg flex items-center justify-center hover:bg-forest-700 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-forest-800 rounded-lg flex items-center justify-center hover:bg-forest-700 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Quick links --}}
            <div>
                <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Explore</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('areas.index') }}" class="hover:text-forest-400 transition-colors">Conservation Areas</a></li>
                    <li><a href="{{ route('booking.create') }}" class="hover:text-forest-400 transition-colors">Book a Visit</a></li>
                    <li><a href="{{ route('booking.track') }}" class="hover:text-forest-400 transition-colors">Track Booking</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-forest-400 transition-colors">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-forest-400 transition-colors">Contact</a></li>
                </ul>
            </div>

            {{-- Conservation Areas --}}
            <div>
                <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Areas</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('areas.show', 'danum-valley') }}" class="hover:text-forest-400 transition-colors">Danum Valley</a></li>
                    <li><a href="{{ route('areas.show', 'maliau-basin') }}" class="hover:text-forest-400 transition-colors">Maliau Basin</a></li>
                    <li><a href="{{ route('areas.show', 'imbak-canyon') }}" class="hover:text-forest-400 transition-colors">Imbak Canyon</a></li>
                    <li><a href="{{ route('areas.show', 'silam-coast') }}" class="hover:text-forest-400 transition-colors">Silam Coast</a></li>
                    <li><a href="{{ route('areas.show', 'taliwas-river') }}" class="hover:text-forest-400 transition-colors">Taliwas River</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-forest-900 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} Yayasan Sabah Conservation Division. All rights reserved.</p>
            <div class="flex gap-5">
                <a href="#" class="hover:text-forest-400 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-forest-400 transition-colors">Terms of Use</a>
            </div>
        </div>
    </div>
</footer>
