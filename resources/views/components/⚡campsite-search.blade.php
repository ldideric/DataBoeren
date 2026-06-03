<?php

use App\Booking\DTO\StayCriteria;
use App\Enums\CampsiteType;
use App\Models\Campsite;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class () extends Component {
    use WithPagination;

    #[Url]
    public ?string $datestart = null;

    #[Url]
    public ?string $dateend = null;

    #[Url]
    public int $adults = 1;

    #[Url]
    public int $children = 0;

    #[Url]
    public int $vehicles = 0;

    /** @var array<int, string> */
    #[Url(except: [])]
    public array $types = [];

    public function updated(): void
    {
        $this->resetPage();
    }

    public function paginationView(): string
    {
        return 'partials.pagination';
    }

    #[Computed]
    public function criteria(): StayCriteria
    {
        return new StayCriteria(
            checkIn: $this->datestart ? Carbon::parse($this->datestart)->startOfDay() : null,
            checkOut: $this->dateend ? Carbon::parse($this->dateend)->startOfDay() : null,
            adults: max(1, $this->adults),
            children: max(0, $this->children),
            vehicles: max(0, $this->vehicles),
        );
    }

    #[Computed]
    public function campsites(): ?\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $criteria = $this->criteria;

        if (! $criteria->isComplete()) {
            return null;
        }

        $types = collect($this->types)
            ->filter(fn ($value) => CampsiteType::tryFrom($value) !== null)
            ->values();

        return Campsite::query()
            ->whereFitsParty($criteria->partySize(), $criteria->vehicles)
            ->whereAvailableBetween($criteria->checkIn, $criteria->checkOut)
            ->when($types->isNotEmpty(), fn ($query) => $query->whereIn('type', $types))
            ->orderBy('name')
            ->paginate(8);
    }
}; ?>

<div class="mx-auto w-full max-w-6xl px-6 py-8" x-data="{ open: false, c: {} }">
    <div class="space-y-4 lg:grid lg:grid-cols-[20rem_minmax(0,1fr)] lg:gap-x-6 lg:gap-y-4 lg:space-y-0">
        {{-- Header row: title sits only above the results column, leaving the filter column untouched --}}
        <div class="hidden lg:block" aria-hidden="true"></div>
        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="text-2xl font-bold text-olivegreen-400">Camping Boekingspagina</h2>
            @if (! $this->criteria->isComplete())
                <p class="mt-1 text-sm text-black">Vul je verblijfsgegevens in om beschikbare plekken te zien.</p>
            @elseif ($this->campsites->isEmpty())
                <p class="mt-1 text-sm text-black">
                    Geen resultaten van {{ $this->criteria->checkIn->format('d-m-Y') }} t/m {{ $this->criteria->checkOut->format('d-m-Y') }}
                    voor {{ $this->criteria->partySize() }} {{ $this->criteria->partySize() === 1 ? 'persoon' : 'personen' }}.
                </p>
            @else
                <p class="mt-1 text-sm text-black">
                    Beschikbaar van {{ $this->criteria->checkIn->format('d-m-Y') }} t/m {{ $this->criteria->checkOut->format('d-m-Y') }}
                    voor {{ $this->criteria->partySize() }} {{ $this->criteria->partySize() === 1 ? 'persoon' : 'personen' }}
                    ({{ $this->criteria->vehicles }} {{ $this->criteria->vehicles === 1 ? 'voertuig' : 'voertuigen' }}).
                </p>
                <p class="mt-2 font-semibold text-black">
                    {{ $this->campsites->total() }} beschikbaarheden gevonden
                </p>
            @endif
        </div>

        <aside class="w-full">
            <div class="space-y-5">
                <div class="rounded-2xl border border-tan-400 bg-tan-300 p-5 shadow-sm ring-1 ring-black/5 space-y-4">
                    <h2 class="text-lg font-bold text-olivegreen-400">Verblijfsgegevens</h2>
                    <p class="text-sm text-black">De resultaten verschijnen automatisch zodra je een aankomst- en vertrekdatum kiest.</p>

                    <div>
                        <label for="datestart" class="text-sm font-semibold text-black">Aankomst</label>
                        <input
                            type="date"
                            id="datestart"
                            wire:model.live="datestart"
                            min="{{ now()->format('Y-m-d') }}"
                            required
                            class="mt-2 w-full rounded-lg border border-olivegreen-600 bg-tan-200 px-3 py-2 text-base focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400"
                        >
                    </div>
                    <div>
                        <label for="dateend" class="text-sm font-semibold text-black">Vertrek</label>
                        <input
                            type="date"
                            id="dateend"
                            wire:model.live="dateend"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            required
                            class="mt-2 w-full rounded-lg border border-olivegreen-600 bg-tan-200 px-3 py-2 text-base focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400"
                        >
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="adults" class="text-sm font-semibold text-black">Volwassenen</label>
                            <input
                                type="number"
                                id="adults"
                                wire:model.live.debounce.400ms="adults"
                                min="1"
                                required
                                class="mt-2 w-full rounded-lg border border-olivegreen-600 bg-tan-200 px-3 py-2 text-base focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400"
                            >
                        </div>
                        <div>
                            <label for="children" class="text-sm font-semibold text-black">Kinderen</label>
                            <input
                                type="number"
                                id="children"
                                wire:model.live.debounce.400ms="children"
                                min="0"
                                required
                                class="mt-2 w-full rounded-lg border border-olivegreen-600 bg-tan-200 px-3 py-2 text-base focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400"
                            >
                        </div>
                    </div>
                    <div>
                        <label for="vehicles" class="text-sm font-semibold text-black">Voertuigen</label>
                        <input
                            type="number"
                            id="vehicles"
                            wire:model.live.debounce.400ms="vehicles"
                            min="0"
                            required
                            class="mt-2 w-full rounded-lg border border-olivegreen-600 bg-tan-200 px-3 py-2 text-base focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400"
                        >
                    </div>
                </div>

                @if ($this->criteria->isComplete())
                    <div class="rounded-xl border border-tan-400 bg-tan-300 p-4 shadow-sm ring-1 ring-black/5">
                        <h2 class="text-base font-semibold text-black">Accommodatie type</h2>
                        <p class="mt-1 text-sm text-black">Filter op één of meer types.</p>

                        <div class="mt-3" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                class="flex w-full items-center justify-between gap-2 rounded-lg border border-olivegreen-600 bg-tan-200 px-3 py-2 text-left text-base text-black transition focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400"
                            >
                                <span class="truncate">
                                    @if (empty($types))
                                        Alle types
                                    @else
                                        {{ count($types) }} {{ count($types) === 1 ? 'type' : 'types' }} geselecteerd
                                    @endif
                                </span>
                                <svg class="h-4 w-4 shrink-0 text-black/60 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-collapse
                                x-cloak
                                class="mt-2 space-y-1 rounded-lg border border-olivegreen-600 bg-tan-200 p-2"
                            >
                                @foreach (\App\Enums\CampsiteType::cases() as $type)
                                    <label class="group flex cursor-pointer items-start gap-3 rounded-lg px-3 py-1.5 transition hover:bg-olivegreen-500 hover:text-white">
                                        <input type="checkbox" wire:model.live="types" value="{{ $type->value }}" class="mt-0.5 h-4 w-4 shrink-0 accent-olivegreen-600">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-black group-hover:text-white">{{ $type->getHeadline() }}</span>
                                            <span class="text-xs text-black/80 group-hover:text-white">{{ $type->getDescription() }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </aside>

        <main class="w-full">
            @if (! $this->criteria->isComplete())
                <div class="rounded-xl border border-tan-400 bg-tan-300 p-10 text-center shadow-sm ring-1 ring-black/5">
                    <p class="text-base font-medium text-olivegreen-400">Nog geen zoekopdracht</p>
                    <p class="mt-2 text-sm text-black">Vul links je aankomst- en vertrekdatum, aantal personen en voertuigen in om beschikbare plekken te zien.</p>
                </div>
            @elseif ($this->campsites->isEmpty())
                <div class="rounded-xl border border-tan-400 bg-tan-300 p-10 text-center shadow-sm ring-1 ring-black/5">
                    <p class="text-base font-medium text-olivegreen-400">Geen beschikbare plekken voor deze gegevens</p>
                    <p class="mt-2 text-sm text-black">Probeer andere data, een kleinere groep, minder voertuigen of een ander accommodatie type.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($this->campsites as $campsite)
                        <button
                            type="button"
                            wire:key="campsite-{{ $campsite->id }}"
                            @click="c = {
                                name: @js($campsite->name),
                                type: @js($campsite->type->getHeadline()),
                                people: @js($campsite->max_people),
                                vehicles: @js($campsite->max_vehicles),
                                electricity: @js($campsite->has_electricity),
                                notes: @js($campsite->notes ?: 'Geen extra informatie beschikbaar'),
                                url: @js(route('bookings.create', [
                                    'campsite' => $campsite->id,
                                    'check_in' => $this->criteria->checkIn->format('Y-m-d'),
                                    'check_out' => $this->criteria->checkOut->format('Y-m-d'),
                                    'adults' => $this->criteria->adults,
                                    'children' => $this->criteria->children,
                                    'vehicles' => $this->criteria->vehicles,
                                ])),
                            }; open = true"
                            class="flex w-full flex-col gap-4 rounded-xl border border-tan-400 bg-tan-300 p-4 text-left transition hover:scale-[1.01] hover:shadow-md sm:flex-row sm:items-center"
                        >
                            <div class="min-w-0 flex-1 sm:px-4">
                                <h3 class="text-lg font-semibold text-black">{{ $campsite->name }}</h3>
                                <p class="mt-1 text-sm text-black">
                                    {{ $campsite->notes ?? $campsite->type->getHeadline() . ' — max ' . $campsite->max_people . ' personen' }}
                                </p>
                                <p class="mt-2 text-xs text-black">
                                    @if ($campsite->has_electricity) ✔ Stroom @endif
                                    ✔ Max {{ $campsite->max_people }} pers • ✔ Max {{ $campsite->max_vehicles }} voertuig
                                </p>
                            </div>

                            <div class="text-lg font-bold text-black">{{ $campsite->type->getHeadline() }}</div>
                        </button>
                    @endforeach
                </div>

                {{ $this->campsites->links() }}
            @endif
        </main>
    </div>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="open = false"
        @keydown.escape.window="open = false"
    >
        <div class="bg-tan-300 rounded-2xl w-11/12 max-w-lg overflow-hidden">
            <div class="bg-olivegreen-500 px-6 pt-6 pb-4">
                <div class="flex items-start justify-between">
                    <h2 class="text-3xl font-semibold text-white" x-text="c.name"></h2>
                    <button type="button" @click="open = false" class="ml-4 text-white/60 hover:text-white text-4xl leading-none">&times;</button>
                </div>
                <p class="text-lg font-medium text-white" x-text="c.type"></p>
            </div>

            <div class="p-6">
                <div class="aspect-3/2 rounded-lg overflow-hidden">
                    <img src="/img/campsite_placeholder.jpg" alt="" class="h-full w-full object-cover">
                </div>
                <div class="mt-4 flex gap-5">
                    <ul class="space-y-2 text-sm text-black flex-1">
                        <li x-text="'• Max personen: ' + c.people"></li>
                        <li x-text="'• Max voertuigen: ' + c.vehicles"></li>
                        <li x-text="'• Stroom: ' + (c.electricity ? 'Ja' : 'Nee')"></li>
                    </ul>
                    <p class="text-sm text-black flex-1" x-text="c.notes"></p>
                </div>
            </div>

            <div class="pb-6 pr-6 flex justify-end">
                <a :href="c.url" class="rounded-lg border-2 border-cerulean-400 px-12 py-4 text-xl font-semibold text-cerulean-900 bg-cerulean-300 hover:bg-cerulean-400 transition">Boek</a>
            </div>
        </div>
    </div>
</div>
