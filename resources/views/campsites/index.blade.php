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
                            <div>
                                <label for="datestart" class="text-sm font-semibold text-gray-700">Aankomst</label>
                                <input
                                    type="date"
                                    id="datestart"
                                    name="datestart"
                                    value="{{ $checkIn?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                >
                            </div>
                            <div>
                                <label for="dateend" class="text-sm font-semibold text-gray-700">Vertrek</label>
                                <input
                                    type="date"
                                    id="dateend"
                                    name="dateend"
                                    value="{{ $checkOut?->format('Y-m-d') ?? now()->addDay()->format('Y-m-d') }}"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-base focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                >
                            </div>
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-black py-3 text-base font-medium text-white transition hover:bg-gray-900"
                            >
                                Toon beschikbaarheid
                            </button>
                        </form>

                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                            <h2 class="text-lg font-semibold text-gray-900">Accommodatie type</h2>
                            <p class="mt-2 text-sm text-gray-600">Selecteer een type om de lijst te filteren.</p>

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
                    </div>
                </aside>

                <main class="flex-1">
                    <h2 class="text-3xl font-semibold text-gray-900">Camping Boekingspagina</h2>
                    <p class="mt-2 text-base text-gray-600">
                        @if ($checkIn && $checkOut)
                            Beschikbaar van {{ $checkIn->format('d M Y') }} t/m {{ $checkOut->format('d M Y') }}
                        @else
                            Bekijk alle accommodaties — kies data om beschikbaarheid te tonen
                        @endif
                    </p>

                    <div class="mt-4 rounded-lg border border-gray-200 bg-white px-4 py-3 font-semibold text-gray-900">
                        <span data-filter-count>{{ $campsites->count() }}</span> beschikbaarheden gevonden
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($campsites as $campsite)
                            <a
                                href="{{ route('bookings.create', ['campsite' => $campsite->id]) }}"
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
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500">
                                Geen accommodaties beschikbaar.
                            </div>
                        @endforelse

                        <div data-filter-empty hidden class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500">
                            Geen accommodaties van dit type gevonden.
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
