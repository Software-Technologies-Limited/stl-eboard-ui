<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class Toolbar extends Component
{
    /**
     * @param  Renderable|string|array<int, Renderable|string>|null  $actions
     * @param  array<string, string|int|float|bool|null>  $hiddenFields
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $formAttributes
     */
    public function __construct(
        private readonly string $query = '',
        private readonly string $placeholder = 'Search',
        private readonly Renderable|string|array|null $actions = null,
        private readonly array $hiddenFields = [],
        array $attributes = [],
        private readonly array $formAttributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $hiddenFields = '';
        foreach ($this->hiddenFields as $name => $value) {
            if ($value === null) {
                continue;
            }

            $hiddenFields .= '<input type="hidden" name="'.Html::escape($name).'" value="'.Html::escape((string) $value).'">';
        }

        return sprintf(
            '<div%1$s><form%2$s><label><span aria-hidden="true">⌕</span><input name="q" type="search" value="%3$s" placeholder="%4$s"></label>%5$s</form><div>%6$s</div></div>',
            $this->attrs(['class' => 'stl-toolbar']),
            Html::attributes(array_replace(['method' => 'get'], $this->formAttributes)),
            Html::escape($this->query),
            Html::escape($this->placeholder),
            $hiddenFields,
            $this->renderActions(),
        );
    }

    private function renderActions(): string
    {
        if (is_array($this->actions)) {
            return implode('', array_map($this->renderContent(...), $this->actions));
        }

        return $this->renderContent($this->actions);
    }

    private function renderContent(Renderable|string|null $content): string
    {
        return $content instanceof Renderable ? $content->render() : Html::escape($content ?? '');
    }
}
