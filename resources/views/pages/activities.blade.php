@extends('layouts.app')

@section('title', 'Activiteiten')

@section('content')

<div class="flex flex-1 items-center justify-center px-6 py-12">

<div class="border-2 border-tan-500 w-full max-w-3xl rounded-2xl bg-tan-300 p-10 text-center shadow-md">

    <h1 class="text-5xl font-bold text-olivegreen-400 mb-6">
        Activiteiten
    </h1>

    <p class="text-xl text-black mb-8">
        Ontdek de leuke activiteiten die we op en rondom onze camping aanbieden.
    </p>

    <div class="space-y-8 text-lg">

        <div>
            <h2 class="text-2xl font-semibold text-olivegreen-400 mb-6"> 
                Wandelen en fietsen
            </h2>

            <p class="text-black mb-6">
                Verken de prachtige natuur rondom de camping met onze wandel- en fietsroutes.
            </p>

            <p class="mb-6">
                <button onclick="openModal('/img/fietsroute1.png')"
                    class="text-black underline">
                    Fietsroute 1: 10 km
                </button>
            </p>

            <p class="mb-6">
                <button onclick="openModal('/img/fietsroute2.png')"
                    class="text-black underline">
                    Fietsroute 2: 20 km
                </button>
            </p>
            
            <p class="mb-6">
                <button onclick="openModal('/img/wandelroute1.png')"
                    class="text-black underline">
                    Wandelroute 1: 4 km
                </button>
            </p>

            <p class="mb-6">
                <button onclick="openModal('/img/wandelroute2.png')"
                    class="text-black underline">
                    Wandelroute 2: 8 km 
                </button>
            </p>

            <p class="mb-6">
                <button onclick="openModal('/img/wandelroute3.png')"
                    class="text-black underline">
                    Wandelroute 3: 17 km 
                </button>
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4">
                Boerderijactiviteiten
            </h2>
            <p class="text-black mb-6">
                Leer meer over het boerenleven met onze interactieve boerderijactiviteiten voor jong en oud.</p>
            <p class="text-black mb-6">Kinderboerderij</p>
        </div>

        
        <div>
            <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4">
                Kinderspeelplaats
            </h2>
            <p class="text-black mb-6">
                Onze kinderspeelplaats is perfect voor de kleintjes om te spelen en nieuwe vriendjes te maken.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4">
                Zwemgelegenheid
            </h2>
            <p class="text-black mb-6">Zwemvijver</p>             
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-olivegreen-400 mb-4">
                Overige activiteiten
            </h2>

            <p class="text-black mb-6">Supermarkt 4 kilometer</p>
            <p class="text-black mb-6">Markt 6 kilometer</p>
            <p class="text-black mb-6">Zwembad 8 kilometer</p>
            <p class="text-black mb-6">Dichtstbijzijnde dorp 3 kilometer</p>
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
