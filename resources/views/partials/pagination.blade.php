@if ($paginator->hasPages())
<nav>
    <ul class="pagination justify-content-center">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link" style="color: #D4AF37;">&laquo; Anterior</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" style="color: #1a1a1a; border-color: #D4AF37;">&laquo; Anterior</a></li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link" style="background: #D4AF37; border-color: #D4AF37; color: #1a1a1a; font-weight: 600;">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}" style="color: #1a1a1a; border-color: #D4AF37;">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" style="color: #1a1a1a; border-color: #D4AF37;">Siguiente &raquo;</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">Siguiente &raquo;</span></li>
        @endif
    </ul>
</nav>
@endif
