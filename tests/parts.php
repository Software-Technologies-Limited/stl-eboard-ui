<?php

declare(strict_types=1);

use Stl\EboardUi\Ui;

spl_autoload_register(static function (string $class): void {
    $prefix = 'Stl\\EboardUi\\';
    if (str_starts_with($class, $prefix)) {
        require dirname(__DIR__).'/src/'.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
    }
});

$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void {
    if (! $condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

$input = Ui::input('name', 'Name', 'text', 'Required', [
    'class' => 'px-4',
    'parts' => ['field' => ['class' => 'gap-2'], 'label' => ['class' => 'text-sm'], 'error' => ['class' => 'text-red-600']],
]);
$html = $input->render();
$check(str_contains($html, 'class="stl-input px-4"'), 'Input keeps its default and caller classes.');
$check(str_contains($html, 'class="stl-field gap-2"'), 'Field attributes apply to the wrapper.');
$check(str_contains($html, 'class="stl-field__label text-sm"'), 'Label classes apply directly.');
$check(str_contains($html, 'aria-describedby="stl-name-error"'), 'Error accessibility remains intact.');
$check(! str_contains($html, 'parts='), 'Part configuration never becomes an HTML attribute.');
$clone = $input->with(['parts' => ['label' => ['class' => 'font-bold']]]);
$check(str_contains($input->render(), 'text-sm'), 'Part updates do not mutate the original component.');
$check(str_contains($clone->render(), 'class="stl-field__label font-bold"'), 'Part attributes can be updated immutably.');

foreach ([Ui::select('status', ['open' => 'Open'], 'Status', ['class' => 'px-3', 'parts' => ['label' => ['class' => 'font-bold']]]), Ui::textarea('description', 'Description', 4, ['class' => 'px-3', 'parts' => ['label' => ['class' => 'font-bold']]])] as $field) {
    $html = $field->render();
    $check(str_contains($html, 'stl-field__label font-bold'), 'Extended field label accepts part attributes.');
    $check(str_contains($html, 'px-3'), 'Extended field merges custom classes.');
    $check(substr_count($html, 'class=') === 3, 'Extended fields do not emit duplicate class attributes.');
}

$attributes = ['parts' => ['header' => ['class' => 'px-4'], 'cell' => ['class' => 'py-3'], 'row' => ['class' => 'hover:bg-gray-50'], 'table' => ['class' => 'w-full']]];
$columns = [['key' => 'name', 'label' => 'Name']];
foreach ([[], [['name' => '<script>']]] as $rows) {
    $html = Ui::richTable($columns, $rows, $attributes)->render();
    $check(str_contains($html, '<table class="w-full">'), 'Table classes apply to the table.');
    $check(str_contains($html, 'class="px-4"'), 'Heading classes apply directly.');
    $check(str_contains($html, 'class="py-3"'), 'Cell classes also apply to empty states.');
    $check(! str_contains($html, '<script>'), 'Table values remain escaped.');
}
$html = Ui::richTable($columns, [], $attributes, options: ['loading' => true])->render();
$check(str_contains($html, 'stl-advanced-table__skeleton hover:bg-gray-50'), 'Loading rows retain part attributes.');
$check(str_contains($html, 'class="py-3"'), 'Loading cells retain part attributes.');
$html = Ui::richTable(['Name'], Ui::html('<tr><td>Example</td></tr>'), $attributes)->render();
$check(str_contains($html, '<th scope="col" class="px-4">'), 'Rich table headings also accept attributes.');

$html = Ui::modal('example', 'Example', Ui::html('<p>Content</p>'), ['parts' => ['body' => ['class' => 'p-0'], 'header' => ['class' => 'px-4']]])->render();
$check(str_contains($html, 'class="stl-modal__body p-0"'), 'Modal body classes apply directly.');
$check(str_contains($html, 'aria-labelledby="example-title"'), 'Modal accessibility remains intact.');
$check(str_contains($html, 'data-stl-close'), 'Modal close behavior remains intact.');
$html = Ui::multiSelect('choices', ['one' => 'One'], ['one'], 'Choices', attributes: ['parts' => ['option' => ['class' => 'gap-2'], 'optionInput' => ['class' => 'sr-only'], 'search' => ['class' => 'font-normal']]])->render();
$check(str_contains($html, 'stl-multiselect__option gap-2'), 'Multiselect options accept classes.');
$check(str_contains($html, 'stl-multiselect__input sr-only'), 'Multiselect inputs accept classes.');
$check(str_contains($html, 'data-stl-multiselect-input'), 'Multiselect input hooks remain intact.');
$check(str_contains($html, 'value="one" checked'), 'Selected multiselect values remain selected.');

echo "Passed {$checks} component part checks.\n";
