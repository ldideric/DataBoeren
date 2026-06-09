@extends('layouts.app')

@section('content')

    <div class="mx-auto w-full max-w-4xl px-6 py-8">

        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-8 shadow-sm ring-1 ring-black/5">

            <h1 class="text-3xl font-bold text-olivegreen-400">Onze campingregels</h1>
            <p class="mt-4 text-sm text-black">Lees de kampeerregels en voorwaarden aub door voor uw bezoek aan camping de Groene Weide.</p>

            <div class="mt-6 space-y-6 text-sm text-black">

                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">Betalingen</h2>
                <h3 class="text-lg font-semibold">In- en uitchecken</h3>
                <p class="mb-3">Altijd melden in het receptiegebouw bij aankomst en vertrek. Mocht er niemand bij de receptie aanwezig zijn, dan kun je het telefoonnummer op het infobord bij de receptie bellen. </p>
                <ul class="mb-3 list-disc list-inside">
                    <li>Kampeer- en tentplekken zijn beschikbaar vanaf 14:00, uitchecken vóór 12:00 uur.</li>
                    <li>Inchecken kan tot uiterlijk 20:00 uur.</li>
                    <li>Restbedrag betalen vóór 11:00 uur op de dag van vertrek.</li>
                </ul>

                <div>
                    <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">Plaatsing kampeermiddelen</h2>

                    <h3 class="text-lg font-semibold">Kampeermiddelen op wielen</h3>
                    <p class="mb-3">Kampeermiddelen op wielen tot 6 meter lengte en 3.500 kg zijn welkom. Campers blijven gedurende het verblijf op hun plek staan.</p>

                    <h3 class="text-lg font-semibold">Parkeren</h3>
                    <p class="mb-3">Onze kampeerplekken zijn autoluw. Auto's op de parkeerplaats. Met alle voertuigen en fietsen s.v.p rustig rijden en houd rekening met kampeerders en spelende kinderen!</p>

                    <h3 class="text-lg font-semibold">Kamperen in het najaar</h3>
                    <p class="mb-3">Bij nat weer, in het najaar of in de winter kan je kampeermiddel op wielen vanwege zachte of natte veengrond worden geplaatst op rijplaten of een verharde grindplek. </p>

                    <h3 class="text-lg font-semibold">Seizoensplaatsen en leegstand</h3>
                    <p class="mb-3">Seizoensplekken en leegstand van je kampeermiddel zijn bij ons niet toegestaan. We zorgen ervoor dat het terrein altijd levendig en goed benut wordt, zodat iedereen optimaal kan genieten van hun verblijf.</p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-olivegreen-400 mb-2">Praktische info & voorwaarden</h2>

                    <h3 class="text-lg font-semibold">Groepen</h3>
                    <p class="mb-3">Groepskamperen is niet toegestaan op ons terrein. Voor groepsaccommodaties verwijzen we u naar natuurkampeerterreinen.nl. Kamperen met twee huishoudens is wel toegestaan, met een maximum van 16 personen (inclusief kinderen) en maximaal 2 kampeer- of tentplekken.</p>

                    <h3 class="text-lg font-semibold">Bezoekers op de camping</h3>
                    <p class="mb-2">Bezoek ontvangen? Dit gaat altijd in overleg met een van onze medewerkers. Om de rust op het terrein te waarborgen:</p>
                    <ul class="list-disc list-inside">
                        <li>Maximum van 4 bezoekers per verblijf.</li>
                        <li>(Verjaardags)feestjes zijn niet toegestaan.</li>
                    </ul>
                    <p class="mt-1 mb-3">Het bezoekerstarief bedraagt €5 per persoon.</p>

                    <h3 class="text-lg font-semibold">Aansprakelijkheid</h3>
                    <p class="mb-2">Je verblijf op kampeerterrein de Groene Weide is op eigen risico. Eventuele schade aan het terrein of de accommodaties, veroorzaakt door bezoekers, wordt bij hen in rekening gebracht. Wij zijn niet aansprakelijk voor letsel, schade, verlies, diefstal of beschadiging van persoonlijke eigendommen, inclusief geld. Wij raden aan een reisverzekering af te sluiten. De Groene Weide is niet aansprakelijk voor storingen en diensten of tekortkomingen van diensten geleverd of verstrekt door derden. </p>

                    <h3 class="text-lg font-semibold">Nachtrust</h3>
                    <p class="mb-3">Op ons terrein geldt tussen 22:00 en 08:00 uur rusttijd. Houd rekening met je kampeerburen. Tijdens je verblijf zijn partytenten, muziekboxen en feestverlichting niet toegestaan. </p>

                    <h3 class="text-lg font-semibold">Vuurtje stoken</h3>
                    <p class="mb-3">Je mag een vuurtje maken op de gezamenlijke vuurplaatsen of bij je eigen kampeerplek. Houd rekening met de rust van medekampeerders en zorg dat er geen overlast ontstaat. Alleen volwassenen mogen het vuur aansteken en moeten erbij blijven tot het volledig is gedoofd. Een vuurkorf kun je bij ons lenen. Daarnaast verkopen we zakken brandhout.</p>

                    <h3 class="text-lg font-semibold">Chemisch toilet</h3>
                    <p class="mb-3">Er is een chemisch toilet loospunt achter de volière. Graag alleen deze plek gebruiken.</p>

                    <h3 class="text-lg font-semibold">Afwasruimte</h3>
                    <p class="mb-3">In de stal vind je de afwasruimte. Er is een koelkast, vriezer en magnetron voor algemeen gebruik. Buiten kun je je afval inleveren bij het afvalstation.</p>

                    <h3 class="text-lg font-semibold">Elektrische aansluiting</h3>
                    <p class="mb-3">De stroomsterkte op de kampeerplekken is 6 ampère.</p>

                    <h3 class="text-lg font-semibold">Speeltuin</h3>
                    <p class="mb-3">Volwassenen mogen niet op de trampoline, en er mogen maximaal drie personen tegelijk op. Vergeet niet je schoenen uit te doen. </p>

                    <h3 class="text-lg font-semibold">Boerencamping</h3>
                    <p class="mb-3">Als boerencamping hebben we dieren op het terrein. Wees voorzichtig en lief voor onze dieren. Onze dieren zijn ingeënt tegen Q-koorts. Ben je zwanger? We adviseren je om de kinderboerderij niet te bezoeken, aangezien dieren ziektekiemen kunnen dragen.</p>

                    <h3 class="text-lg font-semibold">Zwemwater en kinderen</h3>
                    <p class="mb-3">Op het kampeerterrein is een plas water waarin gezwommen kan worden. We adviseren kinderen zonder zwemdiploma een zwemvestje te dragen. </p>

                    <h3 class="text-lg font-semibold">Wifi</h3>
                    <p class="mb-3">Wifi is gratis beschikbaar in en rondom het receptiegebouw, niet op de rest van de camping.</p>

                    <h3 class="text-lg font-semibold">Huisdieren op de camping</h3>
                    <p class="mb-3">Honden zijn toegestaan op ons terrein. Andere huisdieren niet. Overigens verzoeken wij u vriendelijk uw hond aan de lijn te houden en als uw hond voor te veel overlast zorgt behouden wij ons altijd het recht om u de toegang tot de camping te ontzeggen.</p>

                    <h3 class="text-lg font-semibold">Foto's en social media</h3>
                    <p class="mb-5"> Medewerkers en campinggasten kunnen tijdens uw verblijf foto's maken en delen op sociale media. Wilt u niet dat foto's van u, gemaakt door de Groene Weide, online komen? Geef dit dan schriftelijk door vóór uw verblijf. De Groene Weide is niet verantwoordelijk voor foto's gedeeld door gasten. Maak geen foto's van onze deelnemers van de dagbesteding. </p>
                </div>

            </div>

        </div>

    </div>

@endsection
