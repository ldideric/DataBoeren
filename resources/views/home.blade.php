@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-center justify-center px-6 py-4">
        <div class="w-full md:w-1/2 p-2">
            <img src="{{ asset('img/home_pagina_foto2.jpg') }}" alt="Camping afbeelding" class="w-full h-64 md:h-full object-cover rounded-lg border-3 border-tan-600"/>
        </div>
        
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
    <div class="mx-auto w-full max-w-4xl px-6 py-8">

        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-8 shadow-sm ring-1 ring-black/5 sm:p-10">

            <h1 class="text-center text-3xl font-bold text-olivegreen-400">Over ons</h1>
            <p class="mx-auto mt-4 max-w-2xl text-center text-sm text-black">Welkom bij Camping De Groene Weide. Bij ons geniet je van het buitenleven: wakker worden met het geluid van fluitende vogels en een prachtig uitzicht over de weilanden. Beleef het boerderijleven van dichtbij. Of je nu de koeien wilt aaien of juist rustig wilt genieten van de natuur om je heen, bij ons kan het allemaal.</p>

            <div class="mt-6 space-y-6 text-sm text-black">

                <div>
                    <h2 class="mb-2 border-l-4 border-olivegreen-400 pl-3 text-2xl font-semibold text-olivegreen-400">Gastvrijheid</h2>
                    <p>Wij delen graag onze passie voor de natuur en de dieren met onze gasten. Gastvrijheid staat bij ons voorop. We maken graag een praatje en doen ons best om ervoor te zorgen dat iedereen zich welkom en thuis voelt.</p>
                </div>

                <div>
                    <h2 class="mb-2 border-l-4 border-olivegreen-400 pl-3 text-2xl font-semibold text-olivegreen-400">Wat kunt u verwachten?</h2>
                    <p>Ruime kampeerplaatsen met een prachtig uitzicht, activiteiten op en rondom de boerderij en een ontspannen sfeer voor jong en oud. Heeft u behoefte aan meer concrete informatie over de camping? Bekijk dan onze <a href="{{ route('houserules') }}" class="text-cerulean-400 underline hover:text-cerulean-500">campingregels</a>.</p>
                </div>

            </div>

            <div class="mt-8 border-t border-tan-600 pt-6 text-center">
                <p class="text-sm font-semibold text-olivegreen-400">Wij hopen u snel te mogen verwelkomen!</p>
                <a href="{{ route('campsites.index') }}" class="mt-6 inline-block rounded-2xl border-2 border-cerulean-400 bg-cerulean-300 px-8 py-3 text-lg font-semibold text-cerulean-900 transition hover:bg-cerulean-400">Bekijk onze kampeerplaatsen</a>
            </div>

        </div>

    </div>
@endsection
