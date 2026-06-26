<nav x-data="{ open: false }" class="relative bg-olivegreen-500 border-b border-olivegreen-600">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-[1fr_auto] items-center h-14 sm:grid-cols-[1fr_auto_1fr]">

            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5 justify-self-start text-base font-bold text-white tracking-tight whitespace-nowrap">
                <img src="{{ asset('logo.svg') }}" alt="De Groene Weide" class="h-8 w-8">
                De Groene Weide
            </a>

            <div class="hidden sm:flex items-center gap-1 justify-self-center text-sm">
                <a href="{{ route('home') }}" @class(['px-3 py-1.5 rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a href="{{ route('campsites.index') }}" @class(['px-3 py-1.5 rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('campsites.*')]) @if(request()->routeIs('campsites.*')) aria-current="page" @endif>Kampeerplaatsen</a>
                <a href="{{ route('activities') }}" @class(['px-3 py-1.5 rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('pages.activities')]) @if(request()->routeIs('pages.activities')) aria-current="page" @endif>Activiteiten</a>
            </div>

            <div class="hidden sm:flex shrink-0 items-center gap-3 justify-self-end text-sm">
                @if (! empty($showLogout))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 border border-white rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 hover:border-olivegreen-800 transition-colors">
                            Uitloggen
                        </button>
                    </form>
                @else
                    <a href="{{ $myBookingsUrl ?? route('login') }}" class="px-3 py-1.5 border border-white rounded-md text-white whitespace-nowrap hover:bg-olivegreen-600 hover:border-olivegreen-800 transition-colors">
                        Mijn boekingen
                    </a>
                @endif
            </div>

            <button type="button" @click="open = ! open" :aria-expanded="open" aria-controls="mobile-menu" aria-label="Menu" class="sm:hidden justify-self-end inline-flex items-center justify-center rounded-md p-2 text-white hover:bg-olivegreen-600 transition-colors">
                <svg x-show="! open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

        </div>
    </div>

    <div x-show="open" x-cloak x-transition.origin.top id="mobile-menu" class="sm:hidden border-t border-olivegreen-600">
        <div class="space-y-1 px-4 py-3 text-sm">
            <a href="{{ route('home') }}" @class(['block px-3 py-2 rounded-md text-white hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
            <a href="{{ route('campsites.index') }}" @class(['block px-3 py-2 rounded-md text-white hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('campsites.*')]) @if(request()->routeIs('campsites.*')) aria-current="page" @endif>Kampeerplaatsen</a>
            <a href="{{ route('activities') }}" @class(['block px-3 py-2 rounded-md text-white hover:bg-olivegreen-600 transition-colors', 'bg-olivegreen-600 font-semibold' => request()->routeIs('pages.activities')]) @if(request()->routeIs('pages.activities')) aria-current="page" @endif>Activiteiten</a>

            <div class="pt-2">
                @if (! empty($showLogout))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 border border-white rounded-md text-white hover:bg-olivegreen-600 hover:border-olivegreen-800 transition-colors">
                            Uitloggen
                        </button>
                    </form>
                @else
                    <a href="{{ $myBookingsUrl ?? route('login') }}" class="block px-3 py-2 border border-white rounded-md text-white hover:bg-olivegreen-600 hover:border-olivegreen-800 transition-colors">
                        Mijn boekingen
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
