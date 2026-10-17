const SLOT_MINUTES = 15;

const toMinutes = (time) => {
    const [hours, minutes] = time.split(':').map(Number);

    return hours * 60 + minutes;
};

export function formatTime(totalMinutes) {
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    const suffix = hours >= 12 ? 'PM' : 'AM';
    const hour = hours % 12 || 12;

    return minutes ? `${hour}:${String(minutes).padStart(2, '0')} ${suffix}` : `${hour} ${suffix}`;
}

/**
 * Minutes since midnight in the shop's own timezone, so visitors abroad
 * still see whether the shop in Hindang is open right now.
 */
function minutesNow(timeZone) {
    const parts = new Intl.DateTimeFormat('en-US', { timeZone, hour: 'numeric', minute: 'numeric', hourCycle: 'h23' }).formatToParts(new Date());
    const read = (type) => Number(parts.find((part) => part.type === type)?.value ?? 0);

    return read('hour') * 60 + read('minute');
}

export function shopStatus(shop) {
    const now = minutesNow(shop.timezone);
    const opensAt = toMinutes(shop.hours.open);
    const closesAt = toMinutes(shop.hours.close);
    const isOpen = now >= opensAt && now < closesAt;

    let label = `Closed · opens ${formatTime(opensAt)}`;

    if (isOpen) {
        label = closesAt - now <= 60 ? `Closing soon · ${formatTime(closesAt)}` : `Open now · until ${formatTime(closesAt)}`;
    }

    return { now, opensAt, closesAt, isOpen, label };
}

/**
 * Pickup times in 15 minute steps, starting about half an hour from now,
 * or from opening time (today or tomorrow) when the shop is closed.
 */
export function pickupSlots(shop) {
    const { now, opensAt, closesAt, isOpen } = shopStatus(shop);
    const lastSlot = closesAt - SLOT_MINUTES;
    const slots = [];
    let day = 'Today';
    let start = opensAt;

    if (isOpen && now <= lastSlot - 30) {
        slots.push({ value: 'As soon as possible', label: 'As soon as possible (about 15 min)' });
        start = Math.ceil((now + 30) / SLOT_MINUTES) * SLOT_MINUTES;
    } else if (now >= opensAt) {
        day = 'Tomorrow';
    }

    for (let minutes = start; minutes <= lastSlot; minutes += SLOT_MINUTES) {
        const value = `${day}, ${formatTime(minutes)}`;
        slots.push({ value, label: value });
    }

    return slots;
}

export function initShopStatus(shop) {
    const badges = document.querySelectorAll('[data-open-status]');

    const render = () => {
        const { isOpen, label } = shopStatus(shop);

        badges.forEach((badge) => {
            badge.dataset.state = isOpen ? 'open' : 'closed';
            badge.querySelector('[data-open-label]').textContent = label;
        });
    };

    render();
    setInterval(render, 60_000);
}
