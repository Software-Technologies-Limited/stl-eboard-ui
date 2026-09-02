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

use Stl\EboardUi\Ui;

echo Ui::button('Save', attributes: ['name' => 'action', 'value' => 'save']);
echo Ui::input('email', 'Email address', 'email');
echo Ui::badge('Approved', 'success');
```

Copy or serve `resources/css/eboard-ui.css` and `resources/js/eboard-ui.js` from your public directory.

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

Add `/vendor/eboard-ui/eboard-ui.css` and `/vendor/eboard-ui/eboard-ui.js` to the layout, then call the same `Ui` API from Blade:

```blade
{!! \Stl\EboardUi\Ui::button('Create meeting') !!}
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
Avatar, Badge, Brand, Breadcrumbs, Button, Calendar, Callout, Card, Carousel,
Chart, Checkbox, Color Picker, Command, Composer, Context Menu, Date Picker,
Dropdown, Editor, Field, File Upload, Heading, Icon, Input, Kanban, Modal,
Navbar, OTP Input, Pagination, Pillbox, Popover, Profile, Progress, Radio,
Select, Separator, Skeleton, Slider, Switch, Table, Tabs, Text, Textarea,
Time Picker, Timeline, Toast, Toggle, and Tooltip. Header and Sidebar layout
primitives are included as well.

All custom HTML attributes pass through to the final element. Component classes
and public variables use the `stl-` prefix to avoid collisions.

The package targets PHP 8.1 and newer. Interactive examples live in the
separate `stl-library-demo` Laravel application rather than in this package.
