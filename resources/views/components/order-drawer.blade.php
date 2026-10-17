@props(['shop', 'upsell'])

<dialog data-order class="order-drawer" aria-labelledby="order-title">
    <div class="flex h-full flex-col">
        <header class="flex items-center gap-3 border-b border-ink/8 px-5 py-4 sm:px-6">
            <button type="button" data-step-back hidden class="icon-btn" aria-label="Back to your order">
                <x-site-icon name="arrow-left" />
            </button>
            <div class="min-w-0 flex-1">
                <p data-step-label class="eyebrow text-brand-deep">Step 1 of 3</p>
                <h2 id="order-title" data-step-title class="font-display text-2xl font-extrabold tracking-tight">Your order</h2>
            </div>
            <button type="button" data-order-close class="icon-btn" aria-label="Close order">
                <x-site-icon name="x" />
            </button>
        </header>

        {{-- Step 1: the bag --}}
        <section data-panel="bag" class="flex min-h-0 flex-1 flex-col">
            <div class="flex-1 overflow-y-auto px-5 py-2 sm:px-6">
                <ul data-cart-lines class="divide-y divide-ink/8"></ul>

                <div data-cart-empty class="flex flex-col items-center py-14 text-center">
                    <div class="grid size-36 place-items-center rounded-full bg-latte/60">
                        <x-menu-art :art="['type' => 'frappe', 'layers' => [['#f3d9bd', 1]], 'whip' => '#fff8ef', 'drizzle' => '#f3d9bd']" class="h-28 w-auto opacity-80" />
                    </div>
                    <p class="mt-6 font-display text-2xl font-extrabold tracking-tight">Your cup is empty.</p>
                    <p class="mt-1 text-ink-soft">{{ $shop['callout'] }} Pick something good.</p>
                    <button type="button" data-browse class="btn btn-primary mt-6">
                        Browse the menu
                        <x-site-icon name="arrow-right" class="size-4" />
                    </button>
                </div>

                <div data-upsell hidden class="my-4 flex items-center gap-4 rounded-3xl bg-paper p-3 pr-4 ring-1 ring-ink/8">
                    <div class="grid size-16 shrink-0 place-items-center rounded-2xl" style="background: color-mix(in oklab, {{ $upsell['tint'] }} 22%, white)">
                        <x-menu-art :art="$upsell['art']" class="h-14 w-auto" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold">Add {{ $upsell['name'] }}?</p>
                        <p class="text-xs text-ink-soft">Perfect with any drink · ₱{{ reset($upsell['options']['choices']) }}</p>
                    </div>
                    <button type="button" data-add="{{ $upsell['id'] }}" class="add-btn">
                        <x-site-icon name="plus" class="size-4" />
                        <span>Add</span>
                    </button>
                </div>
            </div>

            <footer data-cart-footer class="space-y-4 border-t border-ink/8 bg-white/60 px-5 py-5 sm:px-6">
                <div class="flex items-baseline justify-between">
                    <span class="font-semibold text-ink-soft">Subtotal</span>
                    <span data-cart-subtotal class="font-display text-3xl font-extrabold tracking-tight">₱0</span>
                </div>
                <p class="flex items-center gap-2 text-xs text-ink-soft">
                    <x-site-icon name="check" class="size-4 text-olive-deep" />
                    No online payment. Pay at the counter when you pick up.
                </p>
                <button type="button" data-goto-details class="btn btn-primary btn-lg w-full">
                    Checkout
                    <x-site-icon name="arrow-right" class="size-4" />
                </button>
            </footer>
        </section>

        {{-- Step 2: who and when --}}
        <form data-panel="details" hidden class="flex min-h-0 flex-1 flex-col">
            <div class="flex-1 space-y-5 overflow-y-auto px-5 py-6 sm:px-6">
                <fieldset>
                    <legend class="label">How are you having it?</legend>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach (['Pickup' => 'Take it to go', 'Dine-in' => 'Tambay with us'] as $type => $hint)
                            <label class="option-card">
                                <input type="radio" name="type" value="{{ $type }}" class="sr-only" @checked($loop->first) required>
                                <span class="block font-bold">{{ $type }}</span>
                                <span class="block text-xs text-ink-soft">{{ $hint }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div>
                    <label for="order-name" class="label">Your name</label>
                    <input id="order-name" name="name" type="text" class="field" autocomplete="given-name" placeholder="Juan" required maxlength="40">
                </div>

                <div>
                    <label for="order-mobile" class="label">Mobile number</label>
                    <input id="order-mobile" name="mobile" type="tel" class="field" autocomplete="tel" inputmode="tel" placeholder="09XX XXX XXXX" required pattern="(\+?63|0)9\d{2}[\s\-]?\d{3}[\s\-]?\d{4}">
                    <p class="mt-1.5 text-xs text-ink-soft">So we can text you when it's ready.</p>
                </div>

                <div>
                    <label for="order-time" class="label">Pickup or arrival time</label>
                    <select id="order-time" name="time" data-pickup-time class="field" required></select>
                    <p class="mt-1.5 text-xs text-ink-soft">We're open {{ $shop['hours']['label'] }}.</p>
                </div>

                <div>
                    <label for="order-notes" class="label">Notes <span class="font-normal text-ink-soft">(optional)</span></label>
                    <textarea id="order-notes" name="notes" rows="3" class="field resize-none" maxlength="200" placeholder="Less ice, less sugar, extra sauce…"></textarea>
                </div>
            </div>

            <footer class="border-t border-ink/8 bg-white/60 px-5 py-5 sm:px-6">
                <button type="submit" class="btn btn-primary btn-lg w-full">
                    Place order · <span data-cart-subtotal>₱0</span>
                </button>
            </footer>
        </form>

        {{-- Step 3: send it to the shop --}}
        <section data-panel="done" hidden class="flex min-h-0 flex-1 flex-col">
            <div class="flex-1 overflow-y-auto px-5 py-6 sm:px-6">
                <div class="ticket">
                    <div class="flex items-center gap-3">
                        <span class="grid size-12 place-items-center rounded-full bg-olive text-ink">
                            <x-site-icon name="check" class="size-6" />
                        </span>
                        <div>
                            <p class="text-xs font-bold tracking-[0.2em] text-ink-soft uppercase">Order code</p>
                            <p data-order-code class="font-display text-3xl font-extrabold tracking-tight"></p>
                        </div>
                    </div>
                    <p class="mt-5 text-sm text-ink-soft">
                        One last step: <strong class="text-ink">send this to us</strong> so we can start mixing.
                        We'll reply to confirm.
                    </p>
                    <pre data-order-summary class="mt-5 overflow-x-auto rounded-2xl bg-paper p-4 font-sans text-sm leading-relaxed whitespace-pre-wrap"></pre>
                </div>
            </div>

            <footer class="space-y-2 border-t border-ink/8 bg-white/60 px-5 py-5 sm:px-6">
                <a data-send-messenger href="{{ $shop['messenger'] ?? $shop['facebook'] }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg w-full">
                    <x-site-icon name="message" class="size-5" />
                    Copy &amp; open Messenger
                </a>
                <div class="grid grid-cols-2 gap-2">
                    <a data-send-email href="mailto:{{ $shop['email'] }}" class="btn btn-ghost">
                        <x-site-icon name="mail" class="size-4" />
                        Email it
                    </a>
                    <button type="button" data-copy-order class="btn btn-ghost">
                        <x-site-icon name="copy" class="size-4" />
                        Copy
                    </button>
                </div>
                <button type="button" data-new-order class="w-full py-2 text-sm font-semibold text-ink-soft underline-offset-4 hover:text-ink hover:underline">
                    Start a new order
                </button>
            </footer>
        </section>
    </div>

    <template data-line-template>
        <li class="flex gap-4 py-4">
            <div data-line-art class="grid size-18 shrink-0 place-items-center overflow-hidden rounded-2xl p-1"></div>
            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <p data-line-name class="leading-tight font-bold"></p>
                    <p data-line-total class="font-display font-extrabold"></p>
                </div>
                <p data-line-choice class="text-sm text-ink-soft"></p>
                <div class="mt-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-1 rounded-full bg-white p-1 ring-1 ring-ink/10">
                        <button type="button" data-line-dec class="qty-btn" aria-label="Remove one">
                            <x-site-icon name="minus" class="size-4" />
                        </button>
                        <span data-line-qty class="w-7 text-center text-sm font-bold tabular-nums"></span>
                        <button type="button" data-line-inc class="qty-btn" aria-label="Add one more">
                            <x-site-icon name="plus" class="size-4" />
                        </button>
                    </div>
                    <button type="button" data-line-remove class="flex items-center gap-1 text-xs font-semibold text-ink-soft hover:text-chili">
                        <x-site-icon name="trash" class="size-3.5" />
                        Remove
                    </button>
                </div>
            </div>
        </li>
    </template>
</dialog>
