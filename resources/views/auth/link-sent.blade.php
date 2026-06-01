@extends('layouts.app')

@section('content')
    <div class="bg-tan-600">
        <div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center px-6 py-10">
            <div class="w-full max-w-md rounded-2xl bg-tan-400 p-6 text-center shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-semibold text-black">Controleer uw e-mail</h1>

                @if (session('status'))
                    <div class="mt-4 rounded-md border border-cerulean-400 bg-cerulean-400 px-4 py-2 text-sm text-white">
                        {{ session('status') }}
                    </div>
                    <p class="mt-4 text-sm text-black">
                        Open de link in uw e-mail om uw boekingen te bekijken of te annuleren. De link is 60 minuten geldig.
                    </p>
                @else
                    <p class="mt-4 text-sm text-black">
                        Als er een link kon worden verstuurd, ontvangt u deze binnen enkele momenten.
                        De link is 60 minuten geldig.
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
