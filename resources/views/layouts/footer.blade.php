@include('cookies.cookie-modal')

<x.cookie-modal>
<nav class="relative bg-olivegreen-500 border-t border-olivegreen-600">
    <div class="max-w-7xl mx-auto px-6">
        <div class="relative flex justify-between items-center h-14">

            <div class="flex gap-4">
                <img src="{{ asset('logo.svg') }}" alt="De Groene Weide" class="h-8 w-8">
            </div>

             <div class="absolute left-1/2 -translate-x-1/2 hidden sm:flex gap-3 text-sm">
                <a href="{{ route('about') }}" class="px-3 py-1.5 text-white hover:underline">Over ons</a>
                <a href="{{ route('contact') }}" class="px-3 py-1.5 text-white hover:underline">Contact</a>
                <a href="{{ route('privacy') }}" class="px-3 py-1.5 text-white hover:underline">Privacystatement</a>
                <a href="{{ route('houserules') }}" class="px-3 py-1.5 text-white hover:underline">Campingregels</a>
            </div>

            <div class="text-right max-w-xs">
                <span class="text-xs text-white tracking-tight">
                    Dit product is gemaakt door studenten op Hogeschool Utrecht.
                </span>
            </div>

        </div>
    </div>
</nav>