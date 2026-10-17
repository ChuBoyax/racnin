@props(['shop', 'menu'])

@php($address = $shop['address'])

{{-- How it works: the section pins while vertical scrolling slides the panels sideways --}}
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
                            <li><a href="#menu" data-filter-link="{{ $category }}" class="chip">{{ $menu->categories()[$category] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <x-menu-art item="cafe-mocha" class="absolute -right-6 -bottom-4 h-[60%] w-auto rotate-6" />
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
                            <x-menu-art item="strawberry-milk" class="{{ $height }} mx-auto w-auto" />
                            <figcaption class="mt-2 font-display text-xl font-extrabold">{{ $size }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </li>

            <li class="story-panel bg-ink text-cream">
                <div class="relative z-10 max-w-[62%]">
                    <span class="story-number">03</span>
                    <h3 class="story-heading">Add a bite</h3>
                    <p class="mt-3 text-cream/75">Siomai sa Tisa from ₱{{ $menu->startingPrice('siomai-sa-tisa') }}, fries, nachos, or a full rice meal when you're properly hungry.</p>
                    <a href="#menu" data-filter-link="snacks" class="btn btn-brand mt-6">See snacks</a>
                </div>
                <div class="absolute -right-[12%] -bottom-[22%] aspect-square w-[78%] rounded-full bg-cream/8" aria-hidden="true"></div>
                <x-menu-art item="siomai-sa-tisa" class="absolute right-[18%] -bottom-2 h-[50%] w-auto" />
                <x-menu-art item="fries" class="absolute -right-2 bottom-[16%] h-[58%] w-auto rotate-6" />
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
