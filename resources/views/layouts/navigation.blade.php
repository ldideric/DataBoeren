<nav class="relative bg-olivegreen-500 border-b border-olivegreen-600">
    <div class="max-w-4xl mx-auto px-6">
        <div class="flex justify-between items-center h-14">

            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="px-3 py-1.5 border bg-transparent border-white rounded-md text-base font-bold text-white tracking-tight hover:border-olivegreen-800 hover:bg-olivegreen-600">
                    De Groene Weide
                </a>

                <div class="hidden sm:flex items-center gap-1 text-sm">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-md text-white hover:bg-olivegreen-600 transition-colors">Home</a>
                    <a href="{{ route('campsites.index') }}" class="px-3 py-1.5 rounded-md text-white hover:bg-olivegreen-600 transition-colors">Kampeerplaatsen</a>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('login') }}" class="px-3 py-1.5 border border-white rounded-md text-white hover:bg-olivegreen-600 hover:border-olivegreen-800 transition-colors">
                    Mijn boekingen
                </a>
            </div>

        </div>
    </div>
</nav>
