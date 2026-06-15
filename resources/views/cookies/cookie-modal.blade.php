@if (!request()->cookie('functional_consent'))
    <div id="cookie_modal" class="fixed bottom-0 left-0 right-0 z-10 p-4">
        <div class="mx-auto w-full max-w-6xl rounded-2xl p-6 border-2 border-tan-400 bg-tan-200">
            <h2 class="text-2xl font-bold text-olivegreen-400 mb-3">Welkom op onze website!</h2>
            <hr class="my-4 border-2 border-tan-400">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-md font-normal text-black">Door gebruik te maken van onze website maak je gebruik van technische en functionele cookies.<br>Wil je hier meer over leren? <a href="/privacy#cookie_statement" class="underline hover:text-cerulean-500 hover:no-underline">Klik dan hier.</a></p>
                <form action="{{ route('cookies.acknowledge') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="text-white bg-olivegreen-600 hover:bg-olivegreen-700 hover:no-underline cursor-pointer rounded-2xl p-3">Begrepen</button>
                </form>
            </div>
        </div>
    </div>
@endif
