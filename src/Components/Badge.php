<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Badge extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(private readonly string $label, private readonly string $tone = 'neutral', array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return '<span'.$this->attrs(['class' => "stl-badge stl-badge--{$this->tone}"]).'>'.Html::escape($this->label).'</span>';
    }
}
