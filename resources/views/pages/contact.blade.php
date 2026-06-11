@extends('layouts.app')

@section('title', 'Contact')

@section('content')
<div class="flex flex-1 items-center justify-center px-6 py-4">

    <div class="border-2 border-tan-500 w-full max-w-3xl rounded-2xl bg-tan-300 p-10 text-center shadow-md">
        <h1 class="text-5xl font-bold text-olivegreen-400 mb-6">
            Contact
        </h1>

        <p class="text-xl text-black mb-8">
            Heeft u vragen of wilt u meer informatie? Neem gerust contact met ons op
        </p>

        <div class="space-y-8 text-lg">

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">
                    Telefoon
                </h2>

                <p class="text-black">
                    U kunt ons bereiken op telefoonnummer: <a href="tel:06123456789" class="text-olivegreen-400 underline hover:no-underline">06123456789</a>
                </p>        
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">
                    E-mail
                </h2>

                <p class="text-black">
                    Stuur ons een e-mail op: <a href="mailto:info@degroeneweide.nl" class="text-olivegreen-400 underline hover:no-underline">info@degroeneweide.nl</a>
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">
                    Adres
                </h2>

                <p class="text-black">
                    Bezoek ons op: De Groenelaan, Schouwen-Duiveland, 4301 AA, Nederland
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
