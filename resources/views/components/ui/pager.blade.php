@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="pager" aria-label="Paginación">
        @if ($paginator->onFirstPage())
            <span class="pager-link is-disabled">Anterior</span>
        @else
            <a class="pager-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
        @endif

        <span>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="pager-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a>
        @else
            <span class="pager-link is-disabled">Siguiente</span>
        @endif
    </nav>
@endif
