<?php

declare(strict_types=1);

use Stl\EboardUi\Ui;

if (! function_exists('eboard_ui')) {
    function eboard_ui(): Ui
    {
        return new Ui;
    }
}
