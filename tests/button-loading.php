<?php

declare(strict_types=1);

use Stl\EboardUi\Ui;

require __DIR__.'/../vendor/autoload.php';

$check = static function (bool $condition): void {
    if (! $condition) {
        throw new RuntimeException('Button loading contract failed.');
    }
};
$plain = Ui::button('Save')->render();
$check(! str_contains($plain, 'data-stl-loading-button'));
$loading = Ui::button('Save', loading: true, loadingLabel: 'Sending…')->render();
$check(str_contains($loading, 'disabled'));
$check(str_contains($loading, 'aria-busy="true"'));
$check(str_contains($loading, 'data-stl-loading="true"'));
$check(str_contains($loading, 'stl-button__spinner'));
$check(str_contains($loading, '>Sending…</span>'));
$submit = Ui::button('Save', attributes: ['type' => 'submit'], loadingOnSubmit: true)->render();
$check(str_contains($submit, 'data-stl-loading-on-submit="true"'));
$check(! str_contains($submit, ' disabled'));
$check(str_contains($submit, 'type="submit"'));
$escaped = Ui::button('<save>', loading: true, loadingLabel: '<sending>')->render();
$check(! str_contains($escaped, '<sending>'));
$check(str_contains($escaped, '&lt;sending&gt;'));
$link = Ui::button('Open', attributes: ['href' => '/'], loading: true)->render();
$check(str_contains($link, 'aria-disabled="true"'));
$check(str_contains($link, '<a '));
echo "Button loading checks passed.\n";
