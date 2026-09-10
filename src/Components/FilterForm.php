<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class FilterForm extends Component
{
    /**
     * @param  array<string, array{label: string, href: string}>  $filters
     * @param  array<string, string|int|float|bool|null>  $hiddenFields
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly array $filters,
        private readonly string $active = '',
        private readonly string $query = '',
        private readonly string $placeholder = 'Search',
        private readonly string $queryName = 'q',
        private readonly array $hiddenFields = [],
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $links = '';

        foreach ($this->filters as $value => $filter) {
            if (! isset($filter['label'], $filter['href'])) {
                continue;
            }

            $links .= '<a'.Html::attributes([
                'class' => (string) $value === $this->active ? 'is-active' : null,
                'href' => $filter['href'],
                'aria-current' => (string) $value === $this->active ? 'page' : null,
            ]).'>'.Html::escape($filter['label']).'</a>';
        }

        $hiddenFields = '';
        foreach ($this->hiddenFields as $name => $value) {
            if ($value === null) {
                continue;
            }

            $hiddenFields .= '<input type="hidden" name="'.Html::escape($name).'" value="'.Html::escape((string) $value).'">';
        }

        return '<form'.$this->attrs(['class' => 'stl-filter-form', 'method' => 'get'])
            .'><nav class="stl-filter-form__options" aria-label="Filters">'.$links.'</nav>'
            .'<label class="stl-filter-form__search"><span class="sr-only">'.Html::escape($this->placeholder).'</span>'
            .'<input name="'.Html::escape($this->queryName).'" value="'.Html::escape($this->query).'" type="search" placeholder="'.Html::escape($this->placeholder).'" aria-label="'.Html::escape($this->placeholder).'">'
            .$hiddenFields.'</label></form>';
    }
}
