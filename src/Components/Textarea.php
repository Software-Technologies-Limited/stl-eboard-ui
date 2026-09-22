<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Textarea extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(private readonly string $name, private readonly string $label = 'Message', private readonly int $rows = 4, private readonly string $value = '', private readonly ?string $error = null, array $attributes = []) { parent::__construct($attributes); }
    public function render(): string
    {
        $id = (string) ($this->attributes['id'] ?? 'stl-'.preg_replace('/[^a-z0-9_-]+/i', '-', $this->name)); $errorId = $id.'-error'; $required = array_key_exists('required', $this->attributes) && $this->attributes['required'] !== false;
        $label = '<label'.$this->partAttrs('label', ['class' => 'stl-field__label', 'for' => $id]).'>'.Html::escape($this->label).($required ? '<span class="stl-field__required" aria-hidden="true">*</span><span class="stl-sr-only"> required</span>' : '').'</label>';
        $defaults = ['class' => 'stl-textarea', 'id' => $id, 'name' => $this->name, 'rows' => $this->rows];
        if ($required) $defaults['aria-required'] = 'true'; if ($this->error !== null) $defaults += ['aria-invalid' => 'true', 'aria-describedby' => $errorId];
        $error = $this->error === null ? '' : '<span'.$this->partAttrs('error', ['class' => 'stl-field__error', 'id' => $errorId, 'role' => 'alert']).'>'.Html::escape($this->error).'</span>';
        return '<div'.$this->partAttrs('field', ['class' => 'stl-field']).'>'.$label.'<textarea'.$this->attrs($defaults).'>'.Html::escape($this->value).'</textarea>'.$error.'</div>';
    }
}
