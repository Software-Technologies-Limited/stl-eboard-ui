<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Alert extends Component
{
    /** @param array<string, mixed> $attributes */
    public function __construct(private readonly string $message, private readonly string $tone = 'info', array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        return '<div'.$this->attrs(['class' => "stl-alert stl-alert--{$this->tone}", 'role' => 'status']).'>'.Html::escape($this->message).'</div>';
    }
}
