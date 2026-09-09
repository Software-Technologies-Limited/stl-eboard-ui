<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class WorkspaceSidebar extends Component
{
    /**
     * @param  array<int, array{key: string, label: string, href: string, icon?: string}>  $primaryItems
     * @param  array<int, array{key: string, label: string, href: string, icon?: string, badge?: string}>  $sidebarItems
     * @param  array{label: string, href?: string}|null  $compose
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly array $primaryItems,
        private readonly array $sidebarItems,
        private readonly string $activePrimary,
        private readonly ?string $activeSidebar = null,
        private readonly ?array $compose = null,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return '<aside'.$this->attrs(['class' => 'stl-workspace-sidebar', 'aria-label' => 'Workspace navigation']).'>'
            .$this->renderPrimaryNavigation()
            .$this->renderContextNavigation()
            .'</aside>';
    }

    private function renderPrimaryNavigation(): string
    {
        $items = '';

        foreach ($this->primaryItems as $item) {
            $isActive = $item['key'] === $this->activePrimary;
            $items .= '<a class="stl-workspace-sidebar__primary-item" href="'.Html::escape($item['href']).'"'
                .($isActive ? ' aria-current="true"' : '')
                .' title="'.Html::escape($item['label']).'">'
                .$this->renderIcon($item['icon'] ?? '')
                .'<span>'.Html::escape($item['label']).'</span></a>';
        }

        return '<nav class="stl-workspace-sidebar__primary" aria-label="Application areas">'.$items.'</nav>';
    }

    private function renderContextNavigation(): string
    {
        $compose = $this->renderCompose();
        $items = '';

        foreach ($this->sidebarItems as $item) {
            $isActive = $item['key'] === $this->activeSidebar;
            $badge = isset($item['badge'])
                ? '<small class="stl-workspace-sidebar__badge">'.Html::escape($item['badge']).'</small>'
                : '';

            $items .= '<a class="stl-workspace-sidebar__context-item" href="'.Html::escape($item['href']).'"'
                .($isActive ? ' aria-current="page"' : '')
                .'>'.$this->renderIcon($item['icon'] ?? '')
                .'<strong>'.Html::escape($item['label']).'</strong>'.$badge.'</a>';
        }

        return '<section class="stl-workspace-sidebar__context" aria-label="Context navigation">'
            .$compose.'<nav>'.$items.'</nav></section>';
    }

    private function renderCompose(): string
    {
        if ($this->compose === null) {
            return '';
        }

        $label = Html::escape($this->compose['label']);
        $content = '<span aria-hidden="true">+</span><strong>'.$label.'</strong>';

        if (! isset($this->compose['href'])) {
            return '<span class="stl-workspace-sidebar__compose" aria-disabled="true">'.$content.'</span>';
        }

        return '<a class="stl-workspace-sidebar__compose" href="'.Html::escape($this->compose['href']).'">'.$content.'</a>';
    }

    private function renderIcon(string $icon): string
    {
        if ($icon === '') {
            return '';
        }

        return (new Icon($icon, attributes: ['class' => 'stl-workspace-sidebar__icon']))->render();
    }
}
