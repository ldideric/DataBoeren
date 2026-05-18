{{-- om server te starten gebruik 2 verschillende git bash instances
1. npm run dev
2. php artisan serve --}}

{{--
    bookings/page.blade.php
    This is a child view. It uses @extends to wrap itself in the layout,
    then fills in the layout's sections with @section ... @endsection.
--}}
@extends('layouts.app')

{{-- This fills the optional header bar in the layout --}}
@section('header')
    Boekingen
@endsection

{{-- This fills the <main> block in the layout --}}
@section('content')
    <div class="max-w-4xl mx-auto py-8 px-6">

        <p class="text-sm text-gray-500 mb-6">
            Welkom terug, <span class="font-medium text-gray-700">{{ Auth::user()?->name ?? 'gast' }}</span>.
            Hier is een overzicht van alle huidige boekingen.
        </p>

        {{-- Placeholder booking cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach (['Kampeerplaats A', 'Kampeerplaats B', 'Kampeerplaats C'] as $campsite)
                <div class="bg-white border border-gray-200 rounded-lg p-5 flex flex-col gap-3">

                    <div class="flex items-start justify-between gap-2">
                        <h2 class="font-medium text-gray-900">{{ $campsite }}</h2>
                        <span class="shrink-0 text-xs font-medium text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">
                            Bevestigd
                        </span>
                    </div>

                    <div class="text-sm text-gray-500 space-y-0.5">
                        <p>Aankomst: <span class="text-gray-700">12 jun 2026</span></p>
                        <p>Vertrek: <span class="text-gray-700">15 jun 2026</span></p>
                    </div>

                    <p class="text-sm text-gray-400 border-t border-gray-100 pt-3">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Ut enim ad minim veniam, quis nostrud exercitation.
                    </p>

                    <div class="flex gap-2 mt-auto pt-1">
                        <a href="#" class="flex-1 text-center text-sm px-3 py-1.5 bg-green-700 text-white rounded-md hover:bg-green-800 transition-colors">
                            Bekijken
                        </a>
                        <a href="#" class="flex-1 text-center text-sm px-3 py-1.5 border border-gray-200 text-gray-600 rounded-md hover:bg-gray-50 transition-colors">
                            Annuleren
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
@endsection
