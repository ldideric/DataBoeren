@if ($paginator->hasPages())
    <nav class="mt-6 flex items-center justify-between">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="rounded-lg border border-tan-400 bg-tan-200 px-4 py-2 text-sm text-black/40 cursor-not-allowed select-none">
                ← Vorige
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="rounded-lg border border-tan-400 bg-tan-300 px-4 py-2 text-sm text-black transition hover:bg-tan-400">
                ← Vorige
            </a>
        @endif

        {{-- Page numbers --}}
        <div class="flex items-center gap-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-2 text-sm text-black/40">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="rounded-lg border-2 border-cerulean-400 bg-cerulean-300 px-3 py-1.5 text-sm font-semibold text-cerulean-900">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="rounded-lg border border-tan-400 bg-tan-300 px-3 py-1.5 text-sm text-black transition hover:bg-tan-400">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="rounded-lg border border-tan-400 bg-tan-300 px-4 py-2 text-sm text-black transition hover:bg-tan-400">
                Volgende →
            </a>
        @else
            <span class="rounded-lg border border-tan-400 bg-tan-200 px-4 py-2 text-sm text-black/40 cursor-not-allowed select-none">
                Volgende →
            </span>
        @endif
    </nav>
@endif
