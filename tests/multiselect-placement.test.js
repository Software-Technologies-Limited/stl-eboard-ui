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
assert.ok(
  stylesheet.includes('.stl-multiselect__options { min-height: 0; overscroll-behavior: contain; scrollbar-gutter: stable; touch-action: pan-y;'),
  'Multiselect options must retain an independently scrollable touch area.',
);
assert.ok(
  stylesheet.includes('max-height:calc(100dvh - 2rem)') && stylesheet.includes('.stl-modal[open] { display:flex; flex-direction:column; }'),
  'Modals must remain bounded by the viewport.',
);
assert.ok(
  stylesheet.includes('.stl-modal__body { flex:1 1 auto;') && stylesheet.includes('min-height:0; overflow-y:auto;'),
  'Modal bodies must scroll when the available viewport is short.',
);
assert.ok(
  javascript.includes('Math.max(0, availableSpace)') && !javascript.includes('Math.max(80, availableSpace)'),
  'Multiselect placement must not exceed the actual available space.',
);

console.log('Passed collision-aware multiselect placement contract checks.');
