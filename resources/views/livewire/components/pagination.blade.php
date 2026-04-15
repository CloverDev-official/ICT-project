@if ($paginator->hasPages())
    <div class="mt-4 flex flex-col md:flex-row items-center justify-between gap-3">
        {{-- Info jumlah data --}}
        <p class="text-sm text-gray-500">
            Menampilkan
            <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span>
            data
        </p>

        {{-- Navigasi pagination --}}
        <nav class="inline-flex items-center gap-1" role="navigation" aria-label="Pagination Navigation">
            {{-- Tombol Prev --}}
            @if ($paginator->onFirstPage())
                <span class="h-9 px-3 inline-flex items-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                    Prev
                </span>
            @else
                <button
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    rel="prev"
                    class="h-9 px-3 inline-flex items-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 text-sm transition"
                >
                    Prev
                </button>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                {{-- "..." --}}
                @if (is_string($element))
                    <span class="h-9 min-w-9 px-3 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 text-sm">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                aria-current="page"
                                class="h-9 min-w-9 px-3 inline-flex items-center justify-center rounded-lg bg-blue-main text-white text-sm font-semibold shadow-sm"
                            >
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                class="h-9 min-w-9 px-3 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-700 text-sm transition"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <button
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    rel="next"
                    class="h-9 px-3 inline-flex items-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 text-sm transition"
                >
                    Next
                </button>
            @else
                <span class="h-9 px-3 inline-flex items-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                    Next
                </span>
            @endif
        </nav>
    </div>
@endif