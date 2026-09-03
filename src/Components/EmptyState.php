<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class EmptyState extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $title,
        private readonly ?string $description = null,
        private readonly Renderable|string|null $action = null,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $description = $this->description === null || $this->description === ''
            ? ''
            : '<p class="stl-empty-state__description">'.Html::escape($this->description).'</p>';
        $action = $this->action === null || $this->action === ''
            ? ''
            : '<div class="stl-empty-state__action">'.$this->content($this->action).'</div>';

        return '<section'.$this->attrs(['class' => 'stl-empty-state']).'>'
            .'<h3 class="stl-empty-state__title">'.Html::escape($this->title).'</h3>'
            .$description
            .$action
            .'</section>';
    }

    private function content(Renderable|string $content): string
    {
        return $content instanceof Renderable ? $content->render() : Html::escape($content);
    }
}
