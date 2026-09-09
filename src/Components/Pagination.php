<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Closure;
use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Pagination extends Component
{
    private readonly Closure $urlForPage;

    /**
     * @param  callable(int): string  $urlForPage
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly int $currentPage,
        private readonly int $lastPage,
        private readonly int $firstItem,
        private readonly int $lastItem,
        private readonly int $total,
        callable $urlForPage,
        private readonly string $label = 'results',
        array $attributes = [],
    ) {
        parent::__construct($attributes);

        $this->urlForPage = Closure::fromCallable($urlForPage);
    }

    public function render(): string
    {
        $summary = sprintf(
            'Showing %d to %d of %d %s',
            $this->firstItem,
            $this->lastItem,
            $this->total,
            Html::escape($this->label),
        );

        return '<nav'.$this->attrs(['class' => 'stl-pagination-bar', 'aria-label' => 'Pagination']).'><span>'.$summary.'</span><div>'.$this->links().'</div></nav>';
    }

    private function links(): string
    {
        if ($this->lastPage < 2) {
            return '';
        }

        $links = $this->navigationLink($this->currentPage - 1, 'Previous', $this->currentPage > 1);
        $start = max(1, $this->currentPage - 2);
        $end = min($this->lastPage, $this->currentPage + 2);

        if ($start > 1) {
            $links .= $this->pageLink(1);
            $links .= '<span aria-hidden="true">…</span>';
        }

        for ($page = $start; $page <= $end; $page++) {
            $links .= $this->pageLink($page);
        }

        if ($end < $this->lastPage) {
            $links .= '<span aria-hidden="true">…</span>';
            $links .= $this->pageLink($this->lastPage);
        }

        return $links.$this->navigationLink($this->currentPage + 1, 'Next', $this->currentPage < $this->lastPage);
    }

    private function navigationLink(int $page, string $label, bool $enabled): string
    {
        if (! $enabled) {
            return '<span class="is-disabled" aria-disabled="true">'.Html::escape($label).'</span>';
        }

        return '<a href="'.Html::escape(($this->urlForPage)($page)).'">'.Html::escape($label).'</a>';
    }

    private function pageLink(int $page): string
    {
        $current = $page === $this->currentPage ? ' aria-current="page"' : '';

        return '<a href="'.Html::escape(($this->urlForPage)($page)).'"'.$current.'>'.$page.'</a>';
    }
}
