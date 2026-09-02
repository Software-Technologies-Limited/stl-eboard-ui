<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Modal extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(private readonly string $id, private readonly string $title, private readonly string $body, array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return '<dialog'.$this->attrs(['class' => 'stl-modal', 'id' => $this->id, 'aria-labelledby' => $this->id.'-title']).'>'
            .'<div class="stl-modal__header"><h2 id="'.Html::escape($this->id).'-title">'.Html::escape($this->title).'</h2><button class="stl-modal__close" type="button" data-stl-close aria-label="Close">&times;</button></div>'
            .'<div class="stl-modal__body">'.Html::escape($this->body).'</div></dialog>';
    }
}
