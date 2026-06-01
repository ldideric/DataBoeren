@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-6 bg-tan-600">

        @if (session('status'))
            <div class="mb-4 rounded-md border border-white bg-cerulean-400 px-4 py-2 text-sm text-white">
                {{ session('status') }}
            </div>
        @endif

        <p class="text-sm text-black mb-6">
            Welkom terug, <span class="font-semibold text-black">{{ $user->name }}</span>.
            Hier is een overzicht van uw boekingen.
        </p>

        @if ($reservations->isEmpty())
            <div class="rounded-lg border border-dashed border-tan-400 bg-tan-400 p-8 text-center text-sm text-black">
                Geen reserveringen gevonden.
                <a href="{{ route('campsites.index') }}" class="text-olivegreen-800 hover:underline">Boek er nu een</a>.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($reservations as $reservation)
                    <div class="bg-tan-400 border border-tan-400 rounded-lg p-5 flex flex-col gap-3">

                        <div class="flex items-start justify-between gap-2">
                            <h2 class="font-medium text-black">{{ $reservation->campsite->name }}</h2>
                            <span class="shrink-0 text-xs font-medium text-white bg-olivegreen-600 border border-olivegreen-600 px-2 py-0.5 rounded-full">
                                {{ ucfirst($reservation->status->value) }}
                            </span>
                        </div>

                        <div class="text-sm text-black space-y-0.5">
                            <p>Aankomst: <span class="text-black">{{ $reservation->check_in->format('d M Y') }}</span></p>
                            <p>Vertrek: <span class="text-black">{{ $reservation->check_out->format('d M Y') }}</span></p>
                        </div>

                        @if ($reservation->status !== \App\Enums\ReservationStatus::Cancelled)
                            <div class="mt-auto pt-1 flex flex-col gap-2">
                                @if ($reservation->status === \App\Enums\ReservationStatus::Pending && isset($paymentUrls[$reservation->id]))
                                    <a href="{{ $paymentUrls[$reservation->id] }}" class="block w-full text-center text-sm px-3 py-1.5 bg-cerulean-400 text-white rounded-md hover:bg-cerulean-600 transition-colors">
                                        Betalen
                                    </a>
                                @endif

                                <form
                                    method="POST"
                                    action="{{ $cancelUrls[$reservation->id] }}"
                                    data-confirm="Weet u zeker dat u deze reservering wilt annuleren?"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="w-full text-center text-sm px-3 py-1.5 border border-cerulean-400 bg-cerulean-400 text-white rounded-md hover:bg-cerulean-600 hover:border-cerulean-600 transition-colors"
                                    >
                                        Annuleren
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
