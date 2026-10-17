const STORAGE_KEY = 'racnin:cart';
const MAX_QUANTITY = 99;

export const peso = (amount) => `₱${amount.toLocaleString('en-PH')}`;

const keyOf = (id, choice) => `${id}::${choice}`;

/**
 * A tiny cart store persisted to localStorage. Prices always come from the
 * menu catalog, never from what was saved, so stale carts can't drift.
 */
export function createCart(catalog) {
    const listeners = new Set();
    let lines = restore();

    function isValid(line) {
        return (
            line &&
            Object.hasOwn(catalog, line.id) &&
            Object.hasOwn(catalog[line.id].choices, line.choice) &&
            Number.isInteger(line.qty) &&
            line.qty > 0
        );
    }

    function restore() {
        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '[]');

            return Array.isArray(saved) ? saved.filter(isValid) : [];
        } catch {
            return [];
        }
    }

    function commit(next) {
        lines = next;

        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(lines));
        } catch {
            // Private mode or storage disabled: the cart still works for this visit.
        }

        listeners.forEach((listener) => listener(cart));
    }

    const cart = {
        get lines() {
            return lines.map((line) => {
                const item = catalog[line.id];
                const unitPrice = item.choices[line.choice];

                return { ...line, key: keyOf(line.id, line.choice), item, unitPrice, total: unitPrice * line.qty };
            });
        },

        get count() {
            return lines.reduce((sum, line) => sum + line.qty, 0);
        },

        get subtotal() {
            return lines.reduce((sum, line) => sum + catalog[line.id].choices[line.choice] * line.qty, 0);
        },

        add(id, choice) {
            const item = catalog[id];

            if (!item) {
                return;
            }

            const resolved = Object.hasOwn(item.choices, choice) ? choice : Object.keys(item.choices)[0];
            const existing = lines.find((line) => line.id === id && line.choice === resolved);

            commit(
                existing
                    ? lines.map((line) => (line === existing ? { ...line, qty: Math.min(line.qty + 1, MAX_QUANTITY) } : line))
                    : [...lines, { id, choice: resolved, qty: 1 }],
            );
        },

        setQuantity(key, qty) {
            commit(
                qty <= 0
                    ? lines.filter((line) => keyOf(line.id, line.choice) !== key)
                    : lines.map((line) => (keyOf(line.id, line.choice) === key ? { ...line, qty: Math.min(qty, MAX_QUANTITY) } : line)),
            );
        },

        clear() {
            commit([]);
        },

        subscribe(listener) {
            listeners.add(listener);
            listener(cart);
        },
    };

    window.addEventListener('storage', (event) => {
        if (event.key === STORAGE_KEY) {
            lines = restore();
            listeners.forEach((listener) => listener(cart));
        }
    });

    return cart;
}
