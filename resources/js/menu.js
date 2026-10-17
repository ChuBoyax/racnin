import { peso } from './cart';

/**
 * Category chips, search, and live prices for the option a guest picks.
 */
export function initMenu(catalog) {
    const grid = document.querySelector('[data-menu-grid]');

    if (!grid) {
        return;
    }

    const cards = [...grid.querySelectorAll('[data-item]')];
    const chips = [...document.querySelectorAll('[data-filter]')];
    const search = document.querySelector('[data-menu-search]');
    const empty = document.querySelector('[data-menu-empty]');
    const prefersReducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
    let category = 'all';

    const applyFilters = () => {
        const query = search?.value.trim().toLowerCase() ?? '';
        let visible = 0;

        for (const card of cards) {
            const matches = (category === 'all' || card.dataset.category === category) && card.dataset.search.includes(query);
            card.hidden = !matches;
            visible += matches ? 1 : 0;
        }

        empty.hidden = visible > 0;
    };

    const selectCategory = (next) => {
        category = next;
        chips.forEach((chip) => chip.setAttribute('aria-pressed', String(chip.dataset.filter === next)));

        if (document.startViewTransition && !prefersReducedMotion.matches) {
            document.startViewTransition(applyFilters);
        } else {
            applyFilters();
        }
    };

    chips.forEach((chip) => chip.addEventListener('click', () => selectCategory(chip.dataset.filter)));
    search?.addEventListener('input', applyFilters);

    // Links elsewhere on the page ("See snacks") jump to the menu pre-filtered.
    document.querySelectorAll('[data-filter-link]').forEach((link) => {
        link.addEventListener('click', () => selectCategory(link.dataset.filterLink));
    });

    document.addEventListener('change', (event) => {
        const scope = event.target.closest('[data-item]');
        const price = catalog[scope?.dataset.item]?.choices[event.target.value];

        if (price !== undefined) {
            scope.querySelectorAll('[data-price]').forEach((element) => {
                element.textContent = peso(price);
            });
        }
    });
}
