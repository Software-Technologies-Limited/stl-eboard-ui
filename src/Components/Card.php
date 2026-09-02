<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class Card extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $body,
        private readonly ?string $title = null,
        private readonly ?string $footer = null,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $title = $this->title === null ? '' : '<h3 class="stl-card__title">'.Html::escape($this->title).'</h3>';
        $footer = $this->footer === null ? '' : '<footer class="stl-card__footer">'.Html::escape($this->footer).'</footer>';

        return '<section'.$this->attrs(['class' => 'stl-card']).'>'.$title.'<div class="stl-card__body">'.$this->content($this->body).'</div>'.$footer.'</section>';
    }

    private function content(mixed $content): string
    {
        return $content instanceof Renderable ? $content->render() : Html::escape($content);
    }
}
