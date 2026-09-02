(() => {
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

    const dismiss = event.target.closest('[data-stl-dismiss]');
    if (dismiss) dismiss.closest('.stl-toast')?.remove();

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
})();
