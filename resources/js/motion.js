/**
 * Motion that needs a little JavaScript next to the CSS scroll-driven
 * animations: reveals as things scroll into view, the header state, and
 * small interactions.
 */
export function initMotion() {
    const prefersReducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Sections and cards below the fold fade up the first time they scroll into view. An observer
    // does this once per element, where a scroll-linked animation would cost work on every frame.
    // Anything already on screen when the page loads is left alone, so nothing blinks.
    if (!prefersReducedMotion && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        entry.target.classList.replace('is-pending', 'is-revealing');
                        observer.unobserve(entry.target);
                    }
                }
            },
            { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
        );

        for (const element of document.querySelectorAll('[data-reveal]')) {
            if (element.getBoundingClientRect().top > innerHeight) {
                element.classList.add('is-pending');
                observer.observe(element);
            }
        }
    }

    // The header turns into a glass pill once the top of the page scrolls away (see .site-header-inner).
    // An observer only runs when that changes; a scroll listener would run, and force a layout, every frame.
    const header = document.querySelector('[data-site-header]');
    const pageTop = document.querySelector('[data-page-top]');

    if (header && pageTop) {
        new IntersectionObserver(([entry]) => header.toggleAttribute('data-scrolled', !entry.isIntersecting)).observe(pageTop);
    }

    // Close the mobile menu once a destination is picked.
    document.querySelectorAll('[popover] a[href^="#"]').forEach((link) => {
        link.addEventListener('click', () => link.closest('[popover]').hidePopover());
    });

    // Soft light that follows the pointer across menu cards, moved at most once per frame.
    if (matchMedia('(hover: hover)').matches) {
        let pointer = null;
        let frame = 0;

        const moveSheen = () => {
            frame = 0;
            const card = pointer.target.closest?.('[data-sheen]');

            if (card) {
                const box = card.getBoundingClientRect();
                card.style.setProperty('--mx', `${pointer.clientX - box.left}px`);
                card.style.setProperty('--my', `${pointer.clientY - box.top}px`);
            }
        };

        document.addEventListener(
            'pointermove',
            (event) => {
                pointer = event;
                frame ||= requestAnimationFrame(moveSheen);
            },
            { passive: true },
        );
    }
}
