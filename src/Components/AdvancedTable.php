<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use InvalidArgumentException;
use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

/** A progressively enhanced, server-rendered data table. */
final class AdvancedTable extends Component
{
    /**
     * @param  array<int, array{key: string, label: string, accessor?: callable|string, formatter?: callable, sortable?: bool, toggleable?: bool, visible?: bool}>  $columns
     * @param  array<int, array<string, mixed>|object>  $rows
     * @param  array<int, array{key: string, label?: string, icon?: Renderable|string, tooltip?: string|callable, href?: string|callable, visible?: bool|callable, disabled?: bool|callable, permission?: bool|callable, attributes?: array<string, mixed>}>  $actions
     * @param  array{title?: string, description?: string, variant?: string, density?: string, striped?: bool, hoverable?: bool, clickableRows?: bool, selectable?: bool, selectionName?: string, selected?: array<int, string|int>, toolbar?: bool, toolbarActions?: Renderable|string|array<int, Renderable|string>, bulkActions?: Renderable|string|array<int, Renderable|string>, addAction?: Renderable|string, refreshHref?: string, filter?: Renderable|string, emptyTitle?: string, emptySubtitle?: string, emptyAction?: Renderable|string, loading?: bool, skeletonRows?: int, rowKey?: string}  $options
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(private readonly array $columns, private readonly array $rows, private readonly array $actions = [], private readonly array $options = [], array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $variant = $this->option('variant', 'default');
        $density = $this->option('density', 'compact');
        if (! in_array($variant, ['default', 'bordered', 'minimal'], true) || ! in_array($density, ['compact', 'normal', 'comfortable'], true)) {
            throw new InvalidArgumentException('Invalid advanced table variant or density.');
        }

        $columns = $this->columns();
        $selectable = (bool) $this->option('selectable', false);
        $visibleActions = $this->hasVisibleActions();
        $columnCount = max(1, count(array_filter($columns, static fn (array $column): bool => ($column['visible'] ?? true) !== false)) + ($selectable ? 1 : 0) + ($visibleActions ? 1 : 0));
        $tableId = 'stl-advanced-table-'.spl_object_id($this);
        $header = $this->header($tableId, $columns, $selectable, $visibleActions);
        $body = $this->body($columns, $selectable, $visibleActions, $columnCount);
        $classes = 'stl-advanced-table stl-advanced-table--'.$variant.' stl-advanced-table--'.$density
            .($this->option('striped', true) ? ' is-striped' : '')
            .($this->option('hoverable', true) ? ' is-hoverable' : '')
            .($this->option('clickableRows', false) ? ' is-clickable' : '');

        return '<section'.$this->attrs(['class' => $classes, 'data-stl-advanced-table' => true]).'>'
            .$this->intro()
            .$this->toolbar($tableId, $columns)
            .'<div class="stl-advanced-table__scroll"><table><thead>'.$header.'</thead><tbody>'.$body.'</tbody></table></div>'
            .'</section>';
    }

    /** @return array<int, array<string, mixed>> */
    private function columns(): array
    {
        foreach ($this->columns as $column) {
            if (! isset($column['key'], $column['label'])) {
                throw new InvalidArgumentException('Every advanced table column needs key and label.');
            }
        }

        return array_values($this->columns);
    }

    /** @param array<int, array<string, mixed>> $columns */
    private function header(string $tableId, array $columns, bool $selectable, bool $actions): string
    {
        $html = '<tr>';
        if ($selectable) {
            $html .= '<th class="stl-advanced-table__selection"><input type="checkbox" aria-label="Select all rows" data-stl-table-select-all></th>';
        }
        foreach ($columns as $column) {
            $sortable = (bool) ($column['sortable'] ?? false);
            $html .= '<th scope="col" data-key="'.Html::escape((string) $column['key']).'"'.(($column['visible'] ?? true) === false ? ' hidden' : '').($sortable ? ' data-stl-table-sort tabindex="0" aria-sort="none"' : '').'>'
                .Html::escape((string) $column['label']).($sortable ? '<span class="stl-advanced-table__sort" aria-hidden="true">↕</span>' : '').'</th>';
        }
        if ($actions) {
            $html .= '<th class="stl-advanced-table__actions-heading">Actions</th>';
        }

        return $html.'</tr>';
    }

