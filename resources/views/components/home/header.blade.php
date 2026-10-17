@props(['shop'])

@php($address = $shop['address'])

<header data-site-header class="site-header fixed inset-x-0 top-0 z-50 px-3 pt-3 sm:px-5">
    <div class="site-header-inner mx-auto flex max-w-7xl items-center justify-between gap-3 rounded-full border border-transparent py-2 pr-2 pl-2 sm:pl-3">
        <a href="#top" class="group flex items-center gap-3 rounded-full pr-2" aria-label="{{ $shop['name'] }}, back to top">
            <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="44" height="44" class="size-11 rounded-full shadow-md ring-2 ring-white transition duration-700 ease-spring group-hover:rotate-[-12deg]">
            <span class="leading-none">
                <span class="block font-display text-xl font-extrabold tracking-tight">RacNin</span>
                <span class="block text-[10px] font-bold tracking-[0.24em] text-ink-soft uppercase">Drink Series</span>
            </span>
        </a>

        <nav aria-label="Primary" class="hidden md:block">
            <ul class="flex items-center gap-1 text-sm font-semibold">
                <li><a href="#menu" class="nav-link">Menu</a></li>
                <li><a href="#story" class="nav-link">How it works</a></li>
                <li><a href="#combos" class="nav-link">Combos</a></li>
                <li><a href="#visit" class="nav-link">Visit</a></li>
            </ul>
        </nav>

        <div class="flex items-center gap-2">
            <x-status-pill :label="$shop['hours']['label']" class="hidden lg:inline-flex" />
            <button type="button" data-cart-open data-cart-target class="btn btn-primary py-2.5 pr-2.5 pl-4">
                <x-site-icon name="bag" class="size-4" />
                <span class="hidden sm:inline">Your order</span>
                <span data-cart-count class="cart-count">0</span>
            </button>
            <button type="button" popovertarget="mobile-nav" class="icon-btn bg-white/70 ring-1 ring-ink/10 md:hidden" aria-label="Open menu">
                <x-site-icon name="menu" />
            </button>
        </div>
    </div>

    <nav id="mobile-nav" popover aria-label="Mobile" class="mobile-nav">
        <div class="flex items-center justify-between">
            <x-status-pill :label="$shop['hours']['label']" />
            <button type="button" popovertarget="mobile-nav" popovertargetaction="hide" class="icon-btn" aria-label="Close menu">
                <x-site-icon name="x" />
            </button>
        </div>
        <ul class="mt-8 space-y-1 font-display text-4xl font-extrabold tracking-tight">
            <li><a href="#menu" class="block py-2">Menu</a></li>
            <li><a href="#story" class="block py-2">How it works</a></li>
            <li><a href="#combos" class="block py-2">Combos</a></li>
            <li><a href="#visit" class="block py-2">Visit us</a></li>
        </ul>
        <p class="mt-10 text-sm text-ink-soft">{{ $address['street'] }}, {{ $address['area'] }}, {{ $address['city'] }} {{ $address['zip'] }}<br>{{ $address['landmark'] }}</p>
    </nav>
</header>
