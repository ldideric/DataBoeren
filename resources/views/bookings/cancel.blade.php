@extends('layouts.app')

@section('header')
  Annuleren
@endsection

@section('content')
  <div class="bg-gray-50">
    <div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center px-6 py-10">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h1 class="text-xl font-semibold text-gray-900">Annuleren</h1>
        <p class="mt-2 text-sm text-gray-600">Voer uw e-mailadres in om uw boeking te annuleren.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="annuleerForm" class="mt-6 space-y-4" method="POST" action="{{ route('bookings.cancel') }}">
          @csrf
          <input
            type="email"
            id="annuleerEmail"
            name="email"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
            placeholder="Voer uw e-mailadres in"
            required
          >
          <button
            type="submit"
            id="annuleerSubmit"
            class="w-full rounded-lg border border-gray-300 bg-blue-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-600"
          >
            Annuleren
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection