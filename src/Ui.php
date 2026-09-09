<?php

declare(strict_types=1);

namespace Stl\EboardUi;

use Stl\EboardUi\Components\{Accordion, Alert, Badge, Button, Card, Checkbox, Input, Modal, Toast, Toaster};

final class Ui
{
    public static function __callStatic(string $name, array $arguments): \Stl\EboardUi\Components\HtmlFragment
    {
        return ExtendedComponents::make($name, $arguments);
    }
    public static function button(string $label, string $variant = 'primary', string $size = 'md', array $attributes = []): Button { return new Button($label, $variant, $size, $attributes); }
    public static function badge(string $label, string $tone = 'neutral', array $attributes = []): Badge { return new Badge($label, $tone, $attributes); }
    public static function alert(string $message, string $tone = 'info', array $attributes = []): Alert { return new Alert($message, $tone, $attributes); }
    public static function card(string $body, ?string $title = null, ?string $footer = null, array $attributes = []): Card { return new Card($body, $title, $footer, $attributes); }
    public static function input(string $name, ?string $label = null, string $type = 'text', ?string $error = null, array $attributes = []): Input { return new Input($name, $label, $type, $error, $attributes); }
    public static function checkbox(string $name, string $label, bool $checked = false, array $attributes = []): Checkbox { return new Checkbox($name, $label, $checked, $attributes); }
    public static function modal(string $id, string $title, string $body, array $attributes = []): Modal { return new Modal($id, $title, $body, $attributes); }
    public static function accordion(array $items, array $attributes = []): Accordion { return new Accordion($items, $attributes); }
    public static function toast(string $title, ?string $message = null, string $tone = 'info', int $timeout = 3500, array $attributes = []): Toast { return new Toast($title, $message, $tone, $timeout, $attributes); }
    public static function toaster(array $toasts = [], string $position = 'top-center', array $attributes = []): Toaster { return new Toaster($toasts, $position, $attributes); }
    public static function flashToaster(array $flash, string $position = 'top-center', array $attributes = []): Toaster { return Toaster::fromFlash($flash, $position, $attributes); }
}
