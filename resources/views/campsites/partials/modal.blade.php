@php
    $wrapperClass = $wrapperClass ?? 'hidden fixed inset-0 bg-black/50 items-center justify-center';
    $dialogClass = $dialogClass ?? 'bg-tan-300 rounded-2xl w-11/12 max-w-lg overflow-hidden';
@endphp

<div id="modal" class="{{ $wrapperClass }}">
    <div class="{{ $dialogClass }}">
        <div class="bg-olivegreen-500 px-6 pt-6 pb-4">
            <div class="flex items-start justify-between">
                <h2 id="modal-title" class="text-3xl font-semibold text-white"></h2>
                <button onclick="closeModal()" class="ml-4 text-white/60 hover:text-white text-4xl leading-none">🗙</button>
            </div>
            <p id="modal-type" class="text-lg font-medium text-white"></p>
        </div>

        <div class="p-6 flex-1 min-h-0">
            <div class="aspect-3/2 rounded-lg overflow-hidden max-h-full">
                <img src="/img/campsite_placeholder.jpg" alt="" class="h-full w-full object-contain">
            </div>
            <div class="mt-4 flex gap-5">
                <ul id="modal-details" class="space-y-2 text-sm text-black flex-1">
                    <li id="modal-people"></li>
                    <li id="modal-vehicles"></li>
                    <li id="modal-electricity"></li>
                </ul>
                <p id="modal-notes" class="text-sm text-black flex-1 pr-24"></p>
            </div>
        </div>

        <div class="pb-6 pr-6 flex justify-end">
            <a id="modal-book-btn" class="rounded-lg border-2 border-cerulean-400 px-12 py-4 text-xl font-semibold text-cerulean-900 bg-cerulean-300 hover:bg-cerulean-400 transition">Boek</a>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal');

    function openModal(el) {
        const d = el.dataset;
        document.getElementById('modal-title').textContent = d.name;
        document.getElementById('modal-type').textContent = d.campsiteType;
        document.getElementById('modal-people').textContent = '• Max personen: ' + d.people;
        document.getElementById('modal-vehicles').textContent = '• Max voertuigen: ' + d.vehicles;
        document.getElementById('modal-electricity').textContent = '• Stroom: ' + (d.electricity === 'true' ? 'Ja' : 'Nee');
        document.getElementById('modal-notes').textContent = d.notes || 'Geen extra informatie beschikbaar';
        document.getElementById('modal-book-btn').href = d.url;
        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>