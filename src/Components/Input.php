<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Input extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $name,
        private readonly ?string $label = null,
        private readonly string $type = 'text',
        private readonly ?string $error = null,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $id = (string) ($this->attributes['id'] ?? 'stl-'.preg_replace('/[^a-z0-9_-]+/i', '-', $this->name));
        $errorId = $id.'-error';
        $label = $this->label === null ? '' : '<label class="stl-field__label" for="'.Html::escape($id).'">'.Html::escape($this->label).'</label>';
        $error = $this->error === null ? '' : '<span class="stl-field__error" id="'.Html::escape($errorId).'">'.Html::escape($this->error).'</span>';
        $defaults = ['class' => 'stl-input', 'id' => $id, 'name' => $this->name, 'type' => $this->type];
        if ($this->error !== null) {
            $defaults['aria-invalid'] = 'true';
            $defaults['aria-describedby'] = $errorId;
        }

        return '<div class="stl-field">'.$label.'<input'.$this->attrs($defaults).'>'.$error.'</div>';
    }
}
