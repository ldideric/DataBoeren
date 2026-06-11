```php
@extends('layouts.app')

@section('title', 'Contact')

@section('content')
<div class="mx-auto w-full max-w-4xl px-6 py-8">

    <div class="rounded-2xl border border-tan-400 bg-tan-300 p-8 text-center shadow-sm ring-1 ring-black/5">

        <h1 class="text-3xl font-bold text-olivegreen-400">
            Contact
        </h1>

        <p class="mt-4 text-sm text-black">
            Heeft u vragen, wilt u meer informatie of bent u benieuwd naar de mogelijkheden? Neem dan gerust contact met ons op. Wij staan klaar om u verder te helpen.
        </p>

        <div class="mt-6 space-y-6 text-sm text-black">

            <div>
                <h2 class="mb-2 text-2xl font-semibold text-olivegreen-400">
                    Telefoon
                </h2>

                <p class="mb-3">
                    U kunt ons bereiken op telefoonnummer:
                    <a href="tel:06123456789" class="text-olivegreen-400 underline hover:no-underline">
                        06123456789
                    </a>
                </p>

                <p>
                    Wij zijn telefonisch bereikbaar van maandag tot en met zondag tussen 09.00 en 19.00 uur. Wanneer wij niet direct kunnen opnemen, kunt u een voicemail achterlaten. Wij nemen dan zo snel mogelijk contact met u op.
                </p>
            </div>

            <div>
                <h2 class="mb-2 text-2xl font-semibold text-olivegreen-400">
                    E-mail
                </h2>

                <p class="mb-3">
                    Stuur ons een e-mail op:
                    <a href="mailto:info@degroeneweide.nl" class="text-olivegreen-400 underline hover:no-underline">
                        info@degroeneweide.nl
                    </a>
                </p>

                <p>
                    Wij streven ernaar om uw e-mail binnen 24 uur te beantwoorden. In drukke periodes kan dit iets langer duren, maar wij reageren altijd zo spoedig mogelijk.
                </p>
            </div>

            <div>
                <h2 class="mb-2 text-2xl font-semibold text-olivegreen-400">
                    Adres
                </h2>

                <p>Bezoek ons op:</p>
                <p>De Groenelaan</p>
                <p>4301 AA Schouwen-Duiveland</p>
                <p>Nederland</p>
            </div>

        </div>

    </div>

</div>
@endsection
