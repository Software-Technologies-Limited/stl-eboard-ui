<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class Tabs extends Component
{
    /**
     * @param  array<int, array{id: string, label: string, content: Renderable|string}>  $items
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly array $items,
        private readonly string $active,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $tabs = $panels = '';
        $active = $this->active ?: ($this->items[0]['id'] ?? '');

        foreach ($this->items as $item) {
            $id = $item['id'];
            $tabId = 'stl-tab-'.$id;
            $panelId = $tabId.'-panel';
            $selected = $id === $active;
            $content = $item['content'] instanceof Renderable ? $item['content']->render() : Html::escape($item['content']);

            $tabs .= '<button id="'.Html::escape($tabId).'" type="button" role="tab" aria-controls="'.Html::escape($panelId).'" aria-selected="'.($selected ? 'true' : 'false').'" tabindex="'.($selected ? '0' : '-1').'">'.Html::escape($item['label']).'</button>';
            $panels .= '<section id="'.Html::escape($panelId).'" role="tabpanel" aria-labelledby="'.Html::escape($tabId).'"'.($selected ? '' : ' hidden').'>'.$content.'</section>';
        }

        return '<div'.$this->attrs(['class' => 'stl-tabs', 'data-stl-tabs' => true]).'><div role="tablist">'.$tabs.'</div>'.$panels.'</div>';
    }
}
