@extends('layouts.app')

@section('title', 'Activiteiten')

@section('content')

<div class="flex flex-1 items-center justify-center px-6 py-12">

    <div class="border-2 border-tan-500 w-full max-w-3xl rounded-2xl bg-tan-300 p-10 text-center shadow-md">

        <h1 class="text-3xl font-bold text-olivegreen-400 mb-6">
            Activiteiten
        </h1>

        <p class="mt-3 text-lg text-black">
            Ontdek de leuke activiteiten die we op en rondom onze camping aanbieden.
        </p>

        <div class="space-y-8 text-lg">

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-6"> 
                    Wandelen en fietsen
                </h2>

                <p class="mt-3 text-lg text-black">   
                    Verken de prachtige natuur rondom de camping met onze wandel- en fietsroutes. Er zijn twee fietsroutes en drie wandelroutes.
                </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-4">
                    <button onclick="openModal('/img/fietsroute1.png')"
                        class="bg-cerulean-300 text-white py-3 px-4 rounded-lg shadow hover:bg-cerulean-400 transition">
                        Fietsroute 1: 10 km
                    </button>
                    <button onclick="openModal('/img/fietsroute2.png')"
                        class="bg-cerulean-300 text-white py-3 px-4 rounded-lg shadow hover:bg-cerulean-400 transition">
                        Fietsroute 2: 20 km
                    </button>
                </div>
                <div class="space-y-4">
                    <button onclick="openModal('/img/wandelroute1.png')"
                        class="bg-cerulean-300 text-white py-3 px-4 rounded-lg shadow hover:bg-cerulean-400 transition">
                        Wandelroute 1: 4 km
                    </button>
                    <button onclick="openModal('/img/wandelroute2.png')"
                        class="bg-cerulean-300 text-white py-3 px-4 rounded-lg shadow hover:bg-cerulean-400 transition">
                        Wandelroute 2: 8 km 
                    </button>
                    <button onclick="openModal('/img/wandelroute3.png')"
                        class="bg-cerulean-300 text-white py-3 px-4 rounded-lg shadow hover:bg-cerulean-400 transition">
                        Wandelroute 3: 17 km
                    </button>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4">
                    Boerderijactiviteiten
                </h2>
                <p class="mt-3 text-lg text-black">
                    Leer meer over het boerenleven. Maak kennis met de dieren op de boerderij en geniet van de heerlijke buitenlucht.</p>
                <div class="grid grid-cols-[1fr_320px] gap-8 items-center border border-tan-500 rounded-lg p-4">
                <p class="mt-3 text-lg text-black">Breng een bezoek aan onze kinderboerderij. Kinderen kunnen hier kennismaken met verschillende dieren, ze bewonderen en aaien. Een leuke activiteit die zorgt voor leuke herinneringen tijdens het verblijf op de camping.
                </p>
                <img src="/img/kinderboerderij.png"
                     onclick="openModel('/img/kinderboerderij.png')"
                     class="mx-auto w-80 rounded-lg shadow-md cursor-pointer border border-tan-500">
                </div>
            </div>

            
            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4">
                    Kinderspeeltuin
                </h2>
                <div class="grid grid-cols-[1fr_320px] gap-8 items-center border border-tan-500 rounded-lg p-4">
                <p class="mt-3 text-lg text-black">
                    Onze kinderspeeltuin is geschikt voor de kleintjes om te spelen en andere kinderen te leren kennen. Er is voor ieder kind wat wils, of je nu houdt van springen of glijden het is er allemaal!
                </p>
                <img src="/img/kinderspeeltuin.png"
                     onclick="openModal('/img/kinderspeeltuin.png')"
                     class="mx-auto w-80 rounded-lg shadow-md cursor-pointer border border-tan-500">
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4">
                    Zwemgelegenheid
                </h2>
                <div class="grid grid-cols-[1fr_320px] gap-8 items-center border border-tan-500 rounded-lg p-4">
                    <p class="mt-3 text-lg text-black">Neem op een warme dagen een duik in de zwemvijver. Het is een fijne plek om te ontspannen en te spelen in het water.</p>  
                    <img src="/img/zwemvijver.png"   
                         onclick="openModal('/img/zwemvijver')"
                         class="mx-auto w-80 rounded-lg shadow-md cursor-pointer border border-tan-500">        
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4"> 
                    Overige activiteiten
                </h2>

                <div class="grid grid-cols-[1fr_320px] gap-8 items-center border border-tan-500 rounded-lg p-4"> 
                <p class="mt-3 text-lg text-black"> Voor dagelijkse boodschappen vind je op 4 afstand van de camping een supermarkt met een ruim assortiment aan producten.
                </p>
                <img src="/img/supermarkt.png"
                     onclick="openModel('/img/supermarkt.png')"
                     class="mx-auto w-80 rounded-lg shadow-md cursor-pointer border border-tan-500 mb-8"> 
                </div>
                <div class="grid grid-cols-[1fr_320px] gap-8 items-center border border-tan-500 rounded-lg p-4">
                    <p class="mt-3 text-lg text-black"> Bezoek de lokale markt op 6 kilometer van de camping en ontdek verse producten uit de streek en leuke kraampjes. 
                    </p>
                    <img src="/img/markt.png"
                     onclick="openModal('/img/markt.png')"
                     class="mx-auto w-80 rounded-lg shadow-md cursor-pointer border border-tan-500 mb-8">
                </div>
                <div class="grid grid-cols-[1fr_320px] gap-8 items-center border border-tan-500 rounded-lg p-4">
                     <p class="mt-3 text-lg text-black"> Liever zwemmen in een zwembad? Op slechts 8 kilometers van de camping bevindt zich een zwembad waar jong en oud zich kan vermaken.
                     </p>
                     <img src="/img/zwembad.png"
                          onclick="openModel('/img/zwembad.png')"
                          class="mx-auto w-80 rounded-lg shadow-md cursor-pointer border border-tan-500 mb-8">
                </div>
                <div class="grid grid-cols-[1fr_320px] gap-8 items-center border border-tan-500 rounded-lg p-4">
                    <p class="mt-3 text-lg text-black"> Het nabijgelegen dorp op 3 kilometer van de camping biedt verschillende voorzieningen, zoals winkels, café's, restaurants en een mooie kerk.
                    </p>
                    <img src="/img/dorp.png"
                     onclick="openModal('/img/dorp.png')"
                     class="mx-auto w-80 rounded-lg shadow-md cursor-pointer border border-tan-500 mb-8">
                </div>

        </div>
    </div>

</div>

<div id="imageModal"
     class="fixed inset-0 bg-black/75 hidden items-center justify-center z-50">

<div class="relative max-w-5xl p-4">
    <button onclick="closeModal()"
            class="absolute top-4 right-6 text-black text-4xl font-bold">
        ×
    </button>

    <img id="modalImage"
         src=""
         alt="Route"
         class="max-h-[90vh] rounded-lg shadow-lg">
</div>

</div>

<script>
    const imageModal = document.getElementById('imageModal');

    function openModal(image) {
        document.getElementById('modalImage').src = image;
        imageModal.style.display = 'flex';
    }

    function closeModal() {
        imageModal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == imageModal) {
            imageModal.style.display = 'none';
        }
    }

</script>

@endsection



