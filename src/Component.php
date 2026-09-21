<?php

declare(strict_types=1);

namespace Stl\EboardUi;

use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

abstract class Component implements Renderable
{
    /** @var array<string, array<string, mixed>> */
    protected array $parts = [];

    /**
     * Attributes apply to the component's primary element. The optional `parts`
     * map applies attributes directly to its named children, for example:
     * ['parts' => ['label' => ['class' => 'text-sm font-bold']]].
     *
     * @param array<string, mixed> $attributes
     */
    public function __construct(protected array $attributes = [])
    {
        $this->parts = (array) ($attributes['parts'] ?? []);
        unset($this->attributes['parts']);
    }

    /** @param array<string, mixed> $attributes */
    public function with(array $attributes): static
    {
        $clone = clone $this;
        if (isset($attributes['parts'])) {
            $clone->parts = array_replace_recursive($clone->parts, (array) $attributes['parts']);
            unset($attributes['parts']);
        }
        $clone->attributes = array_replace($clone->attributes, $attributes);

        return $clone;
    }

    /** @param array<string, mixed> $defaults */
    protected function attrs(array $defaults = []): string
    {
        return Html::attributes(Html::mergeAttributes($defaults, $this->attributes));
    }

    /** @param array<string, mixed> $defaults */
    protected function partAttrs(string $part, array $defaults = []): string
    {
        return Html::attributes(Html::mergeAttributes($defaults, $this->parts[$part] ?? []));
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
