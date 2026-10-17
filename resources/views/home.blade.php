@php
    $address = $shop['address'];
    [$cheapest] = $menu->priceRange();

    // The order script prices the cart from this, so the menu lives in one place.
    $orderData = [
        'shop' => Arr::only($shop, ['name', 'email', 'messenger', 'facebook', 'timezone', 'hours']),
        'items' => $menu->catalog(),
    ];
@endphp

<x-layouts.site :title="$shop['name'].' · '.$shop['tagline'].' | Hindang, Leyte'">
    <x-slot:head>
        <meta name="description" content="Iced coffee, frappes, fruit sodas, snacks and rice meals from ₱{{ $cheapest }}. Order ahead from {{ $shop['name'] }}, {{ $address['landmark'] }} in {{ $address['area'] }}, Hindang, Leyte.">

        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $shop['name'] }} · {{ $shop['tagline'] }}">
        <meta property="og:description" content="Ice coffee, non-coffee, frappe, fruit soda and rice meals. {{ $shop['callout'] }}">
        <meta property="og:image" content="{{ asset('images/logo-mark.png') }}">

        <x-home.structured-data :shop="$shop" :menu="$menu" />
    </x-slot:head>

    {{-- The first 24px of the page: motion.js watches it to know when the page has scrolled --}}
    <div data-page-top class="pointer-events-none absolute inset-x-0 top-0 h-6" aria-hidden="true"></div>

    <a href="#menu" class="sr-only z-[100] rounded-full bg-ink px-5 py-3 font-bold text-cream focus:not-sr-only focus:fixed focus:top-4 focus:left-4">Skip to the menu</a>

    <div class="scroll-progress fixed inset-x-0 top-0 z-[60] h-1 bg-linear-to-r from-amber via-brand to-chili" aria-hidden="true"></div>

    <x-home.header :shop="$shop" />

    <main id="top">
        <x-home.hero :shop="$shop" :menu="$menu" />
        <x-home.marquee :shop="$shop" :menu="$menu" />
        <x-home.featured :menu="$menu" />
        <x-home.story :shop="$shop" :menu="$menu" />
        <x-home.menu :menu="$menu" />
        <x-home.combos :menu="$menu" />
        <x-home.manifesto :shop="$shop" :menu="$menu" />
        <x-home.visit :shop="$shop" />
    </main>

    <x-home.footer :shop="$shop" :staff-login-url="$staffLoginUrl" />

    <x-home.order-bar />

    <x-order-drawer :shop="$shop" :menu="$menu" upsell="siomai-sa-tisa" />

    <div data-toast popover="manual" role="status" aria-live="polite" class="toast"></div>

    <script type="application/json" id="racnin-data">@json($orderData)</script>
</x-layouts.site>
