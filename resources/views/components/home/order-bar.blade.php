{{-- Phones get a bar at the bottom once something is in the order --}}
<div data-order-bar hidden class="fixed inset-x-3 bottom-[max(0.75rem,env(safe-area-inset-bottom))] z-40 md:hidden">
    <button type="button" data-cart-open data-cart-target class="flex w-full items-center justify-between rounded-full bg-ink py-3 pr-5 pl-3 text-cream shadow-[0_20px_40px_-12px_rgb(31_21_16/0.6)]">
        <span class="flex items-center gap-3 font-bold">
            <span data-cart-count class="cart-count bg-brand text-ink">0</span>
            View your order
        </span>
        <span data-cart-subtotal class="font-display text-lg font-extrabold">₱0</span>
    </button>
</div>
