@if (!request()->cookie('functional_consent'))
    <div id="cookie_modal" class="fixed bottom-0 left-0 right-0 z-10 p-4"> {{-- kan nog bg-black/50 ofzo bij --}}
        <div class="rounded-2xl p-8 w-full border-2 border-tan-400 bg-tan-200">
            <div class="flex justify-between mb-3">
                <h2 class="text-2xl font-bold text-olivegreen-400">Welkom bij onze website!</h2>
            </div>
            <hr class="my-4 border-2 border-tan-400">
            <p class="text-md font-normal text-black">Door gebruik te maken van onze website maak je gebruik van technische en functionele cookies.<br>Wil je hier meer over leren? <a href="/privacy#cookie_statement" class="underline hover:text-cerulean-500 hover:no-underline">Klik dan hier. </a></p>
            <form action="{{ route('cookies.acknowledge') }}" method="POST">
                @csrf
                <div class="flex justify-end">
                    <button type="submit" class="text-white underline bg-olivegreen-600 hover:bg-olivegreen-700 hover:no-underline cursor-pointer rounded-2xl p-3">Begrepen</button>
                </div>
            </form>
        </div>
    </div>
@endif