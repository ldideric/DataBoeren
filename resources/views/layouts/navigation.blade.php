<nav class="bg-olivegreen border-b border-olivegreen">
    <div class="max-w-4xl mx-auto px-6">
        <div class="flex justify-between items-center h-14">

            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="px-3 py-1.5 border bg-transparent border-white rounded-md text-base font-bold text-white tracking-tight hover:border-olivegreen2 hover:bg-olivegreen2">
                    De Groene Weide
                </a>

                <div class="hidden sm:flex items-center gap-1 text-sm">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-md text-white hover:bg-olivegreen2 transition-colors">Home</a>
                    <a href="{{ route('campsites.index') }}" class="px-3 py-1.5 rounded-md text-white hover:bg-olivegreen2 transition-colors">Kampeerplaatsen</a>
                    <a href="{{ route('map.index') }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Plattegrond</a>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('login') }}" class="px-3 py-1.5 border border-gray-200 rounded-md text-white hover:bg-olivegreen2 hover:border-olivegreen2 transition-colors">
                    Mijn boekingen
                </a>
            </div>

        </div>
    </div>
</nav>
