<?php

declare(strict_types=1);

namespace Stl\EboardUi\Components;

use Stl\EboardUi\Component;
use Stl\EboardUi\Contracts\Renderable;
use Stl\EboardUi\Support\Html;

final class DataTable extends Component
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, Renderable|string|int|float|null>>  $rows
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private readonly array $headers,
        private readonly array $rows,
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

        $rows = '';
        foreach ($this->rows as $row) {
            $cells = '';
            foreach ($row as $cell) {
                $content = $cell instanceof Renderable ? $cell->render() : Html::escape((string) $cell);
                $cells .= '<td>'.$content.'</td>';
            }

            $rows .= '<tr>'.$cells.'</tr>';
        }

        return '<div'.$this->attrs(['class' => 'stl-data-table']).'><table><thead><tr>'.$headers.'</tr></thead><tbody>'.$rows.'</tbody></table></div>';
    }
}
