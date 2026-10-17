@php
    $menuCount = count($items);
    $categoryCounts = collect($items)->countBy('category');
    $address = $shop['address'];
    $fullAddress = "{$address['street']}, {$address['area']}, {$address['city']} {$address['zip']}";
    $from = fn (string $id): int => reset($items[$id]['options']['choices']);

    $heroFloats = [
        ['id' => 'strawberry-milk', 'class' => 'hero-float-a top-[4%] -left-[3%] w-[31%]', 'tilt' => '-6deg', 'delay' => '0s'],
        ['id' => 'dirty-matcha', 'class' => 'hero-float-b top-[40%] -right-[4%] w-[29%]', 'tilt' => '5deg', 'delay' => '-2.4s'],
        ['id' => 'mango-soda', 'class' => 'hero-float-c -bottom-[3%] left-[8%] w-[27%]', 'tilt' => '-3deg', 'delay' => '-4.1s'],
    ];

    $bento = [
        ['id' => 'combo-soda-siomai', 'theme' => 'bg-ink text-cream', 'button' => 'add-btn-light'],
        ['id' => 'combo-coffee-siomai', 'theme' => 'bg-olive text-ink', 'button' => ''],
        ['id' => 'combo-frappe-fries', 'theme' => 'bg-brand text-ink', 'button' => ''],
        ['id' => 'buldak-carbonara', 'theme' => 'bg-chili text-white', 'button' => 'add-btn-light'],
    ];

    $manifesto = 'Every cup is mixed when you order it. Every rice meal is cooked fresh. And almost everything costs *₱50* or less, so the whole barkada can come back *tomorrow.*';

    $marqueeDrinks = collect($items)->whereNotIn('category', ['combos'])->pluck('name')->all();
    $marqueeTaglines = [$shop['tagline'], $shop['callout'], $shop['motto'], "{$address['landmark']}, Hindang", "Open {$shop['hours']['label']}"];

    $payload = [
        'shop' => collect($shop)->only(['name', 'email', 'messenger', 'facebook', 'timezone', 'hours']),
        'items' => collect($items)->map(fn (array $item): array => [
            'name' => $item['name'],
            'category' => $item['category'],
            'tint' => $item['tint'],
            'label' => $item['options']['label'],
            'choices' => $item['options']['choices'],
        ]),
    ];

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'CafeOrCoffeeShop',
        'name' => $shop['name'],
        'slogan' => $shop['tagline'],
        'email' => $shop['email'],
        'image' => asset('images/logo-mark.png'),
        'priceRange' => '₱25–₱79',
        'servesCuisine' => ['Coffee', 'Frappe', 'Fruit Soda', 'Filipino'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => "{$address['street']}, {$address['area']}",
            'addressLocality' => 'Hindang',
            'addressRegion' => 'Leyte',
            'postalCode' => $address['zip'],
            'addressCountry' => 'PH',
        ],
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens' => $shop['hours']['open'],
            'closes' => $shop['hours']['close'],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <title>{{ $shop['name'] }} · {{ $shop['tagline'] }} | Hindang, Leyte</title>
        <meta name="description" content="Iced coffee, frappes, fruit sodas, snacks and rice meals from ₱25. Order ahead from {{ $shop['name'] }}, {{ $address['landmark'] }} in {{ $address['area'] }}, Hindang, Leyte.">
        <meta name="theme-color" content="#fff8ef">

        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $shop['name'] }} · {{ $shop['tagline'] }}">
        <meta property="og:description" content="Ice coffee, non-coffee, frappe, fruit soda and rice meals. {{ $shop['callout'] }}">
        <meta property="og:image" content="{{ asset('images/logo-mark.png') }}">

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
        <link rel="preload" as="image" href="{{ asset('images/logo-mark.webp') }}" type="image/webp">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script type="application/ld+json">@json($structuredData)</script>
    </head>
    <body class="min-h-screen overflow-x-clip">
        <x-menu-art-defs />

        <a href="#menu" class="sr-only z-[100] rounded-full bg-ink px-5 py-3 font-bold text-cream focus:not-sr-only focus:fixed focus:top-4 focus:left-4">Skip to the menu</a>

        <div class="scroll-progress fixed inset-x-0 top-0 z-[60] h-1 bg-linear-to-r from-amber via-brand to-chili" aria-hidden="true"></div>

        {{-- Header --}}
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
                    <span data-open-status class="status-pill hidden lg:inline-flex">
                        <span class="status-dot"></span>
                        <span data-open-label>{{ $shop['hours']['label'] }}</span>
                    </span>
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
                    <span data-open-status class="status-pill">
                        <span class="status-dot"></span>
                        <span data-open-label>{{ $shop['hours']['label'] }}</span>
                    </span>
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
                <p class="mt-10 text-sm text-ink-soft">{{ $fullAddress }}<br>{{ $address['landmark'] }}</p>
            </nav>
        </header>

        <main id="top">
            {{-- Hero --}}
            <section class="hero relative isolate overflow-hidden pt-28 pb-24 sm:pt-36 lg:pb-32">
                <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
                    <div class="absolute -top-40 -right-40 size-[44rem] rounded-full bg-amber/35 blur-3xl"></div>
                    <div class="absolute top-1/3 -left-60 size-[36rem] rounded-full bg-olive/25 blur-3xl"></div>
                    <div class="dots absolute inset-0 [mask-image:radial-gradient(ellipse_at_center,black,transparent_70%)] opacity-50"></div>
                </div>

                <div class="mx-auto grid max-w-7xl items-center gap-16 px-5 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:gap-10">
                    <div class="hero-copy">
                        <a href="#featured" class="group inline-flex items-center gap-2 rounded-full bg-white/70 py-1.5 pr-4 pl-1.5 text-sm font-semibold ring-1 ring-ink/10 backdrop-blur transition hover:ring-ink/25">
                            <span class="rounded-full bg-ink px-2.5 py-1 text-xs font-bold text-cream">New</span>
                            Lumpia Silog just dropped
                            <x-site-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5" />
                        </a>

                        <h1 class="mt-8 font-display text-[clamp(3.4rem,9.5vw,7.8rem)] leading-[0.86] font-extrabold tracking-[-0.045em]">
                            <span class="hero-line block">Drink your</span>
                            <span class="hero-line block">
                                own
                                <span class="relative inline-block font-serif font-normal tracking-[-0.02em] text-brand italic">
                                    story.
                                    <svg class="absolute -bottom-[0.08em] left-0 h-[0.18em] w-full text-ink" viewBox="0 0 300 20" preserveAspectRatio="none" aria-hidden="true">
                                        <path class="squiggle" d="M3 14 C 40 4, 70 4, 100 12 S 160 20, 200 10 S 260 2, 297 10" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" pathLength="1" />
                                    </svg>
                                </span>
                            </span>
                        </h1>

                        <p class="mt-8 max-w-xl text-lg leading-relaxed text-ink-soft sm:text-xl">
                            Iced coffee, creamy frappes, fizzy fruit sodas and honest rice meals, mixed to order and mostly
                            <strong class="font-bold text-ink">₱50 and under</strong>. Find us {{ Str::lcfirst($address['landmark']) }} in {{ $address['area'] }}, Hindang.
                        </p>

                        <div class="mt-10 flex flex-wrap items-center gap-3">
                            <a href="#menu" class="btn btn-primary btn-lg group">
                                Start your order
                                <x-site-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" />
                            </a>
                            <a href="#visit" class="btn btn-ghost btn-lg">
                                <x-site-icon name="map-pin" class="size-4" />
                                Find us
                            </a>
                        </div>

                        <dl class="mt-14 grid max-w-lg grid-cols-3 gap-6 border-t border-ink/10 pt-6">
                            <div>
                                <dt class="text-xs font-semibold text-ink-soft">Starts at</dt>
                                <dd class="font-display text-3xl font-extrabold tracking-tight">₱{{ $from('siomai-sa-tisa') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-ink-soft">Open daily</dt>
                                <dd class="font-display text-3xl font-extrabold tracking-tight">10–9</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-ink-soft">On the menu</dt>
                                <dd class="font-display text-3xl font-extrabold tracking-tight">{{ $menuCount }}+</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="hero-visual relative mx-auto aspect-square w-full max-w-[34rem]">
                        <div class="absolute inset-[6%] rounded-full bg-radial from-amber/60 via-brand/20 to-transparent blur-2xl" aria-hidden="true"></div>

                        <svg class="hero-ring absolute inset-0 size-full text-ink" viewBox="0 0 400 400" aria-hidden="true">
                            <defs>
                                <path id="hero-ring-path" d="M200 200 m-178 0 a178 178 0 1 1 356 0 a178 178 0 1 1 -356 0" />
                            </defs>
                            <circle cx="200" cy="200" r="194" fill="none" stroke="currentColor" stroke-opacity=".12" stroke-dasharray="2 8" />
                            <text class="font-display" font-size="15.5" font-weight="800" letter-spacing="5" fill="currentColor">
                                <textPath href="#hero-ring-path" textLength="1110" lengthAdjust="spacing">{{ Str::upper($shop['tagline']) }} ✦ {{ Str::upper($shop['callout']) }} ✦ {{ Str::upper($shop['motto']) }} ✦</textPath>
                            </text>
                        </svg>

                        <img src="{{ asset('images/logo-mark.webp') }}" alt="{{ $shop['name'] }} logo: a frappe with a raccoon on the cup and the words Drink Your Own Story" width="512" height="512" fetchpriority="high"
                            class="hero-badge absolute inset-[13%] size-[74%] rounded-full shadow-[0_40px_80px_-30px_rgb(31_21_16/0.55)]">

                        @foreach ($heroFloats as $float)
                            <div class="hero-float {{ $float['class'] }} absolute">
                                <div class="animate-float rounded-[1.6rem] bg-white/70 p-2.5 pb-3 shadow-[0_24px_50px_-20px_rgb(31_21_16/0.45)] ring-1 ring-white backdrop-blur-xl" style="--float-tilt: {{ $float['tilt'] }}; animation-delay: {{ $float['delay'] }}">
                                    <div class="rounded-[1.1rem] px-2 pt-2" style="background: color-mix(in oklab, {{ $items[$float['id']]['tint'] }} 22%, white)">
                                        <x-menu-art :art="$items[$float['id']]['art']" class="mx-auto h-auto w-full" />
                                    </div>
                                    <p class="mt-2 truncate px-1 text-[11px] leading-tight font-bold sm:text-xs">{{ $items[$float['id']]['name'] }}</p>
                                    <p class="px-1 text-[11px] text-ink-soft">from ₱{{ $from($float['id']) }}</p>
                                </div>
                            </div>
                        @endforeach

                        <div class="hero-sticker absolute top-[2%] right-[6%] rotate-12 rounded-full bg-olive px-4 py-2.5 font-display text-base font-extrabold tracking-tight shadow-lg ring-4 ring-cream sm:text-lg">
                            {{ $shop['callout'] }}
                        </div>
                    </div>
                </div>
            </section>

            {{-- Crossing marquee bands --}}
            <div class="relative z-10 -mt-10 mb-4 h-40 overflow-x-clip sm:h-44" aria-hidden="true">
                <div class="band band-a absolute inset-x-[-5%] top-6 overflow-hidden bg-ink py-4 text-cream shadow-xl">
                    <div class="flex w-max animate-marquee">
                        @foreach ([1, 2] as $copy)
                            <ul class="flex shrink-0 items-center gap-8 pr-8 font-display text-2xl font-bold tracking-tight sm:text-3xl">
                                @foreach ($marqueeDrinks as $name)
                                    <li>{{ $name }}</li>
                                    <li class="text-brand">✦</li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
                <div class="band band-b absolute inset-x-[-5%] top-16 overflow-hidden bg-brand py-3 text-ink sm:top-18">
                    <div class="flex w-max animate-marquee-reverse">
                        @foreach ([1, 2, 3] as $copy)
                            <ul class="flex shrink-0 items-center gap-8 pr-8 text-sm font-extrabold tracking-[0.2em] uppercase sm:text-base">
                                @foreach ($marqueeTaglines as $line)
                                    <li>{{ $line }}</li>
                                    <li>●</li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- New: Lumpia Silog --}}
            <section id="featured" class="relative isolate overflow-hidden bg-amber py-24 sm:py-32" aria-labelledby="featured-title">
                <div class="dots absolute inset-0 -z-10 opacity-40" aria-hidden="true"></div>
                <div data-item="lumpia-silog" class="mx-auto grid max-w-7xl items-center gap-14 px-5 sm:px-8 lg:grid-cols-2">
                    <div data-reveal>
                        <p class="eyebrow">
                            <span class="rounded-full bg-ink px-3 py-1 tracking-[0.18em] text-cream">New</span>
                            On the RacNin menu
                        </p>
                        <h2 id="featured-title" class="mt-6 font-display text-[clamp(3.8rem,11vw,9rem)] leading-[0.8] font-extrabold tracking-[-0.05em] uppercase">
                            <span class="feature-line block font-serif text-[0.62em] font-normal tracking-[-0.02em] text-cream normal-case italic">Ultimate</span>
                            <span class="feature-line feature-line-reverse block">Lumpia</span>
                            <span class="feature-line block">Silog</span>
                        </h2>
                        <p class="mt-8 max-w-md text-lg leading-relaxed font-medium">
                            Garlic rice, a sunny-side-up egg and golden, crackly lumpia. Simple. Satisfying. Addictive.
                        </p>
                        <ul class="mt-6 flex flex-wrap gap-2 text-sm font-bold">
                            @foreach (['Garlic rice', 'Fried egg', 'Crispy lumpia'] as $part)
                                <li class="flex items-center gap-1.5 rounded-full bg-cream/70 px-3.5 py-2 ring-1 ring-ink/10">
                                    <x-site-icon name="check" class="size-4 text-olive-deep" />
                                    {{ $part }}
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-10 flex flex-wrap items-center gap-3">
                            <button type="button" data-add="lumpia-silog" class="btn btn-primary btn-lg">
                                <x-site-icon name="plus" class="size-4" />
                                Add to order · ₱{{ $from('lumpia-silog') }}
                            </button>
                            <a href="#menu" data-filter-link="rice-meals" class="btn btn-ghost btn-lg">More rice meals</a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="checker absolute inset-x-[8%] bottom-[8%] h-[34%] -rotate-3 rounded-[2rem] opacity-90 shadow-xl" aria-hidden="true"></div>
                        <div class="feature-art relative">
                            <x-menu-art :art="$items['lumpia-silog']['art']" data-art class="mx-auto h-auto w-full max-w-[36rem] drop-shadow-[0_30px_30px_rgb(31_21_16/0.3)]" />
                        </div>
                        <div class="feature-price absolute top-[2%] right-[2%] grid size-28 rotate-12 place-items-center rounded-full bg-ink text-center text-cream shadow-2xl ring-4 ring-amber sm:size-36">
                            <div>
                                <p class="text-[10px] font-bold tracking-[0.25em] text-amber uppercase">Only</p>
                                <p class="font-display text-4xl font-extrabold tracking-tight sm:text-5xl">₱{{ $from('lumpia-silog') }}</p>
                            </div>
                        </div>
                        <div class="absolute bottom-[2%] left-[2%] -rotate-6 rounded-2xl bg-cream px-4 py-3 shadow-xl ring-1 ring-ink/10">
                            <p class="text-[10px] font-bold tracking-[0.2em] text-brand-deep uppercase">Perfect combo</p>
                            <p class="font-display text-lg leading-tight font-extrabold">Rice, egg &amp; lumpia</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- How it works: pinned horizontal story --}}
            <section id="story" class="story relative bg-paper" aria-labelledby="story-title">
                <div class="story-sticky py-16">
                    <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-5 pb-8 sm:px-8">
                        <p class="eyebrow text-brand-deep">
                            <x-site-icon name="sparkles" class="size-4" />
                            How it works
                        </p>
                        <div class="flex items-center gap-3 text-xs font-bold text-ink-soft">
                            <span class="hidden sm:inline">Keep scrolling</span>
                            <span class="relative h-1.5 w-24 overflow-hidden rounded-full bg-ink/10 sm:w-40">
                                <span class="story-progress absolute inset-0 origin-left rounded-full bg-brand"></span>
                            </span>
                        </div>
                    </div>

                    <ol class="story-track no-scrollbar flex gap-5 px-5 sm:gap-8 sm:px-8 lg:px-[max(2rem,calc((100vw-80rem)/2+2rem))]">
                        <li class="story-panel justify-center bg-transparent p-0! sm:p-0!">
                            <h2 id="story-title" class="font-display text-[clamp(3rem,7vw,6.2rem)] leading-[0.88] font-extrabold tracking-[-0.045em]">
                                Your story,<br>
                                <span class="font-serif font-normal text-brand italic">four sips</span> long.
                            </h2>
                            <p class="mt-6 max-w-sm text-lg text-ink-soft">From base to bite to pickup, this is how ordering at RacNin works.</p>
                            <p class="mt-8 flex items-center gap-2 text-sm font-bold">
                                Scroll
                                <x-site-icon name="arrow-right" class="size-4 animate-pulse" />
                            </p>
                        </li>

                        <li class="story-panel bg-white ring-1 ring-ink/8">
                            <div class="relative z-10 max-w-[62%]">
                                <span class="story-number">01</span>
                                <h3 class="story-heading">Pick your base</h3>
                                <p class="mt-3 text-ink-soft">Coffee kick, creamy non-coffee, a thick frappe or a fizzy fruit soda.</p>
                                <ul class="mt-6 flex flex-wrap gap-2">
                                    @foreach (['iced-coffee', 'non-coffee', 'frappe', 'fruit-soda'] as $category)
                                        <li><a href="#menu" data-filter-link="{{ $category }}" class="chip">{{ $categories[$category] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <x-menu-art :art="$items['cafe-mocha']['art']" class="absolute -right-6 -bottom-4 h-[60%] w-auto rotate-6" />
                        </li>

                        <li class="story-panel bg-olive">
                            <div class="relative z-10">
                                <span class="story-number">02</span>
                                <h3 class="story-heading">Size it up</h3>
                                <p class="mt-3 max-w-xs text-ink/75">16 oz for a quick fix. 22 oz for the long study session.</p>
                            </div>
                            <div class="relative z-10 flex items-end justify-center gap-6">
                                @foreach (['16 oz' => 'h-36 sm:h-44', '22 oz' => 'h-48 sm:h-60'] as $size => $height)
                                    <figure class="text-center">
                                        <x-menu-art :art="$items['strawberry-milk']['art']" class="{{ $height }} mx-auto w-auto" />
                                        <figcaption class="mt-2 font-display text-xl font-extrabold">{{ $size }}</figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        </li>

                        <li class="story-panel bg-ink text-cream">
                            <div class="relative z-10 max-w-[62%]">
                                <span class="story-number">03</span>
                                <h3 class="story-heading">Add a bite</h3>
                                <p class="mt-3 text-cream/75">Siomai sa Tisa from ₱{{ $from('siomai-sa-tisa') }}, fries, nachos, or a full rice meal when you're properly hungry.</p>
                                <a href="#menu" data-filter-link="snacks" class="btn btn-brand mt-6">See snacks</a>
                            </div>
                            <div class="absolute -right-[12%] -bottom-[22%] aspect-square w-[78%] rounded-full bg-cream/8" aria-hidden="true"></div>
                            <x-menu-art :art="$items['siomai-sa-tisa']['art']" class="absolute right-[18%] -bottom-2 h-[50%] w-auto" />
                            <x-menu-art :art="$items['fries']['art']" class="absolute -right-2 bottom-[16%] h-[58%] w-auto rotate-6" />
                        </li>

                        <li class="story-panel bg-brand">
                            <div class="relative z-10 max-w-[70%]">
                                <span class="story-number">04</span>
                                <h3 class="story-heading">{{ $shop['callout'] }}</h3>
                                <p class="mt-3 text-ink/80">Send us your order, pay at the counter and grab it {{ Str::lcfirst($address['landmark']) }} in {{ $address['area'] }}.</p>
                                <a href="#menu" class="btn btn-primary btn-lg mt-6">
                                    Start your order
                                    <x-site-icon name="arrow-right" class="size-4" />
                                </a>
                            </div>
                            <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="512" height="512" loading="lazy" class="absolute -right-10 -bottom-10 size-[58%] max-w-72 rotate-12 rounded-full shadow-2xl">
                        </li>
                    </ol>
                </div>
            </section>

            {{-- Menu --}}
            <section id="menu" class="relative py-24 sm:py-32" aria-labelledby="menu-title">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                        <div data-reveal>
                            <p class="eyebrow text-brand-deep">The menu</p>
                            <h2 id="menu-title" class="section-title mt-4">Pick your <span class="font-serif font-normal text-brand italic">story.</span></h2>
                            <p class="mt-5 max-w-lg text-lg text-ink-soft">Every drink comes in 16 oz or 22 oz. Tap <strong class="text-ink">Add</strong>, then check out when you're ready. No account needed.</p>
                        </div>
                        <label class="search">
                            <x-site-icon name="search" class="size-5 text-ink-soft" />
                            <span class="sr-only">Search the menu</span>
                            <input type="search" data-menu-search placeholder="Search matcha, siomai, frappe…" autocomplete="off" class="w-full bg-transparent text-base outline-none placeholder:text-ink-soft/70">
                        </label>
                    </div>

                    <div role="toolbar" aria-label="Filter the menu" class="no-scrollbar sticky top-20 z-30 -mx-5 mt-10 flex gap-2 overflow-x-auto bg-cream/85 px-5 py-3 backdrop-blur-lg sm:mx-0 sm:rounded-full sm:px-2">
                        <button type="button" data-filter="all" aria-pressed="true" class="chip">
                            All <span class="chip-count">{{ $menuCount }}</span>
                        </button>
                        @foreach ($categories as $key => $label)
                            <button type="button" data-filter="{{ $key }}" aria-pressed="false" class="chip">
                                {{ $label }} <span class="chip-count">{{ $categoryCounts[$key] ?? 0 }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div data-menu-grid class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($items as $id => $item)
                            <x-menu-card :id="$id" :item="$item" :categories="$categories" />
                        @endforeach
                    </div>

                    <div data-menu-empty hidden class="mt-8 rounded-[2rem] bg-paper px-6 py-16 text-center">
                        <p class="font-display text-3xl font-extrabold tracking-tight">Nothing matches that.</p>
                        <p class="mt-2 text-ink-soft">Try "matcha", "siomai" or "frappe".</p>
                    </div>
                </div>
            </section>

            {{-- Combos bento --}}
            <section id="combos" class="relative bg-paper py-24 sm:py-32" aria-labelledby="combos-title">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <div data-reveal class="max-w-2xl">
                        <p class="eyebrow text-brand-deep">Sulit combos</p>
                        <h2 id="combos-title" class="section-title mt-4">Better <span class="font-serif font-normal text-brand italic">together.</span></h2>
                        <p class="mt-5 text-lg text-ink-soft">Pair a 16 oz drink with a bite and save ₱5. Or go all in with something spicy.</p>
                    </div>

                    <div class="mt-14 grid gap-5 md:grid-cols-2">
                        @foreach ($bento as $card)
                            @php
                                $item = $items[$card['id']];
                                $choices = $item['options']['choices'];
                            @endphp
                            <article data-item="{{ $card['id'] }}" data-reveal class="bento group {{ $card['theme'] }}">
                                <div>
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <p class="eyebrow opacity-80">
                                                @isset($item['regular_price'])
                                                    Save ₱{{ $item['regular_price'] - reset($choices) }}
                                                @else
                                                    For the brave
                                                @endisset
                                            </p>
                                            <h3 class="mt-4 font-display text-[clamp(2rem,3.6vw,3.2rem)] leading-[0.92] font-extrabold tracking-[-0.035em] text-balance">{{ $item['name'] }}</h3>
                                        </div>
                                        <div class="bento-stage -mt-3 -mr-3">
                                            <x-menu-art :art="$item['art']" data-art class="h-full w-auto transition-[rotate,scale] duration-700 ease-spring group-hover:-rotate-3 group-hover:scale-105" />
                                        </div>
                                    </div>
                                    <p class="mt-4 max-w-md text-sm leading-relaxed opacity-80">{{ $item['description'] }}</p>
                                </div>

                                <div class="mt-8 flex flex-wrap items-center gap-3">
                                    @if (count($choices) > 1)
                                        <label class="sr-only" for="bento-{{ $card['id'] }}">{{ $item['options']['label'] }}</label>
                                        <select id="bento-{{ $card['id'] }}" data-choice class="bento-select">
                                            @foreach ($choices as $choice => $price)
                                                <option value="{{ $choice }}">{{ $choice }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <p class="flex items-baseline gap-2">
                                        <span data-price class="font-display text-4xl font-extrabold tracking-tight">₱{{ reset($choices) }}</span>
                                        @isset($item['regular_price'])
                                            <s class="text-base font-semibold opacity-60">₱{{ $item['regular_price'] }}</s>
                                        @endisset
                                    </p>
                                    <button type="button" data-add="{{ $card['id'] }}" class="add-btn {{ $card['button'] }}" aria-label="Add {{ $item['name'] }} to order">
                                        <x-site-icon name="plus" class="size-4" />
                                        <span>Add</span>
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Manifesto --}}
            <section class="relative isolate overflow-hidden bg-ink py-28 text-cream sm:py-40" aria-label="Our promise">
                <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 size-[50rem] -translate-x-1/2 rounded-full bg-brand/15 blur-3xl" aria-hidden="true"></div>
                <div class="mx-auto max-w-6xl px-5 sm:px-8">
                    <p class="eyebrow text-amber">{{ $shop['motto'] }}</p>
                    <p class="manifesto mt-8 font-display text-[clamp(2rem,5.4vw,4.6rem)] leading-[1.02] font-bold tracking-[-0.03em] text-balance">
                        @foreach (explode(' ', $manifesto) as $word)
                            @if (Str::startsWith($word, '*'))
                                <span class="word font-serif font-normal text-amber italic">{{ trim($word, '*') }}</span>
                            @else
                                <span class="word">{{ $word }}</span>
                            @endif
                        @endforeach
                    </p>

                    <dl class="mt-20 grid grid-cols-2 gap-px overflow-hidden rounded-[2rem] bg-cream/10 lg:grid-cols-4">
                        @foreach ([
                            ['₱'.$from('siomai-sa-tisa'), 'Cheapest bite', 'Siomai sa Tisa, four pieces'],
                            ['2', 'Cup sizes', '16 oz and 22 oz on every drink'],
                            [(string) $menuCount, 'Things to try', 'Drinks, snacks, meals and combos'],
                            ['11', 'Hours a day', "Open {$shop['hours']['label']}"],
                        ] as [$value, $label, $hint])
                            <div data-reveal class="bg-ink p-6 sm:p-8">
                                <dt class="text-sm font-bold text-amber">{{ $label }}</dt>
                                <dd class="mt-3 font-display text-5xl font-extrabold tracking-tight sm:text-6xl">{{ $value }}</dd>
                                <dd class="mt-2 text-sm text-cream/60">{{ $hint }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </section>

            {{-- Visit --}}
            <section id="visit" class="relative py-24 sm:py-32" aria-labelledby="visit-title">
                <div class="mx-auto grid max-w-7xl gap-5 px-5 sm:px-8 lg:grid-cols-[0.9fr_1.1fr]">
                    <div data-reveal class="flex flex-col justify-between rounded-[2.5rem] bg-white p-8 ring-1 ring-ink/8 sm:p-10">
                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="eyebrow text-brand-deep">Visit us</p>
                                <span data-open-status class="status-pill">
                                    <span class="status-dot"></span>
                                    <span data-open-label>{{ $shop['hours']['label'] }}</span>
                                </span>
                            </div>
                            <h2 id="visit-title" class="section-title mt-4">Your new <span class="font-serif font-normal text-brand italic">tambayan.</span></h2>

                            <ul class="mt-10 space-y-6">
                                <li class="flex gap-4">
                                    <span class="info-icon"><x-site-icon name="map-pin" /></span>
                                    <div>
                                        <p class="font-bold">{{ $address['street'] }}, {{ $address['area'] }}</p>
                                        <p class="text-ink-soft">{{ $address['city'] }} {{ $address['zip'] }} · {{ $address['landmark'] }}</p>
                                    </div>
                                </li>
                                <li class="flex gap-4">
                                    <span class="info-icon"><x-site-icon name="clock" /></span>
                                    <div>
                                        <p class="font-bold">Open daily, {{ $shop['hours']['label'] }}</p>
                                        <p class="text-ink-soft">Dine in, take out, or order ahead here.</p>
                                    </div>
                                </li>
                                <li class="flex gap-4">
                                    <span class="info-icon"><x-site-icon name="mail" /></span>
                                    <div>
                                        <a href="mailto:{{ $shop['email'] }}" class="font-bold underline-offset-4 hover:underline">{{ $shop['email'] }}</a>
                                        <p class="text-ink-soft">For bulk orders and events.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-10 flex flex-wrap gap-3">
                            <a href="{{ $shop['maps'] }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
                                <x-site-icon name="navigation" class="size-4" />
                                Get directions
                            </a>
                            <a href="{{ $shop['messenger'] ?? $shop['facebook'] }}" target="_blank" rel="noopener" class="btn btn-ghost btn-lg">
                                <x-site-icon name="message" class="size-4" />
                                Message us
                            </a>
                        </div>
                    </div>

                    <div data-reveal class="relative min-h-[26rem] overflow-hidden rounded-[2.5rem] bg-latte ring-1 ring-ink/8">
                        <iframe
                            src="{{ $shop['map_embed'] }}"
                            title="Map showing {{ $shop['name'] }} on {{ $address['street'] }}, Hindang"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="map-frame absolute inset-0 size-full"
                        ></iframe>
                        <div class="pointer-events-none absolute top-5 right-5 flex items-center gap-3 rounded-full bg-cream/95 py-2 pr-5 pl-2 shadow-xl ring-1 ring-ink/10 backdrop-blur">
                            <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="40" height="40" loading="lazy" class="size-10 rounded-full">
                            <div class="leading-tight">
                                <p class="text-sm font-extrabold">RacNin</p>
                                <p class="text-xs text-ink-soft">{{ $address['landmark'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="relative overflow-hidden bg-brand text-ink">
            <div class="mx-auto max-w-7xl px-5 pt-20 sm:px-8">
                <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="64" height="64" loading="lazy" class="size-16 rounded-full shadow-lg ring-4 ring-cream/60">
                            <div>
                                <p class="font-display text-3xl font-extrabold tracking-tight">{{ $shop['name'] }}</p>
                                <p class="font-serif text-xl italic">{{ $shop['tagline'] }}.</p>
                            </div>
                        </div>
                        <p class="mt-6 max-w-sm text-ink/75">Ice coffee, non-coffee, frappe, fruit soda and rice meals, made fresh in Hindang, Leyte.</p>
                    </div>
                    <div>
                        <p class="eyebrow">Explore</p>
                        <ul class="mt-4 space-y-2 font-semibold">
                            <li><a href="#menu" class="footer-link">Menu</a></li>
                            <li><a href="#story" class="footer-link">How it works</a></li>
                            <li><a href="#combos" class="footer-link">Combos</a></li>
                            <li><a href="#visit" class="footer-link">Visit us</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="eyebrow">Say hi</p>
                        <ul class="mt-4 space-y-2 font-semibold">
                            <li><a href="{{ $shop['facebook'] }}" target="_blank" rel="noopener" class="footer-link inline-flex items-center gap-2"><x-site-icon name="facebook" class="size-4" /> Facebook</a></li>
                            <li><a href="mailto:{{ $shop['email'] }}" class="footer-link inline-flex items-center gap-2"><x-site-icon name="mail" class="size-4" /> Email</a></li>
                            <li class="pt-2 text-sm font-medium text-ink/75">{{ $fullAddress }}</li>
                        </ul>
                    </div>
                </div>

                {{-- Letters sit on one line with no whitespace between spans, so the tracking stays tight --}}
                <p class="footer-word mt-20 overflow-hidden pb-[0.12em] text-center font-display text-[clamp(3.2rem,13vw,12rem)] leading-[0.9] font-extrabold tracking-[-0.06em] whitespace-nowrap select-none" aria-hidden="true">@foreach (mb_str_split($shop['callout']) as $index => $letter)<span style="--i: {{ $index }}">{!! $letter === ' ' ? '&nbsp;' : e($letter) !!}</span>@endforeach</p>

                <div class="flex flex-col gap-4 border-t border-ink/15 py-6 text-sm text-ink/70 lg:flex-row lg:items-center lg:justify-between">
                    <p>© {{ now()->year }} {{ $shop['name'] }}. All rights reserved.</p>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                        <p>Prices and availability may change. {{ $shop['motto'] }}</p>
                        @isset($staffLoginUrl)
                            <a href="{{ $staffLoginUrl }}" rel="nofollow" class="inline-flex shrink-0 items-center gap-2 self-start rounded-full bg-ink py-2 pr-4 pl-3 font-bold text-cream transition duration-300 hover:bg-cream hover:text-ink sm:self-auto">
                                <x-site-icon name="log-in" class="size-4" />
                                Staff login
                            </a>
                        @endisset
                    </div>
                </div>
            </div>
        </footer>

        {{-- Mobile order bar --}}
        <div data-order-bar hidden class="fixed inset-x-3 bottom-[max(0.75rem,env(safe-area-inset-bottom))] z-40 md:hidden">
            <button type="button" data-cart-open data-cart-target class="flex w-full items-center justify-between rounded-full bg-ink py-3 pr-5 pl-3 text-cream shadow-[0_20px_40px_-12px_rgb(31_21_16/0.6)]">
                <span class="flex items-center gap-3 font-bold">
                    <span data-cart-count class="cart-count bg-brand text-ink">0</span>
                    View your order
                </span>
                <span data-cart-subtotal class="font-display text-lg font-extrabold">₱0</span>
            </button>
        </div>

        <x-order-drawer :shop="$shop" :upsell="['id' => 'siomai-sa-tisa'] + $items['siomai-sa-tisa']" />

        <div data-toast popover="manual" role="status" aria-live="polite" class="toast"></div>

        <script type="application/json" id="racnin-data">@json($payload)</script>
    </body>
</html>
