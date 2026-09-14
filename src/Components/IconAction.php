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

        return '<button'.$this->with(['aria-label' => $this->label, 'data-tooltip' => $tooltip])->attrs(['class' => 'stl-icon-action stl-tooltip', 'type' => 'button'])
            .'><span aria-hidden="true">'.($this->icon instanceof Renderable ? $this->icon->render() : Html::escape($this->icon)).'</span></button>';
    }
}
