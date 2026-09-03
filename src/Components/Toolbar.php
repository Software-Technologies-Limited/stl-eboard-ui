<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class Toolbar extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $query = '',
        private readonly string $placeholder = 'Search',
        private readonly Renderable|string|null $actions = null,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $actions = $this->actions instanceof Renderable ? $this->actions->render() : Html::escape($this->actions ?? '');

        return sprintf(
            '<div%1$s><label><span aria-hidden="true">⌕</span><input name="q" type="search" value="%2$s" placeholder="%3$s"></label><div>%4$s</div></div>',
            $this->attrs(['class' => 'stl-toolbar']),
            Html::escape($this->query),
            Html::escape($this->placeholder),
            $actions,
        );
    }
}
