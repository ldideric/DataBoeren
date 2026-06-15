@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-4xl px-6 py-8">
        <div class="rounded-2xl border border-tan-400 bg-tan-300 p-8 shadow-sm ring-1 ring-black/5">
            <h1 class="text-3xl font-bold text-olivegreen-400">Onze privacyverklaring</h1>
            <p class="mt-4 text-sm text-black">Camping De Groene Weide, gevestigd aan De Groenelaan 67 4301 AA Schouwen-Duiveland Nederland, is verantwoordelijk voor de verwerking van persoonsgegevens zoals weergegeven in deze privacyverklaring.</p>

            <div class="mt-6 space-y-6 text-sm text-black">

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Contactgegevens</h2>
                    <p class="mt-2">De Groenelaan 67<br>4301 AA<br>Schouwen-Duiveland<br>Nederland<br>06123456789</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Persoonsgegevens die wij verwerken</h2>
                    <p class="mt-2">Camping De Groene Weide verwerkt uw persoonsgegevens doordat u gebruik maakt van onze diensten en/of omdat u deze zelf aan ons verstrekt.</p>
                    <p class="mt-2">Hieronder vindt u een overzicht van de persoonsgegevens die wij verwerken:</p>
                    <p class="mt-2">- Voor- en achternaam<br>- Telefoonnummer<br>- E-mailadres<br>- Betalingsgegevens, worden verwerkt via Stripe<br>- Overige persoonsgegevens die de klant actief verstrekt bijvoorbeeld door een profiel op uw website aan te maken, in correspondentie en telefonisch<br>- Gegevens over uw activiteiten op onze website</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Bijzondere en/of gevoelige persoonsgegevens die wij verwerken</h2>
                    <p class="mt-2">Onze website en/of dienst heeft niet de intentie gegevens te verzamelen over websitebezoekers die jonger zijn dan 16 jaar. Tenzij ze toestemming hebben van ouders of voogd. We kunnen echter niet controleren of een bezoeker ouder dan 16 is. Wij raden ouders dan ook aan betrokken te zijn bij de online activiteiten van hun kinderen, om zo te voorkomen dat er gegevens over kinderen verzameld worden zonder ouderlijke toestemming. Als u er van overtuigd bent dat wij zonder die toestemming persoonlijke gegevens hebben verzameld over een minderjarige, neem dan contact met ons op via <a href="mailto:{{ config('mail.from.address') }}" class="text-cerulean-500 underline hover:no-underline">{{ config('mail.from.address') }}</a>, dan verwijderen wij deze informatie.</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Met welk doel en op basis van welke grondslag wij persoonsgegevens verwerken</h2>
                    <p class="mt-2">Camping De Groene Weide verwerkt jouw persoonsgegevens voor de volgende doelen:</p>
                    <p class="mt-2">- U de mogelijkheid te bieden een account aan te maken<br>- U te kunnen bellen of e-mailen indien dit nodig is om onze dienstverlening uit te kunnen voeren<br>- U te informeren over wijzigingen van onze diensten en producten<br>- Camping De Groene Weide verwerkt ook persoonsgegevens als wij hier wettelijk toe verplicht zijn, zoals gegevens die wij nodig hebben voor onze belastingaangifte.</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Geautomatiseerde besluitvorming</h2>
                    <p class="mt-2">Camping De Groene Weide maakt geen gebruik van geautomatiseerde besluitvorming.</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Hoe lang we gegevens bewaren</h2>
                    <p class="mt-2">Camping De Groene Weide bewaart uw persoonsgegevens niet langer dan strikt nodig is om de doelen te realiseren waarvoor uw gegevens worden verzameld. Wij hanteren de volgende bewaartermijn voor persoonsgegevens:</p>
                    <p class="mt-2">- 36 maanden</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Delen van persoonsgegevens met derden</h2>
                    <p class="mt-2">Voor het verwerken van betalingen maken wij gebruik van Stripe. Stripe verwerkt uw betalingsgegevens namens ons en handelt daarbij als verwerker in de zin van de AVG. Stripe heeft passende technische en organisatorische maatregelen getroffen om uw gegevens te beschermen. Wij sluiten met Stripe een verwerkersovereenkomst af conform de AVG-vereisten.</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400" id="cookie_statement">Cookies, of vergelijkbare technieken, die wij gebruiken</h2>
                    <p class="mt-2">Camping De Groene Weide gebruikt alleen technische en functionele cookies. En analytische cookies die geen inbreuk maken op uw privacy. Een cookie is een klein tekstbestand dat bij het eerste bezoek aan deze website wordt opgeslagen op uw computer, tablet of smartphone. De cookies die wij gebruiken zijn noodzakelijk voor de technische werking van de website en uw gebruiksgemak. Ze zorgen ervoor dat de website naar behoren werkt en onthouden bijvoorbeeld uw voorkeursinstellingen. Ook kunnen wij hiermee onze website optimaliseren. U kunt zich afmelden voor cookies door uw internetbrowser zo in te stellen dat deze geen cookies meer opslaat. Daarnaast kunt u ook alle informatie die eerder is opgeslagen via de instellingen van uw browser verwijderen.</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Gegevens inzien, aanpassen of verwijderen</h2>
                    <p class="mt-2">U heeft het recht om uw persoonsgegevens in te zien, te corrigeren of te verwijderen. Daarnaast heeft u het recht om uw eventuele toestemming voor de gegevensverwerking in te trekken of bezwaar te maken tegen de verwerking van uw persoonsgegevens door Camping De Groene Weide en heeft u het recht op gegevensoverdraagbaarheid. Dat betekent dat u bij ons een verzoek kunt indienen om de persoonsgegevens die wij van u beschikken in een computerbestand naar u of een ander, door u genoemde organisatie, te sturen.</p>
                    <p class="mt-2">U kunt een verzoek tot inzage, correctie, verwijdering, gegevensoverdraging van uw persoonsgegevens of verzoek tot intrekking van uw toestemming of bezwaar op de verwerking van uw persoonsgegevens sturen naar boerbert@campingdgw.nl.</p>
                    <p class="mt-2">Om er zeker van te zijn dat het verzoek tot inzage door u is gedaan, vragen wij u een kopie van uw identiteitsbewijs met het verzoek mee te sturen. Maak in deze kopie uw pasfoto, MRZ (machine readable zone, de strook met nummers onderaan het paspoort), paspoortnummer en Burgerservicenummer (BSN) zwart. Dit ter bescherming van uw privacy. We reageren zo snel mogelijk, maar binnen vier weken, op uw verzoek.</p>
                    <p class="mt-2">Camping De Groene Weide wil u er tevens op wijzen dat u de mogelijkheid heeft om een klacht in te dienen bij de nationale toezichthouder, de Autoriteit Persoonsgegevens. Dat kan via de volgende link: <a href="https://autoriteitpersoonsgegevens.nl/nl/contact-met-de-autoriteit-persoonsgegevens/tip-ons" class="text-cerulean-500 underline hover:no-underline">https://autoriteitpersoonsgegevens.nl/nl/contact-met-de-autoriteit-persoonsgegevens/tip-ons</a>.</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-olivegreen-400">Hoe wij persoonsgegevens beveiligen</h2>
                    <p class="mt-2">Camping De Groene Weide neemt de bescherming van uw gegevens serieus en neemt passende maatregelen om misbruik, verlies, onbevoegde toegang, ongewenste openbaarmaking en ongeoorloofde wijziging tegen te gaan. Als u de indruk heeft dat uw gegevens niet goed beveiligd zijn of er aanwijzingen zijn van misbruik, neem dan contact op met onze klantenservice of via <a href="mailto:{{ config('mail.from.address') }}" class="text-cerulean-500 underline hover:no-underline">{{ config('mail.from.address') }}</a>.</p>
                </div>

            </div>
        </div>
    </div>
@endsection
