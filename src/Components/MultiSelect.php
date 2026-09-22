<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use InvalidArgumentException;
use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class MultiSelect extends Component
{
    private static int $sequence = 0;

    /**
     * @param  array<array-key, string>|array<int, array{value: string|int, label: string}>  $options
     * @param  array<int, string|int>  $selected
     * @param  array<string, mixed>  $attributes  Attributes applied to the trigger button.
     */
    public function __construct(
        private readonly string $name,
        private readonly array $options,
        private readonly array $selected = [],
        private readonly ?string $label = null,
        private readonly string $placeholder = 'Select options',
        array $attributes = [],
        private readonly ?string $error = null,
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $id = (string) ($this->attributes['id'] ?? 'stl-multiselect-'.++self::$sequence);
        $disabled = ! empty($this->attributes['disabled']);
        $required = array_key_exists('required', $this->attributes) && $this->attributes['required'] !== false;
        $errorId = $id.'-error';
        $options = $this->normaliseOptions();
        $selected = array_fill_keys(array_map(static fn (string|int $value): string => (string) $value, $this->selected), true);
        $selectedLabels = [];
        $items = '';

        foreach ($options as $option) {
            $isSelected = isset($selected[$option['value']]);
            if ($isSelected) {
                $selectedLabels[] = '<span'.$this->partAttrs('chip', ['class' => 'stl-multiselect__chip']).'>'.Html::escape($option['label']).'</span>';
            }

            $items .= '<label'.$this->partAttrs('option', ['class' => 'stl-multiselect__option', 'data-stl-multiselect-option' => true]).'>'
                .'<input'.$this->partAttrs('optionInput', ['class' => 'stl-multiselect__input', 'type' => 'checkbox', 'name' => $this->inputName(), 'value' => $option['value']])
                .($isSelected ? ' checked' : '').($disabled ? ' disabled' : '').' data-stl-multiselect-input>'
                .'<span'.$this->partAttrs('check', ['class' => 'stl-multiselect__check', 'aria-hidden' => 'true']).'>✓</span>'
                .'<span'.$this->partAttrs('optionLabel').'>'.Html::escape($option['label']).'</span>'
                .'</label>';
        }

        $value = $selectedLabels === []
            ? '<span'.$this->partAttrs('placeholder', ['class' => 'stl-multiselect__placeholder', 'data-stl-multiselect-value' => true]).'>'.Html::escape($this->placeholder).'</span>'
            : '<span'.$this->partAttrs('chips', ['class' => 'stl-multiselect__chips', 'data-stl-multiselect-value' => true]).'>'.implode('', $selectedLabels).'</span>';
        $label = $this->label === null ? '' : '<label'.$this->partAttrs('label', ['class' => 'stl-field__label', 'for' => $id]).'>'.Html::escape($this->label).($required ? '<span class="stl-field__required" aria-hidden="true">*</span><span class="stl-sr-only"> required</span>' : '').'</label>';
        $triggerAttributes = $this->attributes;
        unset($triggerAttributes['required']);
        $triggerDefaults = ['class' => 'stl-multiselect__trigger', 'type' => 'button', 'id' => $id, 'aria-label' => $this->label ?? $this->placeholder, 'aria-expanded' => 'true', 'aria-controls' => $id.'-listbox', 'data-stl-multiselect-trigger' => true, 'data-placeholder' => $this->placeholder];
        $hasError = $this->error !== null && $this->error !== '';
        if ($required) $triggerDefaults['aria-required'] = 'true';
        if ($hasError) $triggerDefaults += ['aria-invalid' => 'true', 'aria-describedby' => $errorId];
        $error = ! $hasError ? '' : '<span'.$this->partAttrs('error', ['class' => 'stl-field__error', 'id' => $errorId, 'role' => 'alert']).'>'.Html::escape($this->error).'</span>';

        return '<div'.$this->partAttrs('field', ['class' => 'stl-field stl-multiselect-field']).'>'.$label
            .'<span'.$this->partAttrs('control', ['class' => 'stl-multiselect', 'data-stl-multiselect' => true, 'data-stl-validation-name' => $this->name, 'data-stl-required' => $required ?: null]).'>'
            .'<button'.Html::attributes(Html::mergeAttributes($triggerDefaults, $triggerAttributes)).'>'.$value.'<span'.$this->partAttrs('chevron', ['class' => 'stl-multiselect__chevron', 'aria-hidden' => 'true']).'>⌄</span></button>'
            .'<span'.$this->partAttrs('panel', ['id' => $id.'-panel', 'class' => 'stl-multiselect__panel', 'data-stl-multiselect-panel' => true]).'>'
            .'<input'.$this->partAttrs('search', ['class' => 'stl-multiselect__search', 'type' => 'search', 'placeholder' => 'Search options…', 'aria-label' => 'Search '.($this->label ?? $this->placeholder), 'data-stl-multiselect-search' => true]).'>'
            .'<span'.$this->partAttrs('options', ['id' => $id.'-listbox', 'class' => 'stl-multiselect__options', 'role' => 'group', 'aria-labelledby' => $id]).'>'.$items.'</span>'
            .'<span'.$this->partAttrs('empty', ['class' => 'stl-multiselect__empty', 'data-stl-multiselect-empty' => true, 'hidden' => true]).'>No options found</span>'
            .'</span></span>'.$error.'</div>';
    }

    /** @return array<int, array{value: string, label: string}> */
    private function normaliseOptions(): array
    {
        $normalised = [];
        foreach ($this->options as $value => $option) {
            if (is_array($option) && array_key_exists('value', $option) && array_key_exists('label', $option)) {
                $normalised[] = ['value' => (string) $option['value'], 'label' => (string) $option['label']];

                continue;
            }
            if (is_array($option) || is_object($option)) {
                throw new InvalidArgumentException('MultiSelect options need value and label keys.');
            }
            if (is_string($value) || is_int($value)) {
                $normalised[] = ['value' => (string) $value, 'label' => (string) $option];

                continue;
            }
            throw new InvalidArgumentException('MultiSelect options must be value => label pairs or arrays with value and label keys.');
        }

        return $normalised;
    }

    private function inputName(): string
    {
        return str_ends_with($this->name, '[]') ? $this->name : $this->name.'[]';
    }
}
