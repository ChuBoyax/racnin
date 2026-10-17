@props(['shop', 'menu'])

@php
    $names = collect($menu->items())->where('category', '!=', 'combos')->pluck('name');
    $taglines = [$shop['tagline'], $shop['callout'], $shop['motto'], "{$shop['address']['landmark']}, Hindang", "Open {$shop['hours']['label']}"];
@endphp

{{-- Crossing bands of everything on the menu; each list repeats so the loop is seamless --}}
<div class="relative z-10 -mt-10 mb-4 h-40 overflow-x-clip sm:h-44" aria-hidden="true">
    <div class="band band-a absolute inset-x-[-5%] top-6 overflow-hidden bg-ink py-4 text-cream shadow-xl">
        <div class="flex w-max animate-marquee">
            @foreach ([1, 2] as $copy)
                <ul class="flex shrink-0 items-center gap-8 pr-8 font-display text-2xl font-bold tracking-tight sm:text-3xl">
                    @foreach ($names as $name)
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
                    @foreach ($taglines as $line)
                        <li>{{ $line }}</li>
                        <li>●</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
</div>
