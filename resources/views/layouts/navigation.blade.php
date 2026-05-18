{{--
    layouts/navigation.blade.php
    This partial is @include'd into the layout. It contains the navbar.
    Anything here shows up at the top of every page that uses layouts.app.
--}}
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-6">
        <div class="flex justify-between items-center h-14">

            {{-- Left side: logo + nav links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="text-base font-bold text-green-700 tracking-tight">
                    De Groene Weide
                </a>

                <div class="hidden sm:flex items-center gap-1 text-sm">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Bookings</a>
                    <a href="#" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Campsites</a>
                    <a href="#" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Extras</a>
                    <a href="{{ route('invulformulier') }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">Invulformulier</a>
                </div>

            {{-- Right side: user info --}}
            <div class="flex items-center gap-3 text-sm">
                @auth
                    <span class="text-gray-400">{{ auth()->user()->name }}</span>
                    <a href="#" class="px-3 py-1.5 border border-gray-200 rounded-md text-gray-600 hover:bg-gray-50 transition-colors">
                        Log out
                    </a>
                @else
                    <a href="#" class="px-3 py-1.5 border border-gray-200 rounded-md text-gray-600 hover:bg-gray-50 transition-colors">
                        Log in
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>
