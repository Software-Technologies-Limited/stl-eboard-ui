<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class IconAction extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $icon,
        private readonly string $label,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return sprintf(
            '<button%1$s aria-label="%2$s"><span aria-hidden="true">%3$s</span></button>',
            $this->attrs(['class' => 'stl-icon-action', 'type' => 'button']),
            Html::escape($this->label),
            Html::escape($this->icon),
        );
    }
}
