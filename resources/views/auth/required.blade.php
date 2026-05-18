@extends('layouts.app')

@section('header')
    Inloggen vereist
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center px-6 py-10">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-semibold text-gray-900">U bent niet ingelogd</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Deze pagina is alleen beschikbaar voor ingelogde gebruikers.
                    Log in of maak een account aan om verder te gaan.
                </p>

                <div class="mt-6 flex flex-col gap-2">
                    <a
                        href="{{ route('login') }}"
                        class="w-full rounded-lg bg-black py-3 text-sm font-semibold text-white transition hover:bg-gray-900"
                    >
                        Inloggen
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="w-full rounded-lg border border-gray-300 py-3 text-sm font-semibold text-gray-900 transition hover:bg-gray-50"
                    >
                        Registreren
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
