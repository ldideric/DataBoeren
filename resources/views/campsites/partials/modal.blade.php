@php
    $wrapperClass = $wrapperClass ?? 'hidden fixed inset-0 bg-black/50 items-center justify-center';
    $dialogClass = $dialogClass ?? 'bg-tan-300 rounded-2xl w-11/12 max-w-lg overflow-hidden';
@endphp
<div id="modal" class="{{ $wrapperClass }}">
    <div class="{{ $dialogClass }}">
        <div class="bg-olivegreen-500 px-6 pt-6 pb-4 flex gap-4 items-start">
            <img src="/img/campsite_placeholder.jpg" alt="" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                    <h2 id="modal-title" class="text-3xl font-semibold text-white"></h2>
                    <button onclick="closeModal()" class="ml-4 text-white/60 hover:text-white text-4xl leading-none">🗙</button>
                </div>
                <p id="modal-type" class="text-lg font-medium text-white"></p>
            </div>
        </div>
        <div class="p-6 flex-1 min-h-0 flex flex-col gap-4">
            <div class="flex gap-5">
                <ul id="modal-details" class="space-y-2 text-sm text-black flex-1">
                    <li id="modal-people"></li>
                    <li id="modal-vehicles"></li>
                    <li id="modal-electricity"></li>
                </ul>
                <p id="modal-notes" class="text-sm text-black flex-1"></p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-black/60 uppercase tracking-wide">Aankomst</label>
                    <input id="modal-checkin" type="date" onchange="updateBookBtn()" class="rounded-lg border-2 border-tan-400 bg-tan-100 px-3 py-2 text-sm text-black focus:outline-none focus:border-cerulean-400">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-black/60 uppercase tracking-wide">Vertrek</label>
                    <input id="modal-checkout" type="date" onchange="updateBookBtn()" class="rounded-lg border-2 border-tan-400 bg-tan-100 px-3 py-2 text-sm text-black focus:outline-none focus:border-cerulean-400">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-black/60 uppercase tracking-wide">Volwassenen</label>
                    <input id="modal-adults" type="number" min="1" value="1" class="rounded-lg border-2 border-tan-400 bg-tan-100 px-3 py-2 text-sm text-black focus:outline-none focus:border-cerulean-400">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-black/60 uppercase tracking-wide">Kinderen</label>
                    <input id="modal-children" type="number" min="0" value="0" class="rounded-lg border-2 border-tan-400 bg-tan-100 px-3 py-2 text-sm text-black focus:outline-none focus:border-cerulean-400">
                </div>
            </div>
            <div class="flex justify-end mt-auto">
                <a id="modal-book-btn"
                   onclick="handleBoek(event)"
                   class="rounded-lg border-2 border-tan-400 bg-tan-200 text-tan-500 cursor-not-allowed px-12 py-4 text-xl font-semibold transition">
                    Boek
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    const modal = document.getElementById('modal');
    const bookBtn = document.getElementById('modal-book-btn');
    let currentCampsiteId = '';

    function updateBookBtn() {
        const checkIn  = document.getElementById('modal-checkin').value;
        const checkOut = document.getElementById('modal-checkout').value;
        if (checkIn && checkOut) {
            bookBtn.classList.remove('border-tan-400', 'bg-tan-200', 'text-tan-500', 'cursor-not-allowed');
            bookBtn.classList.add('border-cerulean-400', 'bg-cerulean-300', 'hover:bg-cerulean-400', 'text-cerulean-900', 'cursor-pointer');
        } else {
            bookBtn.classList.remove('border-cerulean-400', 'bg-cerulean-300', 'hover:bg-cerulean-400', 'text-cerulean-900', 'cursor-pointer');
            bookBtn.classList.add('border-tan-400', 'bg-tan-200', 'text-tan-500', 'cursor-not-allowed');
        }
    }

    function openModal(el) {
        const d = el.dataset;
        currentCampsiteId = d.campsiteId;
        document.getElementById('modal-title').textContent = d.name;
        document.getElementById('modal-type').textContent = d.campsiteType;
        document.getElementById('modal-people').textContent = '• Max personen: ' + d.people;
        document.getElementById('modal-vehicles').textContent = '• Max voertuigen: ' + d.vehicles;
        document.getElementById('modal-electricity').textContent = '• Stroom: ' + (d.electricity === 'true' ? 'Ja' : 'Nee');
        document.getElementById('modal-notes').textContent = d.notes || 'Geen extra informatie beschikbaar';
        document.getElementById('modal-checkin').value  = d.checkIn  ?? '';
        document.getElementById('modal-checkout').value = d.checkOut ?? '';
        document.getElementById('modal-adults').value   = d.adults   ?? 1;
        document.getElementById('modal-children').value = d.children ?? 0;
        updateBookBtn();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function handleBoek(e) {
        e.preventDefault();
        const checkIn  = document.getElementById('modal-checkin').value;
        const checkOut = document.getElementById('modal-checkout').value;
        const adults   = document.getElementById('modal-adults').value;
        const children = document.getElementById('modal-children').value;
        if (!checkIn || !checkOut) return;
        window.location.href = '/bookings/create?campsite=' + currentCampsiteId
            + '&check_in='  + checkIn
            + '&check_out=' + checkOut
            + '&adults='    + adults
            + '&children='  + children;
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>