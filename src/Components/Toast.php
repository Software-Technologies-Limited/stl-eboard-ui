<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Support\Html;

final class Toast extends Component
{
    private const TONES = ['info', 'success', 'warning', 'danger'];

    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly string $title,
        private readonly ?string $message = null,
        private readonly string $tone = 'info',
        private readonly int $timeout = 3500,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $tone = in_array($this->tone, self::TONES, true) ? $this->tone : 'info';
        $timeout = max(0, $this->timeout);
        $role = $tone === 'danger' ? 'alert' : 'status';
        $message = $this->message !== null && $this->message !== ''
            ? '<p class="stl-toast__message">'.Html::escape($this->message).'</p>'
            : '';

        return '<div'.$this->attrs([
            'class' => "stl-toast stl-toast--{$tone}",
            'role' => $role,
            'aria-live' => $tone === 'danger' ? 'assertive' : 'polite',
            'aria-atomic' => 'true',
            'data-stl-toast' => true,
            'data-timeout' => $timeout,
        ]).'><div class="stl-toast__content"><p class="stl-toast__title">'.Html::escape($this->title).'</p>'.$message.'</div><button class="stl-toast__close" type="button" data-stl-toast-dismiss aria-label="Close notification">&times;</button></div>';
    }
}
