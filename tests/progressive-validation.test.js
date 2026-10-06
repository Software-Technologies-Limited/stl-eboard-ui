// Source-level contract test: the browser behavior itself is dependency-free.
const assert = require('node:assert');
const fs = require('node:fs');
const source = fs.readFileSync(`${__dirname}/../resources/js/eboard-ui.js`, 'utf8');
for (const hook of ['form[data-stl-validate]', 'event.preventDefault()', 'setFieldError', 'scrollIntoView', 'stlValidateToast', 'stlValidateSubmitted']) assert.ok(source.includes(hook), `Missing progressive validation hook: ${hook}`);
for (const removedHook of ['updateErrorSummary', 'stlValidateSummary', 'data-stl-error-summary', 'Please correct the following fields']) assert.equal(source.includes(removedHook), false, `Removed summary hook is still present: ${removedHook}`);
console.log('Passed progressive-validation JavaScript contract checks.');
