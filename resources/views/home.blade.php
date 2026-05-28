@extends('layouts.app')

@section('content')
    <div class="bg-tan">
        <div class="mx-auto flex min-h-[calc(100vh-56px)] max-w-6xl items-center justify-center px-6 py-12">
            <div class="w-full max-w-lg rounded-2xl bg-tan2 p-10 text-center shadow-[0_4px_20px_rgba(0,0,0,0.08)]">
                @if (session('status'))
                    <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <h1 class="text-4xl font-bold text-olivegreen2">Camping De Groene Weide</h1>
                <p class="mt-3 text-lg text-black">Welkom bij onze gezellige camping midden in de natuur.</p>

                <a
                    href="{{ route('campsites.index') }}"
                    class="mt-8 block w-full rounded-2xl bg-cerulean px-6 py-4 text-lg font-medium text-white transition hover:bg-cerulean2"
                >
                    Boek nu
                </a>

                <div class="mt-8 border-t border-olivegreen2 pt-5">
                    <p class="text-base text-black">Al geboekt? Vraag een link aan om uw boekingen te bekijken of te annuleren:</p>
                    <a
                        href="{{ route('login') }}"
                        class="mt-2 inline-block font-semibold text-olivegreen2 underline hover:no-underline"
                    >
                        Naar mijn boekingen
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection