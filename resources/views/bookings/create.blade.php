@extends('layouts.app')

@section('content')
    <div class="bg-tan-600">
        <div class="mx-auto max-w-2xl px-6 py-8">
            <div class="rounded-2xl bg-tan-400 p-6 shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-bold text-olivegreen-800">Invulformulier</h1>

                @if ($errors->any())
                    <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold mb-1">✗ Controleer de volgende velden:</p>
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="registratie" class="mt-6 space-y-6" method="POST" action="{{ route('bookings.store') }}">
                    @csrf

                    <input type="hidden" name="campsite_id" value="{{ $campsite->id }}">
                    <input type="hidden" name="check_in" value="{{ $checkIn->format('Y-m-d') }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut->format('Y-m-d') }}">
                    <input type="hidden" name="num_adults" value="{{ $adults }}">
                    <input type="hidden" name="num_children" value="{{ $children }}">
                    <input type="hidden" name="num_vehicles" value="{{ $vehicles }}">

                    <div class="space-y-2 rounded-lg border border-white bg-cerulean-400 px-4 py-3 text-sm text-white">
                        <div>
                            Gekozen plek: <strong>{{ $campsite->name }}</strong>
                            ({{ \Illuminate\Support\Str::headline($campsite->type->value) }})
                        </div>
                        <div>
                            Verblijf:
                            <strong>{{ $checkIn->format('d M Y') }}</strong> t/m
                            <strong>{{ $checkOut->format('d M Y') }}</strong>
                        </div>
                        <div>
                            Groep:
                            <strong>{{ $adults }}</strong> {{ $adults === 1 ? 'volwassene' : 'volwassenen' }},
                            <strong>{{ $children }}</strong> {{ $children === 1 ? 'kind' : 'kinderen' }},
                            <strong>{{ $vehicles }}</strong> {{ $vehicles === 1 ? 'voertuig' : 'voertuigen' }}.
                        </div>
                        <div class="pt-1">
                            <a href="{{ route('campsites.index', [
                                'datestart' => $checkIn->format('Y-m-d'),
                                'dateend' => $checkOut->format('Y-m-d'),
                                'adults' => $adults,
                                'children' => $children,
                                'vehicles' => $vehicles,
                            ]) }}" class="underline hover:no-underline">Wijzig verblijfsgegevens</a>
                        </div>
                    </div>

                    @if ($extras->isNotEmpty())
                        <fieldset>
                            <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Extra's</legend>

                            <div class="mt-4 space-y-3">
                                @foreach ($extras as $row)
                                    @php
                                        $extra = $row['model'];
                                        $cap = $row['cap'];
                                        $perNight = $extra->billing_type->value === 'per_night';
                                    @endphp
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="text-sm">
                                            <span class="font-medium text-black">{{ $extra->name }}</span>
                                            <span class="text-black">— € {{ number_format($extra->price / 100, 2, ',', '.') }} {{ $perNight ? 'per nacht' : 'eenmalig' }}</span>
                                            @if ($extra->description)
                                                <p class="text-xs text-black">{{ $extra->description }}</p>
                                            @endif
                                            @if ($cap === 0)
                                                <p class="text-xs text-red-600">Uitverkocht voor deze data</p>
                                            @endif
                                        </div>
                                        <input type="number" name="extras[{{ $extra->id }}]" value="{{ old('extras.'.$extra->id, 0) }}"
                                            min="0" @if ($cap !== null) max="{{ $cap }}" @endif @disabled($cap === 0)
                                            data-extra-price="{{ $extra->price }}" data-extra-per-night="{{ $perNight ? '1' : '0' }}"
                                            class="w-20 rounded-lg border border-olivegreen-800  px-3 py-2 text-sm focus:border-olivegreen-600 focus:outline-none focus:ring-2 focus:ring-olivegreen-600">
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif

                    @isset($order)
                        <div class="rounded-lg border border-white bg-cerulean-400 px-4 py-3">
                            <h2 class="text-sm font-semibold text-white">Prijsoverzicht</h2>
                            <div class="mt-2">
                                @include('partials.price-breakdown', [
                                    'order' => $order,
                                    'adults' => $adults,
                                    'children' => $children,
                                ])
                            </div>
                        </div>
                    @endisset

                    <fieldset>
                        <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Persoonsgegevens</legend>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="block text-sm text-black">Voornaam*</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required class="mt-1 w-full rounded-lg border border-olivegreen-800 px-3 py-2 text-sm focus:border-olivegreen-600 focus:outline-none focus:ring-2 focus:ring-olivegreen-600">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm text-black">Achternaam*</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required class="mt-1 w-full rounded-lg border border-olivegreen-800 px-3 py-2 text-sm focus:border-olivegreen-600 focus:outline-none focus:ring-2 focus:ring-olivegreen-600">
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="block text-sm text-black">Telefoonnummer*</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required class="mt-1 w-full rounded-lg border border-olivegreen-800 px-3 py-2 text-sm focus:border-olivegreen-600 focus:outline-none focus:ring-2 focus:ring-olivegreen-600">
                            </div>
                            <div>
                                <label for="email" class="block text-sm text-black">E-mailadres*</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-olivegreen-800 px-3 py-2 text-sm focus:border-olivegreen-600 focus:outline-none focus:ring-2 focus:ring-olivegreen-600">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Betaalmethode</legend>

                        <div class="mt-4">
                            <label for="pay_method" class="block text-sm text-black">Betaalmethode*</label>
                            <select id="pay_method" name="pay_method" required class="mt-1 w-full rounded-lg border border-olivegreen-800 px-3 py-2 text-sm focus:border-olivegreen-600 focus:outline-none focus:ring-2 focus:ring-olivegreen-600">
                                <option value="">Selecteer</option>
                                <option value="online" @selected(old('pay_method') === 'online')>Online betalen (Stripe)</option>
                                <option value="in_person" @selected(old('pay_method') === 'in_person')>Betalen op locatie</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Akkoord</legend>

                        <div class="mt-4 space-y-2 text-sm text-black">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="adult_confirmation" name="adult_confirmation" value="1" @checked(old('adult_confirmation')) required class="h-4 w-4 rounded border-white text-black">
                                Ik ben 18 jaar of ouder*
                            </label>

                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="house_rules" name="house_rules" value="1" @checked(old('house_rules')) required class="h-4 w-4 rounded border-white text-black">
                                <div>Ik ga akkoord met de <a href="{{ route('privacy') }}" class="font-semibold underline hover:decoration-cerulean hover:font-normal">huisregels*</a></div>
                            </label>
                        </div>
                    </fieldset>

                    <button type="submit" class="w-full rounded-lg border bg-cerulean-400 border-cerulean-400 py-2 text-sm font-semibold text-white transition hover:border-cerulean-600 hover:bg-cerulean-600">
                        Reservering indienen
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const total = document.querySelector('[data-grand-total]');
            const inputs = document.querySelectorAll('input[data-extra-price]');

            if (! total || inputs.length === 0) {
                return;
            }

            const baseCents = parseInt(total.dataset.base, 10);
            const nights = parseInt(total.dataset.nights, 10);
            const euro = new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' });

            const recalculate = () => {
                let extrasCents = 0;

                inputs.forEach((input) => {
                    const quantity = parseInt(input.value, 10) || 0;
                    const units = input.dataset.extraPerNight === '1' ? quantity * nights : quantity;
                    extrasCents += parseInt(input.dataset.extraPrice, 10) * units;
                });

                total.textContent = euro.format((baseCents + extrasCents) / 100);
            };

            inputs.forEach((input) => input.addEventListener('input', recalculate));
            recalculate();
        });
    </script>
@endsection
