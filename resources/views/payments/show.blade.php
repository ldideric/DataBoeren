@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-2xl px-6 py-8">
            <div class="rounded-2xl border border-tan-400 bg-tan-300 p-6 shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-bold text-black">Reservering betalen</h1>

                <div class="mt-4 rounded-lg border border-tan-500 bg-tan-200 px-4 py-3 text-sm text-black space-y-1">
                    <p>Plek: <span class="font-medium text-black">{{ $reservation->campsite->name }}</span></p>
                    <p>Aankomst: <span class="font-medium text-black">{{ $reservation->check_in->format('d M Y') }}</span></p>
                    <p>Vertrek: <span class="font-medium text-black">{{ $reservation->check_out->format('d M Y') }}</span></p>
                    <p>Aantal personen: <span class="font-medium text-black">{{ $reservation->num_adults + $reservation->num_children }}</span></p>
                </div>

                <div class="mt-4">
                    @include('partials.price-breakdown', [
                        'order' => $order,
                        'adults' => $reservation->num_adults,
                        'children' => $reservation->num_children,
                    ])
                </div>

                <p class="mt-4 text-sm text-black">
                    U wordt doorgestuurd naar Stripe om uw betaling veilig af te ronden.
                </p>

                <form method="POST" action="{{ $checkoutUrl }}" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-cerulean-300 border-2 border-cerulean-400 py-2 text-sm font-semibold text-cerulean-900 transition hover:bg-cerulean-400">
                        Betalen met Stripe
                    </button>
                </form>

                <a href="{{ $bookingsUrl }}" class="mt-3 block text-center text-sm text-olivegreen-800 hover:underline">
                    Terug naar boekingen
                </a>
            </div>
    </div>
@endsection
