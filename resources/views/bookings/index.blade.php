@extends('layouts.app')

@section('header')
    Boekingen
@endsection

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-6">

        @if (session('status'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <p class="text-sm text-gray-500 mb-6">
            Welkom terug, <span class="font-medium text-gray-700">{{ Auth::user()?->name ?? 'gast' }}</span>.
            Hier is een overzicht van uw boekingen.
        </p>

        @if ($reservations->isEmpty())
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                Geen reserveringen gevonden.
                <a href="{{ route('campsites.index') }}" class="text-green-700 hover:underline">Boek er nu een</a>.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($reservations as $reservation)
                    <div class="bg-white border border-gray-200 rounded-lg p-5 flex flex-col gap-3">

                        <div class="flex items-start justify-between gap-2">
                            <h2 class="font-medium text-gray-900">{{ $reservation->campsite->name }}</h2>
                            <span class="shrink-0 text-xs font-medium text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">
                                {{ ucfirst($reservation->status->value) }}
                            </span>
                        </div>

                        <div class="text-sm text-gray-500 space-y-0.5">
                            <p>Aankomst: <span class="text-gray-700">{{ $reservation->check_in->format('d M Y') }}</span></p>
                            <p>Vertrek: <span class="text-gray-700">{{ $reservation->check_out->format('d M Y') }}</span></p>
                        </div>

                        @if ($reservation->status !== \App\Enums\ReservationStatus::Cancelled)
                            <form
                                method="POST"
                                action="{{ route('bookings.destroy', $reservation) }}"
                                data-confirm="Weet u zeker dat u deze reservering wilt annuleren?"
                                class="mt-auto pt-1"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="w-full text-center text-sm px-3 py-1.5 border border-gray-200 text-gray-600 rounded-md hover:bg-gray-50 transition-colors"
                                >
                                    Annuleren
                                </button>
                            </form>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
