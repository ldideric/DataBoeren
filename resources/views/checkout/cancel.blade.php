@extends('layouts.app')

@section('header')
    Betaling geannuleerd
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto max-w-2xl px-6 py-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-700 text-2xl">
                    ✕
                </div>
                <h1 class="text-xl font-bold text-gray-900">Betaling geannuleerd</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Uw betaling is niet afgerond. U kunt het later opnieuw proberen vanuit uw boekingen.
                </p>

                <a href="{{ route('bookings.index') }}" class="mt-6 inline-block rounded-lg border border-gray-900 px-6 py-2 text-sm font-semibold text-gray-900 transition hover:bg-gray-900 hover:text-white">
                    Naar mijn boekingen
                </a>
            </div>
        </div>
    </div>
@endsection
