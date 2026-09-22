(() => {
  const toastTimers = new WeakMap();

  const dismissToast = (toast) => {
    if (!toast || toast.classList.contains('stl-toast--leaving')) return;
    window.clearTimeout(toastTimers.get(toast));
    toast.classList.add('stl-toast--leaving');
    const removeToast = () => {
      if (!toast.isConnected) return;
      toast.remove();
      document.dispatchEvent(new CustomEvent('stl:toast-dismissed'));
    };
    const handleTransitionEnd = (event) => {
      if (event.target !== toast || event.propertyName !== 'opacity') return;
      toast.removeEventListener('transitionend', handleTransitionEnd);
      removeToast();
    };
    toast.addEventListener('transitionend', handleTransitionEnd);
    window.setTimeout(removeToast, 450);
  };

  const initialiseToast = (toast) => {
    if (!toast || toast.dataset.stlToastReady === 'true') return;
    toast.dataset.stlToastReady = 'true';
    const timeout = Number(toast.dataset.timeout ?? 3500);
    if (Number.isFinite(timeout) && timeout > 0) {
      toastTimers.set(toast, window.setTimeout(() => dismissToast(toast), timeout));
    }
  };

  const initialiseToasts = (root = document) => {
    if (root.matches?.('[data-stl-toast]')) initialiseToast(root);
    root.querySelectorAll?.('[data-stl-toast]').forEach(initialiseToast);
  };

  const ensureToaster = (position = 'top-center') => {
    let toaster = document.querySelector(`[data-stl-toaster][data-position="${position}"]`);
    if (toaster) return toaster;

    toaster = document.createElement('div');
    toaster.className = `stl-toaster stl-toaster--${position}`;
    toaster.dataset.stlToaster = 'true';
    toaster.dataset.position = position;
    toaster.setAttribute('aria-label', 'Notifications');
    document.body.appendChild(toaster);
    return toaster;
  };

  const showToast = ({ title = 'Notification', message = '', tone = 'info', timeout = 3500, position = 'top-center' } = {}) => {
    const allowedTones = ['info', 'success', 'warning', 'danger'];
    const allowedPositions = ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'];
    const resolvedTone = allowedTones.includes(tone) ? tone : 'info';
    const resolvedPosition = allowedPositions.includes(position) ? position : 'top-center';
    const toast = document.createElement('div');
    toast.className = `stl-toast stl-toast--${resolvedTone}`;
    toast.dataset.stlToast = 'true';
    toast.dataset.timeout = String(Math.max(0, Number(timeout) || 0));
    toast.setAttribute('role', resolvedTone === 'danger' ? 'alert' : 'status');
    toast.setAttribute('aria-live', resolvedTone === 'danger' ? 'assertive' : 'polite');
    toast.setAttribute('aria-atomic', 'true');

    const content = document.createElement('div');
    content.className = 'stl-toast__content';
    const heading = document.createElement('p');
    heading.className = 'stl-toast__title';
    heading.textContent = title;
    content.appendChild(heading);
    if (message) {
      const copy = document.createElement('p');
      copy.className = 'stl-toast__message';
      copy.textContent = message;
      content.appendChild(copy);
    }

    const close = document.createElement('button');
    close.className = 'stl-toast__close';
    close.type = 'button';
    close.dataset.stlToastDismiss = 'true';
    close.setAttribute('aria-label', 'Close notification');
    close.textContent = '×';
    toast.append(content, close);
    ensureToaster(resolvedPosition).appendChild(toast);
    initialiseToast(toast);
    document.dispatchEvent(new CustomEvent('stl:toast-shown', { detail: { toast, tone: resolvedTone } }));
    return toast;
  };

  window.StlEboardUi = Object.assign(window.StlEboardUi || {}, { toast: showToast, dismissToast });
  document.addEventListener('stl:toast', (event) => showToast(event.detail));

  document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-stl-open]');
    if (opener) document.getElementById(opener.dataset.stlOpen)?.showModal();

    const closer = event.target.closest('[data-stl-close]');
    if (closer) closer.closest('dialog')?.close();

    const menuTrigger = event.target.closest('[data-stl-menu-trigger], [data-stl-context-trigger]');
    if (menuTrigger) {
      const panel = menuTrigger.closest('[data-stl-menu]')?.querySelector('.stl-menu__panel');
      if (panel) { panel.hidden = !panel.hidden; menuTrigger.setAttribute('aria-expanded', String(!panel.hidden)); }
    }

    const popoverTrigger = event.target.closest('[data-stl-popover-trigger]');
    if (popoverTrigger) {
      const panel = popoverTrigger.nextElementSibling;
      panel.hidden = !panel.hidden;
      popoverTrigger.setAttribute('aria-expanded', String(!panel.hidden));
    }

    const toggle = event.target.closest('[data-stl-toggle]');
    if (toggle) toggle.setAttribute('aria-pressed', String(toggle.getAttribute('aria-pressed') !== 'true'));

    const workspaceToggle = event.target.closest('[data-stl-workspace-toggle]');
    if (workspaceToggle) {
      const shell = workspaceToggle.closest('[data-stl-workspace-shell]');
      if (shell) {
        const isCollapsed = shell.dataset.stlWorkspaceCollapsed !== 'true';
        shell.dataset.stlWorkspaceCollapsed = String(isCollapsed);
        workspaceToggle.setAttribute('aria-expanded', String(!isCollapsed));
      }
    }

    const dismiss = event.target.closest('[data-stl-toast-dismiss], [data-stl-dismiss]');
    if (dismiss) dismissToast(dismiss.closest('[data-stl-toast], .stl-toast'));

    const tab = event.target.closest('[role="tab"]');
    if (tab) {
      const tabs = tab.closest('[data-stl-tabs]');
      if (tabs) activateTab(tabs, tab);
    }

    const carouselButton = event.target.closest('[data-stl-prev], [data-stl-next]');
    if (carouselButton) {
      const slides = [...carouselButton.closest('[data-stl-carousel]').querySelectorAll('[data-stl-slide]')];
      const current = Math.max(0, slides.findIndex(slide => !slide.hidden));
      const step = carouselButton.hasAttribute('data-stl-next') ? 1 : -1;
      slides.forEach((slide, index) => slide.hidden = index !== (current + step + slides.length) % slides.length);
    }

    const format = event.target.closest('[data-stl-format]');
    if (format) document.execCommand(format.dataset.stlFormat, false);

    const multiSelectTrigger = event.target.closest('[data-stl-multiselect-trigger]');
    if (multiSelectTrigger) {
      const root = multiSelectTrigger.closest('[data-stl-multiselect]');
      const panel = root?.querySelector('[data-stl-multiselect-panel]');
      document.querySelectorAll('[data-stl-multiselect-panel]:not([hidden])').forEach(other => {
        if (other === panel) return;
        other.hidden = true;
        other.closest('[data-stl-multiselect]').querySelector('[data-stl-multiselect-trigger]').setAttribute('aria-expanded', 'false');
      });
      if (panel && !multiSelectTrigger.disabled) {
        panel.hidden = !panel.hidden;
        multiSelectTrigger.setAttribute('aria-expanded', String(!panel.hidden));
        if (!panel.hidden) panel.querySelector('[data-stl-multiselect-search]')?.focus();
      }
    } else if (!event.target.closest('[data-stl-multiselect]')) {
      document.querySelectorAll('[data-stl-multiselect-panel]:not([hidden])').forEach(panel => {
        panel.hidden = true;
        panel.closest('[data-stl-multiselect]')?.querySelector('[data-stl-multiselect-trigger]')?.setAttribute('aria-expanded', 'false');
      });
    }
  });

  document.addEventListener('keydown', (event) => {
    const tab = event.target.closest('[role="tab"]');
    const tabs = tab?.closest('[data-stl-tabs]');
    if (!tabs || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;

    const tabItems = [...tabs.querySelectorAll('[role="tab"]')];
    const current = tabItems.indexOf(tab);
    const next = event.key === 'Home' ? 0
      : event.key === 'End' ? tabItems.length - 1
      : (current + (event.key === 'ArrowRight' ? 1 : -1) + tabItems.length) % tabItems.length;

    event.preventDefault();
    tabItems[next]?.focus();
    activateTab(tabs, tabItems[next]);
  });

  function activateTab(tabs, tab) {
    tabs.querySelectorAll('[role="tab"]').forEach(item => {
      const selected = item === tab;
      item.setAttribute('aria-selected', String(selected));
      item.tabIndex = selected ? 0 : -1;
    });
    tabs.querySelectorAll('[role="tabpanel"]').forEach(panel => {
      panel.hidden = panel.id !== tab.getAttribute('aria-controls');
    });
  }

  document.addEventListener('input', (event) => {
    if (event.target.matches('[data-stl-command-input]')) {
      const value = event.target.value.toLowerCase();
      const root = event.target.closest('.stl-command');
      let visible = 0;
      root.querySelectorAll('[data-stl-command-item]').forEach(item => { item.hidden = !item.textContent.toLowerCase().includes(value); if (!item.hidden) visible++; });
      root.querySelector('[data-stl-empty]').hidden = visible > 0;
    }
    if (event.target.matches('.stl-slider input')) event.target.closest('.stl-slider').querySelector('output').value = event.target.value;
    if (event.target.matches('[data-stl-otp] input') && event.target.value) event.target.nextElementSibling?.focus();
    if (event.target.matches('[data-stl-multiselect-search]')) {
      const query = event.target.value.trim().toLowerCase();
      const root = event.target.closest('[data-stl-multiselect]');
      let visible = 0;
      root.querySelectorAll('[data-stl-multiselect-option]').forEach(option => {
        option.hidden = !option.textContent.toLowerCase().includes(query);
        if (!option.hidden) visible++;
      });
      root.querySelector('[data-stl-multiselect-empty]').hidden = visible > 0;
    }
  });

  document.addEventListener('change', (event) => {
    if (!event.target.matches('[data-stl-multiselect-input]')) return;
    const root = event.target.closest('[data-stl-multiselect]');
    const value = root.querySelector('[data-stl-multiselect-value]');
    const selected = [...root.querySelectorAll('[data-stl-multiselect-input]:checked')];
    value.className = selected.length ? 'stl-multiselect__chips' : 'stl-multiselect__placeholder';
    if (!selected.length) {
      value.textContent = root.querySelector('[data-stl-multiselect-trigger]').dataset.placeholder || 'Select options';
      return;
    }
    value.replaceChildren(...selected.map(input => {
      const chip = document.createElement('span');
      chip.className = 'stl-multiselect__chip';
      chip.textContent = input.closest('[data-stl-multiselect-option]').lastElementChild.textContent.trim();
      return chip;
    }));
  });

  document.addEventListener('keydown', (event) => {
    const sortableHeader = event.target.closest('[data-stl-table-sort]');
    if (sortableHeader && ['Enter', ' '].includes(event.key)) {
      event.preventDefault();
      sortAdvancedTable(sortableHeader);
      return;
    }
    if (event.key !== 'Escape') return;
    const root = event.target.closest('[data-stl-multiselect]');
    const panel = root?.querySelector('[data-stl-multiselect-panel]');
    if (panel && !panel.hidden) {
      panel.hidden = true;
      root.querySelector('[data-stl-multiselect-trigger]').setAttribute('aria-expanded', 'false');
      root.querySelector('[data-stl-multiselect-trigger]').focus();
    }
  });

  document.addEventListener('change', (event) => {
    if (event.target.matches('[data-stl-table-select-all]')) {
      const table = event.target.closest('[data-stl-advanced-table]');
      table?.querySelectorAll('[data-stl-table-select]:not(:disabled)').forEach(input => input.checked = event.target.checked);
    }
    if (event.target.matches('[data-stl-table-select]')) {
      const table = event.target.closest('[data-stl-advanced-table]');
      const inputs = [...table.querySelectorAll('[data-stl-table-select]:not(:disabled)')];
      const all = table.querySelector('[data-stl-table-select-all]');
      if (all) { all.checked = inputs.length > 0 && inputs.every(input => input.checked); all.indeterminate = !all.checked && inputs.some(input => input.checked); }
    }
    if (event.target.matches('[data-stl-table-column-toggle]')) {
      const table = event.target.closest('[data-stl-advanced-table]');
      const key = event.target.value;
      table?.querySelectorAll(`[data-stl-table-cell="${CSS.escape(key)}"], [data-key="${CSS.escape(key)}"]`).forEach(cell => cell.hidden = !event.target.checked);
    }
    if (event.target.matches('[data-stl-table-select-all], [data-stl-table-select]')) {
      const table = event.target.closest('[data-stl-advanced-table]');
      const bulkActions = table?.querySelector('[data-stl-table-bulk-actions]');
      if (bulkActions) bulkActions.hidden = !table.querySelector('[data-stl-table-select]:checked');
    }
  });

  const sortAdvancedTable = (header) => {
    const table = header.closest('[data-stl-advanced-table]');
    const key = header.dataset.key;
    const direction = header.getAttribute('aria-sort') === 'ascending' ? 'descending' : 'ascending';
    [...table.querySelectorAll('[data-stl-table-sort]')].forEach(item => item.setAttribute('aria-sort', item === header ? direction : 'none'));
    const rows = [...table.querySelectorAll('tbody tr[data-stl-table-row]')];
    rows.sort((a, b) => {
      const aText = a.querySelector(`[data-stl-table-cell="${CSS.escape(key)}"]`)?.textContent.trim() || '';
      const bText = b.querySelector(`[data-stl-table-cell="${CSS.escape(key)}"]`)?.textContent.trim() || '';
      const aNumber = aText.replace(/,/g, '');
      const bNumber = bText.replace(/,/g, '');
      const isNumeric = value => /^[-+]?(?:\d+(?:\.\d*)?|\.\d+)%?$/.test(value);
      const result = isNumeric(aNumber) && isNumeric(bNumber)
        ? parseFloat(aNumber) - parseFloat(bNumber)
        : aText.localeCompare(bText, undefined, { numeric: true });
      return direction === 'ascending' ? result : -result;
    });
    rows.forEach(row => row.parentElement.appendChild(row));
  };

  document.addEventListener('click', (event) => {
    const header = event.target.closest('[data-stl-table-sort]');
    if (header) sortAdvancedTable(header);
    const row = event.target.closest('[data-stl-row-href]');
    if (row && !event.target.closest('a, button, input, select, textarea, label, [contenteditable]')) {
      window.location.assign(row.dataset.stlRowHref);
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.target.matches('[data-stl-row-href]') && ['Enter', ' '].includes(event.key)) {
      event.preventDefault();
      window.location.assign(event.target.dataset.stlRowHref);
    }
  });

  const initialiseControls = (root = document) => {
    const find = selector => [ ...(root.matches?.(selector) ? [root] : []), ...root.querySelectorAll(selector) ];
    find('[data-stl-multiselect]').forEach(control => {
      if (control.dataset.stlEnhanced) return;
      control.dataset.stlEnhanced = 'true';
      control.querySelector('[data-stl-multiselect-panel]').hidden = true;
      control.querySelector('[data-stl-multiselect-trigger]').setAttribute('aria-expanded', 'false');
    });
    find('[data-stl-advanced-table]').forEach(table => {
      const inputs = [...table.querySelectorAll('[data-stl-table-select]:not(:disabled)')];
      const all = table.querySelector('[data-stl-table-select-all]');
      const selected = inputs.filter(input => input.checked).length;
      if (all) {
        all.checked = inputs.length > 0 && selected === inputs.length;
        all.indeterminate = selected > 0 && selected < inputs.length;
        all.disabled = inputs.length === 0;
      }
      const bulk = table.querySelector('[data-stl-table-bulk-actions]');
      if (bulk) bulk.hidden = selected === 0;
    });
    find('form[data-stl-validate]').forEach(form => {
      if (form.dataset.stlValidateReady === 'true') return;
      form.dataset.stlValidateReady = 'true';
      // Validation is handled below so every invalid field receives a visible message.
      form.noValidate = true;
    });
  };

  const validationMessage = (control) => control.dataset.stlErrorRequired || 'This field is required.';
  const validationId = (control) => control.id || `stl-${control.name || 'field'}-${Math.random().toString(36).slice(2, 8)}`;
  const fieldFor = (control) => control.closest('.stl-field') || control.parentElement;
  const setFieldError = (control, message) => {
    const field = fieldFor(control); if (!field) return;
    const id = validationId(control); control.id = id;
    const errorId = `${id}-error`;
    let error = field.querySelector(`#${CSS.escape(errorId)}`);
    if (!error) { error = document.createElement('span'); error.id = errorId; error.className = 'stl-field__error'; error.setAttribute('role', 'alert'); field.appendChild(error); }
    error.textContent = message;
    control.setAttribute('aria-invalid', 'true');
    control.setAttribute('aria-describedby', errorId);
  };
  const clearFieldError = (control) => {
    const id = control.id; if (!id) return;
    const errorId = `${id}-error`; const error = fieldFor(control)?.querySelector(`#${CSS.escape(errorId)}`);
    // Preserve server-rendered errors until a user makes this field valid.
    error?.remove(); control.removeAttribute('aria-invalid');
    const descriptions = (control.getAttribute('aria-describedby') || '').split(' ').filter(item => item && item !== errorId);
    if (descriptions.length) control.setAttribute('aria-describedby', descriptions.join(' ')); else control.removeAttribute('aria-describedby');
  };
  const multiselectValid = (root) => !root.hasAttribute('data-stl-required') || !!root.querySelector('[data-stl-multiselect-input]:checked');
  const validateControl = (control) => {
    if (control.matches('[data-stl-multiselect]')) {
      const trigger = control.querySelector('[data-stl-multiselect-trigger]');
      if (multiselectValid(control)) { clearFieldError(trigger); return true; }
      setFieldError(trigger, validationMessage(control)); return false;
    }
    const required = control.required || control.getAttribute('aria-required') === 'true';
    const valid = !required || (control.type === 'checkbox' || control.type === 'radio' ? control.checked : String(control.value || '').trim() !== '');
    if (valid) clearFieldError(control); else setFieldError(control, validationMessage(control));
    return valid;
  };
  const updateErrorSummary = (form, invalid) => {
    let summary = form.querySelector('[data-stl-error-summary]');
    if (!invalid.length) { summary?.remove(); return; }
    if (!summary) { summary = document.createElement('section'); summary.className = 'stl-form-error-summary'; summary.dataset.stlErrorSummary = 'true'; summary.setAttribute('role', 'alert'); summary.tabIndex = -1; form.prepend(summary); }
    const title = form.dataset.stlValidateTitle || 'Please correct the following fields';
    const list = invalid.map(control => { const target = control.matches('[data-stl-multiselect]') ? control.querySelector('[data-stl-multiselect-trigger]') : control; return `<li><a href="#${target.id}" data-stl-error-focus>${validationMessage(control)}</a></li>`; }).join('');
    summary.innerHTML = `<p class="stl-form-error-summary__title"></p><ul>${list}</ul>`; summary.firstElementChild.textContent = title;
  };
  document.addEventListener('submit', event => {
    const form = event.target.closest('form[data-stl-validate]'); if (!form) return;
    const controls = [...form.querySelectorAll('input, select, textarea, [data-stl-multiselect]')].filter(control => !control.disabled && (control.matches('[data-stl-multiselect]') || control.required || control.getAttribute('aria-required') === 'true'));
    const invalid = controls.filter(control => !validateControl(control));
    updateErrorSummary(form, invalid);
    if (!invalid.length) return;
    event.preventDefault();
    const first = invalid[0].matches('[data-stl-multiselect]') ? invalid[0].querySelector('[data-stl-multiselect-trigger]') : invalid[0];
    first.focus({ preventScroll: true }); first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    if (form.dataset.stlValidateToast === 'true') showToast({ title: 'Please correct the form', message: 'Review the highlighted fields.', tone: 'danger' });
  });
  document.addEventListener('input', event => { const form = event.target.closest('form[data-stl-validate]'); if (form) { validateControl(event.target); updateErrorSummary(form, [...form.querySelectorAll('input, select, textarea, [data-stl-multiselect]')].filter(control => (control.matches('[data-stl-multiselect]') || control.required || control.getAttribute('aria-required') === 'true') && !validateControl(control))); } });
  document.addEventListener('change', event => {
    const form = event.target.closest('form[data-stl-validate]'); if (!form) return;
    const multi = event.target.closest('[data-stl-multiselect]');
    validateControl(multi || event.target);
    const invalid = [...form.querySelectorAll('input, select, textarea, [data-stl-multiselect]')].filter(control => !control.disabled && (control.matches('[data-stl-multiselect]') || control.required || control.getAttribute('aria-required') === 'true') && !validateControl(control));
    updateErrorSummary(form, invalid);
  });
  document.addEventListener('click', event => { const link = event.target.closest('[data-stl-error-focus]'); if (!link) return; const target = document.getElementById(link.getAttribute('href').slice(1)); if (target) { event.preventDefault(); target.focus(); target.scrollIntoView({ behavior: 'smooth', block: 'center' }); } });

  let activeTooltip = null;
  let tooltipOwner = null;
  const hideTooltip = () => {
    if (tooltipOwner) {
      tooltipOwner.removeAttribute('data-stl-tooltip-floating');
      const ids = (tooltipOwner.getAttribute('aria-describedby') || '').split(' ').filter(id => id && id !== 'stl-floating-tooltip');
      if (ids.length) tooltipOwner.setAttribute('aria-describedby', ids.join(' '));
      else tooltipOwner.removeAttribute('aria-describedby');
    }
    activeTooltip?.remove();
    activeTooltip = null;
    tooltipOwner = null;
  };
  const showTooltip = owner => {
    hideTooltip();
    if (!owner.dataset.tooltip) return;
    tooltipOwner = owner;
    activeTooltip = document.createElement('span');
    activeTooltip.id = 'stl-floating-tooltip';
    activeTooltip.className = 'stl-tooltip__floating';
    activeTooltip.setAttribute('role', 'tooltip');
    activeTooltip.textContent = owner.dataset.tooltip;
    document.body.appendChild(activeTooltip);
    owner.dataset.stlTooltipFloating = 'true';
    owner.setAttribute('aria-describedby', `${owner.getAttribute('aria-describedby') || ''} stl-floating-tooltip`.trim());
    const rect = owner.getBoundingClientRect();
    const width = activeTooltip.offsetWidth;
    const height = activeTooltip.offsetHeight;
    activeTooltip.style.left = `${Math.max(8, Math.min(rect.left + rect.width / 2 - width / 2, window.innerWidth - width - 8))}px`;
    activeTooltip.style.top = `${rect.top > height + 12 ? rect.top - height - 8 : rect.bottom + 8}px`;
  };
  document.addEventListener('pointerover', event => {
    const owner = event.target.closest('.stl-tooltip[data-tooltip]');
    if (owner && !owner.contains(event.relatedTarget)) showTooltip(owner);
  });
  document.addEventListener('pointerout', event => {
    if (tooltipOwner?.contains(event.target) && !tooltipOwner.contains(event.relatedTarget)) hideTooltip();
  });
  document.addEventListener('focusin', event => {
    const owner = event.target.closest('.stl-tooltip[data-tooltip]');
    if (owner) showTooltip(owner);
  });
  document.addEventListener('focusout', hideTooltip);
  document.addEventListener('keydown', event => { if (event.key === 'Escape') hideTooltip(); });
  document.addEventListener('scroll', hideTooltip, true);
  window.addEventListener('resize', hideTooltip);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => { initialiseToasts(); initialiseControls(); });
  } else {
    initialiseToasts();
    initialiseControls();
  }

  new MutationObserver((records) => {
    records.forEach((record) => record.addedNodes.forEach((node) => {
      if (node instanceof Element) { initialiseToasts(node); initialiseControls(node); }
    }));
  }).observe(document.documentElement, { childList: true, subtree: true });
})();
