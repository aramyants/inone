/*
 * InOne Properties filtering (vanilla JS, no jQuery)
 * - AJAX updates via admin-ajax.php
 * - Keeps filter state in the URL
 */

(() => {
  const config = window.InoneProperties || null;
  if (!config) return;

  const form = document.querySelector('[data-inone-properties-filters]');
  const results = document.querySelector('[data-inone-properties-results]');
  const countEl = document.querySelector('[data-inone-properties-count]');

  if (!form || !results) return;

  const submitBtn = form.querySelector('button[type="submit"]');
  const resetBtn = form.querySelector('[data-inone-reset]');

  const minInput = form.querySelector('input[name="min_price"]');
  const maxInput = form.querySelector('input[name="max_price"]');
  const minRange = form.querySelector('input[name="min_price_range"]');
  const maxRange = form.querySelector('input[name="max_price_range"]');
  const rangeLabel = form.querySelector('[data-inone-price-label]');

  const debounce = (fn, ms) => {
    let handle = null;
    return (...args) => {
      if (handle) window.clearTimeout(handle);
      handle = window.setTimeout(() => fn(...args), ms);
    };
  };

  const readFilters = () => {
    const fd = new FormData(form);
    return {
      keyword: (fd.get('keyword') || '').toString().trim(),
      type: (fd.get('type') || '').toString(),
      status: (fd.get('status') || '').toString(),
      location: (fd.get('location') || '').toString(),
      bedrooms_min: (fd.get('bedrooms_min') || '').toString(),
      bathrooms_min: (fd.get('bathrooms_min') || '').toString(),
      min_price: (fd.get('min_price') || '').toString(),
      max_price: (fd.get('max_price') || '').toString(),
      sort: (fd.get('sort') || '').toString(),
      paged: '1',
    };
  };

  const writeUrlState = (filters) => {
    const url = new URL(window.location.href);
    const params = url.searchParams;

    const setOrDelete = (key, value) => {
      if (value === null || value === undefined) {
        params.delete(key);
        return;
      }
      const v = value.toString().trim();
      if (!v) params.delete(key);
      else params.set(key, v);
    };

    setOrDelete('keyword', filters.keyword);
    setOrDelete('type', filters.type);
    setOrDelete('status', filters.status);
    setOrDelete('location', filters.location);
    setOrDelete('bedrooms_min', filters.bedrooms_min);
    setOrDelete('bathrooms_min', filters.bathrooms_min);
    setOrDelete('min_price', filters.min_price);
    setOrDelete('max_price', filters.max_price);
    setOrDelete('sort', filters.sort);

    // Always reset paging on filter change.
    params.delete('paged');

    window.history.replaceState({}, '', url.toString());
  };

  const setBusy = (busy) => {
    if (submitBtn) submitBtn.disabled = busy;
    if (resetBtn) resetBtn.disabled = busy;
    form.classList.toggle('is-loading', !!busy);
  };

  const updateCount = (count) => {
    if (!countEl) return;
    countEl.textContent = `${count} result${count === 1 ? '' : 's'}`;
  };

  const syncPriceLabel = () => {
    if (!rangeLabel) return;
    const min = parseInt(minInput?.value || '0', 10) || 0;
    const max = parseInt(maxInput?.value || '0', 10) || 0;
    if (max > 0) rangeLabel.textContent = `$${min.toLocaleString()} – $${max.toLocaleString()}`;
    else rangeLabel.textContent = `$${min.toLocaleString()}+`;
  };

  const clampPrice = () => {
    const minBound = Number.isFinite(config.priceMinBound) ? config.priceMinBound : 0;
    const maxBound = Number.isFinite(config.priceMaxBound) ? config.priceMaxBound : 0;

    let min = parseInt(minInput?.value || '0', 10) || 0;
    let max = parseInt(maxInput?.value || '0', 10) || 0;

    if (min < minBound) min = minBound;
    if (maxBound > 0 && max > maxBound) max = maxBound;
    if (max > 0 && max < min) max = min;

    if (minInput) minInput.value = String(min);
    if (maxInput) maxInput.value = max > 0 ? String(max) : '';

    if (minRange) minRange.value = String(min);
    if (maxRange) maxRange.value = String(max > 0 ? max : (maxBound || min));

    syncPriceLabel();
  };

  const fetchResults = async (filters) => {
    setBusy(true);

    const body = new URLSearchParams();
    body.set('action', 'inone_properties_search');
    body.set('nonce', config.nonce);
    Object.entries(filters).forEach(([k, v]) => body.set(k, v ?? ''));

    try {
      const resp = await fetch(config.ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString(),
        credentials: 'same-origin',
      });
      const json = await resp.json();
      if (!json || !json.success) {
        throw new Error((json && json.data && json.data.message) ? json.data.message : 'Request failed');
      }

      results.innerHTML = json.data.html || '';
      updateCount(Number(json.data.foundPosts || 0));
    } catch (err) {
      results.innerHTML = '<div class="inone-properties-empty">Sorry — something went wrong while loading results.</div>';
      updateCount(0);
      // eslint-disable-next-line no-console
      console.error(err);
    } finally {
      setBusy(false);
    }
  };

  const applyFromUrl = () => {
    const url = new URL(window.location.href);
    const params = url.searchParams;
    ['keyword', 'type', 'status', 'location', 'bedrooms_min', 'bathrooms_min', 'min_price', 'max_price', 'sort'].forEach((k) => {
      const el = form.querySelector(`[name="${CSS.escape(k)}"]`);
      if (!el) return;
      if (!params.has(k)) return;
      el.value = params.get(k) || '';
    });
    clampPrice();
  };

  const debouncedFetch = debounce(() => {
    clampPrice();
    const filters = readFilters();
    writeUrlState(filters);
    fetchResults(filters);
  }, 250);

  // Initial sync from URL.
  applyFromUrl();
  updateCount(parseInt(countEl?.textContent || '0', 10) || 0);

  // Listen for any changes.
  form.addEventListener('change', (e) => {
    const target = e.target;
    if (!(target instanceof HTMLElement)) return;

    // Prevent double-triggering when range inputs sync into number inputs.
    if (target.name === 'min_price_range' && minInput) {
      minInput.value = target.value;
    }
    if (target.name === 'max_price_range' && maxInput) {
      maxInput.value = target.value;
    }

    debouncedFetch();
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    clampPrice();
    const filters = readFilters();
    writeUrlState(filters);
    fetchResults(filters);
  });

  if (resetBtn) {
    resetBtn.addEventListener('click', (e) => {
      e.preventDefault();
      form.reset();

      // Reset price bounds intelligently.
      if (minInput) minInput.value = String(config.priceMinBound || 0);
      if (maxInput) maxInput.value = '';
      clampPrice();

      const url = new URL(window.location.href);
      url.search = '';
      window.history.replaceState({}, '', url.toString());

      fetchResults(readFilters());
    });
  }
})();

