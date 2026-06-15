@extends('layouts.app')

@section('title', 'Activiteiten')

@section('content')

    <div
        x-data="{
            open: false,
            src: '',
            async show(image) {
                this.open = true;
                this.src = '';
                const loader = new Image();
                loader.src = image;
                try {
                    await loader.decode();
                } catch (e) {
                 // No action needed
                }
                this.src = image;
            },
        }"
        @keydown.escape.window="open = false"
        class="mx-auto w-full max-w-4xl px-6 py-8"
    >

        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-8 shadow-sm ring-1 ring-black/5 sm:p-10">

            <h1 class="text-center text-3xl font-bold text-olivegreen-400">Activiteiten</h1>
            <p class="mx-auto mt-4 max-w-2xl text-center text-sm text-black">Ontdek de leuke activiteiten die we op en rondom onze camping aanbieden.</p>

            <div class="mt-6 space-y-8 text-sm text-black">

                <div>
                    <h2 class="mb-2 border-l-4 border-olivegreen-400 pl-3 text-2xl font-semibold text-olivegreen-400">Wandelen en fietsen</h2>

                    <p class="mb-4">
                        Verken de prachtige natuur rondom de camping met onze wandel- en fietsroutes.
                        Er zijn twee fietsroutes en drie wandelroutes.
                    </p>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <button type="button" @click="show('/img/fietsroute1.png')"
                                class="cursor-pointer rounded-lg border border-cerulean-400 bg-cerulean-300 px-4 py-3 font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                            Fietsroute 1: 10 km
                        </button>
                        <button type="button" @click="show('/img/fietsroute2.png')"
                                class="cursor-pointer rounded-lg border border-cerulean-400 bg-cerulean-300 px-4 py-3 font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                            Fietsroute 2: 20 km
                        </button>
                        <button type="button" @click="show('/img/wandelroute1.png')"
                                class="cursor-pointer rounded-lg border border-cerulean-400 bg-cerulean-300 px-4 py-3 font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                            Wandelroute 1: 4 km
                        </button>
                        <button type="button" @click="show('/img/wandelroute2.png')"
                                class="cursor-pointer rounded-lg border border-cerulean-400 bg-cerulean-300 px-4 py-3 font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                            Wandelroute 2: 8 km
                        </button>
                        <button type="button" @click="show('/img/wandelroute3.png')"
                                class="cursor-pointer rounded-lg border border-cerulean-400 bg-cerulean-300 px-4 py-3 font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                            Wandelroute 3: 17 km
                        </button>
                    </div>
                </div>

                <div>
                    <h2 class="mb-2 border-l-4 border-olivegreen-400 pl-3 text-2xl font-semibold text-olivegreen-400">Boerderijactiviteiten</h2>

                    <p class="mb-4">
                        Leer meer over het boerenleven. Maak kennis met de dieren op de boerderij en geniet van de heerlijke buitenlucht.
                    </p>

                    <div class="grid items-center gap-6 rounded-lg border border-tan-500 p-4 md:grid-cols-[1fr_280px]">
                        <p>
                            Breng een bezoek aan onze kinderboerderij. Kinderen kunnen hier kennismaken met verschillende dieren, ze bewonderen en aaien. Een leuke activiteit die zorgt voor mooie herinneringen tijdens het verblijf op de camping.
                        </p>
                        <img src="/img/kinderboerderij.png"
                             @click="show('/img/kinderboerderij.png')"
                             alt="Kinderboerderij"
                             class="w-full cursor-pointer rounded-lg border border-tan-500 shadow-md transition duration-300 hover:scale-[1.03] md:w-72">
                    </div>
                </div>

                <div>
                    <h2 class="mb-2 border-l-4 border-olivegreen-400 pl-3 text-2xl font-semibold text-olivegreen-400">Kinderspeeltuin</h2>

                    <div class="grid items-center gap-6 rounded-lg border border-tan-500 p-4 md:grid-cols-[1fr_280px]">
                        <p>
                            Onze kinderspeeltuin is geschikt voor de kleintjes om te spelen en andere kinderen te leren kennen. Er is voor ieder kind wat wils: of je nu houdt van springen of glijden, het is er allemaal!
                        </p>
                        <img src="/img/kinderspeeltuin.png"
                             @click="show('/img/kinderspeeltuin.png')"
                             alt="Kinderspeeltuin"
                             class="w-full cursor-pointer rounded-lg border border-tan-500 shadow-md transition duration-300 hover:scale-[1.03] md:w-72">
                    </div>
                </div>

                <div>
                    <h2 class="mb-2 border-l-4 border-olivegreen-400 pl-3 text-2xl font-semibold text-olivegreen-400">Zwemgelegenheid</h2>

                    <div class="grid items-center gap-6 rounded-lg border border-tan-500 p-4 md:grid-cols-[1fr_280px]">
                        <p>
                            Neem op een warme dag een duik in de zwemvijver. Het is een fijne plek om te ontspannen en te spelen in het water.
                        </p>
                        <img src="/img/zwemvijver.png"
                             @click="show('/img/zwemvijver.png')"
                             alt="Zwemvijver"
                             class="w-full cursor-pointer rounded-lg border border-tan-500 shadow-md transition duration-300 hover:scale-[1.03] md:w-72">
                    </div>
                </div>

                <div>
                    <h2 class="mb-2 border-l-4 border-olivegreen-400 pl-3 text-2xl font-semibold text-olivegreen-400">Overige activiteiten</h2>

                    <div class="space-y-4">
                        <div class="grid items-center gap-6 rounded-lg border border-tan-500 p-4 md:grid-cols-[1fr_280px]">
                            <p>
                                Voor dagelijkse boodschappen vind je op 4 kilometer van de camping een supermarkt met een ruim assortiment aan producten.
                            </p>
                            <img src="/img/supermarkt.png"
                                 @click="show('/img/supermarkt.png')"
                                 alt="Supermarkt"
                                 class="w-full cursor-pointer rounded-lg border border-tan-500 shadow-md transition duration-300 hover:scale-[1.03] md:w-72">
                        </div>

                        <div class="grid items-center gap-6 rounded-lg border border-tan-500 p-4 md:grid-cols-[1fr_280px]">
                            <p>
                                Bezoek de lokale markt op 6 kilometer van de camping en ontdek verse producten uit de streek en leuke kraampjes.
                            </p>
                            <img src="/img/markt.png"
                                 @click="show('/img/markt.png')"
                                 alt="Lokale markt"
                                 class="w-full cursor-pointer rounded-lg border border-tan-500 shadow-md transition duration-300 hover:scale-[1.03] md:w-72">
                        </div>

                        <div class="grid items-center gap-6 rounded-lg border border-tan-500 p-4 md:grid-cols-[1fr_280px]">
                            <p>
                                Liever zwemmen in een zwembad? Op slechts 8 kilometer van de camping bevindt zich een zwembad waar jong en oud zich kan vermaken.
                            </p>
                            <img src="/img/zwembad.png"
                                 @click="show('/img/zwembad.png')"
                                 alt="Zwembad"
                                 class="w-full cursor-pointer rounded-lg border border-tan-500 shadow-md transition duration-300 hover:scale-[1.03] md:w-72">
                        </div>

                        <div class="grid items-center gap-6 rounded-lg border border-tan-500 p-4 md:grid-cols-[1fr_280px]">
                            <p>
                                Het nabijgelegen dorp op 3 kilometer van de camping biedt verschillende voorzieningen, zoals winkels, cafés, restaurants en een mooie kerk.
                            </p>
                            <img src="/img/dorp.png"
                                 @click="show('/img/dorp.png')"
                                 alt="Nabijgelegen dorp"
                                 class="w-full cursor-pointer rounded-lg border border-tan-500 shadow-md transition duration-300 hover:scale-[1.03] md:w-72">
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Image modal --}}
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/75"
            @click.self="open = false"
        >
            <div class="relative max-w-5xl p-4">
                <button type="button" @click="open = false" class="absolute right-6 top-4 text-4xl font-bold text-white">&times;</button>
                <img :src="src" x-show="src" x-transition.opacity alt="Activiteit" class="max-h-[90vh] rounded-lg shadow-lg">
            </div>
        </div>

    </div>

@endsection
