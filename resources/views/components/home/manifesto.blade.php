@props(['shop', 'menu'])

@php
    $manifesto = 'Every cup is mixed when you order it. Every rice meal is cooked fresh. And almost everything costs *₱50* or less, so the whole barkada can come back *tomorrow.*';

    $facts = [
        ['₱'.$menu->startingPrice('siomai-sa-tisa'), 'Cheapest bite', 'Siomai sa Tisa, four pieces'],
        ['2', 'Cup sizes', '16 oz and 22 oz on every drink'],
        [(string) $menu->count(), 'Things to try', 'Drinks, snacks, meals and combos'],
        ['11', 'Hours a day', "Open {$shop['hours']['label']}"],
    ];
@endphp

<section class="relative isolate overflow-hidden bg-ink py-28 text-cream sm:py-40" aria-label="Our promise">
    <div class="pointer-events-none absolute -top-72 left-1/2 -z-10 size-[64rem] -translate-x-1/2 bg-radial from-brand/15 from-25% to-transparent to-70%" aria-hidden="true"></div>
    <div class="mx-auto max-w-6xl px-5 sm:px-8">
        <p class="eyebrow text-amber">{{ $shop['motto'] }}</p>
        {{-- Each word lights up as it scrolls past; *starred* words are set in the italic serif --}}
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
            @foreach ($facts as [$value, $label, $hint])
                <div data-reveal class="bg-ink p-6 sm:p-8">
                    <dt class="text-sm font-bold text-amber">{{ $label }}</dt>
                    <dd class="mt-3 font-display text-5xl font-extrabold tracking-tight sm:text-6xl">{{ $value }}</dd>
                    <dd class="mt-2 text-sm text-cream/60">{{ $hint }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