    /** @param array<int, array<string, mixed>> $columns */
    private function body(array $columns, bool $selectable, bool $actions, int $columnCount): string
    {
        if ((bool) $this->option('loading', false)) {
            return $this->skeleton($columns, $selectable, $actions);
        }
        if ($this->rows === []) {
            return '<tr class="stl-advanced-table__empty"><td colspan="'.$columnCount.'">'.$this->empty().'</td></tr>';
        }

        $selected = array_fill_keys(array_map('strval', (array) $this->option('selected', [])), true);
        $name = (string) $this->option('selectionName', 'selected');
        $name = str_ends_with($name, '[]') ? $name : $name.'[]';
        $keyField = (string) $this->option('rowKey', 'id');
        $html = '';
        foreach ($this->rows as $index => $row) {
            $rowKey = (string) ($this->value($row, $keyField) ?? $index);
            $rowHref = $this->rowHref($row);
            $html .= '<tr data-stl-table-row'.($rowHref !== null ? ' data-stl-row-href="'.Html::escape($rowHref).'" tabindex="0"' : '').'>';
            if ($selectable) {
                $html .= '<td class="stl-advanced-table__selection"><input type="checkbox" name="'.Html::escape($name).'" value="'.Html::escape($rowKey).'"'.(isset($selected[$rowKey]) ? ' checked' : '').' data-stl-table-select></td>';
            }
            foreach ($columns as $column) {
                $value = $this->cellValue($row, $column);
                $html .= '<td data-stl-table-cell="'.Html::escape((string) $column['key']).'"'.(($column['visible'] ?? true) === false ? ' hidden' : '').'>'.$this->content($value).'</td>';
            }
            if ($actions) {
                $html .= '<td class="stl-advanced-table__actions">'.$this->actions($row).'</td>';
            }
            $html .= '</tr>';
        }

        return $html;
    }

    /** @param array<int, array<string, mixed>> $columns */
    private function skeleton(array $columns, bool $selectable, bool $actions): string
    {
        $out = '';
        for ($row = 0; $row < max(1, (int) $this->option('skeletonRows', 5)); $row++) {
            $out .= '<tr class="stl-advanced-table__skeleton">'.($selectable ? '<td><span></span></td>' : '');
            foreach ($columns as $column) {
                $out .= '<td data-stl-table-cell="'.Html::escape((string) $column['key']).'"'.(($column['visible'] ?? true) === false ? ' hidden' : '').'><span></span></td>';
            }
            if ($actions) {
                $out .= '<td><span></span><span></span></td>';
            }
            $out .= '</tr>';
        }

        return $out;
    }

    private function intro(): string
    {
        $title = $this->option('title');
        $description = $this->option('description');

        return $title || $description ? '<header class="stl-advanced-table__intro">'.($title ? '<h2>'.Html::escape((string) $title).'</h2>' : '').($description ? '<p>'.Html::escape((string) $description).'</p>' : '').'</header>' : '';
    }

    /** @param array<int, array<string, mixed>> $columns */
    private function toolbar(string $id, array $columns): string
    {
        $bulk = $this->renderMany($this->option('bulkActions', []));
        $bulkHidden = (array) $this->option('selected', []) === [] ? ' hidden' : '';
        if (! (bool) $this->option('toolbar', false) && ! $this->option('toolbarActions') && ! $this->option('addAction') && ! $this->option('refreshHref') && ! $this->option('filter')) {
            return $bulk === '' ? '' : '<div class="stl-advanced-table__bulk-actions" data-stl-table-bulk-actions'.$bulkHidden.'>'.$bulk.'</div>';
        }
        $toggle = '';
        foreach ($columns as $column) {
            if (($column['toggleable'] ?? true) !== false) {
                $toggle .= '<label><input type="checkbox"'.(($column['visible'] ?? true) !== false ? ' checked' : '').' data-stl-table-column-toggle value="'.Html::escape((string) $column['key']).'">'.Html::escape((string) $column['label']).'</label>';
            }
        }
        $extra = $this->renderMany($this->option('toolbarActions', []));
        $filter = $this->content($this->option('filter', ''));
        $add = $this->content($this->option('addAction', ''));
        $refresh = $this->option('refreshHref') ? '<a class="stl-button stl-button--secondary stl-button--sm" href="'.Html::escape((string) $this->option('refreshHref')).'">Refresh</a>' : '';

        return '<div class="stl-advanced-table__toolbar">'.$filter.'<div>'.$extra.$refresh.$add.'<details class="stl-advanced-table__column-picker"><summary>Columns</summary><div>'.$toggle.'</div></details></div></div>'
            .($bulk === '' ? '' : '<div class="stl-advanced-table__bulk-actions" data-stl-table-bulk-actions'.$bulkHidden.'>'.$bulk.'</div>');
    }

