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
      chip.textContent = input.closest('[data-stl-multiselect-option]').textContent.trim();
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
      table?.querySelectorAll('[data-stl-table-select]').forEach(input => input.checked = event.target.checked);
    }
    if (event.target.matches('[data-stl-table-select]')) {
      const table = event.target.closest('[data-stl-advanced-table]');
      const inputs = [...table.querySelectorAll('[data-stl-table-select]')];
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
      const numeric = Number(aText.replace(/[^0-9.-]/g, '')) - Number(bText.replace(/[^0-9.-]/g, ''));
      const result = Number.isFinite(numeric) && aText !== '' && bText !== '' ? numeric : aText.localeCompare(bText, undefined, { numeric: true });
      return direction === 'ascending' ? result : -result;
    });
    rows.forEach(row => row.parentElement.appendChild(row));
  };

  document.addEventListener('click', (event) => {
    const header = event.target.closest('[data-stl-table-sort]');
    if (header) sortAdvancedTable(header);
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initialiseToasts());
  } else {
    initialiseToasts();
  }

  new MutationObserver((records) => {
    records.forEach((record) => record.addedNodes.forEach((node) => {
      if (node instanceof Element) initialiseToasts(node);
    }));
  }).observe(document.documentElement, { childList: true, subtree: true });
})();
