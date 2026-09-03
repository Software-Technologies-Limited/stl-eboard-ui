<?php

declare(strict_types=1);

namespace Stl\EboardUi;

final class Assets
{
    public static function cssPath(): string
    {
        return dirname(__DIR__).'/resources/css/eboard-ui.css';
    }

    public static function jsPath(): string
    {
        return dirname(__DIR__).'/resources/js/eboard-ui.js';
    }

    public static function styles(string $url): string
    {
        return '<link rel="stylesheet" href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'">';
    }

    public static function scripts(string $url, bool $defer = true): string
    {
        return '<script src="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'"'.($defer ? ' defer' : '').'></script>';
    }
}
