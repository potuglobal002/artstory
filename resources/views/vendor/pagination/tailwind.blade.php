@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="art-pagination">
        <p class="art-pagination__summary">
            Showing
            <span>{{ $paginator->firstItem() }}</span>
            to
            <span>{{ $paginator->lastItem() }}</span>
            of
            <span>{{ $paginator->total() }}</span>
            results
        </p>

        <div class="art-pagination__links">
            @if ($paginator->onFirstPage())
                <span class="art-pagination__control is-disabled" aria-hidden="true">
                    <span class="material-symbols-rounded">chevron_left</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="art-pagination__control" aria-label="{{ __('pagination.previous') }}">
                    <span class="material-symbols-rounded">chevron_left</span>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="art-pagination__ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="art-pagination__page is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="art-pagination__page" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="art-pagination__control" aria-label="{{ __('pagination.next') }}">
                    <span class="material-symbols-rounded">chevron_right</span>
                </a>
            @else
                <span class="art-pagination__control is-disabled" aria-hidden="true">
                    <span class="material-symbols-rounded">chevron_right</span>
                </span>
            @endif
        </div>
    </nav>
@endif
