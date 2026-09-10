<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class Panel extends Component
{
    /**
     * @param  Renderable|array<int, Renderable|string>|null  $actions
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly string $title,
        private readonly Renderable|string $body,
        private readonly Renderable|array|null $actions = null,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return '<section'.$this->attrs(['class' => 'stl-panel']).'>'
            .'<header class="stl-panel__header"><h2>'.Html::escape($this->title).'</h2>'
            .'<div class="stl-panel__actions">'.$this->renderActions().'</div></header>'
            .'<div class="stl-panel__body">'.$this->renderContent($this->body).'</div></section>';
    }

    private function renderActions(): string
    {
        if (! is_array($this->actions)) {
            return $this->renderContent($this->actions);
        }

        return implode('', array_map($this->renderContent(...), $this->actions));
    }

    private function renderContent(Renderable|string|null $content): string
    {
        return $content instanceof Renderable ? $content->render() : Html::escape($content ?? '');
    }
}
