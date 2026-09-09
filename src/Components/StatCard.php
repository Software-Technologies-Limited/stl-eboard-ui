<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class StatCard extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string|int|float $value,
        private readonly string $label,
        private readonly ?string $trend = null,
        private readonly string $tone = 'primary',
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $trend = $this->trend === null || $this->trend === ''
            ? ''
            : '<span class="stl-stat-card__trend">'.Html::escape($this->trend).'</span>';

        return '<section'.$this->attrs(['class' => "stl-stat-card stl-stat-card--{$this->tone}"]).'>'
            .'<span class="stl-stat-card__label">'.Html::escape($this->label).'</span>'
            .'<strong class="stl-stat-card__value">'.Html::escape($this->value).'</strong>'
            .$trend
            .'</section>';
    }
}
