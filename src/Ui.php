<?php

declare(strict_types=1);

namespace Stl\EboardUi;

use Stl\EboardUi\Components\Accordion;
use Stl\EboardUi\Components\Alert;
use Stl\EboardUi\Components\Badge;
use Stl\EboardUi\Components\Button;
use Stl\EboardUi\Components\Card;
use Stl\EboardUi\Components\Checkbox;
use Stl\EboardUi\Components\DataTable;
use Stl\EboardUi\Components\EmptyState;
use Stl\EboardUi\Components\HtmlFragment;
use Stl\EboardUi\Components\Icon;
use Stl\EboardUi\Components\IconAction;
use Stl\EboardUi\Components\Input;
use Stl\EboardUi\Components\Modal;
use Stl\EboardUi\Components\PageHeader;
use Stl\EboardUi\Components\Pagination;
use Stl\EboardUi\Components\StatCard;
use Stl\EboardUi\Components\Toolbar;
use Stl\EboardUi\Components\WorkspaceShell;
use Stl\EboardUi\Components\WorkspaceSidebar;
use Stl\EboardUi\Contracts\Renderable;

final class Ui
{
    public static function __callStatic(string $name, array $arguments): HtmlFragment
    {
        return ExtendedComponents::make($name, $arguments);
    }

    public static function button(string $label, string $variant = 'primary', string $size = 'md', array $attributes = []): Button
    {
        return new Button($label, $variant, $size, $attributes);
    }

    public static function badge(string $label, string $tone = 'neutral', array $attributes = []): Badge
    {
        return new Badge($label, $tone, $attributes);
    }

    public static function alert(string $message, string $tone = 'info', array $attributes = []): Alert
    {
        return new Alert($message, $tone, $attributes);
    }

    public static function card(string $body, ?string $title = null, ?string $footer = null, array $attributes = []): Card
    {
        return new Card($body, $title, $footer, $attributes);
    }

    public static function statCard(string|int|float $value, string $label, ?string $trend = null, string $tone = 'primary', array $attributes = []): StatCard
    {
        return new StatCard($value, $label, $trend, $tone, $attributes);
    }

    public static function emptyState(string $title, ?string $description = null, Renderable|string|null $action = null, array $attributes = []): EmptyState
    {
        return new EmptyState($title, $description, $action, $attributes);
    }

    public static function input(string $name, ?string $label = null, string $type = 'text', ?string $error = null, array $attributes = []): Input
    {
        return new Input($name, $label, $type, $error, $attributes);
    }

    public static function checkbox(string $name, string $label, bool $checked = false, array $attributes = []): Checkbox
    {
        return new Checkbox($name, $label, $checked, $attributes);
    }

    public static function modal(string $id, string $title, string $body, array $attributes = []): Modal
    {
        return new Modal($id, $title, $body, $attributes);
    }

    public static function accordion(array $items, array $attributes = []): Accordion
    {
        return new Accordion($items, $attributes);
    }

    /** @param array<string, mixed> $attributes */
    public static function icon(string $name, ?string $label = null, array $attributes = []): Icon
    {
        return new Icon($name, $label, $attributes);
    }

    /** @param array<string, mixed> $attributes */
    public static function pageHeader(string $eyebrow, string $title, array $attributes = []): PageHeader
    {
        return new PageHeader($eyebrow, $title, $attributes);
    }

    /** @param array<string, mixed> $attributes */
    public static function toolbar(string $query = '', string $placeholder = 'Search', Renderable|string|null $actions = null, array $attributes = []): Toolbar
    {
        return new Toolbar($query, $placeholder, $actions, $attributes);
    }

    /** @param array<string, mixed> $attributes */
    public static function iconAction(string $icon, string $label, array $attributes = []): IconAction
    {
        return new IconAction($icon, $label, $attributes);
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, Renderable|string|int|float|null>>  $rows
     * @param  array<string, mixed>  $attributes
     */
    public static function dataTable(array $headers, array $rows, array $attributes = []): DataTable
    {
        return new DataTable($headers, $rows, $attributes);
    }

    /**
     * @param  callable(int): string  $urlForPage
     * @param  array<string, mixed>  $attributes
     */
    public static function pagination(int $currentPage, int $lastPage, int $firstItem, int $lastItem, int $total, callable $urlForPage, string $label = 'results', array $attributes = []): Pagination
    {
        return new Pagination($currentPage, $lastPage, $firstItem, $lastItem, $total, $urlForPage, $label, $attributes);
    }

    /** @param array<string, mixed> $attributes */
    public static function workspaceShell(Renderable|string $header, Renderable|string $sidebar, Renderable|string $content, string $toggleLabel = 'Toggle navigation', array $attributes = []): WorkspaceShell
    {
        return new WorkspaceShell($header, $sidebar, $content, $toggleLabel, $attributes);
    }

    /** @param array<int, array{key: string, label: string, href: string, icon?: string}> $primaryItems
     * @param  array<int, array{key: string, label: string, href: string, icon?: string, badge?: string}>  $sidebarItems
     * @param  array{label: string, href?: string}|null  $compose
     * @param  array<string, mixed>  $attributes
     */
    public static function workspaceSidebar(array $primaryItems, array $sidebarItems, string $activePrimary, ?string $activeSidebar = null, ?array $compose = null, array $attributes = []): WorkspaceSidebar
    {
        return new WorkspaceSidebar($primaryItems, $sidebarItems, $activePrimary, $activeSidebar, $compose, $attributes);
    }
}
