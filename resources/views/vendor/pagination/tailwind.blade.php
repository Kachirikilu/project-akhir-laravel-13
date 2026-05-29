@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        {{-- Mobile View --}}
        <div class="flex gap-2 items-center justify-between flex-1 sm:hidden">
            @php $pageName = $paginator->getPageName(); @endphp
            @if ($paginator->onFirstPage())
                <span
                    class="cursor-pointer inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed leading-5 rounded-md dark:text-gray-500 dark:bg-neutral-800 dark:border-neutral-700">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button type="button"
                    wire:click="previousPage('{{ $pageName }}'); {{ $typeXLoading ?? 'loadingTable' }}()"
                    rel="prev"
                    class="cursor-pointer inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 leading-5 rounded-md hover:bg-gray-100 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-200 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button"
                    wire:click="nextPage('{{ $pageName }}'); {{ $typeXLoading ?? 'loadingTable' }}()" rel="next"
                    class="cursor-pointer inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 leading-5 rounded-md hover:bg-gray-100 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-200 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span
                    class="cursor-pointer inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed leading-5 rounded-md dark:text-gray-500 dark:bg-neutral-800 dark:border-neutral-700">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between sm:gap-4">
            @if (!($isSmall ?? false))
            <div>
                <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
                    {!! __('Menampilkan') !!}
                    @if ($paginator->firstItem())
                        <span class="font-bold text-[var(--contrast-main-text)]">{{ $paginator->firstItem() }}</span>
                        {!! __('sampai') !!}
                        <span class="font-bold text-[var(--contrast-main-text)]">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('dari') !!}
                    <span class="font-bold text-[var(--contrast-main-text)]">{{ $paginator->total() }}</span>
                    {!! __('hasil') !!}
                </p>
            </div>
            @endif
            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Page Link --}}
                    @php $pageName = $paginator->getPageName(); @endphp
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed rounded-l-md leading-5 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-500"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <button type="button"
                            wire:click="previousPage('{{ $pageName }}'); {{ $typeXLoading ?? 'loadingTable' }}()"
                            rel="prev"
                            class="cursor-pointer inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300 dark:hover:bg-gray-900 transition ease-in-out duration-150"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="cursor-pointer inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="cursor-pointer inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-[var(--main-color)] border border-[var(--main-color)] cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <button type="button"
                                        wire:click="gotoPage({{ $page }}, '{{ $pageName }}'); {{ $typeXLoading ?? 'loadingTable' }}()"
                                        class="cursor-pointer inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:bg-gray-100 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300 dark:hover:bg-gray-900 transition ease-in-out duration-150"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button type="button"
                            wire:click="nextPage('{{ $pageName }}'); {{ $typeXLoading ?? 'loadingTable' }}()"
                            rel="next"
                            class="cursor-pointer inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300 dark:hover:bg-gray-900 transition ease-in-out duration-150"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed rounded-r-md leading-5 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-500"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>

@endif
