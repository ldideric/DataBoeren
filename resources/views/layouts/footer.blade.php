@include('cookies.cookie-modal')

<nav class="relative bg-olivegreen-500 border-t border-olivegreen-600">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col items-center gap-4 py-5 text-center sm:flex-row sm:justify-between sm:gap-4 sm:py-0 sm:h-14 sm:text-left">

            <div class="flex shrink-0 gap-4">
                <img src="{{ asset('logo.svg') }}" alt="De Groene Weide" class="h-8 w-8">
            </div>

            <div class="flex flex-col items-center gap-1 text-sm sm:flex-row sm:gap-3">
                <a href="{{ route('contact') }}" class="px-3 py-1.5 text-white hover:underline">Contact</a>
                <a href="{{ route('privacy') }}" class="px-3 py-1.5 text-white hover:underline">Privacystatement</a>
                <a href="{{ route('houserules') }}" class="px-3 py-1.5 text-white hover:underline">Campingregels</a>
            </div>

            <div class="sm:text-right sm:max-w-xs">
                <span class="text-xs text-white tracking-tight">
                    Dit product is gemaakt door studenten op Hogeschool Utrecht.
                </span>
            </div>

        </div>
    </div>
</nav>
