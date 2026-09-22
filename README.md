# STL eBoard UI

A small, framework-agnostic PHP component library for STL eBoard products. Components return ordinary HTML, use no templating engine, escape dynamic content by default, and share a namespaced CSS/JavaScript layer.

## Install

During local development, add this repository to an application's `composer.json`:

```json
{
  "repositories": [{ "type": "path", "url": "../stl-eboard-ui" }],
  "require": { "stl/eboard-ui": "@dev" }
}
```

Then run `composer update stl/eboard-ui`.

## Raw PHP

```php
require 'vendor/autoload.php';

echo Ui::button('Save', attributes: ['name' => 'action', 'value' => 'save']);
echo Ui::input('email', 'Email address', 'email');
echo Ui::badge('Approved', 'success');
echo Ui::toaster([
    ['title' => 'Success', 'message' => 'The meeting was saved.', 'tone' => 'success'],
]);
echo Ui::filterForm(
    filters: [
        'all' => ['label' => 'All', 'href' => '/items'],
        'open' => ['label' => 'Open', 'href' => '/items?status=open'],
    ],
    active: 'all',
    query: '',
    placeholder: 'Search items',
);
echo Ui::statCard('1,240', 'Active members', '+8.2% this month');
echo Ui::emptyState('No meetings yet', 'Create a meeting to get started.', Ui::button('Create meeting'));
echo Ui::icon('calendar');
echo Ui::multiSelect(
    'committee_members',
    ['ada' => 'Ada Lovelace', 'grace' => 'Grace Hopper'],
    selected: ['ada'],
    label: 'Committee members',
);

// Mark a form for progressive browser validation. Server/Laravel validation still runs.
echo '<form method="post" data-stl-validate data-stl-validate-toast="true">';
echo Ui::input('email', 'Email address', 'email', $errors->first('email'), ['required' => true, 'value' => old('email')]);
echo Ui::select('status', ['draft' => 'Draft', 'published' => 'Published'], 'Status', old('status'), $errors->first('status'), ['required' => true]);
echo Ui::textarea('notes', 'Notes', 5, old('notes'), $errors->first('notes'), ['required' => true]);
echo Ui::multiSelect('owners', ['ada' => 'Ada Lovelace'], old('owners', []), 'Owners', attributes: ['required' => true], error: $errors->first('owners'));
echo Ui::button('Save');
echo '</form>';

// A Laravel error bag can also be surfaced at the top of the form.
echo Ui::formErrorSummary($errors->toArray());
echo Ui::iconAction(Ui::icon('edit'), 'Edit member', tooltip: 'Edit this member');
echo Ui::circularProgress(68, size: 'lg'); // Dynamic orange at 68%
echo Ui::richTable(
    [
        ['key' => 'member.name', 'label' => 'Member', 'sortable' => true],
        ['key' => 'status', 'label' => 'Status', 'formatter' => fn ($value) => Ui::badge($value, 'success')],
    ],
    [['id' => 1, 'member' => ['name' => 'Ada Lovelace'], 'status' => 'Active']],
    actions: [['key' => 'edit', 'label' => 'Edit member', 'icon' => Ui::icon('edit'), 'tooltip' => 'Edit this member']],
    options: ['title' => 'Members', 'selectable' => true, 'toolbar' => true],
);
```

Copy or serve `resources/css/eboard-ui.css` and `resources/js/eboard-ui.js` from your public directory.

## Enhanced form and table components

Multiselect accepts either keyed labels or value/label records. Values submit as
`owners[]`; checkboxes remain usable without JavaScript. `disabled` applies to
both the trigger and submitted controls.

```php
echo Ui::multiSelect('owners', [
    ['value' => 'hr', 'label' => 'Human Resources'],
    ['value' => 'finance', 'label' => 'Finance'],
], selected: ['hr'], label: 'Owners', placeholder: 'Choose owners');

echo Ui::circularProgress(3, max: 5, size: 'sm', showRawValue: true);
echo Ui::circularProgress(80, variant: 'success', dynamicColor: false);
echo Ui::circularProgress(60, dynamicColorStops: [
    ['max' => 50, 'stroke' => '#ef4444', 'textColor' => '#b91c1c'],
    ['max' => 100, 'stroke' => '#22c55e', 'textClass' => 'text-green-700'],
]);
echo Ui::iconAction(Ui::icon('edit'), 'Edit risk', tooltip: 'Update this risk');
```

Circular progress clamps percentages and ARIA values. Default dynamic colors
are red through 39%, orange through 79%, and green through 100%. Sizes are
`xs`, `sm`, `md`, `lg`, and `xl`; line caps are `round`, `butt`, or `square`.
Custom stops accept `max`, `stroke`, and optional `textColor` or `textClass`.

Structured rows use the same renderer through `richTable` or `advancedTable`:

