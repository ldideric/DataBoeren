@extends('layouts.app')

@section('content')
    <div class="bg-tan" data-filter-root>
        <div class="mx-auto max-w-6xl px-6 py-8">
            <div class="flex flex-col gap-6 lg:flex-row">
                <aside class="w-full lg:w-80">
                    <div class="space-y-5">
                        <form method="GET" action="{{ route('campsites.index') }}" class="rounded-2xl bg-tan2 p-5 shadow-sm ring-1 ring-black/5 space-y-4">
                            <h2 class="text-lg font-bold text-olivegreen2">Verblijfsgegevens</h2>
                            <p class="text-sm text-black">Vul alle velden in om beschikbare plekken te zien.</p>

                            <div>
                                <label for="datestart" class="text-sm font-semibold text-black">Aankomst</label>
                                <input
                                    type="date"
                                    id="datestart"
                                    name="datestart"
                                    value="{{ $checkIn?->format('Y-m-d') }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                    required
                                    class="mt-2 w-full rounded-lg border border-olivegreen2 px-3 py-2 text-base focus:border-olivegreen focus:outline-none focus:ring-2 focus:ring-olivegreen"
                                >
                            </div>
                            <div>
                                <label for="dateend" class="text-sm font-semibold text-black">Vertrek</label>
                                <input
                                    type="date"
                                    id="dateend"
                                    name="dateend"
                                    value="{{ $checkOut?->format('Y-m-d') }}"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    required
                                    class="mt-2 w-full rounded-lg border border-olivegreen2 px-3 py-2 text-base focus:border-olivegreen focus:outline-none focus:ring-2 focus:ring-olivegreen"
                                >
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="adults" class="text-sm font-semibold text-black">Volwassenen</label>
                                    <input
                                        type="number"
                                        id="adults"
                                        name="adults"
                                        value="{{ $adults ?? 1}}"
                                        min="1"
                                        required
                                        class="mt-2 w-full rounded-lg border border-olivegreen2 px-3 py-2 text-base focus:border-olivegreen focus:outline-none focus:ring-2 focus:ring-olivegreen"
                                    >
                                </div>
                                <div>
                                    <label for="children" class="text-sm font-semibold text-black">Kinderen</label>
                                    <input
                                        type="number"
                                        id="children"
                                        name="children"
                                        value="{{ $children ?? 0 }}"
                                        min="0"
                                        required
                                        class="mt-2 w-full rounded-lg border border-olivegreen2 px-3 py-2 text-base focus:border-olivegreen focus:outline-none focus:ring-2 focus:ring-olivegreen"
                                    >
                                </div>
                            </div>
                            <div>
                                <label for="vehicles" class="text-sm font-semibold text-black">Voertuigen</label>
                                <input
                                    type="number"
                                    id="vehicles"
                                    name="vehicles"
                                    value="{{ $vehicles ?? 0 }}"
                                    min="0"
                                    required
                                    class="mt-2 w-full rounded-lg border border-olivegreen2 px-3 py-2 text-base focus:border-olivegreen focus:outline-none focus:ring-2 focus:ring-olivegreen"
                                >
                            </div>
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-cerulean py-3 text-base font-medium text-white transition hover:bg-cerulean2"
                            >
                                Zoek beschikbare plekken
                            </button>
                        </form>

                        @if ($hasAllCriteria && $campsites->isNotEmpty())
                            <div class="rounded-xl border border-tan2 bg-tan2 p-5">
                                <h2 class="text-lg font-semibold text-black">Accommodatie type</h2>
                                <p class="mt-2 text-sm text-black">Selecteer een type om de resultaten te filteren.</p>

                                <div class="mt-4 space-y-2">
                                    <label class="group flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-olivegreen">
                                        <input type="radio" name="type" value="" checked data-filter-input class="h-4 w-4 accent-olivegreen">
                                        <span>Alle</span>
                                    </label>
                                    @foreach (\App\Enums\CampsiteType::cases() as $type)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-olivegreen">
                                            <input type="radio" name="type" value="{{ $type->value }}" data-filter-input class="h-4 w-4 accent-olivegreen">
                                            <div class="flex flex-col">
                                                <span class="text-base font-normal text-gray-900">{{ $type->getHeadline() }}</span>
                                                <span class="text-sm text-gray-500">{{ $type->getDescription() }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>

                <main class="flex-1">
                    <h2 class="text-3xl font-semibold text-olivegreen2">Camping Boekingspagina</h2>

                    @if (! $hasAllCriteria)
                        <p class="mt-2 text-base text-black">Vul je verblijfsgegevens in om beschikbare plekken te zien.</p>

                        <div class="mt-6 rounded-xl border border-dashed border-tan2 bg-tan2 p-10 text-center">
                            <p class="text-base font-medium text-olivegreen2">Nog geen zoekopdracht</p>
                            <p class="mt-2 text-sm text-black">Vul links je aankomst- en vertrekdatum, aantal personen en voertuigen in om beschikbare plekken te zien.</p>
                        </div>
                    @elseif ($campsites->isEmpty())
                        <p class="mt-2 text-base text-black">
                            Geen resultaten van {{ $checkIn->format('d-m-Y') }} t/m {{ $checkOut->format('d-m-Y') }}
                            voor {{ $adults + $children }} {{ $adults + $children === 1 ? 'persoon' : 'personen' }}.
                        </p>

                        <div class="mt-6 rounded-xl border border-dashed border-tan2 bg-tan2 p-10 text-center">
                            <p class="text-base font-medium text-olivegreen2">Geen beschikbare plekken voor deze gegevens</p>
                            <p class="mt-2 text-sm text-black">Probeer andere data, een kleinere groep of minder voertuigen.</p>
                        </div>
                    @else
                        <p class="mt-2 text-base text-black">
                            Beschikbaar van {{ $checkIn->format('d-m-Y') }} t/m {{ $checkOut->format('d-m-Y') }}
                            voor {{ $adults + $children }} {{ $adults + $children === 1 ? 'persoon' : 'personen' }}
                            ({{ $vehicles }} {{ $vehicles === 1 ? 'voertuig' : 'voertuigen' }}).
                        </p>

                        <div class="mt-4 rounded-lg border border-tan2 bg-tan2 px-4 py-3 font-semibold text-black">
                            <span data-filter-count>{{ $campsites->count() }}</span> beschikbaarheden gevonden
                        </div>

                        <div class="mt-6 space-y-4">
                            @foreach ($campsites as $campsite)
                                <a
                                    href="#"
                                    onclick="event.preventDefault(); openModal(this)"
                                    data-filter-item
                                    data-type="{{ $campsite->type->value }}"
                                    data-name="{{ $campsite->name }}"
                                    data-campsite-type="{{ $campsite->type->getHeadline() }}"
                                    data-people="{{ $campsite->max_people }}"
                                    data-vehicles="{{ $campsite->max_vehicles }}"
                                    data-electricity="{{ $campsite->has_electricity ? 'true' : 'false' }}"
                                    data-notes="{{ $campsite->notes }}"
                                    data-url="{{ route('bookings.create', [
                                        'campsite' => $campsite->id,
                                        'check_in' => $checkIn->format('Y-m-d'),
                                        'check_out' => $checkOut->format('Y-m-d'),
                                        'adults' => $adults,
                                        'children' => $children,
                                        'vehicles' => $vehicles,
                                    ]) }}"
                                    data-filter-item
                                    data-type="{{ $campsite->type->value }}"
                                    class="flex flex-col gap-4 rounded-xl border border-tan2 bg-tan2 p-4 transition hover:scale-[1.01] hover:shadow-md sm:flex-row sm:items-center"
                                >
                                    <div class="flex-1 sm:px-4">
                                        <h3 class="text-lg font-semibold text-black">{{ $campsite->name }}</h3>
                                        <p class="mt-1 text-sm text-black">
                                            {{ $campsite->notes ?? \Illuminate\Support\Str::headline($campsite->type->value) . ' — max ' . $campsite->max_people . ' personen' }}
                                        </p>
                                        <p class="mt-2 text-xs text-black">
                                            @if ($campsite->has_electricity) ✔ Stroom @endif
                                            ✔ Max {{ $campsite->max_people }} pers • ✔ Max {{ $campsite->max_vehicles }} voertuig
                                        </p>
                                    </div>

                                    <div class="text-lg font-bold text-black">{{ \Illuminate\Support\Str::headline($campsite->type->value) }}</div>
                                </a>
                            @endforeach

                            <div data-filter-empty hidden class="rounded-lg border border-dashed border-tan2 bg-tan2 p-6 text-center text-sm text-black">
                                Geen accommodaties van dit type gevonden.
                            </div>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>

    <div id="modal" class="hidden fixed inset-0 bg-black/50 items-center justify-center ">
        <div class="bg-white rounded-2xl p-6 w-11/12 max-w-lg">
            <div class="flex items-start justify-between">
                <h2 id="modal-title" class="text-3xl font-semibold text-gray-900"></h2>
                <button onclick="closeModal()" class="ml-4 text-gray-400 hover:text-gray-700 text-xl leading-none">✕</button>
            </div>
            <p id="modal-type" class="text-lg font-medium text-gray-600"></p>
            <div class="mt-4 aspect-3/2 bg-gray-100 rounded-lg"></div>

            <div class="mt-4 flex gap-5">
                <ul id="modal-details" class="space-y-2 text-sm text-gray-500 flex-1">
                    <li id="modal-people"></li>
                    <li id="modal-vehicles"></li>
                    <li id="modal-electricity"></li>
                </ul>
                <p id="modal-notes" class="text-sm text-gray-700 flex-1"></p>
            </div>

            <div class="mt-4 flex justify-end">
                <a id="modal-book-btn" class="rounded-lg px-6 py-2 text-sm font-semibold text-white" style="background: #265513;">Boek</a>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal');

        function openModal(el) {
            const d = el.dataset;
            document.getElementById('modal-title').textContent = d.name;
            document.getElementById('modal-type').textContent = d.campsiteType;
            document.getElementById('modal-people').textContent = 'Max personen: ' + d.people;
            document.getElementById('modal-vehicles').textContent = 'Max voertuigen: ' + d.vehicles;
            document.getElementById('modal-electricity').textContent = 'Stroom: ' + (d.electricity === 'true' ? 'Ja' : 'Nee');
            document.getElementById('modal-notes').textContent = d.notes || 'Geen extra informatie beschikbaar';
            document.getElementById('modal-book-btn').href = d.url;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        }
    </script>
@endsection
