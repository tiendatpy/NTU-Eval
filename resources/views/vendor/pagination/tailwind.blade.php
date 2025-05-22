@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center mt-6 gap-4">
        {{-- Pagination Info --}}
        <div class="text-sm text-gray-600">
            {{ __('Hiển thị') }}
            @if ($paginator->firstItem())
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                {{ __('đến') }}
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {{ __('trong tổng số') }}
            <span class="font-medium">{{ $paginator->total() }}</span>
            {{ __('kết quả') }}
        </div>

        {{-- Pagination Elements --}}
        <div class="flex justify-center">
            <div class="inline-flex rounded-md shadow-sm">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-l-md cursor-not-allowed">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">{{ __('Trang trước') }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-gray-300 rounded-l-md hover:bg-blue-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-300">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">{{ __('Trang trước') }}</span>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="px-4 py-2 text-sm font-medium text-white bg-states-600 border border-blue-600">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-300" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-gray-300 rounded-r-md hover:bg-blue-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-300">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">{{ __('Trang sau') }}</span>
                    </a>
                @else
                    <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-r-md cursor-not-allowed">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">{{ __('Trang sau') }}</span>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif