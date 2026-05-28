@extends('layouts.app')

@section('header')
    Betaling voltooid
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto max-w-2xl px-6 py-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-700 text-2xl">
                    ✓
                </div>
                <h1 class="text-xl font-bold text-gray-900">Betaling voltooid</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Bedankt voor uw betaling. Uw reservering is bevestigd.
                </p>

                <div class="mt-4 rounded-lg bg-gray-50 p-4 text-left text-sm text-gray-700">
                    <p><span class="font-medium">Standplaats:</span> {{ $reservation->campsite->name }}</p>
                    <p class="mt-1"><span class="font-medium">Incheck:</span> {{ $reservation->check_in->format('d M Y') }}</p>
                    <p class="mt-1"><span class="font-medium">Uitcheck:</span> {{ $reservation->check_out->format('d M Y') }}</p>
                </div>

                <a href="{{ route('login') }}" class="mt-6 inline-block rounded-lg border border-gray-900 px-6 py-2 text-sm font-semibold text-gray-900 transition hover:bg-gray-900 hover:text-white">
                    Naar mijn boekingen
                </a>
            </div>
        </div>
    </div>
@endsection
