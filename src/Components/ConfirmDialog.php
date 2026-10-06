<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class ConfirmDialog extends Component
{
    /**
     * @param  array<string, mixed>  $confirmAttributes
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly Renderable|string $message,
        private readonly string $confirmLabel = 'Confirm',
        private readonly string $cancelLabel = 'Cancel',
        private readonly string $variant = 'danger',
        private readonly array $confirmAttributes = [],
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $variant = in_array($this->variant, ['primary', 'secondary', 'danger'], true) ? $this->variant : 'primary';
        $message = $this->message instanceof Renderable ? $this->message->render() : Html::escape($this->message);
        $confirmAttributes = Html::mergeAttributes([
            'class' => "stl-button stl-button--{$variant} stl-button--md",
            'type' => 'button',
        ], $this->confirmAttributes);

        return '<dialog'.$this->attrs([
            'class' => 'stl-modal stl-confirm-dialog',
            'id' => $this->id,
            'aria-labelledby' => $this->id.'-title',
            'aria-describedby' => $this->id.'-message',
        ]).'>'
            .'<div'.$this->partAttrs('header', ['class' => 'stl-modal__header']).'>'
            .'<h2'.$this->partAttrs('title', ['id' => $this->id.'-title']).'>'.Html::escape($this->title).'</h2>'
            .'<button'.$this->partAttrs('close', ['class' => 'stl-modal__close', 'type' => 'button', 'data-stl-close' => true, 'aria-label' => 'Close']).'>&times;</button></div>'
            .'<div'.$this->partAttrs('body', ['class' => 'stl-modal__body']).'>'
            .'<p'.$this->partAttrs('message', ['class' => 'stl-confirm-dialog__message', 'id' => $this->id.'-message']).'>'.$message.'</p></div>'
            .'<div'.$this->partAttrs('footer', ['class' => 'stl-confirm-dialog__footer']).'>'
            .'<button'.$this->partAttrs('cancel', ['class' => 'stl-button stl-button--secondary stl-button--md', 'type' => 'button', 'data-stl-close' => true]).'>'.Html::escape($this->cancelLabel).'</button>'
            .'<button'.Html::attributes(Html::mergeAttributes($confirmAttributes, $this->parts['confirm'] ?? [])).'>'.Html::escape($this->confirmLabel).'</button>'
            .'</div></dialog>';
    }
}
