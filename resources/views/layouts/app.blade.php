<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overscroll-y-none bg-olivegreen-500">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preload" as="image" href="/img/camping_background.jpg">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-black">
<div class="relative flex flex-col min-h-screen bg-cover bg-center bg-fixed" style="background-image: url('/img/camping_background.jpg')">
    <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>

    @include('layouts.navigation')

    @hasSection('header')
        <header class="relative bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto py-4 px-6">
                <h1 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">@yield('header')</h1>
            </div>
        </header>
    @endif

    @hasSection('footer')
        <footer class="relative bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto py-4 px-6">
                <h1 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">@yield('footer')</h1>
            </div>
        </footer>
    @endif

    <main class="relative flex-1 flex flex-col">
        @yield('content')
    </main>

     @include('layouts.footer')

</div>
@livewireScripts
</body>
</html>
