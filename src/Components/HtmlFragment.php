<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;

final class HtmlFragment extends Component
{
    public function __construct(private readonly string $html)
    {
        parent::__construct();
    }

    public function render(): string
    {
        return $this->html;
    }
}
