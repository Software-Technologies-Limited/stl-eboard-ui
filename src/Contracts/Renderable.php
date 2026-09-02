<?php

declare(strict_types=1);

namespace Stl\EboardUi\Contracts;

use Stringable;

interface Renderable extends Stringable
{
    public function render(): string;
}
