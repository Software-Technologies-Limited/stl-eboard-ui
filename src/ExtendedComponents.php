<?php

declare(strict_types=1);

namespace Stl\EboardUi;

use BadMethodCallException;
use Stl\EboardUi\Components\HtmlFragment;
use Stl\EboardUi\Support\Html;

final class ExtendedComponents
{
    /** @param array<int, mixed> $arguments */
    public static function make(string $name, array $arguments): HtmlFragment
    {
        $a = fn (int $index, mixed $default = null): mixed => $arguments[$index] ?? $default;
        $e = fn (mixed $value): string => Html::escape($value);
        $attrs = fn (mixed $value): string => Html::attributes(is_array($value) ? $value : []);

        $html = match ($name) {
            'autocomplete' => self::autocomplete((string) $a(0), (array) $a(1, []), (string) $a(2, 'Search'), (array) $a(3, [])),
            'avatar' => self::avatar((string) $a(0), $a(1), (string) $a(2, 'md'), (array) $a(3, [])),
            'brand' => '<a class="stl-brand" href="'.$e($a(2, '#')).'"><span class="stl-brand__mark">'.$e($a(0, 'S')).'</span><span><strong>'.$e($a(1, 'STL eBoard')).'</strong><small>'.$e($a(3, 'Workspace')).'</small></span></a>',
            'breadcrumbs' => self::breadcrumbs((array) $a(0, []), (array) $a(1, [])),
            'calendar' => self::calendar((string) $a(0, date('Y-m')), (array) $a(1, [])),
            'callout' => '<aside class="stl-callout stl-callout--'.$e($a(2, 'info')).'" role="note"><strong>'.$e($a(0)).'</strong><p>'.$e($a(1)).'</p></aside>',
            'carousel' => self::carousel((array) $a(0, []), (array) $a(1, [])),
            'chart' => self::chart((array) $a(0, []), (string) $a(1, 'Chart'), (array) $a(2, [])),
            'colorPicker' => '<label class="stl-color-picker"><span>'.$e($a(1, 'Color')).'</span><input type="color" name="'.$e($a(0, 'color')).'" value="'.$e($a(2, '#2563eb')).'"'.$attrs($a(3, [])).'></label>',
            'command' => self::command((array) $a(0, []), (string) $a(1, 'Search commands…'), (array) $a(2, [])),
            'composer' => '<label class="stl-composer"><span class="stl-field__label">'.$e($a(1, 'Message')).'</span><textarea name="'.$e($a(0, 'message')).'" rows="3" placeholder="'.$e($a(2, 'Write a message…')).'"></textarea><span class="stl-composer__bar"><button type="button" aria-label="Attach file">＋</button><button class="stl-button stl-button--primary stl-button--sm" type="submit">Send</button></span></label>',
            'context', 'contextMenu' => self::menu((array) $a(1, []), 'stl-context-menu', '<button class="stl-button stl-button--secondary stl-button--md" type="button" data-stl-context-trigger>'.$e($a(0, 'Actions')).'</button>'),
            'datePicker' => '<label class="stl-field"><span class="stl-field__label">'.$e($a(1, 'Date')).'</span><input class="stl-input" type="date" name="'.$e($a(0, 'date')).'" value="'.$e($a(2, '')).'"'.$attrs($a(3, [])).'></label>',
            'dropdown' => self::menu((array) $a(1, []), 'stl-dropdown', '<button class="stl-button stl-button--secondary stl-button--md" type="button" data-stl-menu-trigger aria-expanded="false">'.$e($a(0, 'Options')).' ▾</button>'),
            'editor' => '<div class="stl-editor"><div class="stl-editor__toolbar" role="toolbar" aria-label="Text formatting"><button type="button" data-stl-format="bold"><b>B</b></button><button type="button" data-stl-format="italic"><i>I</i></button><button type="button" data-stl-format="insertUnorderedList">• List</button></div><div class="stl-editor__content" contenteditable="true" role="textbox" aria-label="'.$e($a(0, 'Rich text editor')).'">'.$e($a(1, 'Start writing…')).'</div></div>',
            'field' => '<label class="stl-field"><span class="stl-field__label">'.$e($a(0)).'</span>'.$a(1, '').($a(2) ? '<small class="stl-field__description">'.$e($a(2)).'</small>' : '').($a(3) ? '<small class="stl-field__error">'.$e($a(3)).'</small>' : '').'</label>',
            'fileUpload' => '<label class="stl-file-upload"><span class="stl-file-upload__icon">⇧</span><strong>'.$e($a(1, 'Drop files here or click to browse')).'</strong><small>'.$e($a(2, 'PDF, PNG or JPG up to 10 MB')).'</small><input type="file" name="'.$e($a(0, 'files')).'"'.$attrs($a(3, [])).'></label>',
            'heading' => '<h'.(int) $a(1, 2).' class="stl-heading"'.$attrs($a(2, [])).'>'.$e($a(0)).'</h'.(int) $a(1, 2).'>',
            'icon' => '<span class="stl-icon" role="img" aria-label="'.$e($a(1, $a(0))).'">'.self::iconGlyph((string) $a(0)).'</span>',
            'kanban' => self::kanban((array) $a(0, []), (array) $a(1, [])),
            'navbar' => self::nav((array) $a(0, []), 'stl-navbar', (array) $a(1, [])),
            'otpInput' => self::otp((string) $a(0, 'code'), (int) $a(1, 6), (array) $a(2, [])),
            'pagination' => self::pagination((int) $a(0, 1), (int) $a(1, 5), (string) $a(2, '?page=')),
            'pillbox' => self::pillbox((array) $a(0, []), (string) $a(1, 'tags'), (array) $a(2, [])),
            'popover' => '<span class="stl-popover"><button class="stl-button stl-button--secondary stl-button--md" type="button" data-stl-popover-trigger aria-expanded="false">'.$e($a(0, 'More info')).'</button><span class="stl-popover__panel" role="dialog" hidden>'.$e($a(1)).'</span></span>',
            'profile' => '<div class="stl-profile">'.self::avatar((string) $a(0), $a(2), 'md', []).'<span><strong>'.$e($a(0)).'</strong><small>'.$e($a(1)).'</small></span></div>',
            'progress' => '<label class="stl-progress"><span><strong>'.$e($a(1, 'Progress')).'</strong><small>'.$e($a(0, 0)).'%</small></span><progress max="100" value="'.$e($a(0, 0)).'">'.$e($a(0, 0)).'%</progress></label>',
            'radio' => '<label class="stl-radio"><input type="radio" name="'.$e($a(0)).'" value="'.$e($a(1)).'"'.$attrs($a(3, [])).'><span>'.$e($a(2)).'</span></label>',
            'select' => self::select((string) $a(0), (array) $a(1, []), (string) $a(2, 'Select'), (array) $a(3, [])),
            'separator' => '<div class="stl-separator" role="separator">'.($a(0) ? '<span>'.$e($a(0)).'</span>' : '').'</div>',
            'sidebar' => self::nav((array) $a(0, []), 'stl-sidebar-layout', (array) $a(1, [])),
            'skeleton' => '<span class="stl-skeleton" aria-hidden="true" style="width:'.$e($a(0, '100%')).';height:'.$e($a(1, '1rem')).'"></span>',
            'slider' => '<label class="stl-slider"><span>'.$e($a(1, 'Value')).'</span><input type="range" name="'.$e($a(0, 'range')).'" min="'.$e($a(2, 0)).'" max="'.$e($a(3, 100)).'" value="'.$e($a(4, 50)).'"><output>'.$e($a(4, 50)).'</output></label>',
            'switch' => '<label class="stl-switch"><input type="checkbox" name="'.$e($a(0)).'"'.$attrs($a(2, [])).'><span class="stl-switch__track"></span><span>'.$e($a(1)).'</span></label>',
            'table' => self::table((array) $a(0, []), (array) $a(1, []), (array) $a(2, [])),
            'tabs' => self::tabs((array) $a(0, []), (string) $a(1, ''), (array) $a(2, [])),
            'text' => '<p class="stl-text"'.$attrs($a(1, [])).'>'.$e($a(0)).'</p>',
            'textarea' => '<label class="stl-field"><span class="stl-field__label">'.$e($a(1, 'Message')).'</span><textarea class="stl-textarea" name="'.$e($a(0, 'message')).'" rows="'.$e($a(2, 4)).'"'.$attrs($a(3, [])).'></textarea></label>',
            'timePicker' => '<label class="stl-field"><span class="stl-field__label">'.$e($a(1, 'Time')).'</span><input class="stl-input" type="time" name="'.$e($a(0, 'time')).'" value="'.$e($a(2, '')).'"'.$attrs($a(3, [])).'></label>',
            'timeline' => self::timeline((array) $a(0, []), (array) $a(1, [])),
            'toast' => '<div class="stl-toast stl-toast--'.$e($a(2, 'info')).'" role="status"><span><strong>'.$e($a(0)).'</strong><small>'.$e($a(1)).'</small></span><button type="button" data-stl-dismiss aria-label="Dismiss">×</button></div>',
            'toggle' => '<button class="stl-toggle" type="button" aria-pressed="'.($a(1, false) ? 'true' : 'false').'" data-stl-toggle'.$attrs($a(2, [])).'>'.$e($a(0)).'</button>',
            'tooltip' => '<span class="stl-tooltip" data-tooltip="'.$e($a(1)).'">'.$a(0).'</span>',
            'header' => '<header class="stl-header"'.$attrs($a(2, [])).'><strong>'.$e($a(0)).'</strong><nav aria-label="Header navigation">'.$a(1, '').'</nav></header>',
            default => throw new BadMethodCallException("Unknown STL eBoard UI component [{$name}]."),
        };

        return new HtmlFragment($html);
    }

