const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(`${__dirname}/../resources/js/eboard-ui.js`, 'utf8');
const handlers = {};
const microtasks = [];
const button = {
  dataset: { stlLoading: 'false', stlLoadingLabel: 'Sending…', stlIdleLabel: 'Send', stlIdleDisabled: 'false' },
  tagName: 'BUTTON', disabled: false, label: { textContent: 'Send' }, attributes: {},
  matches: () => true,
  getAttribute(name) { return this.attributes[name] ?? null; },
  setAttribute(name, value) { this.attributes[name] = value; },
  querySelector() { return this.label; },
};
const window = { addEventListener(name, callback) { handlers[name] = callback; } };
vm.runInNewContext(source.slice(source.indexOf('  const loadingButtonState'), source.indexOf('  const toastTimers')), {
  window, document: { addEventListener(name, callback) { handlers[name] = callback; }, querySelectorAll: () => [button] },
  queueMicrotask(callback) { microtasks.push(callback); },
});
const event = { submitter: button, defaultPrevented: false, preventDefault() { this.defaultPrevented = true; } };
handlers.submit(event);
event.preventDefault();
microtasks.shift()();
assert.equal(button.disabled, false, 'Prevented submissions stay usable.');
handlers.submit({ ...event, defaultPrevented: false });
microtasks.shift()();
assert.equal(button.disabled, true);
assert.equal(button.label.textContent, 'Sending…');
assert.equal(button.attributes['aria-busy'], 'true');
handlers.pageshow();
assert.equal(button.disabled, false);
assert.equal(button.label.textContent, 'Send');
button.disabled = true;
window.StlEboardUi.setButtonLoading(button, true);
window.StlEboardUi.setButtonLoading(button, false);
assert.equal(button.disabled, true, 'Originally disabled controls stay disabled.');
console.log('Button loading behavior checks passed.');
