@extends('layouts.app')

@section('header')
    Controleer uw e-mail
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center px-6 py-10">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-semibold text-gray-900">Controleer uw e-mail</h1>

                @if (session('status'))
                    <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <p class="mt-4 text-sm text-gray-600">
                    Als er een account bestaat met dit e-mailadres, hebben we u een inloglink gestuurd.
                    De link is 15 minuten geldig.
                </p>
            </div>
        </div>
    </div>
@endsection
