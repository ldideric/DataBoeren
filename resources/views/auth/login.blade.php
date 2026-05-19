@extends('layouts.app')

@section('header')
    Inloggen
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center px-6 py-10">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-semibold text-gray-900">Inloggen</h1>
                <p class="mt-2 text-sm text-gray-600">Log in om uw boekingen te beheren.</p>

                @if (session('status'))
                    <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm text-gray-700">E-mailadres</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm text-gray-700">Wachtwoord</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                        >
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300 text-gray-900">
                        Onthoud mij
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-black py-3 text-sm font-semibold text-white transition hover:bg-gray-900"
                    >
                        Inloggen
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-gray-600">
                    Nog geen account?
                    <a href="{{ route('register') }}" class="font-semibold text-gray-900 hover:underline">Registreren</a>
                </p>
            </div>
        </div>
    </div>
@endsection
