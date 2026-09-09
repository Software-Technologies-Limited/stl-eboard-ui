(() => {
  const toastTimers = new WeakMap();

  const dismissToast = (toast) => {
    if (!toast || toast.classList.contains('stl-toast--leaving')) return;
    window.clearTimeout(toastTimers.get(toast));
    toast.classList.add('stl-toast--leaving');
    window.setTimeout(() => {
      toast.remove();
      document.dispatchEvent(new CustomEvent('stl:toast-dismissed'));
    }, 300);
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

    const dismiss = event.target.closest('[data-stl-toast-dismiss], [data-stl-dismiss]');
    if (dismiss) dismissToast(dismiss.closest('[data-stl-toast], .stl-toast'));

    const tab = event.target.closest('[role="tab"]');
    if (tab) {
      const tabs = tab.closest('[data-stl-tabs]');
      tabs?.querySelectorAll('[role="tab"]').forEach(item => item.setAttribute('aria-selected', String(item === tab)));
      tabs?.querySelectorAll('[role="tabpanel"]').forEach(panel => panel.hidden = panel.id !== tab.getAttribute('aria-controls'));
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
  });

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
