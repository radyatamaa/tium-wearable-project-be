<div class="pagination-container">
    <div class="pagination-info">
        @if (config('app.lang') != 'en')
        {{ $paginator->total() }}개 항목 중 {{ $paginator->firstItem() }}~{{ $paginator->lastItem() }}개 표시 중
        @else
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
        @endif
    </div>

    <nav data-pagination>
        {{-- First Page Link --}}
        @if ($paginator->onFirstPage())
        <a href="#" class="disabled"><i class="ion-chevron-left-double"></i></a>
        @else
        <a href="{{ $paginator->url(1) }}{{ request()->has('limit') ? '&limit=' . request()->limit : '' }}">
            <i class="ion-chevron-left-double"></i>
        </a>
        @endif

        {{-- Previous 5 Pages Link --}}
        @if ($paginator->currentPage() > 5)
        <a
            href="{{ $paginator->url($paginator->currentPage() - 5) }}{{ request()->has('limit') ? '&limit=' . request()->limit : '' }}">
            <i class="ion-chevron-left"></i><i class="ion-chevron-left"></i>
        </a>
        @else
        <a href="#" class="disabled"><i class="ion-chevron-left"></i><i class="ion-chevron-left"></i></a>
        @endif

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <a href="#" class="disabled"><i class="ion-chevron-left"></i></a>
        @else
        <a href="{{ $paginator->previousPageUrl() }}{{ request()->has('limit') ? '&limit=' . request()->limit : '' }}">
            <i class="ion-chevron-left"></i>
        </a>
        @endif

        <ul>
            {{-- Pagination Elements --}}
            @php
            $start = floor(($paginator->currentPage() - 1) / 5) * 5 + 1;
            $end = $start + 4 > $paginator->lastPage() ? $paginator->lastPage() : $start + 4;
            @endphp

            @for ($i = $start; $i <= $end; $i++) @if ($i==$paginator->currentPage())
                <li class="current"><a href="#">{{ $i }}</a></li>
                @else
                <li><a
                        href="{{ $paginator->url($i) }}{{ request()->has('limit') ? '&limit=' . request()->limit : '' }}">{{ $i }}</a>
                </li>
                @endif
                @endfor
        </ul>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}{{ request()->has('limit') ? '&limit=' . request()->limit : '' }}">
            <i class="ion-chevron-right"></i>
        </a>
        @else
        <a href="#" class="disabled"><i class="ion-chevron-right"></i></a>
        @endif

        {{-- Next 5 Pages Link --}}
        @if ($paginator->currentPage() + 5 <= $paginator->lastPage())
            <a
                href="{{ $paginator->url($paginator->currentPage() + 5) }}{{ request()->has('limit') ? '&limit=' . request()->limit : '' }}">
                <i class="ion-chevron-right"></i><i class="ion-chevron-right"></i>
            </a>
            @else
            <a href="#" class="disabled"><i class="ion-chevron-right"></i><i class="ion-chevron-right"></i></a>
            @endif

            {{-- Last Page Link --}}
            @if ($paginator->hasMorePages())
            <a
                href="{{ $paginator->url($paginator->lastPage()) }}{{ request()->has('limit') ? '&limit=' . request()->limit : '' }}">
                <i class="ion-chevron-right-double"></i>
            </a>
            @else
            <a href="#" class="disabled"><i class="ion-chevron-right-double"></i></a>
            @endif
    </nav>

    <div class="pagination-limit">
        <form action="{{ url()->current() }}" method="GET">
            <div class="btn-group exclude-dropdown">
                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    @if (request()->has('limit'))
                    @if (config('app.lang') != 'en')
                    {{ __(':limit 개씩 보기', ['limit' => request()->limit]) }}
                    @else
                    {{ __('See :limit each', ['limit' => request()->limit]) }}
                    @endif
                    @else
                    @if (config('app.lang') != 'en')
                    {{ __('10개씩 보기') }}
                    @else
                    {{ __('See 10 each') }}
                    @endif
                    @endif
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['limit' => 10]) }}">10</a>
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['limit' => 25]) }}">25</a>
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['limit' => 50]) }}">50</a>
                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['limit' => 100]) }}">100</a>
                </div>
            </div>
        </form>
    </div>
</div>