```php
$columns = [
    ['key' => 'name', 'label' => 'Risk', 'accessor' => 'risk.name', 'sortable' => true],
    ['key' => 'score', 'label' => 'Score', 'accessor' => fn ($row) => $row['score'],
        'formatter' => fn ($value, $row) => Ui::circularProgress($value, max: 25)],
    ['key' => 'internal', 'label' => 'Internal note', 'visible' => false],
];
$rows = [['id' => 1, 'risk' => ['name' => 'Service outage'], 'score' => 12]];
$actions = [[
    'key' => 'edit', 'label' => 'Edit risk', 'icon' => Ui::icon('edit'),
    'tooltip' => 'Edit this risk', 'href' => fn ($row) => '/risks/'.$row['id'].'/edit',
    'permission' => $canEdit, // Boolean or callable receiving the row; never a permission name.
    'disabled' => fn ($row) => false,
    'attributes' => ['class' => 'custom-action'],
]];
$options = ['title' => 'Risks', 'description' => 'Current register',
    'density' => 'normal', 'variant' => 'bordered', 'toolbar' => true,
    'selectable' => true, 'selectionName' => 'risk_ids', 'selected' => [1],
    'bulkActions' => [Ui::button('Export selected')], 'clickableRows' => true];
echo Ui::richTable($columns, $rows, actions: $actions, options: $options);
echo Ui::advancedTable($columns, $rows, $actions, $options);

// The original third argument remains HTML attributes for legacy rich tables.
echo Ui::richTable(['Risk'], new \Stl\EboardUi\Components\HtmlFragment(
    '<tr><td>Service outage</td></tr>'
), ['class' => 'custom-table']);
```

`clickableRows` navigates to the first visible, permitted, enabled action URL.
Interactive controls inside a row keep their own behavior. Permission checks
are supplied by the application and must also be enforced by the server.
Strings are escaped, including formatter results; only `Renderable` values
are trusted HTML. Column visibility, sorting, selection, and action tooltips
are enhanced by the existing JavaScript asset. Additional options include
`striped`, `hoverable`, `toolbarActions`, `addAction`, `refreshHref`, `filter`,
`emptyTitle`, `emptySubtitle`, `emptyAction`, `loading`, and `skeletonRows`.

## Theming

STL eBoard UI follows Flux's public base/accent model with `stl-` namespacing.
The base scale controls text, surfaces, and borders. The three accent roles
control primary actions and interactive content:

```css
:root {
    --stl-accent: #ef4444;
    --stl-accent-content: #dc2626;
    --stl-accent-foreground: #ffffff;
}

.dark {
    --stl-accent: #ef4444;
    --stl-accent-content: #f87171;
    --stl-accent-foreground: #ffffff;
}
```

The package exposes `--stl-base-50` through `--stl-base-950`. Override the
whole scale to replace the default zinc family. See
[`docs/THEMING.md`](docs/THEMING.md) for the stable public contract.

## Laravel

Package discovery registers the service provider. Publish the assets:

```bash
php artisan vendor:publish --tag=eboard-ui-assets
```

Add `/vendor/eboard-ui/eboard-ui.css` and `/vendor/eboard-ui/eboard-ui.js` to the layout. The package registers the global `Ui` alias, so the same API is available in every Blade view without an import:

```blade
{!! Ui::button('Create meeting') !!}
```

## Yii 2

Render components directly or use the optional widget bridge:

```php
use Stl\EboardUi\Bridge\Yii2\Widget;
use Stl\EboardUi\Ui;

echo Widget::widget(['component' => Ui::badge('Ready', 'success')]);
```

## Included components

The package covers the complete STL component catalog: Accordion, Autocomplete,
Avatar, Badge, Brand, Breadcrumbs, Button, Calendar, Callout, Card, Carousel, Circular Progress,
Chart, Checkbox, Color Picker, Command, Composer, Context Menu, Date Picker,
Dropdown, Editor, Empty State, Field, File Upload, Heading, Icon, Input, Kanban, Modal,
Navbar, OTP Input, Pagination, Pillbox, Popover, Profile, Progress, Radio,
Select, Separator, Skeleton, Slider, Stat Card, Switch, Table, Advanced Table, Tabs, Text, Textarea,
Time Picker, Timeline, Toast, Toggle, and Tooltip. Header and Sidebar layout
primitives are included as well, including Workspace Shell and Workspace Sidebar.

Table headers and scalar cells are escaped by default. To render a component in
a cell, pass any package component that implements `Renderable`:

```php
echo Ui::table(
    ['Member', 'Status'],
    [['Ada Lovelace', Ui::badge('Active', 'success')]],
);
```

All custom HTML attributes pass through to the final element. Component classes
and public variables use the `stl-` prefix to avoid collisions.

The package targets PHP 8.1 and newer. Interactive examples live in the
separate `stl-library-demo` Laravel application rather than in this package.

## Toasts

Render server-side notifications inside a fixed toaster host. Supported tones
are `info`, `success`, `warning`, and `danger`. The timeout is expressed in
milliseconds; use `0` to keep a toast visible until it is dismissed.

```php
echo Ui::toaster([
    [
        'title' => 'Success',
        'message' => 'Your changes were saved.',
        'tone' => 'success',
        'timeout' => 3500,
    ],
]);
```

Laravel flash messages can be mapped directly without coupling the package to
Laravel's session implementation:

```blade
{!! Ui::flashToaster(session()->all()) !!}
```

The JavaScript asset can show a toast from anywhere and creates its host when
needed:

```js
window.StlEboardUi.toast({
  title: 'Success',
  message: 'Your changes were saved.',
  tone: 'success',
  timeout: 3500,
  position: 'top-center',
});
```

You can also dispatch an `stl:toast` event with the same detail object.
Positions include `top-left`, `top-center`, `top-right`, `bottom-left`,
`bottom-center`, and `bottom-right`.
