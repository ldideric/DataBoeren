@extends('layouts.app')

@section('header')
    Betalen
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto max-w-2xl px-6 py-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-bold text-gray-900">Reservering betalen</h1>

                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 space-y-1">
                    <p>Plek: <span class="font-medium text-gray-900">{{ $reservation->campsite->name }}</span></p>
                    <p>Aankomst: <span class="font-medium text-gray-900">{{ $reservation->check_in->format('d M Y') }}</span></p>
                    <p>Vertrek: <span class="font-medium text-gray-900">{{ $reservation->check_out->format('d M Y') }}</span></p>
                    <p>Aantal personen: <span class="font-medium text-gray-900">{{ $reservation->num_adults + $reservation->num_children }}</span></p>
                </div>

                {{-- @todo The amount is a €1.00 placeholder set in PaymentController::checkout(); --}}
                {{--       show the real OrderSummary.total here once it is calculated. --}}
                <p class="mt-4 text-sm text-gray-500">
                    U wordt doorgestuurd naar Stripe om uw betaling veilig af te ronden.
                </p>

                <form method="POST" action="{{ $checkoutUrl }}" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-green-700 py-2 text-sm font-semibold text-white transition hover:bg-green-800">
                        Betalen met Stripe
                    </button>
                </form>

                <a href="{{ $bookingsUrl }}" class="mt-3 block text-center text-sm text-gray-500 hover:underline">
                    Terug naar boekingen
                </a>
            </div>
        </div>
    </div>
@endsection
