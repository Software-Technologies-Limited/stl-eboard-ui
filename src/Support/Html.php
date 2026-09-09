<?php

declare(strict_types=1);

namespace Stl\EboardUi\Support;

final class Html
{
    private const BOOLEAN_ATTRIBUTES = [
        'autofocus', 'checked', 'disabled', 'hidden', 'multiple', 'open',
        'readonly', 'required', 'selected',
    ];

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** @param array<string, mixed> $attributes */
    public static function attributes(array $attributes): string
    {
        $rendered = [];

        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            $name = self::escape($name);
            if ($value === true && in_array($name, self::BOOLEAN_ATTRIBUTES, true)) {
                $rendered[] = $name;

                continue;
            }

            if (is_array($value)) {
                $value = implode(' ', array_filter($value));
            }

            $rendered[] = sprintf('%s="%s"', $name, self::escape($value));
        }

        return $rendered === [] ? '' : ' '.implode(' ', $rendered);
    }

    public static function classes(string ...$classes): string
    {
        return implode(' ', array_values(array_unique(array_filter(
            preg_split('/\s+/', implode(' ', $classes)) ?: []
        ))));
    }
}
