<nav class="bg-white border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-6">
        <div class="flex justify-between items-center h-14">

            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="text-base font-bold text-green-700 tracking-tight">
                    De Groene Weide
                </a>

                <div class="hidden sm:flex items-center gap-1 text-sm">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Home</a>
                    <a href="{{ route('campsites.index') }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Kampeerplaatsen</a>
                    @auth
                        <a href="{{ route('bookings.index') }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Mijn boekingen</a>
                        <a href="{{ route('bookings.create') }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Reserveren</a>
                    @endauth
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                @auth
                    <span class="text-gray-400">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 border border-gray-200 rounded-md text-gray-600 hover:bg-gray-50 transition-colors">
                            Uitloggen
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 border border-gray-200 rounded-md text-gray-600 hover:bg-gray-50 transition-colors">
                        Inloggen
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>
