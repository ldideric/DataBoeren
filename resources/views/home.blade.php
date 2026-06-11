@extends('layouts.app')

@section('content')
    <div class="flex flex-1 items-center justify-center px-6 py-4">
        <div class="border-2 border-tan-500 w-full max-w-lg rounded-2xl bg-tan-300 p-10 text-center shadow-md">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                    <span class="font-semibold">✓</span> {{ session('status') }}
                </div>
            @endif

            <h1 class="text-4xl font-bold text-olivegreen-400">Camping De Groene Weide</h1>
            <p class="mt-3 text-lg text-black">Welkom bij onze gezellige camping midden in de natuur.</p>

            <a
                href="{{ route('campsites.index') }}"
                class="mt-8 block border-2 border-cerulean-400 w-full rounded-2xl bg-cerulean-300 px-6 py-4 text-xl font-semibold text-cerulean-900 transition hover:bg-cerulean-400"
            >
                Boek nu
            </a>

            <div class="mt-8 border-t border-tan-600 pt-5">
                <p class="text-base text-black">Al geboekt? Vraag een link aan om uw boekingen te bekijken of te annuleren:</p>
                <a
                    href="{{ route('login') }}"
                    class="mt-2 inline-block font-semibold text-olivegreen-400 underline hover:no-underline"
                >
                    Naar mijn boekingen
                </a>
            </div>
        </div>
    </div>
@endsection
