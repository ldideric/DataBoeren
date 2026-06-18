@extends('layouts.app')

@section('title', 'Contact')

@section('content')

    <div class="mx-auto w-full max-w-4xl px-6 py-8">

        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-8 shadow-sm ring-1 ring-black/5 sm:p-10">

            <h1 class="text-center text-3xl font-bold text-olivegreen-400">Contact</h1>
            <p class="mx-auto mt-4 max-w-2xl text-center text-sm text-black">Heeft u vragen, wilt u meer informatie of bent u benieuwd naar de mogelijkheden? Neem dan gerust contact met ons op. Wij staan klaar om u verder te helpen.</p>

            <div class="mt-8 grid gap-4 text-sm text-black sm:grid-cols-3">

                <div class="flex h-full flex-col rounded-xl border border-tan-300 bg-tan-100 p-5">
                    <x-heroicon-o-phone class="mb-3 h-7 w-7 text-olivegreen-400" />
                    <h2 class="text-lg font-semibold text-olivegreen-400">Telefoon</h2>
                    <a href="tel:06123456789" class="mt-1 text-olivegreen-400 underline hover:no-underline">06123456789</a>
                    <p class="mt-2 text-black/80">Wij zijn telefonisch bereikbaar van maandag tot en met zondag tussen 09.00 en 19.00 uur. Wanneer wij niet direct kunnen opnemen, kunt u een voicemail achterlaten. Wij nemen dan zo snel mogelijk contact met u op.</p>
                </div>

                <div class="flex h-full flex-col rounded-xl border border-tan-300 bg-tan-100 p-5">
                    <x-heroicon-o-envelope class="mb-3 h-7 w-7 text-olivegreen-400" />
                    <h2 class="text-lg font-semibold text-olivegreen-400">E-mail</h2>
                    <a href="mailto:info@degroeneweide.nl" class="mt-1 text-olivegreen-400 underline hover:no-underline">info@degroeneweide.nl</a>
                    <p class="mt-2 text-black/80">Wij streven ernaar om uw e-mail binnen 24 uur te beantwoorden. In drukke periodes kan dit iets langer duren, maar wij reageren altijd zo spoedig mogelijk.</p>
                </div>

                <div class="flex h-full flex-col rounded-xl border border-tan-300 bg-tan-100 p-5">
                    <x-heroicon-o-map-pin class="mb-3 h-7 w-7 text-olivegreen-400" />
                    <h2 class="text-lg font-semibold text-olivegreen-400">Adres</h2>
                    <address class="mt-1 not-italic">
                        De Groenelaan<br>
                        4301 AA Schouwen-Duiveland<br>
                        Nederland
                    </address>
                </div>

            </div>

        </div>

    </div>

@endsection
