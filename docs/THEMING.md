# Theming contract

This contract intentionally mirrors Flux's public theming concepts while using
an `stl-` namespace and ordinary CSS so it works without Tailwind.

## Base colors

The base family supplies neutral text, background, surface, and border colors.
Zinc is the default. Every shade is public and replaceable:

```css
:root {
    --stl-base-50: #fafafa;
    --stl-base-100: #f4f4f5;
    --stl-base-200: #e4e4e7;
    --stl-base-300: #d4d4d8;
    --stl-base-400: #a1a1aa;
    --stl-base-500: #71717a;
    --stl-base-600: #52525b;
    --stl-base-700: #3f3f46;
    --stl-base-800: #27272a;
    --stl-base-900: #18181b;
    --stl-base-950: #09090b;
}
```

Applications may map this scale to slate, gray, stone, or a custom neutral
palette without changing component source.

## Accent colors

Only three variables are required to rebrand interactive components:

- `--stl-accent`: primary button backgrounds and active indicators.
- `--stl-accent-content`: readable accent-colored text and hover treatment.
- `--stl-accent-foreground`: text/icons placed on the accent background.

Example red brand:

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

Load application overrides after `eboard-ui.css`. Never edit published vendor
assets because republishing replaces them.

## Dark mode

Add `class="dark"` to an ancestor—normally `<html>`—to activate the package's
dark token aliases. Applications can set or remove this class according to
their own system/user preference strategy.

## Stability policy

The base scale and three accent variables are public API. They will not be
renamed or removed in a major release without a documented migration path.
Internal aliases such as `--stl-color-primary` exist for package compatibility;
application code should use the base/accent variables above.
