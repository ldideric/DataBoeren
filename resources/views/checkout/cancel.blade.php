@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl px-6 py-8">
        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-6 shadow-sm ring-1 ring-black/5 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-700 text-2xl">
                ✕
            </div>
            <h1 class="text-xl font-bold text-olivegreen-400">Betaling geannuleerd</h1>
            <p class="mt-2 text-sm text-black">
                Uw betaling is niet afgerond. U kunt het later opnieuw proberen vanuit uw boekingen.
            </p>

            @if (isset($retryUrl))
                <a href="{{ $retryUrl }}" class="mt-6 inline-block rounded-lg border-2 border-cerulean-400 bg-cerulean-300 px-6 py-2 text-sm font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                    Opnieuw proberen
                </a>
            @endif

            <a href="{{ route('login') }}" class="{{ isset($retryUrl) ? 'mt-3' : 'mt-6' }} inline-block rounded-lg border-2 border-cerulean-400 bg-cerulean-300 px-6 py-2 text-sm font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                Naar mijn boekingen
            </a>
        </div>
    </div>
@endsection