    private function empty(): string
    {
        return '<div><strong>'.Html::escape((string) $this->option('emptyTitle', 'No data available')).'</strong><p>'.Html::escape((string) $this->option('emptySubtitle', 'Try adjusting your search or filter criteria.')).'</p>'.$this->content($this->option('emptyAction', '')).'</div>';
    }

    private function actions(array|object $row): string
    {
        $html = '<div class="stl-advanced-table__action-list">';
        foreach ($this->actions as $action) {
            if (! $this->allowed($action, $row)) {
                continue;
            }
            $label = (string) ($action['label'] ?? $action['key']);
            $tooltip = $action['tooltip'] ?? $label;
            $tooltip = is_callable($tooltip) ? $tooltip($row) : $tooltip;
            $disabled = (bool) $this->resolve($action['disabled'] ?? false, $row);
            $attributes = (array) ($action['attributes'] ?? []);
            $attributes['class'] = Html::classes('stl-icon-action stl-tooltip', (string) ($attributes['class'] ?? ''));
            $attributes['aria-label'] = $label;
            $attributes['data-tooltip'] = (string) $tooltip;
            $attributes['data-stl-action-key'] = (string) ($action['key'] ?? $label);
            $icon = $action['icon'] ?? null;
            $body = $icon instanceof Renderable ? $icon->render() : ($icon === null ? Html::escape($label) : Html::escape((string) $icon));
            $href = $action['href'] ?? null;
            $href = is_callable($href) ? $href($row) : $href;
            if ($href && ! $disabled) {
                $attributes['href'] = (string) $href;
                $html .= '<a'.Html::attributes($attributes).'><span aria-hidden="true">'.$body.'</span></a>';
            } else {
                unset($attributes['href']);
                $attributes['type'] = 'button';
                $attributes['disabled'] = $disabled;
                $html .= '<button'.Html::attributes($attributes).'><span aria-hidden="true">'.$body.'</span></button>';
            }
        }

        return $html.'</div>';
    }

    private function allowed(array $action, array|object $row): bool
    {
        $permission = $this->resolve($action['permission'] ?? true, $row);

        return (bool) $this->resolve($action['visible'] ?? true, $row) && is_bool($permission) && $permission;
    }

    private function rowHref(array|object $row): ?string
    {
        if (! $this->option('clickableRows', false)) {
            return null;
        }
        foreach ($this->actions as $action) {
            if ($this->allowed($action, $row) && ! $this->resolve($action['disabled'] ?? false, $row)) {
                $href = $this->resolve($action['href'] ?? null, $row);
                if ($href) {
                    return (string) $href;
                }
            }
        }

        return null;
    }

    private function resolve(mixed $value, array|object $row): mixed
    {
        return is_callable($value) ? $value($row) : $value;
    }

    private function cellValue(array|object $row, array $column): mixed
    {
        $value = isset($column['accessor']) ? (is_callable($column['accessor']) ? ($column['accessor'])($row) : $this->value($row, (string) $column['accessor'])) : $this->value($row, (string) $column['key']);

        return isset($column['formatter']) && is_callable($column['formatter']) ? ($column['formatter'])($value, $row) : $value;
    }

    private function value(array|object $row, string $path): mixed
    {
        $value = $row;
        foreach (explode('.', $path) as $key) {
            if (is_array($value)) {
                $value = $value[$key] ?? null;
            } elseif (is_object($value)) {
                $value = $value->{$key} ?? null;
            } else {
                return null;
            }
        }

return $value;
    }

    private function content(mixed $value): string
    {
        return $value instanceof Renderable ? $value->render() : Html::escape((string) $value);
    }

    private function renderMany(mixed $values): string
    {
        return is_array($values) ? implode('', array_map(fn ($value) => $this->content($value), $values)) : $this->content($values);
    }

    private function option(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    private function hasVisibleActions(): bool
    {
        foreach ($this->actions as $action) {
            if (($action['permission'] ?? true) !== false && ($action['visible'] ?? true) !== false) {
                return true;
            }
        }

return false;
    }
}