    private static function avatar(string $name, mixed $src, string $size, array $attributes): string
    {
        $initials = implode('', array_map(fn ($part) => mb_substr($part, 0, 1), array_slice(preg_split('/\s+/', trim($name)) ?: [], 0, 2)));
        $content = $src ? '<img src="'.Html::escape($src).'" alt="'.Html::escape($name).'">' : Html::escape(strtoupper($initials));
        return '<span class="stl-avatar stl-avatar--'.Html::escape($size).'"'.Html::attributes($attributes).'>'.$content.'</span>';
    }

    private static function autocomplete(string $name, array $options, string $label, array $attributes): string
    {
        $id = 'stl-list-'.substr(md5($name), 0, 8); $out = ''; foreach ($options as $option) { $out .= '<option value="'.Html::escape($option).'"></option>'; }
        return '<label class="stl-field"><span class="stl-field__label">'.Html::escape($label).'</span><input class="stl-input" name="'.Html::escape($name).'" list="'.$id.'"'.Html::attributes($attributes).'><datalist id="'.$id.'">'.$out.'</datalist></label>';
    }

    private static function breadcrumbs(array $items, array $attributes): string
    {
        $last = array_key_last($items); $out = '';
        foreach ($items as $i => $item) { $label = Html::escape(is_array($item) ? $item['label'] : $item); $href = is_array($item) ? ($item['href'] ?? null) : null; $out .= '<li>'.($href && $i !== $last ? '<a href="'.Html::escape($href).'">'.$label.'</a>' : '<span aria-current="page">'.$label.'</span>').'</li>'; }
        return '<nav class="stl-breadcrumbs" aria-label="Breadcrumb"'.Html::attributes($attributes).'><ol>'.$out.'</ol></nav>';
    }

