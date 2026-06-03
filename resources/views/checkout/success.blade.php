@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-2xl px-6 py-8">
        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-6 shadow-sm ring-1 ring-black/5 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-700 text-2xl">
                ✓
            </div>
            <h1 class="text-xl font-bold text-olivegreen-400">Betaling voltooid</h1>
            <p class="mt-2 text-sm text-black">
                Bedankt voor uw betaling. Uw reservering is bevestigd.
            </p>

            <div class="mt-4 rounded-lg border border-olivegreen-600 bg-olivegreen-300 p-4 text-left text-sm text-black">
                <p><span class="font-medium">Standplaats:</span> {{ $reservation->campsite->name }}</p>
                <p class="mt-1"><span class="font-medium">Incheck:</span> {{ $reservation->check_in->format('d M Y') }}</p>
                <p class="mt-1"><span class="font-medium">Uitcheck:</span> {{ $reservation->check_out->format('d M Y') }}</p>
            </div>

            <a href="{{ route('login') }}" class="mt-6 inline-block rounded-lg border-2 border-cerulean-400 bg-cerulean-300 px-6 py-2 text-sm font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                Naar mijn boekingen
            </a>
        </div>
    </div>
@endsection
