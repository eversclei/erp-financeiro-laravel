@if ($paginator->hasPages())
    <nav class="acoes" style="justify-content:center;align-items:center" aria-label="Paginação">
        <md-outlined-icon-button href="{{ $paginator->previousPageUrl() }}" @if ($paginator->onFirstPage()) disabled @endif aria-label="Página anterior">
            <md-icon>chevron_left</md-icon>
        </md-outlined-icon-button>
        <span class="md-typescale-body-medium">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
        <md-outlined-icon-button href="{{ $paginator->nextPageUrl() }}" @if (! $paginator->hasMorePages()) disabled @endif aria-label="Próxima página">
            <md-icon>chevron_right</md-icon>
        </md-outlined-icon-button>
    </nav>
@endif
