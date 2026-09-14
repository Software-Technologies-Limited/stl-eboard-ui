<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use InvalidArgumentException;
use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class CircularProgress extends Component
{
    private const SIZES = ['xs' => 42, 'sm' => 48, 'md' => 64, 'lg' => 80, 'xl' => 96];
    private const STROKE_WIDTHS = ['xs' => 2.5, 'sm' => 4.0, 'md' => 5.0, 'lg' => 6.0, 'xl' => 7.0];
    private const VARIANT_COLORS = [
        'default' => 'var(--stl-color-primary)',
        'success' => '#22c55e',
        'warning' => '#f97316',
        'danger' => '#ef4444',
    ];

    /**
     * @param  array<int, array{max: int|float|string, stroke: string, textColor?: string, textClass?: string}>  $dynamicColorStops
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly string|int|float $value = 0,
        private readonly string|int|float $max = 100,
        private readonly string $size = 'md',
        private readonly string $variant = 'default',
        private readonly bool $showValue = true,
        private readonly bool $showRawValue = false,
        private readonly bool $dynamicColor = true,
        private readonly array $dynamicColorStops = [],
        private readonly string $dynamicFallbackStroke = '#22c55e',
        private readonly string $dynamicFallbackTextClass = 'text-green-700',
        private readonly string $strokeLinecap = 'round',
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        if (!isset(self::SIZES[$this->size])) {
            throw new InvalidArgumentException("Unknown circular progress size [{$this->size}].");
        }
        if (!isset(self::VARIANT_COLORS[$this->variant])) {
            throw new InvalidArgumentException("Unknown circular progress variant [{$this->variant}].");
        }
        if (!in_array($this->strokeLinecap, ['butt', 'round', 'square'], true)) {
            throw new InvalidArgumentException("Unknown circular progress line cap [{$this->strokeLinecap}].");
        }

        $value = $this->number($this->value);
        $max = $this->number($this->max, 100.0);
        $percentage = $max > 0 ? min(max(($value / $max) * 100, 0), 100) : 0;
        $size = self::SIZES[$this->size];
        $strokeWidth = self::STROKE_WIDTHS[$this->size];
        $center = $size / 2;
        $radius = $center - ($strokeWidth / 2);
        $circumference = 2 * M_PI * $radius;
        $offset = $circumference - (($percentage / 100) * $circumference);
        [$stroke, $textColor] = $this->colorsFor($percentage);
        $displayValue = $this->showRawValue ? number_format($value, 2, '.', '') : (string) round($percentage).'%';

        $style = '--stl-circular-progress-stroke:'.$stroke.';--stl-circular-progress-text:'.$textColor.';';
        $content = '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 '.$size.' '.$size.'" aria-hidden="true">'
            .'<circle cx="'.$center.'" cy="'.$center.'" r="'.$radius.'" stroke="var(--stl-color-border)" stroke-width="'.$strokeWidth.'" fill="none"/>'
            .'<circle class="stl-circular-progress__value" cx="'.$center.'" cy="'.$center.'" r="'.$radius.'" stroke="var(--stl-circular-progress-stroke)" stroke-width="'.$strokeWidth.'" fill="none" stroke-dasharray="'.$circumference.'" stroke-dashoffset="'.$offset.'" stroke-linecap="'.$this->strokeLinecap.'"/>'
            .'</svg>';

        if ($this->showValue) {
            $content .= '<span class="stl-circular-progress__label">'.Html::escape($displayValue).'</span>';
        }

        return '<span'.$this->attrs([
            'class' => 'stl-circular-progress stl-circular-progress--'.$this->size,
            'style' => $style,
            'role' => 'progressbar',
            'aria-valuemin' => '0',
            'aria-valuemax' => (string) $max,
            'aria-valuenow' => (string) $value,
            'aria-valuetext' => $displayValue,
        ]).'>'.$content.'</span>';
    }

    /** @return array{0: string, 1: string} */
    private function colorsFor(float $percentage): array
    {
        if (!$this->dynamicColor) {
            return [self::VARIANT_COLORS[$this->variant], self::VARIANT_COLORS[$this->variant]];
        }

        $stops = $this->dynamicColorStops === [] ? [
            ['max' => 39, 'stroke' => '#ef4444', 'textColor' => '#b91c1c'],
            ['max' => 79, 'stroke' => '#f97316', 'textColor' => '#c2410c'],
            ['max' => 100, 'stroke' => '#22c55e', 'textColor' => '#15803d'],
        ] : $this->dynamicColorStops;
        usort($stops, static fn (array $a, array $b): int => (float) ($a['max'] ?? 0) <=> (float) ($b['max'] ?? 0));

        foreach ($stops as $stop) {
            if ($percentage <= (float) ($stop['max'] ?? 0)) {
                return [(string) ($stop['stroke'] ?? $this->dynamicFallbackStroke), $this->stopTextColor($stop)];
            }
        }

        return [$this->dynamicFallbackStroke, $this->resolveTextColor($this->dynamicFallbackTextClass)];
    }

    /** @param array<string, mixed> $stop */
    private function stopTextColor(array $stop): string
    {
        if (isset($stop['textColor'])) {
            return (string) $stop['textColor'];
        }

        return $this->resolveTextColor((string) ($stop['textClass'] ?? $this->dynamicFallbackTextClass));
    }

    private function resolveTextColor(string $colorOrClass): string
    {
        return match ($colorOrClass) {
            'text-red-700' => '#b91c1c',
            'text-orange-700' => '#c2410c',
            'text-green-700' => '#15803d',
            default => $colorOrClass,
        };
    }

    private function number(string|int|float $value, float $fallback = 0.0): float
    {
        return is_numeric($value) ? (float) $value : $fallback;
    }
}