    private static function calendar(string $month, array $attributes): string
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m', $month) ?: new \DateTimeImmutable('first day of this month'); $days = (int) $date->format('t'); $offset = (int) $date->format('N'); $cells = str_repeat('<span></span>', $offset - 1);
        for ($day = 1; $day <= $days; $day++) { $cells .= '<button type="button"'.($day === (int) date('j') && $date->format('Y-m') === date('Y-m') ? ' aria-current="date"' : '').'>'.$day.'</button>'; }
        return '<section class="stl-calendar"'.Html::attributes($attributes).'><header><button type="button" aria-label="Previous month">‹</button><strong>'.$date->format('F Y').'</strong><button type="button" aria-label="Next month">›</button></header><div class="stl-calendar__week"><b>Mon</b><b>Tue</b><b>Wed</b><b>Thu</b><b>Fri</b><b>Sat</b><b>Sun</b></div><div class="stl-calendar__days">'.$cells.'</div></section>';
    }

    private static function carousel(array $slides, array $attributes): string
    {
        $out = ''; foreach ($slides as $i => $slide) { $out .= '<div class="stl-carousel__slide"'.($i ? ' hidden' : '').' data-stl-slide><strong>'.Html::escape(is_array($slide) ? $slide['title'] : $slide).'</strong>'.(is_array($slide) ? '<p>'.Html::escape($slide['text'] ?? '').'</p>' : '').'</div>'; }
        return '<section class="stl-carousel" data-stl-carousel'.Html::attributes($attributes).'><div>'.$out.'</div><footer><button type="button" data-stl-prev aria-label="Previous slide">←</button><span>Browse highlights</span><button type="button" data-stl-next aria-label="Next slide">→</button></footer></section>';
    }

    private static function chart(array $values, string $label, array $attributes): string
    {
        $max = max(array_values($values) ?: [1]); $bars = ''; foreach ($values as $name => $value) { $bars .= '<span style="--value:'.((float) $value / $max * 100).'%"><i></i><small>'.Html::escape($name).'</small><b>'.Html::escape($value).'</b></span>'; }
        return '<figure class="stl-chart" aria-label="'.Html::escape($label).'"'.Html::attributes($attributes).'><figcaption>'.Html::escape($label).'</figcaption><div class="stl-chart__bars">'.$bars.'</div></figure>';
    }

    private static function command(array $items, string $placeholder, array $attributes): string
    {
        $out = ''; foreach ($items as $item) { $out .= '<button type="button" data-stl-command-item>'.Html::escape($item).'</button>'; }
        return '<div class="stl-command"'.Html::attributes($attributes).'><input class="stl-input" type="search" placeholder="'.Html::escape($placeholder).'" data-stl-command-input aria-label="'.$placeholder.'"><div>'.$out.'<p data-stl-empty hidden>No results found.</p></div></div>';
    }

    private static function menu(array $items, string $class, string $trigger): string
    {
        $out = ''; foreach ($items as $label => $href) { $out .= '<a href="'.Html::escape(is_string($label) ? $href : '#').'">'.Html::escape(is_string($label) ? $label : $href).'</a>'; }
        return '<span class="'.$class.'" data-stl-menu>'.$trigger.'<span class="stl-menu__panel" hidden>'.$out.'</span></span>';
    }

    private static function iconGlyph(string $name): string
    {
        return Html::escape(['home' => '⌂', 'search' => '⌕', 'user' => '♙', 'calendar' => '◫', 'book' => '▤', 'settings' => '⚙', 'plus' => '+', 'check' => '✓'][$name] ?? '◇');
    }

    private static function kanban(array $columns, array $attributes): string
    {
        $out = ''; foreach ($columns as $title => $cards) { $items = ''; foreach ($cards as $card) { $items .= '<article draggable="true">'.Html::escape($card).'</article>'; } $out .= '<section><header><strong>'.Html::escape($title).'</strong><small>'.count($cards).'</small></header>'.$items.'</section>'; }
        return '<div class="stl-kanban"'.Html::attributes($attributes).'>'.$out.'</div>';
    }

    private static function nav(array $items, string $class, array $attributes): string
    {
        $out = ''; foreach ($items as $label => $href) { $out .= '<a href="'.Html::escape($href).'">'.Html::escape($label).'</a>'; } return '<nav class="'.$class.'"'.Html::attributes($attributes).'>'.$out.'</nav>';
    }

    private static function otp(string $name, int $length, array $attributes): string
    {
        $out = ''; for ($i = 0; $i < $length; $i++) { $out .= '<input inputmode="numeric" pattern="[0-9]" maxlength="1" name="'.Html::escape($name).'[]" aria-label="Digit '.($i + 1).'">'; } return '<div class="stl-otp" data-stl-otp'.Html::attributes($attributes).'>'.$out.'</div>';
    }

    private static function pagination(int $current, int $pages, string $base): string
    {
        $out = ''; for ($i = 1; $i <= $pages; $i++) { $out .= '<a href="'.Html::escape($base.$i).'"'.($i === $current ? ' aria-current="page"' : '').'>'.$i.'</a>'; } return '<nav class="stl-pagination" aria-label="Pagination">'.$out.'</nav>';
    }

    private static function pillbox(array $items, string $name, array $attributes): string
    {
        $out = ''; foreach ($items as $item) { $out .= '<span>'.Html::escape($item).'<input type="hidden" name="'.Html::escape($name).'[]" value="'.Html::escape($item).'"><button type="button" aria-label="Remove '.Html::escape($item).'">×</button></span>'; } return '<div class="stl-pillbox"'.Html::attributes($attributes).'>'.$out.'<input type="text" placeholder="Add item…" aria-label="Add item"></div>';
    }

    private static function select(string $name, array $options, string $label, array $attributes): string
    {
        $out = ''; foreach ($options as $value => $text) { $out .= '<option value="'.Html::escape(is_string($value) ? $value : $text).'">'.Html::escape($text).'</option>'; } return '<label class="stl-field"><span class="stl-field__label">'.Html::escape($label).'</span><select class="stl-select" name="'.Html::escape($name).'"'.Html::attributes($attributes).'>'.$out.'</select></label>';
    }

    private static function table(array $columns, array $rows, array $attributes): string
    {
        $head = ''; foreach ($columns as $column) { $head .= '<th scope="col">'.Html::escape($column).'</th>'; } $body = ''; foreach ($rows as $row) { $body .= '<tr>'; foreach ($row as $cell) { $body .= '<td>'.Html::escape($cell).'</td>'; } $body .= '</tr>'; } return '<div class="stl-table-wrap"><table class="stl-table"'.Html::attributes($attributes).'><thead><tr>'.$head.'</tr></thead><tbody>'.$body.'</tbody></table></div>';
    }

    private static function tabs(array $tabs, string $active, array $attributes): string
    {
        $buttons = $panels = ''; $active = $active ?: (string) array_key_first($tabs); foreach ($tabs as $label => $content) { $id = 'stl-tab-'.substr(md5($label), 0, 8); $selected = $label === $active; $buttons .= '<button id="'.$id.'-button" role="tab" aria-controls="'.$id.'" aria-selected="'.($selected ? 'true' : 'false').'">'.Html::escape($label).'</button>'; $panels .= '<section id="'.$id.'" role="tabpanel" aria-labelledby="'.$id.'-button"'.($selected ? '' : ' hidden').'>'.Html::escape($content).'</section>'; } return '<div class="stl-tabs" data-stl-tabs'.Html::attributes($attributes).'><div role="tablist">'.$buttons.'</div>'.$panels.'</div>';
    }

    private static function timeline(array $items, array $attributes): string
    {
        $out = ''; foreach ($items as $item) { $out .= '<li><i></i><div><strong>'.Html::escape($item['title'] ?? '').'</strong><p>'.Html::escape($item['text'] ?? '').'</p><small>'.Html::escape($item['time'] ?? '').'</small></div></li>'; } return '<ol class="stl-timeline"'.Html::attributes($attributes).'>'.$out.'</ol>';
    }
}
