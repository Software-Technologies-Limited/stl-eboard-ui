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
        private readonly bool $loading = false,
        private readonly string $loadingLabel = 'Please wait…',
        private readonly bool $loadingOnSubmit = false,
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

        if (! $this->loading && ! $this->loadingOnSubmit) {
            return sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $this->attrs($defaults), Html::escape($this->label));
        }

        $attributes = Html::mergeAttributes($defaults, $this->attributes);
        $attributes['data-stl-loading-button'] = true;
        $attributes['data-stl-loading-label'] = $this->loadingLabel;
        $attributes['data-stl-idle-label'] = $this->label;
        $attributes['data-stl-loading-on-submit'] = $this->loadingOnSubmit ? 'true' : 'false';
        $attributes['data-stl-loading'] = $this->loading ? 'true' : 'false';
        $attributes['data-stl-idle-disabled'] = ! empty($this->attributes['disabled']) ? 'true' : 'false';
        $attributes['aria-busy'] = $this->loading ? 'true' : 'false';
        if ($this->loading) {
            if ($tag === 'button') {
                $attributes['disabled'] = true;
            } else {
                $attributes['aria-disabled'] = 'true';
            }
        }

        return sprintf('<%1$s%2$s><span class="stl-button__spinner" aria-hidden="true"></span><span data-stl-button-label>%3$s</span></%1$s>', $tag, Html::attributes($attributes), Html::escape($this->loading ? $this->loadingLabel : $this->label));
    }
}
