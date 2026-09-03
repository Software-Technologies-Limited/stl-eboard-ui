<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class WorkspaceShell extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly Renderable|string $header,
        private readonly Renderable|string $sidebar,
        private readonly Renderable|string $content,
        private readonly string $toggleLabel = 'Toggle navigation',
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return '<div'.$this->attrs([
            'class' => 'stl-workspace-shell',
            'data-stl-workspace-shell' => true,
            'data-stl-workspace-collapsed' => 'false',
        ]).'><header class="stl-workspace-shell__header">'
            .'<button class="stl-workspace-shell__toggle" type="button" data-stl-workspace-toggle aria-expanded="true" aria-label="'.Html::escape($this->toggleLabel).'">☰</button>'
            .$this->content($this->header).'</header>'
            .'<div class="stl-workspace-shell__body">'.$this->content($this->sidebar)
            .'<main class="stl-workspace-shell__main">'.$this->content($this->content).'</main></div></div>';
    }

    private function content(Renderable|string $content): string
    {
        return $content instanceof Renderable ? $content->render() : Html::escape($content);
    }
}
