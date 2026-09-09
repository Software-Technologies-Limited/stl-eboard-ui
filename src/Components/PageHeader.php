<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class PageHeader extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $eyebrow,
        private readonly string $title,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return sprintf(
            '<header%1$s><span>%2$s</span><h1>%3$s</h1></header>',
            $this->attrs(['class' => 'stl-page-header']),
            Html::escape($this->eyebrow),
            Html::escape($this->title),
        );
    }
}
