@extends('layouts.app')

@section('content')
<div class="flex flex-1 items-center justify-center px-6 py-12">

    <div class="border-2 border-tan-500 w-full max-w-3xl rounded-2xl bg-tan-300 p-10 text-center shadow-md">

        <h1 class="text-5xl font-bold text-olivegreen-400 mb-6">
            Over ons
        </h1>

        <p class="text-xl text-black mb-8">
            Welkom bij Camping De Groene Weide.
            Een rustige boerencamping midden in de natuur.
        </p>

        <div class="space-y-8 text-lg">

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">
                    Rust en natuur
                </h2>

                <p class="text-black">
                    Geniet van de rust, de ruimte en de frisse buitenlucht.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">
                    Het boerenleven
                </h2>

                <p class="text-black">
                    Beleef het echte buitenleven tussen de dieren en de weilanden.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">
                    Voor iedereen
                </h2>

                <p class="text-black">
                    Of u nu komt met een tent, caravan of camper:
                    u bent van harte welkom.
                </p>
            </div>

        </div>

        <div class="mt-8 border-t border-tan-600 pt-6">

            <p class="text-xl font-semibold text-olivegreen-400">
                Wij hopen u snel te zien!
            </p>

            <a
                href="{{ route('campsites.index') }}"
                class="mt-6 inline-block border-2 border-cerulean-400 rounded-2xl bg-cerulean-300 px-8 py-3 text-lg font-semibold text-cerulean-900 transition hover:bg-cerulean-400"
            >
                Bekijk onze kampeerplaatsen
            </a>

        </div>

    </div>

</div>
@endsection