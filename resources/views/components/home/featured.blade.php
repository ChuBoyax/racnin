@props(['menu'])

@php
    $id = 'lumpia-silog';
    $price = $menu->startingPrice($id);
@endphp

<section id="featured" class="relative isolate overflow-hidden bg-amber py-24 sm:py-32" aria-labelledby="featured-title">
    <div class="dots absolute inset-0 -z-10 opacity-40" aria-hidden="true"></div>
    <div data-item="{{ $id }}" class="mx-auto grid max-w-7xl items-center gap-14 px-5 sm:px-8 lg:grid-cols-2">
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
                <button type="button" data-add="{{ $id }}" class="btn btn-primary btn-lg">
                    <x-site-icon name="plus" class="size-4" />
                    Add to order · ₱{{ $price }}
                </button>
                <a href="#menu" data-filter-link="rice-meals" class="btn btn-ghost btn-lg">More rice meals</a>
            </div>
        </div>

        <div class="relative">
            <div class="checker absolute inset-x-[8%] bottom-[8%] h-[34%] -rotate-3 rounded-[2rem] opacity-90 shadow-xl" aria-hidden="true"></div>
            <div class="feature-art relative">
                <x-menu-art :item="$id" data-art class="mx-auto h-auto w-full max-w-[36rem] drop-shadow-[0_30px_30px_rgb(31_21_16/0.3)]" />
            </div>
            <div class="feature-price absolute top-[2%] right-[2%] grid size-28 rotate-12 place-items-center rounded-full bg-ink text-center text-cream shadow-2xl ring-4 ring-amber sm:size-36">
                <div>
                    <p class="text-[10px] font-bold tracking-[0.25em] text-amber uppercase">Only</p>
                    <p class="font-display text-4xl font-extrabold tracking-tight sm:text-5xl">₱{{ $price }}</p>
                </div>
            </div>
            <div class="absolute bottom-[2%] left-[2%] -rotate-6 rounded-2xl bg-cream px-4 py-3 shadow-xl ring-1 ring-ink/10">
                <p class="text-[10px] font-bold tracking-[0.2em] text-brand-deep uppercase">Perfect combo</p>
                <p class="font-display text-lg leading-tight font-extrabold">Rice, egg &amp; lumpia</p>
            </div>
        </div>
    </div>
</section>
