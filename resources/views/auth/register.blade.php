@extends('layouts.app')

@section('header')
    Registreren
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center px-6 py-10">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-semibold text-gray-900">Account aanmaken</h1>
                <p class="mt-2 text-sm text-gray-600">Maak een account om te kunnen boeken en uw reserveringen te beheren.</p>

                @if ($errors->any())
                    <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="first_name" class="block text-sm text-gray-700">Voornaam</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm text-gray-700">Achternaam</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm text-gray-700">E-mailadres</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm text-gray-700">Telefoonnummer</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <div>
                        <label for="password" class="block text-sm text-gray-700">Wachtwoord</label>
                        <input type="password" id="password" name="password" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm text-gray-700">Wachtwoord bevestigen</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-black py-3 text-sm font-semibold text-white transition hover:bg-gray-900">
                        Registreren
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-gray-600">
                    Al een account?
                    <a href="{{ route('login') }}" class="font-semibold text-gray-900 hover:underline">Inloggen</a>
                </p>
            </div>
        </div>
    </div>
@endsection
