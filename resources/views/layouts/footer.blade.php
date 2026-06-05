<nav class="relative bg-olivegreen-500 border-t border-olivegreen-600">
    <div class="max-w-4xl mx-auto px-6">
        <div class="flex justify-between items-center h-14">

            <div class="flex items-center gap-6">
                <img src="{{ asset('favicon.svg') }}" alt="De Groene Weide" class="h-8 w-8">
                <a class="px-3 py-1.5 text-base text-white tracking-tight">
                    Dit product is gemaakt door studenten aan Hogeschool Utrecht.
                </a>

                <div class="hidden sm:flex items-center gap-3 text-sm">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-md text-white hover:bg-olivegreen-600 transition-colors">Over ons</a>
                    <a href="{{ route('campsites.index') }}" class="px-3 py-1.5 rounded-md text-white hover:bg-olivegreen-600 transition-colors">Contact</a>
                    <a href="{{ route('privacy') }}" class="px-3 py-1.5 rounded-md text-white hover:bg-olivegreen-600 transition-colors">Privacystatement</a>
                </div>
            </div>

        </div>
    </div>
</nav>