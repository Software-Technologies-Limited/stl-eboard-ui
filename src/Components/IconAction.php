<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class IconAction extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly Renderable|string $icon,
        private readonly string $label,
        array $attributes = [],
        private readonly ?string $tooltip = null,
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $tooltip = $this->tooltip ?? $this->label;

        return sprintf(
            '<button%1$s aria-label="%2$s" data-tooltip="%3$s"><span aria-hidden="true">%4$s</span></button>',
            $this->attrs(['class' => 'stl-icon-action stl-tooltip', 'type' => 'button']),
            Html::escape($this->label),
            Html::escape($tooltip),
            $this->icon instanceof Renderable ? $this->icon->render() : Html::escape($this->icon),
        );
    }
}
