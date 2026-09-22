<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class FormErrorSummary extends Component
{
    /** @param array<string, string|array<int, string>> $errors @param array<string, mixed> $attributes */
    public function __construct(private readonly array $errors, private readonly string $title = 'Please correct the following fields', array $attributes = []) { parent::__construct($attributes); }
    public function render(): string
    {
        $items = '';
        foreach ($this->errors as $field => $messages) foreach ((array) $messages as $message) {
            $id = $this->fieldId((string) $field);
            $items .= '<li><a href="#'.Html::escape($id).'" data-stl-error-focus>'.Html::escape($message).'</a></li>';
        }
        return '<section'.$this->attrs(['class' => 'stl-form-error-summary', 'role' => 'alert', 'tabindex' => '-1', 'data-stl-error-summary' => true]).'><p class="stl-form-error-summary__title">'.Html::escape($this->title).'</p><ul>'.$items.'</ul></section>';
    }

    private function fieldId(string $field): string
    {
        // Laravel error keys use dots while HTML names commonly use brackets.
        $parts = explode('.', $field);
        $name = array_shift($parts);
        foreach ($parts as $part) $name .= '['.$part.']';
        return 'stl-'.preg_replace('/[^a-z0-9_-]+/i', '-', $name);
    }
}
