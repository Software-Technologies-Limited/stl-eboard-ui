<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Checkbox extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(private readonly string $name, private readonly string $label, private readonly bool $checked = false, array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return '<label class="stl-checkbox"><input'.$this->attrs(['class' => 'stl-checkbox__input', 'type' => 'checkbox', 'name' => $this->name, 'value' => '1', 'checked' => $this->checked]).'><span>'.Html::escape($this->label).'</span></label>';
    }
}
