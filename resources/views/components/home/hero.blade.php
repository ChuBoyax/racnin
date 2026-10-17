@props(['shop', 'menu'])

@php
    $address = $shop['address'];

    $floats = [
        ['id' => 'strawberry-milk', 'class' => 'hero-float-a top-[4%] -left-[3%] w-[27%] md:w-[31%]', 'tilt' => '-6deg', 'delay' => '0s'],
        ['id' => 'dirty-matcha', 'class' => 'hero-float-b top-[40%] -right-[4%] w-[26%] md:w-[29%]', 'tilt' => '5deg', 'delay' => '-2.4s'],
        ['id' => 'mango-soda', 'class' => 'hero-float-c -bottom-[3%] left-[8%] w-[25%] md:w-[27%]', 'tilt' => '-3deg', 'delay' => '-4.1s'],
    ];
@endphp

<section class="hero relative isolate overflow-hidden pt-28 pb-16 sm:pt-36 md:pb-24 lg:pb-32">
    {{-- Soft glows are plain gradients: a blur filter this size costs every frame on a phone --}}
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <div class="absolute -top-56 -right-56 size-[54rem] bg-radial from-amber/35 from-25% to-transparent to-70%"></div>
        <div class="absolute top-1/4 -left-72 size-[46rem] bg-radial from-olive/25 from-25% to-transparent to-70%"></div>
        <div class="dots absolute inset-0 [mask-image:radial-gradient(ellipse_at_center,black,transparent_70%)] opacity-50"></div>
    </div>

    {{--
        Phones show the headline over a smaller logo visual, then the buttons; the
        intro stays for screen readers and search engines. Tablets stack copy,
        buttons, then the visual, and from lg the visual sits in its own column.
    --}}
    <div class="mx-auto grid max-w-7xl px-5 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:gap-x-10">
        <div class="hero-copy lg:col-start-1 lg:row-start-1 lg:self-end">
            <a href="#featured" class="group inline-flex items-center gap-2 rounded-full bg-white/80 py-1.5 pr-4 pl-1.5 text-sm font-semibold ring-1 ring-ink/10 transition hover:ring-ink/25 max-md:hidden">
                <span class="rounded-full bg-ink px-2.5 py-1 text-xs font-bold text-cream">New</span>
                Lumpia Silog just dropped
                <x-site-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5" />
            </a>

            {{-- Phones keep the headline on one line: it is 9× its font size wide, so the size follows the screen width --}}
            <h1 class="mt-8 font-display text-[clamp(3.4rem,9.5vw,7.8rem)] leading-[0.86] font-extrabold tracking-[-0.045em] max-md:mt-0 max-md:text-center max-md:text-[clamp(1.5rem,calc((100vw_-_2.5rem)/9.4),3.75rem)]">
                <span class="hero-line block max-md:inline-block">Drink your</span>
                <span class="hero-line block max-md:inline-block">
                    own
                    <span class="relative inline-block font-serif font-normal tracking-[-0.02em] text-brand italic">
                        story.
                        <svg class="absolute -bottom-[0.08em] left-0 h-[0.18em] w-full text-ink" viewBox="0 0 300 20" preserveAspectRatio="none" aria-hidden="true">
                            <path class="squiggle" d="M3 14 C 40 4, 70 4, 100 12 S 160 20, 200 10 S 260 2, 297 10" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" pathLength="1" />
                        </svg>
                    </span>
                </span>
            </h1>

            <p class="mt-8 max-w-xl text-lg leading-relaxed text-ink-soft max-md:sr-only sm:text-xl">
                Iced coffee, creamy frappes, fizzy fruit sodas and honest rice meals, mixed to order and mostly
                <strong class="font-bold text-ink">₱50 and under</strong>. Find us {{ Str::lcfirst($address['landmark']) }} in {{ $address['area'] }}, Hindang.
            </p>
        </div>

        <div class="hero-visual relative mx-auto mt-8 aspect-square w-[84%] max-w-[22rem] md:order-1 md:mt-16 md:w-full md:max-w-[34rem] lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:mt-0 lg:self-center">
            <div class="absolute inset-0 bg-radial from-amber/60 via-brand/20 via-35% to-transparent to-70%" aria-hidden="true"></div>

            {{-- The ring spins on wrapper divs: animating the <svg> itself redraws its curved text on every frame --}}
            <div class="hero-ring absolute inset-0" aria-hidden="true">
                <div class="hero-ring-spin size-full">
                    <svg class="size-full text-ink" viewBox="0 0 400 400">
                        <defs>
                            <path id="hero-ring-path" d="M200 200 m-178 0 a178 178 0 1 1 356 0 a178 178 0 1 1 -356 0" />
                        </defs>
                        <circle cx="200" cy="200" r="194" fill="none" stroke="currentColor" stroke-opacity=".12" stroke-dasharray="2 8" />
                        <text class="font-display" font-size="15.5" font-weight="800" letter-spacing="5" fill="currentColor">
                            <textPath href="#hero-ring-path" textLength="1110" lengthAdjust="spacing">{{ Str::upper($shop['tagline']) }} ✦ {{ Str::upper($shop['callout']) }} ✦ {{ Str::upper($shop['motto']) }} ✦</textPath>
                        </text>
                    </svg>
                </div>
            </div>

            <img src="{{ asset('images/logo-mark.webp') }}" alt="{{ $shop['name'] }} logo: a frappe with a raccoon on the cup and the words Drink Your Own Story" width="512" height="512" fetchpriority="high"
                class="hero-badge absolute inset-[13%] size-[74%] rounded-full shadow-[0_40px_80px_-30px_rgb(31_21_16/0.55)]">

            @foreach ($floats as $float)
                @php($item = $menu->item($float['id']))
                <div class="hero-float {{ $float['class'] }} absolute">
                    <div class="animate-float rounded-2xl bg-white/85 p-1.5 pb-2 shadow-[0_24px_50px_-20px_rgb(31_21_16/0.45)] ring-1 ring-white md:rounded-[1.6rem] md:p-2.5 md:pb-3" style="--float-tilt: {{ $float['tilt'] }}; animation-delay: {{ $float['delay'] }}">
                        <div class="rounded-xl px-1 pt-1 md:rounded-[1.1rem] md:px-2 md:pt-2" style="background: color-mix(in oklab, {{ $item['tint'] }} 22%, white)">
                            <x-menu-art :item="$float['id']" loading="eager" class="mx-auto h-auto w-full" />
                        </div>
                        <p class="mt-1.5 px-0.5 text-[10px] leading-tight font-bold md:mt-2 md:truncate md:px-1 md:text-xs">{{ $item['name'] }}</p>
                        <p class="px-0.5 text-[10px] text-ink-soft md:px-1 md:text-[11px]">from ₱{{ $menu->startingPrice($float['id']) }}</p>
                    </div>
                </div>
            @endforeach

            <div class="hero-sticker absolute top-[2%] right-[6%] rotate-12 rounded-full bg-olive px-3 py-2 font-display text-sm font-extrabold tracking-tight shadow-lg ring-4 ring-cream md:px-4 md:py-2.5 md:text-lg">
                {{ $shop['callout'] }}
            </div>
        </div>

        <div class="hero-actions mt-10 lg:col-start-1 lg:row-start-2 lg:self-start">
            {{-- Two equal halves on phones, natural widths from tablets up --}}
            <div class="grid grid-cols-2 gap-3 md:flex md:flex-wrap md:items-center">
                <a href="#menu" class="btn btn-primary btn-lg group max-md:px-3 max-[380px]:text-sm">
                    Start your order
                    <x-site-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" />
                </a>
                <a href="#visit" class="btn btn-ghost btn-lg max-md:px-3 max-[380px]:text-sm">
                    <x-site-icon name="map-pin" class="size-4" />
                    Find us
                </a>
            </div>

            <dl class="mt-14 grid max-w-lg grid-cols-3 gap-6 border-t border-ink/10 pt-6">
                <div>
                    <dt class="text-xs font-semibold text-ink-soft">Starts at</dt>
                    <dd class="font-display text-3xl font-extrabold tracking-tight">₱{{ $menu->priceRange()[0] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-ink-soft">Open daily</dt>
                    <dd class="font-display text-3xl font-extrabold tracking-tight">10–9</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-ink-soft">On the menu</dt>
                    <dd class="font-display text-3xl font-extrabold tracking-tight">{{ $menu->count() }}+</dd>
                </div>
            </dl>
        </div>
    </div>
</section>
