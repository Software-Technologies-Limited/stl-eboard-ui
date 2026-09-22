<?php

declare(strict_types=1);

use Stl\EboardUi\Ui;

require __DIR__.'/parts.php';

$check(str_contains(Ui::input('email', 'Email', attributes: ['required' => true])->render(), 'aria-required="true"'), 'Required inputs expose aria-required.');
$check(str_contains(Ui::input('email', 'Email', attributes: ['required' => true])->render(), 'stl-field__required'), 'Required inputs render a visible marker.');
$select = Ui::select('state', ['draft' => 'Draft', 'live' => 'Live'], 'State', 'live', 'Choose a state', ['required' => true])->render();
$check(str_contains($select, 'value="live" selected'), 'Select preserves selected values.');
$check(str_contains($select, 'aria-invalid="true"'), 'Select errors invalidate the control.');
$check(str_contains($select, 'aria-describedby="stl-state-error"'), 'Select errors are described by their message.');
$textarea = Ui::textarea('notes', 'Notes', 3, '<script>', 'Notes are required', ['required' => true])->render();
$check(str_contains($textarea, '&lt;script&gt;'), 'Textarea values are escaped.');
$check(str_contains($textarea, 'aria-required="true"'), 'Required textareas expose aria-required.');
$multi = Ui::multiSelect('owners', ['ada' => 'Ada'], ['ada'], 'Owners', attributes: ['required' => true], error: 'Choose an owner')->render();
$check(str_contains($multi, 'value="ada" checked'), 'Multiselect preserves selected values.');
$check(str_contains($multi, 'aria-invalid="true"'), 'Multiselect errors invalidate its trigger.');
$summary = Ui::formErrorSummary(['email' => ['Email is required.']])->render();
$check(str_contains($summary, 'role="alert"'), 'Error summary announces errors.');
$check(str_contains($summary, 'href="#stl-email"'), 'Error summary links to its control.');
echo "Passed {$checks} form checks.\n";
