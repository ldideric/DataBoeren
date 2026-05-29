@extends('layouts.app')

@section('content')
    <div class="bg-tan2">
        <div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center px-6 py-10">
            <div class="w-full max-w-md rounded-2xl bg-tan p-6 shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-semibold text-black">Mijn boekingen</h1>
                <p class="mt-2 text-sm text-b">
                    Vul uw e-mailadres in. We sturen u een link naar uw boekingen.
                </p>

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

                <form method="POST" action="{{ route('login.send') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm text-black">E-mailadres</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="mt-1 w-full rounded-lg border border-olivegreen2 px-3 py-2 text-sm focus:border-olivegreen focus:outline-none focus:ring-2 focus:ring-olivegreen"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-cerulean py-3 text-sm font-semibold text-white transition hover:bg-cerulean2"
                    >
                        Stuur mij de link
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
