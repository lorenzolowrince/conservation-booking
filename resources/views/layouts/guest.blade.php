<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Yayasan Sabah Conservation') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-forest-950 to-forest-900 py-10 px-4">
            <div class="mb-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="w-12 h-12 bg-forest-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <div class="text-white font-display font-bold text-xl">Yayasan Sabah</div>
                        <div class="text-forest-400 text-sm">Conservation Booking Portal</div>
                    </div>
                </a>
            </div>
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl px-8 py-8">
                {{ $slot }}
            </div>
            <p class="mt-6 text-forest-400 text-sm">
                <a href="{{ route('home') }}" class="hover:text-forest-300 transition-colors">&larr; Back to main site</a>
            </p>
        </div>
    </body>
</html>
