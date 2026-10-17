import { peso } from './cart';
import { pickupSlots } from './shop';

const CUSTOMER_KEY = 'racnin:customer';
const MEAL_CATEGORIES = ['snacks', 'rice-meals', 'combos'];

const STEPS = {
    bag: { label: 'Step 1 of 3', title: 'Your order' },
    details: { label: 'Step 2 of 3', title: 'Pickup details' },
    done: { label: 'Step 3 of 3', title: 'Almost there!' },
};

const describe = (line) => (line.item.label ? `${line.item.name} (${line.choice})` : line.item.name);

/**
 * The order drawer: bag, pickup details, then a ready-to-send summary.
 * There's no backend yet, so the order goes to the shop via Messenger or email.
 */
export function initOrder({ cart, items, shop }) {
    const drawer = document.querySelector('[data-order]');

    if (!drawer) {
        return;
    }

    const find = (selector) => drawer.querySelector(selector);
    const panels = [...drawer.querySelectorAll('[data-panel]')];
    const form = find('[data-panel="details"]');
    const lineList = find('[data-cart-lines]');
    const lineTemplate = find('[data-line-template]');
    const toastElement = document.querySelector('[data-toast]');
    const prefersReducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
    let lastOrder = null;
    let toastTimer;

    function toast(message) {
        clearTimeout(toastTimer);
        toastElement.textContent = message;

        // Re-showing moves the toast to the top of the top layer, above the drawer.
        if (toastElement.matches(':popover-open')) {
            toastElement.hidePopover();
        }

        toastElement.showPopover();
        toastTimer = setTimeout(() => toastElement.hidePopover(), 2600);
    }

    function showStep(step) {
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.panel !== step;
        });
        find('[data-step-label]').textContent = STEPS[step].label;
        find('[data-step-title]').textContent = STEPS[step].title;
        find('[data-step-back]').hidden = step !== 'details';
    }

    function openDrawer() {
        showStep(lastOrder && cart.count === 0 ? 'done' : 'bag');

        if (toastElement.matches(':popover-open')) {
            toastElement.hidePopover();
        }

        if (!drawer.open) {
            drawer.showModal();
        }
    }

    function artFor(id) {
        const source = document.querySelector(`[data-menu-grid] [data-item="${id}"] [data-art]`);

        if (!source) {
            return document.createElement('span');
        }

        // Keep what kind of picture it is (drawing, photo or combo) and fit it to the line.
        const art = source.cloneNode(true);
        const kind = ['menu-art-combo', 'menu-photo', 'menu-art'].find((name) => art.classList.contains(name));
        art.removeAttribute('data-art');
        art.setAttribute('class', `${kind} size-full`);

        return art;
    }

    function renderLines() {
        lineList.replaceChildren(
            ...cart.lines.map((line) => {
                const row = lineTemplate.content.firstElementChild.cloneNode(true);
                const art = row.querySelector('[data-line-art]');

                row.dataset.lineKey = line.key;
                art.style.background = `color-mix(in oklab, ${line.item.tint} 20%, white)`;
                art.append(artFor(line.id));
                row.querySelector('[data-line-name]').textContent = line.item.name;
                row.querySelector('[data-line-choice]').textContent = line.item.label ? `${line.item.label}: ${line.choice} · ${peso(line.unitPrice)} each` : `${peso(line.unitPrice)} each`;
                row.querySelector('[data-line-qty]').textContent = line.qty;
                row.querySelector('[data-line-total]').textContent = peso(line.total);

                return row;
            }),
        );
    }

    function render() {
        const count = cart.count;
        const isEmpty = count === 0;

        document.querySelectorAll('[data-cart-count]').forEach((element) => {
            element.textContent = count;
        });
        document.querySelectorAll('[data-cart-subtotal]').forEach((element) => {
            element.textContent = peso(cart.subtotal);
        });
        document.querySelectorAll('[data-order-bar]').forEach((element) => {
            element.hidden = isEmpty;
        });

        renderLines();
        find('[data-cart-empty]').hidden = !isEmpty;
        find('[data-cart-footer]').hidden = isEmpty;
        find('[data-upsell]').hidden = isEmpty || cart.lines.some((line) => MEAL_CATEGORIES.includes(line.item.category));
    }

    function bumpCounters() {
        document.querySelectorAll('[data-cart-count]').forEach((element) => {
            element.classList.remove('is-bumped');
            void element.offsetWidth;
            element.classList.add('is-bumped');
        });
    }

    function flyToCart(art) {
        const target = [...document.querySelectorAll('[data-cart-target]')].find((element) => element.getClientRects().length > 0);

        if (!art || !target || prefersReducedMotion.matches) {
            bumpCounters();

            return;
        }

        const from = art.getBoundingClientRect();
        const to = target.getBoundingClientRect();
        const ghost = art.cloneNode(true);
        const deltaX = to.left + to.width / 2 - (from.left + from.width / 2);
        const deltaY = to.top + to.height / 2 - (from.top + from.height / 2);

        Object.assign(ghost.style, {
            position: 'fixed',
            left: `${from.left}px`,
            top: `${from.top}px`,
            width: `${from.width}px`,
            height: `${from.height}px`,
            margin: '0',
            zIndex: '80',
            pointerEvents: 'none',
        });
        document.body.append(ghost);

        ghost
            .animate(
                [
                    { transform: 'translate(0, 0) scale(1) rotate(0deg)', opacity: 1 },
                    { transform: `translate(${deltaX * 0.35}px, ${deltaY * 0.35 - 140}px) scale(0.55) rotate(-14deg)`, opacity: 1, offset: 0.45 },
                    { transform: `translate(${deltaX}px, ${deltaY}px) scale(0.1) rotate(10deg)`, opacity: 0.3 },
                ],
                { duration: 800, easing: 'cubic-bezier(0.55, 0, 0.45, 1)' },
            )
            .finished.finally(() => {
                ghost.remove();
                bumpCounters();
            });
    }

    function fillPickupTimes() {
        const select = find('[data-pickup-time]');
        const previous = select.value;

        select.replaceChildren(...pickupSlots(shop).map(({ value, label }) => new Option(label, value, false, value === previous)));
    }

    function restoreCustomer() {
        try {
            const saved = JSON.parse(localStorage.getItem(CUSTOMER_KEY) ?? '{}');
            form.elements.name.value ||= saved.name ?? '';
            form.elements.mobile.value ||= saved.mobile ?? '';
        } catch {
            // Nothing saved yet.
        }
    }

    function rememberCustomer(name, mobile) {
        try {
            localStorage.setItem(CUSTOMER_KEY, JSON.stringify({ name, mobile }));
        } catch {
            // Storage unavailable; the guest just types it again next time.
        }
    }

    function buildOrder(data) {
        const code = `RN-${Date.now().toString(36).slice(-5).toUpperCase()}`;
        const lines = cart.lines;
        const total = cart.subtotal;
        const notes = data.get('notes').trim();

        const text = [
            `RacNin order ${code}`,
            '',
            ...lines.map((line) => `${line.qty} × ${describe(line)} · ${peso(line.total)}`),
            '',
            `Total: ${peso(total)} (pay at the counter)`,
            '',
            `Name: ${data.get('name').trim()}`,
            `Mobile: ${data.get('mobile').trim()}`,
            `${data.get('type')}: ${data.get('time')}`,
            notes ? `Notes: ${notes}` : null,
        ]
            .filter((row) => row !== null)
            .join('\n');

        return { code, text };
    }

    function showOrder(order) {
        find('[data-order-code]').textContent = order.code;
        find('[data-order-summary]').textContent = order.text;
        find('[data-send-email]').href = `mailto:${shop.email}?subject=${encodeURIComponent(`New order ${order.code}`)}&body=${encodeURIComponent(order.text)}`;
        showStep('done');
    }

    async function copy(text) {
        try {
            await navigator.clipboard.writeText(text);

            return true;
        } catch {
            // Clipboard API needs HTTPS; fall back to a selection copy inside the dialog.
            const area = Object.assign(document.createElement('textarea'), { value: text, readOnly: true });
            area.style.cssText = 'position:fixed;opacity:0;pointer-events:none';
            drawer.append(area);
            area.select();
            const copied = document.execCommand('copy');
            area.remove();

            return copied;
        }
    }

    // Adding from anywhere on the page
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-add]');

        if (!button) {
            return;
        }

        const id = button.dataset.add;
        const scope = button.closest('[data-item]');
        const choice = scope?.querySelector('input[type="radio"]:checked')?.value ?? scope?.querySelector('[data-choice]')?.value;

        cart.add(id, choice);

        button.classList.remove('is-added');
        void button.offsetWidth;
        button.classList.add('is-added');

        const label = items[id].label && choice ? ` (${choice})` : '';
        toast(`${items[id].name}${label} added to your order`);

        if (!drawer.open) {
            flyToCart(scope?.querySelector('[data-art]'));
        }
    });

    document.querySelectorAll('[data-cart-open]').forEach((button) => button.addEventListener('click', openDrawer));

    find('[data-order-close]').addEventListener('click', () => drawer.close());

    find('[data-browse]').addEventListener('click', () => {
        drawer.close();
        document.getElementById('menu').scrollIntoView();
    });

    // Clicking the dimmed backdrop closes the drawer.
    drawer.addEventListener('click', (event) => {
        const box = drawer.getBoundingClientRect();
        const outside = event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom;

        if (event.target === drawer && outside) {
            drawer.close();
        }
    });

    lineList.addEventListener('click', (event) => {
        const row = event.target.closest('[data-line-key]');
        const line = row && cart.lines.find((candidate) => candidate.key === row.dataset.lineKey);

        if (!line) {
            return;
        }

        if (event.target.closest('[data-line-inc]')) {
            cart.setQuantity(line.key, line.qty + 1);
        } else if (event.target.closest('[data-line-dec]')) {
            cart.setQuantity(line.key, line.qty - 1);
        } else if (event.target.closest('[data-line-remove]')) {
            cart.setQuantity(line.key, 0);
        }
    });

    find('[data-goto-details]').addEventListener('click', () => {
        fillPickupTimes();
        restoreCustomer();
        showStep('details');
        form.elements.name.focus();
    });

    find('[data-step-back]').addEventListener('click', () => showStep('bag'));

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        if (cart.count === 0) {
            showStep('bag');

            return;
        }

        const data = new FormData(form);
        lastOrder = buildOrder(data);
        rememberCustomer(data.get('name').trim(), data.get('mobile').trim());
        cart.clear();
        form.elements.notes.value = '';
        showOrder(lastOrder);
    });

    find('[data-send-messenger]').addEventListener('click', async () => {
        if (await copy(lastOrder.text)) {
            toast('Order copied. Paste it in the chat and hit send!');
        }
    });

    find('[data-copy-order]').addEventListener('click', async () => {
        toast((await copy(lastOrder.text)) ? 'Order copied to your clipboard' : 'Copy failed. Select the text and copy it manually.');
    });

    find('[data-new-order]').addEventListener('click', () => {
        lastOrder = null;
        showStep('bag');
    });

    cart.subscribe(render);
}
