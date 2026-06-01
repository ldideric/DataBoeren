<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-tan-600 text-black">
<div class="min-h-screen">

    @include('layouts.navigation')

    @hasSection('header')
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto py-4 px-6">
                <h1 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">@yield('header')</h1>
            </div>
        </header>
    @endif

    <main>
        @yield('content')
    </main>

</div>
</body>
</html>
