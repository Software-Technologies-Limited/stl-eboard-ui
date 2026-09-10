<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class RichTable extends Component
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly array $headers,
        private readonly Renderable|string $body,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function render(): string
    {
        $headers = implode('', array_map(
            static fn (string $header): string => '<th scope="col">'.Html::escape($header).'</th>',
            $this->headers,
        ));
        $body = $this->body instanceof Renderable ? $this->body->render() : Html::escape($this->body);

        return '<div'.$this->attrs(['class' => 'stl-rich-table']).'><table><thead><tr>'
            .$headers.'</tr></thead><tbody>'.$body.'</tbody></table></div>';
    }
}
