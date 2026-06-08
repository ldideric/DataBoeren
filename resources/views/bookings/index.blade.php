@extends('layouts.app')

@php
    use App\Enums\PaymentStatus;
    use App\Enums\ReservationStatus;

    $euro = fn ($cents) => '€ ' . number_format($cents / 100, 2, ',', '.');

    $statusLabels = [
        ReservationStatus::Pending->value => 'In afwachting',
        ReservationStatus::Confirmed->value => 'Bevestigd',
        ReservationStatus::Cancelled->value => 'Geannuleerd',
    ];
    $statusStyles = [
        ReservationStatus::Pending->value => 'bg-cerulean-300 text-cerulean-900',
        ReservationStatus::Confirmed->value => 'bg-olivegreen-500 text-white',
        ReservationStatus::Cancelled->value => 'bg-tan-400 text-tan-900',
    ];
@endphp

@section('content')
    <div class="mx-auto w-full max-w-3xl px-6 py-8">

        @if (session('status'))
            <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                <span class="font-semibold">✓</span> {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-6 shadow-sm ring-1 ring-black/5">
            <h1 class="text-2xl font-bold text-olivegreen-400">Mijn boekingen</h1>
            <p class="mt-1 text-sm text-black">
                Welkom terug, <strong>{{ $user->name }}</strong>. Hier is een overzicht van uw boekingen.
            </p>

            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-lg border border-tan-500 bg-tan-200 px-4 py-3">
                    <p class="text-xl font-bold text-olivegreen-400">{{ $stats['total'] }}</p>
                    <p class="text-xs text-black">Reserveringen</p>
                </div>
                <div class="rounded-lg border border-tan-500 bg-tan-200 px-4 py-3">
                    <p class="text-xl font-bold text-olivegreen-400">{{ $stats['upcoming'] }}</p>
                    <p class="text-xs text-black">Aankomende verblijven</p>
                </div>
                <div class="rounded-lg border border-tan-500 bg-tan-200 px-4 py-3">
                    <p class="text-xl font-bold text-olivegreen-400">{{ $stats['nights'] }}</p>
                    <p class="text-xs text-black">Geboekte nachten</p>
                </div>
                <div class="rounded-lg border border-tan-500 bg-tan-200 px-4 py-3">
                    <p class="text-xl font-bold text-olivegreen-400">{{ $euro($stats['paid']) }}</p>
                    <p class="text-xs text-black">Totaal betaald</p>
                </div>
            </div>
        </div>

        @if ($reservations->isEmpty())
            <div class="mt-6 rounded-2xl border border-tan-400 bg-tan-300 p-10 text-center shadow-sm ring-1 ring-black/5">
                <p class="text-base font-medium text-olivegreen-400">Geen reserveringen gevonden</p>
                <p class="mt-2 text-sm text-black">
                    U heeft nog geen boekingen.
                    <a href="{{ route('campsites.index') }}" class="font-semibold text-olivegreen-400 underline hover:no-underline">Boek er nu een</a>.
                </p>
            </div>
        @else
            <div class="mt-6 space-y-5">
                @foreach ($reservations as $reservation)
                    @php
                        $summary = $reservation->orderSummary;
                        $nights = $summary?->num_nights ?? (int) $reservation->check_in->diffInDays($reservation->check_out);
                        $isCancelled = $reservation->status === ReservationStatus::Cancelled;
                        $paidPayment = $reservation->payments->firstWhere('status', PaymentStatus::Paid);
                    @endphp

                    <div class="rounded-2xl border border-tan-400 bg-tan-300 p-6 shadow-sm ring-1 ring-black/5">

                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-bold text-olivegreen-400">{{ $reservation->campsite->name }}</h2>
                                <p class="text-sm text-black">{{ $reservation->campsite->type->getHeadline() }}</p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusStyles[$reservation->status->value] }}">
                                {{ $statusLabels[$reservation->status->value] }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-1 rounded-lg border border-tan-500 bg-tan-200 px-4 py-3 text-sm text-black">
                            <div>
                                Verblijf:
                                <strong>{{ $reservation->check_in->format('d M Y') }}</strong> t/m
                                <strong>{{ $reservation->check_out->format('d M Y') }}</strong>
                                ({{ $nights }} {{ $nights === 1 ? 'nacht' : 'nachten' }})
                            </div>
                            <div>
                                Groep:
                                <strong>{{ $reservation->num_adults }}</strong> {{ $reservation->num_adults === 1 ? 'volwassene' : 'volwassenen' }},
                                <strong>{{ $reservation->num_children }}</strong> {{ $reservation->num_children === 1 ? 'kind' : 'kinderen' }}.
                            </div>
                            <div>
                                Voorzieningen:
                                <strong>{{ $reservation->campsite->has_electricity ? 'Met stroom' : 'Zonder stroom' }}</strong>
                            </div>
                        </div>

                        @if ($reservation->extras->isNotEmpty() || ! $isCancelled)
                            <div class="mt-4 rounded-lg border border-tan-500 bg-tan-200 px-4 py-3">
                                <h3 class="text-sm font-semibold text-olivegreen-400">Extra's</h3>
                                @if ($reservation->extras->isNotEmpty())
                                    <ul class="mt-2 space-y-1 text-sm text-black">
                                        @foreach ($reservation->extras as $line)
                                            <li class="flex justify-between gap-3">
                                                <span>{{ $line->quantity }}× {{ $line->extra->name }}</span>
                                                <span class="font-medium">{{ $euro($line->subtotal) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @unless ($isCancelled)
                                    <p class="mt-2 text-xs text-black/70">
                                        Extra's toevoegen of wijzigen? Dat kan bij de receptie.
                                    </p>
                                @endunless
                            </div>
                        @endif

                        @if ($summary)
                            <div class="mt-4 rounded-lg border border-tan-500 bg-tan-200 px-4 py-3">
                                <h3 class="text-sm font-semibold text-olivegreen-400">Prijsoverzicht</h3>
                                @if ($reservation->coupon)
                                    <p class="mt-1 text-xs text-black">Couponcode <strong>{{ $reservation->coupon->code }}</strong> toegepast.</p>
                                @endif
                                <div class="mt-2">
                                    @include('partials.price-breakdown', [
                                        'order' => $summary,
                                        'adults' => $reservation->num_adults,
                                        'children' => $reservation->num_children,
                                        'extraLines' => $reservation->extraLineItems(),
                                    ])
                                </div>
                                @if ($paidPayment)
                                    <p class="mt-3 border-t border-tan-500 pt-2 text-sm text-black">
                                        Betaald op {{ ($paidPayment->paid_at ?? $paidPayment->created_at)->format('d M Y') }}.
                                    </p>
                                @elseif (! $isCancelled)
                                    <p class="mt-3 border-t border-tan-500 pt-2 text-sm text-black">Nog te betalen.</p>
                                @endif
                            </div>
                        @endif

                        @if ($isCancelled)
                            <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">
                                Geannuleerd
                                @if ($reservation->cancelled_at) op {{ $reservation->cancelled_at->format('d M Y') }} @endif
                                @if ($reservation->cancellation_reason) — {{ $reservation->cancellation_reason }} @endif
                            </div>
                        @endif

                        @unless ($isCancelled)
                            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                @if ($reservation->status === ReservationStatus::Pending && isset($paymentUrls[$reservation->id]))
                                    <a href="{{ $paymentUrls[$reservation->id] }}" class="flex-1 rounded-lg border-2 border-cerulean-400 bg-cerulean-300 px-3 py-2 text-center text-sm font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                                        Betalen
                                    </a>
                                @endif

                                <form
                                    method="POST"
                                    action="{{ $cancelUrls[$reservation->id] }}"
                                    data-confirm="Weet u zeker dat u deze reservering wilt annuleren?"
                                    class="flex-1"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="w-full rounded-lg border-2 border-red-300 bg-red-100 px-6 py-2 text-center text-sm font-semibold text-red-800 transition hover:bg-red-200"
                                    >
                                        Annuleren
                                    </button>
                                </form>
                            </div>
                        @endunless

                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
