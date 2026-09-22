// Source-level contract test: the browser behavior itself is dependency-free.
const assert = require('node:assert');
const fs = require('node:fs');
const source = fs.readFileSync(`${__dirname}/../resources/js/eboard-ui.js`, 'utf8');
for (const hook of ['form[data-stl-validate]', 'event.preventDefault()', 'setFieldError', 'updateErrorSummary', 'scrollIntoView', 'stlValidateToast']) assert.ok(source.includes(hook), `Missing progressive validation hook: ${hook}`);
console.log('Passed progressive-validation JavaScript contract checks.');
