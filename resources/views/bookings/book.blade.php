@extends('layouts.app')

@section('header')
    Voorkeuren
@endsection

@section('content')
    <div class="bg-gray-100">
        <div class="mx-auto max-w-6xl px-6 py-8">
            <div class="flex flex-col gap-6 lg:flex-row">
                <aside class="w-full lg:w-80">
                    <div class="space-y-5">
                        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                            <label for="datestart" class="text-sm font-semibold text-gray-700">Start datum</label>
                            <input
                                type="date"
                                id="datestart"
                                name="datestart"
                                min="2026-05-18"
                                class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                            >
                        </div>

                        <h1 class="text-2xl font-semibold text-gray-900">Voorkeuren</h1>

                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                            <h2 class="text-lg font-semibold text-gray-900">Accommodatie type</h2>
                            <p class="mt-2 text-sm text-gray-600">Je mag maar een accommodatie type selecteren.</p>

                            <div class="mt-4 space-y-2">
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                    <input type="radio" name="accommodatie" value="Trekkersveldje" class="h-4 w-4 accent-green-600">
                                    <span>Trekkersveldje</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                    <input type="radio" name="accommodatie" value="Camper" class="h-4 w-4 accent-green-600">
                                    <span>Camper</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                    <input type="radio" name="accommodatie" value="Caravan" class="h-4 w-4 accent-green-600">
                                    <span>Caravan</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                    <input type="radio" name="accommodatie" value="Tent" class="h-4 w-4 accent-green-600">
                                    <span>Tent</span>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                            <h2 class="text-lg font-semibold text-gray-900">Ligging</h2>
                            <p class="mt-2 text-sm text-gray-600">Je mag maar een ligging voorkeur selecteren.</p>

                            <div class="mt-4 space-y-2">
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                    <input type="radio" name="ligging" value="Dichter bij het water" class="h-4 w-4 accent-green-600">
                                    <span>Dichter bij het water</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                    <input type="radio" name="ligging" value="Dichtbij de speeltuin" class="h-4 w-4 accent-green-600">
                                    <span>Dichtbij de speeltuin</span>
                                </label>
                            </div>
                        </div>

                        <button
                            id="toonVoorkeuren"
                            class="w-full rounded-lg bg-black py-3 text-base font-medium text-white transition hover:bg-gray-900"
                        >
                            Bekijk voorkeuren
                        </button>
                    </div>
                </aside>

                <main class="flex-1">
                    <h2 class="text-3xl font-semibold text-gray-900">Camping Boekingspagina</h2>
                    <p class="mt-2 text-base text-gray-600">Bekijk alle beschikbare accommodaties</p>

                    <div id="resultaatTekst" class="mt-4 rounded-lg border border-gray-200 bg-white px-4 py-3 font-semibold text-gray-900">
                        20 beschikbaarheden gevonden
                    </div>

                    <div class="mt-6 space-y-4">
                        <div
                            class="accommodatie flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 transition hover:scale-[1.01] hover:shadow-md sm:flex-row sm:items-center"
                            data-type="Trekkersveldje"
                            data-ligging="Water"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1504851149312-7a075b496cc7?q=80&w=1200"
                                alt="Trekkersveldje"
                                class="h-20 w-full rounded-lg object-cover sm:w-32"
                            >

                            <div class="flex-1 sm:px-4">
                                <h3 class="text-lg font-semibold text-gray-900">Trekkersveldje</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Kleine rustige natuurplekken, ideaal voor wandelaars en fietsers.
                                </p>
                                <p class="mt-2 text-xs text-gray-500">✔ Natuur • ✔ Budget • ✔ Rustig</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">Nog <strong>5</strong> plekken beschikbaar</p>
                            </div>

                            <div class="text-lg font-bold text-gray-900">vanaf €21</div>
                        </div>

                        <div
                            class="accommodatie flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 transition hover:scale-[1.01] hover:shadow-md sm:flex-row sm:items-center"
                            data-type="Camper"
                            data-ligging="Water"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1516939884455-1445c8652f83?q=80&w=1200"
                                alt="Camperplaats"
                                class="h-20 w-full rounded-lg object-cover sm:w-32"
                            >

                            <div class="flex-1 sm:px-4">
                                <h3 class="text-lg font-semibold text-gray-900">Camperplaats</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Ruime camperplaatsen met stroom en uitzicht op water.
                                </p>
                                <p class="mt-2 text-xs text-gray-500">✔ Stroom • ✔ Ruim • ✔ Comfort</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">Nog <strong>5</strong> plekken beschikbaar</p>
                            </div>

                            <div class="text-lg font-bold text-gray-900">vanaf €38</div>
                        </div>

                        <div
                            class="accommodatie flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 transition hover:scale-[1.01] hover:shadow-md sm:flex-row sm:items-center"
                            data-type="Caravan"
                            data-ligging="Speeltuin"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=1200"
                                alt="Caravanplaatsen"
                                class="h-20 w-full rounded-lg object-cover sm:w-32"
                            >

                            <div class="flex-1 sm:px-4">
                                <h3 class="text-lg font-semibold text-gray-900">Caravanplaatsen</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Comfortabele vaste staanplaatsen dichtbij faciliteiten.
                                </p>
                                <p class="mt-2 text-xs text-gray-500">✔ Vast • ✔ Comfort • ✔ Familie</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">Nog <strong>5</strong> plekken beschikbaar</p>
                            </div>

                            <div class="text-lg font-bold text-gray-900">vanaf €33</div>
                        </div>

                        <div
                            class="accommodatie flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 transition hover:scale-[1.01] hover:shadow-md sm:flex-row sm:items-center"
                            data-type="Tent"
                            data-ligging="Water"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?q=80&w=1200"
                                alt="Tentplekken"
                                class="h-20 w-full rounded-lg object-cover sm:w-32"
                            >

                            <div class="flex-1 sm:px-4">
                                <h3 class="text-lg font-semibold text-gray-900">Tentplekken</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Natuurlijke grasvelden met mooie plekken in de natuur.
                                </p>
                                <p class="mt-2 text-xs text-gray-500">✔ Gras • ✔ Natuur • ✔ Rust</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900">Nog <strong>5</strong> plekken beschikbaar</p>
                            </div>

                            <div class="text-lg font-bold text-gray-900">vanaf €18</div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection