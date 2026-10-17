/**
 * Progressive enhancements around the CSS scroll-driven animations:
 * fallbacks for browsers without scroll timelines, plus small interactions.
 */
export function initMotion() {
    const root = document.documentElement;
    const prefersReducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const supportsScrollTimeline = CSS.supports('animation-timeline: view()');

    if (!supportsScrollTimeline && !prefersReducedMotion && 'IntersectionObserver' in window) {
        root.classList.add('reveal-fallback');

        const observer = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                }
            },
            { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
        );

        document.querySelectorAll('[data-reveal]').forEach((element) => observer.observe(element));
    }

    // The header animates on a scroll timeline where supported; this flag covers everyone else.
    const header = document.querySelector('[data-site-header]');
    const syncHeader = () => header?.toggleAttribute('data-scrolled', window.scrollY > 24);
    syncHeader();
    window.addEventListener('scroll', syncHeader, { passive: true });

    // Close the mobile menu once a destination is picked.
    document.querySelectorAll('[popover] a[href^="#"]').forEach((link) => {
        link.addEventListener('click', () => link.closest('[popover]').hidePopover());
    });

    // Soft light that follows the pointer across menu cards.
    if (matchMedia('(hover: hover)').matches) {
        document.addEventListener(
            'pointermove',
            (event) => {
                const card = event.target.closest?.('[data-sheen]');

                if (card) {
                    const box = card.getBoundingClientRect();
                    card.style.setProperty('--mx', `${event.clientX - box.left}px`);
                    card.style.setProperty('--my', `${event.clientY - box.top}px`);
                }
            },
            { passive: true },
        );
    }
}
