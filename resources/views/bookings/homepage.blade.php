@extends('layouts.app')

@section('content')
    <div class="bg-gray-100">
        <div class="mx-auto flex min-h-[calc(100vh-56px)] max-w-6xl items-center justify-center px-6 py-12">
            <div class="w-full max-w-lg rounded-2xl bg-white p-10 text-center shadow-[0_4px_20px_rgba(0,0,0,0.08)]">
                <h1 class="text-4xl font-semibold text-gray-900">Camping De Groene Weide</h1>
                <p class="mt-3 text-lg text-gray-600">Welkom bij onze gezellige camping midden in de natuur.</p>

                <a
                    id="boekBtn"
                    href="{{ route('boeken') }}"
                    class="mt-8 block w-full rounded-2xl bg-black px-6 py-4 text-lg font-medium text-white transition hover:bg-gray-900"
                >
                    Boek nu
                </a>

                <div class="mt-8 border-t border-gray-200 pt-5">
                    <p class="text-base text-gray-600">Wilt u uw boeking annuleren?</p>
                    <a
                        id="annuleerBtn"
                        href="{{ route('annuleren') }}"
                        class="mt-2 inline-block font-semibold text-gray-900 hover:underline"
                    >
                        Klik hier
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection