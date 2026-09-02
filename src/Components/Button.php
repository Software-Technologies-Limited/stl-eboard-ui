<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Button extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $label,
        private readonly string $variant = 'primary',
        private readonly string $size = 'md',
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $tag = isset($this->attributes['href']) ? 'a' : 'button';
        $defaults = ['class' => "stl-button stl-button--{$this->variant} stl-button--{$this->size}"];
        if ($tag === 'button') {
            $defaults['type'] = 'button';
        }

        return sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $this->attrs($defaults), Html::escape($this->label));
    }
}
