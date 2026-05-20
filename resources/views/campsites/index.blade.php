@extends('layouts.app')

@section('header')
    Kampeerplaatsen
@endsection

@section('content')
    <div class="bg-gray-100" data-filter-root>
        <div class="mx-auto max-w-6xl px-6 py-8">
            <div class="flex flex-col gap-6 lg:flex-row">
                <aside class="w-full lg:w-80">
                    <div class="space-y-5">
                        <form method="GET" action="{{ route('campsites.index') }}" class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 space-y-4">
                            <h2 class="text-lg font-semibold text-gray-900">Verblijfsgegevens</h2>
                            <p class="text-sm text-gray-600">Vul alle velden in om beschikbare plekken te zien.</p>

                            <div>
                                <label for="datestart" class="text-sm font-semibold text-gray-700">Aankomst</label>
                                <input
                                    type="date"
                                    id="datestart"
                                    name="datestart"
                                    value="{{ $checkIn?->format('Y-m-d') }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                    required
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                >
                            </div>
                            <div>
                                <label for="dateend" class="text-sm font-semibold text-gray-700">Vertrek</label>
                                <input
                                    type="date"
                                    id="dateend"
                                    name="dateend"
                                    value="{{ $checkOut?->format('Y-m-d') }}"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    required
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                >
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="adults" class="text-sm font-semibold text-gray-700">Volwassenen</label>
                                    <input
                                        type="number"
                                        id="adults"
                                        name="adults"
                                        value="{{ $adults ?? 1}}"
                                        min="1"
                                        required
                                        class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                    >
                                </div>
                                <div>
                                    <label for="children" class="text-sm font-semibold text-gray-700">Kinderen</label>
                                    <input
                                        type="number"
                                        id="children"
                                        name="children"
                                        value="{{ $children ?? 0 }}"
                                        min="0"
                                        required
                                        class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                    >
                                </div>
                            </div>
                            <div>
                                <label for="vehicles" class="text-sm font-semibold text-gray-700">Voertuigen</label>
                                <input
                                    type="number"
                                    id="vehicles"
                                    name="vehicles"
                                    value="{{ $vehicles ?? 0 }}"
                                    min="0"
                                    required
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                >
                            </div>
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-black py-3 text-base font-medium text-white transition hover:bg-gray-900"
                            >
                                Zoek beschikbare plekken
                            </button>
                        </form>

                        @if ($hasAllCriteria && $campsites->isNotEmpty())
                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                <h2 class="text-lg font-semibold text-gray-900">Accommodatie type</h2>
                                <p class="mt-2 text-sm text-gray-600">Selecteer een type om de resultaten te filteren.</p>

                                <div class="mt-4 space-y-2">
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                        <input type="radio" name="type" value="" checked data-filter-input class="h-4 w-4 accent-green-600">
                                        <span>Alle</span>
                                    </label>
                                    @foreach (\App\Enums\CampsiteType::cases() as $type)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-100">
                                            <input type="radio" name="type" value="{{ $type->value }}" data-filter-input class="h-4 w-4 accent-green-600">
                                            <span>{{ \Illuminate\Support\Str::headline($type->value) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>

                <main class="flex-1">
                    <h2 class="text-3xl font-semibold text-gray-900">Camping Boekingspagina</h2>

                    @if (! $hasAllCriteria)
                        <p class="mt-2 text-base text-gray-600">Vul je verblijfsgegevens in om beschikbare plekken te zien.</p>

                        <div class="mt-6 rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                            <p class="text-base font-medium text-gray-700">Nog geen zoekopdracht</p>
                            <p class="mt-2 text-sm text-gray-500">Vul links je aankomst- en vertrekdatum, aantal personen en voertuigen in om beschikbare plekken te zien.</p>
                        </div>
                    @elseif ($campsites->isEmpty())
                        <p class="mt-2 text-base text-gray-600">
                            Geen resultaten van {{ $checkIn->format('d-m-Y') }} t/m {{ $checkOut->format('d-m-Y') }}
                            voor {{ $adults + $children }} {{ $adults + $children === 1 ? 'persoon' : 'personen' }}.
                        </p>

                        <div class="mt-6 rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                            <p class="text-base font-medium text-gray-700">Geen beschikbare plekken voor deze gegevens</p>
                            <p class="mt-2 text-sm text-gray-500">Probeer andere data, een kleinere groep of minder voertuigen.</p>
                        </div>
                    @else
                        <p class="mt-2 text-base text-gray-600">
                            Beschikbaar van {{ $checkIn->format('d-m-Y') }} t/m {{ $checkOut->format('d-m-Y') }}
                            voor {{ $adults + $children }} {{ $adults + $children === 1 ? 'persoon' : 'personen' }}
                            ({{ $vehicles }} {{ $vehicles === 1 ? 'voertuig' : 'voertuigen' }}).
                        </p>

                        <div class="mt-4 rounded-lg border border-gray-200 bg-white px-4 py-3 font-semibold text-gray-900">
                            <span data-filter-count>{{ $campsites->count() }}</span> beschikbaarheden gevonden
                        </div>

                        <div class="mt-6 space-y-4">
                            @foreach ($campsites as $campsite)
                                <a
                                    href="{{ route('bookings.create', [
                                        'campsite' => $campsite->id,
                                        'check_in' => $checkIn->format('Y-m-d'),
                                        'check_out' => $checkOut->format('Y-m-d'),
                                        'adults' => $adults,
                                        'children' => $children,
                                        'vehicles' => $vehicles,
                                    ]) }}"
                                    data-filter-item
                                    data-type="{{ $campsite->type->value }}"
                                    class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 transition hover:scale-[1.01] hover:shadow-md sm:flex-row sm:items-center"
                                >
                                    <div class="flex-1 sm:px-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $campsite->name }}</h3>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $campsite->notes ?? \Illuminate\Support\Str::headline($campsite->type->value) . ' — max ' . $campsite->max_people . ' personen' }}
                                        </p>
                                        <p class="mt-2 text-xs text-gray-500">
                                            @if ($campsite->has_electricity) ✔ Stroom @endif
                                            ✔ Max {{ $campsite->max_people }} pers • ✔ Max {{ $campsite->max_vehicles }} voertuig
                                        </p>
                                    </div>

                                    <div class="text-lg font-bold text-gray-900">{{ \Illuminate\Support\Str::headline($campsite->type->value) }}</div>
                                </a>
                            @endforeach

                            <div data-filter-empty hidden class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500">
                                Geen accommodaties van dit type gevonden.
                            </div>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
