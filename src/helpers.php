<?php

declare(strict_types=1);

use Stl\EboardUi\Ui;

if (! class_exists('Ui')) {
    class_alias(Ui::class, 'Ui');
}

if (! function_exists('eboard_ui')) {
    function eboard_ui(): Ui
    {
        return new Ui;
    }
}
