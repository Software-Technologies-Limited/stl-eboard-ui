<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Select extends Component
{
    /** @param array<array-key, string|int> $options @param array<string, mixed> $attributes */
    public function __construct(private readonly string $name, private readonly array $options, private readonly string $label = 'Select', private readonly string|int|null $selected = null, private readonly ?string $error = null, array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $id = (string) ($this->attributes['id'] ?? 'stl-'.preg_replace('/[^a-z0-9_-]+/i', '-', $this->name));
        $errorId = $id.'-error';
        $required = array_key_exists('required', $this->attributes) && $this->attributes['required'] !== false;
        $label = '<label'.$this->partAttrs('label', ['class' => 'stl-field__label', 'for' => $id]).'>'.Html::escape($this->label).$this->requiredMark($required).'</label>';
        $options = '';
        foreach ($this->options as $value => $text) {
            $optionValue = is_string($value) ? $value : $text;
            $options .= '<option value="'.Html::escape($optionValue).'"'.((string) $optionValue === (string) $this->selected ? ' selected' : '').'>'.Html::escape($text).'</option>';
        }
        $defaults = ['class' => 'stl-select', 'id' => $id, 'name' => $this->name];
        if ($required) $defaults['aria-required'] = 'true';
        if ($this->error !== null) $defaults += ['aria-invalid' => 'true', 'aria-describedby' => $errorId];
        $error = $this->error === null ? '' : '<span'.$this->partAttrs('error', ['class' => 'stl-field__error', 'id' => $errorId, 'role' => 'alert']).'>'.Html::escape($this->error).'</span>';

        return '<div'.$this->partAttrs('field', ['class' => 'stl-field']).'>'.$label.'<select'.$this->attrs($defaults).'>'.$options.'</select>'.$error.'</div>';
    }

    private function requiredMark(bool $required): string { return $required ? '<span class="stl-field__required" aria-hidden="true">*</span><span class="stl-sr-only"> required</span>' : ''; }
}
