<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;

final class Toaster extends Component
{
    private const POSITIONS = ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'];

    /**
     * @param array<int, array{title: string, message?: string|null, tone?: string, timeout?: int}> $toasts
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private readonly array $toasts = [],
        private readonly string $position = 'top-center',
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $position = in_array($this->position, self::POSITIONS, true) ? $this->position : 'top-center';
        $content = '';

        foreach ($this->toasts as $toast) {
            if (! isset($toast['title'])) {
                continue;
            }

            $content .= (new Toast(
                (string) $toast['title'],
                isset($toast['message']) ? (string) $toast['message'] : null,
                (string) ($toast['tone'] ?? 'info'),
                (int) ($toast['timeout'] ?? 3500),
            ))->render();
        }

        return '<div'.$this->attrs([
            'class' => "stl-toaster stl-toaster--{$position}",
            'data-stl-toaster' => true,
            'data-position' => $position,
            'aria-label' => 'Notifications',
        ]).'>'.$content.'</div>';
    }

    /**
     * Build the standard eBoard notifications from Laravel-style flash data.
     *
     * @param array<string, mixed> $flash
     * @param array<string, mixed> $attributes
     */
    public static function fromFlash(array $flash, string $position = 'top-center', array $attributes = []): self
    {
        $definitions = [
            'success' => ['Success', 'success'],
            'error' => ['Something went wrong', 'danger'],
            'warning' => ['Notice', 'warning'],
            'info' => ['Info', 'info'],
            'status' => ['Update', 'info'],
        ];
        $toasts = [];

        foreach ($definitions as $key => [$title, $tone]) {
            if (isset($flash[$key]) && $flash[$key] !== '') {
                $toasts[] = [
                    'title' => $title,
                    'message' => (string) $flash[$key],
                    'tone' => $tone,
                    'timeout' => 3500,
                ];
            }
        }

        return new self($toasts, $position, $attributes);
    }
}
