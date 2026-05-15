{{--
    layouts/app.blade.php
    This is the main layout. Every page that does @extends('layouts.app') gets
    wrapped in this HTML shell.

    The two "slots" child views can fill in are:
      - @section('header')  → rendered inside the page heading bar (optional)
      - @section('content') → rendered inside <main> (required)
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
<div class="min-h-screen">

    {{-- Navbar partial — lives in layouts/navigation.blade.php --}}
    @include('layouts.navigation')

    {{-- Optional page heading bar — filled with @section('header') in the child view --}}
    @hasSection('header')
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto py-4 px-6">
                <h1 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">@yield('header')</h1>
            </div>
        </header>
    @endif

    {{-- Main content — filled with @section('content') in the child view --}}
    <main>
        @yield('content')
    </main>

</div>
</body>
</html>
