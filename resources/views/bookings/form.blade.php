@extends('layouts.app')

@section('header')
    Invulformulier
@endsection

@section('content')
    <div class="bg-gray-50">
        <div class="mx-auto max-w-2xl px-6 py-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h1 class="text-xl font-bold text-gray-900">Invulformulier</h1>

                <form id="registratie" class="mt-6 space-y-6" action="">
                    <fieldset>
                        <legend class="w-full border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Persoonsgegevens</legend>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="block text-sm text-gray-700">Voornaam*</label>
                                <input type="text" id="first_name" name="first_name" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm text-gray-700">Achternaam*</label>
                                <input type="text" id="last_name" name="last_name" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="city" class="block text-sm text-gray-700">Woonplaats*</label>
                            <input type="text" id="city" name="city" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="block text-sm text-gray-700">Telefoonnummer*</label>
                                <input type="tel" id="phone" name="phone" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                            <div>
                                <label for="email" class="block text-sm text-gray-700">E-mailadres*</label>
                                <input type="email" id="email" name="email" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="w-full border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Reservering &amp; Verblijf</legend>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="check_in" class="block text-sm text-gray-700">Aankomstdatum*</label>
                                <input type="date" id="check_in" name="check_in" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                            <div>
                                <label for="check_out" class="block text-sm text-gray-700">Vertrekdatum*</label>
                                <input type="date" id="check_out" name="check_out" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="accommodatietype" class="block text-sm text-gray-700">Type accommodatie*</label>
                                <select id="accommodatietype" name="accommodatietype" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                                    <option value="">Selecteer</option>
                                    <option value="tent">Tent</option>
                                    <option value="caravan">Caravan</option>
                                    <option value="stacaravan">Stacaravan</option>
                                    <option value="trekker">Trekkerstentje</option>
                                </select>
                            </div>
                            <div>
                                <label for="num_people" class="block text-sm text-gray-700">Aantal personen*</label>
                                <input type="number" id="num_people" name="num_people" min="1" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="aantalkinderen" class="block text-sm text-gray-700">Aantal kinderen*</label>
                            <input type="number" id="aantalkinderen" name="aantalkinderen" min="0" value="0" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="w-full border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Voertuiginformatie</legend>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="num_plate" class="block text-sm text-gray-700">Kenteken*</label>
                                <input type="text" id="num_plate" name="num_plate" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            </div>
                            <div>
                                <label for="voertuigtype" class="block text-sm text-gray-700">Type voertuig*</label>
                                <select id="voertuigtype" name="voertuigtype" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                                    <option value="">Selecteer</option>
                                    <option value="auto">Auto</option>
                                    <option value="camper">Camper</option>
                                    <option value="caravan">Caravan</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="w-full border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Overig</legend>

                        <div class="mt-4">
                            <label for="pay_method" class="block text-sm text-gray-700">Betaalmethode*</label>
                            <select id="pay_method" name="pay_method" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                                <option value="">Selecteer</option>
                                <option value="creditcard">Creditcard</option>
                                <option value="pin">Pinnen</option>
                                <option value="contant">Contant</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="w-full border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Checkboxes</legend>

                        <div class="mt-4 space-y-2 text-sm text-gray-700">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="18+" name="18+" required class="h-4 w-4 rounded border-gray-300 text-gray-900">
                                Ik ben 18 jaar of ouder*
                            </label>

                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="huisregels" name="huisregels" required class="h-4 w-4 rounded border-gray-300 text-gray-900">
                                Ik ga akkoord met de huisregels*
                            </label>
                        </div>
                    </fieldset>

                    <button type="submit" class="w-full rounded-lg border border-gray-900 py-2 text-sm font-semibold text-gray-900 transition hover:bg-gray-900 hover:text-white">
                        Reservering indienen
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection