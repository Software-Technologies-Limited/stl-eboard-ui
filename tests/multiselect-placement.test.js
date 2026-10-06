const assert = require('node:assert');
const fs = require('node:fs');

const javascript = fs.readFileSync(`${__dirname}/../resources/js/eboard-ui.js`, 'utf8');
const stylesheet = fs.readFileSync(`${__dirname}/../resources/css/eboard-ui.css`, 'utf8');

for (const hook of ['multiselectBounds', 'positionMultiselect', 'stlPlacement', 'spaceAbove', 'spaceBelow']) {
  assert.ok(javascript.includes(hook), `Missing multiselect placement hook: ${hook}`);
}

assert.ok(
  stylesheet.includes('.stl-multiselect[data-stl-placement="top"] .stl-multiselect__panel'),
  'Missing upward multiselect placement style.',
);

console.log('Passed collision-aware multiselect placement contract checks.');
