<nav class="relative bg-olivegreen-500 border-b border-olivegreen-600">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center h-14">

            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5 justify-self-start text-base font-bold text-white tracking-tight whitespace-nowrap">
                <img src="{{ asset('logo.svg') }}" alt="De Groene Weide" class="h-8 w-8">
                De Groene Weide
            </a>

            <div class="hidden sm:flex items-center gap-1 justify-self-center text-sm">
                <a href="{{ route('home') }}" @class(['px-3 py-1.5 rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a href="{{ route('campsites.index') }}" @class(['px-3 py-1.5 rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('campsites.*')]) @if(request()->routeIs('campsites.*')) aria-current="page" @endif>Kampeerplaatsen</a>
                <a href="{{ route('privacy') }}" @class(['px-3 py-1.5 rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('privacy')]) @if(request()->routeIs('privacy')) aria-current="page" @endif>Privacystatement</a>
            </div>

            <div class="flex shrink-0 items-center gap-3 justify-self-end text-sm">
                <a href="{{ route('login') }}" class="px-3 py-1.5 border border-white rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 hover:border-olivegreen-800 transition-colors">
                    Mijn boekingen
                </a>
            </div>

        </div>
    </div>
</nav>
