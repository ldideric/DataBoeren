@extends('layouts.app')

@section('header')
    Invulformulier
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
@endsection

@section('content')
<body class="bg-white max-w-xl mx-auto px-4 py-8 text-sm text-gray-800">

    <h1 class="text-xl font-bold mb-6 m-4 md:m-6 lg:m-12">Invulformulier</h1>

    <form id="registratie" class="space-y-6 m-4 md:m-6 lg:m-12" action="">

        <fieldset>
            <legend class="font-semibold border-b border-gray-300 w-full pb-1 mb-4">Persoonsgegevens</legend>

                <div class="flex gap-3 mb-3">
                    <div class="flex-1">
                        <label for="first_name" class="block mb-1">Voornaam*</label>
                        <input type="text" id="first_name" name="first_name" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                    <div class="flex-1">
                        <label for="last_name" class="block mb-1">Achternaam*</label>
                        <input type="text" id="last_name" name="last_name" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                </div>

                <div class="flex gap-3 mb-3">
                    <div class="flex-1">
                        <label for="city" class="block mb-1">Woonplaats*</label>
                        <input type="text" id="city" name="city" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex-1">
                        <label for="phone" class="block mb-1">Telefoonnummer*</label>
                        <input type="tel" id="phone" name="phone" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                    <div class="flex-1">
                        <label for="email" class="block mb-1">E-mailadres*</label>
                        <input type="email" id="email" name="email" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                </div>
        </fieldset>

        <fieldset>
            <legend class="font-semibold border-b border-gray-300 w-full pb-1 mb-4">Reservering & Verblijf</legend>

                <div class="flex gap-3 mb-3">
                    <div class="flex-1">
                        <label for="check_in" class="block mb-1">Aankomstdatum*</label>
                        <input type="date" id="check_in" name="check_in" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                    <div class="flex-1">
                        <label for="check_out" class="block mb-1">Vertrekdatum*</label>
                        <input type="date" id="check_out" name="check_out" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                </div>

                <div class="flex gap-3 mb-3">
                    <div class="flex-1">
                        <label for="accommodatietype" class="block mb-1">Type accommodatie*</label>
                        <select id="accommodatietype" name="accommodatietype" required class="w-full border border-gray-300 px-2 py-1">
                            <option value="">Selecteer</option>
                            <option value="tent">Tent</option>
                            <option value="caravan">Caravan</option>
                            <option value="stacaravan">Stacaravan</option>
                            <option value="trekker">Trekkerstentje</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="num_people" class="block mb-1">Aantal personen*</label>
                        <input type="number" id="num_people" name="num_people" min="1" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex-1">
                        <label for="aantalkinderen" class="block mb-1">Aantal kinderen*</label>
                        <input type="number" id="aantalkinderen" name="aantalkinderen" min="0" value="0" required class="w-full border border-gray-300 px-2 py-1">
                    </div>
                </div>
        </fieldset>

        <fieldset>
            <legend class="font-semibold border-b border-gray-300 w-full pb-1 mb-4">Voertuiginformatie</legend>

            <div class="flex gap-3">
                <div class="flex-1">
                    <label for="num_plate" class="block mb-1">Kenteken*</label>
                    <input type="text" id="num_plate" name="num_plate" required class="w-full border border-gray-300 px-2 py-1">
                </div>
                <div class="flex-1">
                    <label for="voertuigtype" class="block mb-1">Type voertuig*</label>
                    <select id="voertuigtype" name="voertuigtype" required class="w-full border border-gray-300 px-2 py-1">
                        <option value="">Selecteer</option>
                        <option value="auto">Auto</option>
                        <option value="camper">Camper</option>
                        <option value="caravan">Caravan</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend class="font-semibold border-b border-gray-300 w-full pb-1 mb-4">Overig</legend>

            <div class="mb-3">
                <label for="pay_method" class="block mb-1">Betaalmethode*</label>
                <select id="pay_method" name="pay_method" required class="w-full border border-gray-300 px-2 py-1">
                    <option value="">Selecteer</option>
                    <option value="creditcard">Creditcard</option>
                    <option value="pin">Pinnen</option>
                    <option value="contant">Contant</option>
                </select>
            </div>
        </fieldset>

        <fieldset>
            <legend class="font-semibold border-b border-gray-300 w-full pb-1 mb-4">Checkboxes</legend>

            <div class="mb-3">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="18+" name="18+" required>Ik ben 18 jaar of ouder*
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" id="huisregels" name="huisregels" required>Ik ga akkoord met de huisregels*
                </label>

            </div>
        </fieldset>

        <button type="submit" class="w-full border border-gray-800 py-2 font-semibold hover:bg-gray-800 hover:text-white">Reservering indienen</button>

    </form>

</body>
@endsection

</html>