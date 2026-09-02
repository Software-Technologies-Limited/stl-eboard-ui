<?php

declare(strict_types=1);

namespace Stl\EboardUi;

use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

abstract class Component implements Renderable
{
    /** @param array<string, mixed> $attributes */
    public function __construct(protected array $attributes = [])
    {
    }

    /** @param array<string, mixed> $attributes */
    public function with(array $attributes): static
    {
        $clone = clone $this;
        $clone->attributes = array_replace($clone->attributes, $attributes);

        return $clone;
    }

    /** @param array<string, mixed> $defaults */
    protected function attrs(array $defaults = []): string
    {
        $attributes = array_replace($defaults, $this->attributes);
        if (isset($defaults['class'], $this->attributes['class'])) {
            $attributes['class'] = Html::classes((string) $defaults['class'], (string) $this->attributes['class']);
        }

        return Html::attributes($attributes);
    }

    final public function __toString(): string
    {
        try {
            return $this->render();
        } catch (\Throwable) {
            return '';
        }
    }
}
