<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Icon extends Component
{
    /** @var array<string, string> */
    private const PATHS = [
        'book' => 'M5 4h14v16H5zM9 4v16',
        'calendar' => 'M7 3v3m10-3v3M4 9h16M5 5h14v15H5z',
        'check' => 'm5 12 4 4L19 6',
        'chat' => 'M4 5h16v12H8l-4 4z',
        'folder' => 'M4 5h6l2 2h8v12H4z',
        'grid' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'home' => 'M3 12 12 3l9 9v8h-6v-6H9v6H3z',
        'scales' => 'M12 3v18M6 6h12M8 6l-4 7h8L8 6zm8 0-4 7h8l-4-7z',
        'settings' => 'M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM19 12l2-1-2-3-2 .5-1-2.5h-4L10 5.5 8 8l-2-.5-2 3 2 1v3l-2 1 2 3 2-.5 2 2.5h4l2-2.5 2 .5 2-3-2-1z',
        'shield' => 'M12 3 5 6v5c0 4.6 2.8 8 7 10 4.2-2 7-5.4 7-10V6z',
        'star' => 'M12 3l2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9z',
        'trend' => 'M4 18 9 13l3 3 8-10M15 6h5v5',
        'user' => 'M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21a8 8 0 0 1 16 0',
    ];

    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $name,
        private readonly ?string $label = null,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $path = self::PATHS[$this->name] ?? self::PATHS['grid'];
        $attributes = $this->label === null
            ? ['class' => 'stl-icon', 'aria-hidden' => 'true']
            : ['class' => 'stl-icon', 'role' => 'img', 'aria-label' => $this->label];

        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"'
            .$this->attrs($attributes).'><path d="'.Html::escape($path).'"/></svg>';
    }
}
