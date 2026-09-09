<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Accordion extends Component
{
    /** @param array<int, array{title: string, content: string, open?: bool}> $items */
    public function __construct(private readonly array $items, array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $items = '';
        foreach ($this->items as $item) {
            $items .= '<details class="stl-accordion__item"'.(! empty($item['open']) ? ' open' : '').'><summary>'.Html::escape($item['title']).'</summary><div class="stl-accordion__content">'.Html::escape($item['content']).'</div></details>';
        }

        return '<div'.$this->attrs(['class' => 'stl-accordion']).'>'.$items.'</div>';
    }
}
