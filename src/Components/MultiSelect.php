<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use InvalidArgumentException;
use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class MultiSelect extends Component
{
    /**
     * @param  array<array-key, string>|array<int, array{value: string|int, label: string}>  $options
     * @param  array<int, string|int>  $selected
     * @param  array<string, mixed>  $attributes Attributes applied to the trigger button.
     */
    public function __construct(
        private readonly string $name,
        private readonly array $options,
        private readonly array $selected = [],
        private readonly ?string $label = null,
        private readonly string $placeholder = 'Select options',
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $id = 'stl-multiselect-'.substr(md5($this->name.'-'.serialize($this->options)), 0, 10);
        $options = $this->normaliseOptions();
        $selected = array_fill_keys(array_map(static fn (string|int $value): string => (string) $value, $this->selected), true);
        $selectedLabels = [];
        $items = '';

        foreach ($options as $option) {
            $isSelected = isset($selected[$option['value']]);
            if ($isSelected) {
                $selectedLabels[] = '<span class="stl-multiselect__chip">'.Html::escape($option['label']).'</span>';
            }

            $items .= '<label class="stl-multiselect__option" data-stl-multiselect-option>'
                .'<input class="stl-multiselect__input" type="checkbox" name="'.Html::escape($this->inputName()).'" value="'.Html::escape($option['value']).'"'
                .($isSelected ? ' checked' : '').' data-stl-multiselect-input>'
                .'<span class="stl-multiselect__check" aria-hidden="true">✓</span>'
                .'<span>'.Html::escape($option['label']).'</span>'
                .'</label>';
        }

        $value = $selectedLabels === []
            ? '<span class="stl-multiselect__placeholder" data-stl-multiselect-value>'.Html::escape($this->placeholder).'</span>'
            : '<span class="stl-multiselect__chips" data-stl-multiselect-value>'.implode('', $selectedLabels).'</span>';
        $label = $this->label === null ? '' : '<span class="stl-field__label">'.Html::escape($this->label).'</span>';

        return '<div class="stl-field stl-multiselect-field">'.$label
            .'<span class="stl-multiselect" data-stl-multiselect>'
            .'<button'.$this->attrs(['class' => 'stl-multiselect__trigger', 'type' => 'button', 'id' => $id, 'aria-expanded' => 'false', 'aria-controls' => $id.'-listbox', 'data-stl-multiselect-trigger' => true, 'data-placeholder' => $this->placeholder]).'>'.$value.'<span class="stl-multiselect__chevron" aria-hidden="true">⌄</span></button>'
            .'<span id="'.$id.'-panel" class="stl-multiselect__panel" data-stl-multiselect-panel hidden>'
            .'<input class="stl-multiselect__search" type="search" placeholder="Search options…" aria-label="Search '.Html::escape($this->label ?? $this->placeholder).'" data-stl-multiselect-search>'
            .'<span id="'.$id.'-listbox" class="stl-multiselect__options" role="group" aria-labelledby="'.$id.'">'.$items.'</span>'
            .'<span class="stl-multiselect__empty" data-stl-multiselect-empty hidden>No options found</span>'
            .'</span></span></div>';
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
