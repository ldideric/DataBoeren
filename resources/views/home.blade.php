@extends('layouts.app')

@section('content')
    <section class="mx-auto flex w-full max-w-3xl flex-1 flex-col items-center justify-center px-6 py-28 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-white/80 drop-shadow">Camping</p>
        <h1 class="mt-4 text-5xl font-bold text-olivegreen-500 drop-shadow-lg sm:text-6xl">De Groene Weide</h1>
        <div class="mt-6 h-px w-16 bg-white/60"></div>
        <p class="mt-6 max-w-lg text-lg leading-relaxed text-white drop-shadow-md">
            Een gezellige camping midden in de natuur. Rust, ruimte en het boerderijleven van dichtbij.
        </p>
        <a href="{{ route('campsites.index') }}"
           class="mt-9 rounded-full bg-cerulean-300 border-2 border-cerulean-400 px-10 py-4 font-semibold text-white shadow-lg transition hover:bg-cerulean-400">
            Boek nu
        </a>
        <a href="{{ route('login') }}"
           class="mt-4 text-sm font-semibold text-olivegreen-100 underline underline-offset-4 hover:no-underline">
            Al geboekt? Bekijk uw boekingen
        </a>
    </section>

    <div class="bg-tan-300">
        <div class="mx-auto w-full max-w-5xl px-6 py-16">

            @if (session('status'))
                <div class="mb-10 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    <span class="font-semibold">✓</span> {{ session('status') }}
                </div>
            @endif

            <div class="text-center">
                <h2 class="text-3xl font-bold text-olivegreen-600">Over ons</h2>
                <p class="mx-auto mt-5 max-w-2xl text-lg leading-relaxed text-black">
                    Bij ons geniet je van het buitenleven: wakker worden met fluitende vogels en een prachtig
                    uitzicht over de weilanden. Of je nu de koeien wilt aaien of juist rustig wilt genieten
                    van de natuur, bij ons kan het allemaal.
                </p>
            </div>

            <div class="mt-12 grid items-center gap-10 md:grid-cols-2">
                <img src="{{ asset('img/home_pagina_foto2.jpg') }}"
                     alt="Uitzicht over de weilanden van Camping De Groene Weide"
                     class="aspect-4/3 w-full rounded-2xl object-cover shadow-xl" />
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-bold text-olivegreen-600">Gastvrijheid</h3>
                        <p class="mt-2 leading-relaxed text-black">
                            We delen onze passie voor de natuur en de dieren graag met onze gasten.
                            Een praatje hoort erbij, iedereen voelt zich hier welkom en thuis.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-olivegreen-600">Wat kunt u verwachten?</h3>
                        <p class="mt-2 leading-relaxed text-black">
                            Ruime kampeerplaatsen met een mooi uitzicht, activiteiten op en rond de boerderij
                            en een ontspannen sfeer voor jong en oud. Meer weten? Bekijk onze
                            <a href="{{ route('houserules') }}" class="font-medium text-cerulean-600 underline hover:no-underline">campingregels</a>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-14 text-center">
                <a href="{{ route('campsites.index') }}"
                   class="inline-block rounded-full bg-cerulean-300 border-2 border-cerulean-400 px-10 py-4 font-semibold text-white shadow-md transition hover:bg-cerulean-400">
                    Bekijk onze kampeerplaatsen
                </a>
            </div>
        </div>
    </div>
@endsection
