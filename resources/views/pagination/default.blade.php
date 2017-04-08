@if($paginator->lastPage() > 1)
    <?php $link_limit = 7; ?>

    @if($paginator->lastPage() > 1)
        <ul class="pagination">
            <li>
                <a href="{{ $paginator->url($paginator->currentPage() - 1) }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                <?php
                $half_total_links = floor($link_limit / 2);
                $from = $paginator->currentPage() - $half_total_links;
                $to = $paginator->currentPage() + $half_total_links;
                
                if ($paginator->currentPage() < $half_total_links) {
                    $to += $half_total_links - $paginator->currentPage();
                }
                if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                    $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
                }
                ?>
                @if ($from < $i && $i < $to)
                    <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                        <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            <li>
                <a href="{{ $paginator->url($paginator->currentPage() + 1 > $paginator->total() ? 1 : $paginator->currentPage() + 1  ) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
      @endif
@endif