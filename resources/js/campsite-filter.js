/**
 * Live filter for lists of items by a single radio-group value.
 *
 * Markup contract (any element can host the root):
 *
 *   <div data-filter-root>
 *     <input type="radio" name="..." value=""     data-filter-input>  {{-- empty value = show all --}}
 *     <input type="radio" name="..." value="tent" data-filter-input>
 *     ...
 *     <div data-filter-item data-type="tent">...</div>
 *     <span data-filter-count></span>
 *     <div data-filter-empty hidden>No matches</div>
 *   </div>
 *
 * The module is generic: items are matched by their `data-type` attribute
 * against the selected input's `value`. Visibility is toggled via the
 * `hidden` attribute so screen readers and CSS-only fallbacks behave correctly.
 */

const SELECTORS = Object.freeze({
    root: '[data-filter-root]',
    input: '[data-filter-input]',
    item: '[data-filter-item]',
    count: '[data-filter-count]',
    empty: '[data-filter-empty]',
});

function applyFilter(root) {
    const selected = root.querySelector(`${SELECTORS.input}:checked`)?.value ?? '';
    const items = root.querySelectorAll(SELECTORS.item);

    let visible = 0;
    items.forEach((item) => {
        const matches = selected === '' || item.dataset.type === selected;
        item.hidden = !matches;
        if (matches) visible += 1;
    });

    const countEl = root.querySelector(SELECTORS.count);
    if (countEl) {
        if (selected === '') {
            const totalEl = countEl.closest('[data-total]');
            countEl.textContent = totalEl ? totalEl.dataset.total : String(visible);
        } else {
            countEl.textContent = String(visible);
        }
    }

    const emptyEl = root.querySelector(SELECTORS.empty);
    if (emptyEl) emptyEl.hidden = visible !== 0 || items.length === 0;
}

function initRoot(root) {
    const inputs = root.querySelectorAll(SELECTORS.input);
    if (inputs.length === 0) return;

    inputs.forEach((input) => {
        input.addEventListener('change', () => applyFilter(root));
    });

    applyFilter(root);
}

function init() {
    document.querySelectorAll(SELECTORS.root).forEach(initRoot);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
} else {
    init();
}
