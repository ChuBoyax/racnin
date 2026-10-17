@props(['menu'])

@php
    $cards = [
        ['id' => 'combo-soda-siomai', 'theme' => 'bg-ink text-cream', 'button' => 'add-btn-light'],
        ['id' => 'combo-coffee-siomai', 'theme' => 'bg-olive text-ink', 'button' => ''],
        ['id' => 'combo-frappe-fries', 'theme' => 'bg-brand text-ink', 'button' => ''],
        ['id' => 'buldak-carbonara', 'theme' => 'bg-chili text-white', 'button' => 'add-btn-light'],
    ];
@endphp

<section id="combos" class="relative bg-paper py-24 sm:py-32" aria-labelledby="combos-title">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div data-reveal class="max-w-2xl">
            <p class="eyebrow text-brand-deep">Sulit combos</p>
            <h2 id="combos-title" class="section-title mt-4">Better <span class="font-serif font-normal text-brand italic">together.</span></h2>
            <p class="mt-5 text-lg text-ink-soft">Pair a 16 oz drink with a bite and save ₱5. Or go all in with something spicy.</p>
        </div>

        <div class="mt-14 grid gap-5 md:grid-cols-2">
            @foreach ($cards as $card)
                @php
                    $item = $menu->item($card['id']);
                    $choices = $item['options']['choices'];
                    $price = $menu->startingPrice($card['id']);
                @endphp
                <article data-item="{{ $card['id'] }}" data-reveal class="bento group {{ $card['theme'] }}">
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="eyebrow opacity-80">
                                    @isset($item['regular_price'])
                                        Save ₱{{ $item['regular_price'] - $price }}
                                    @else
                                        For the brave
                                    @endisset
                                </p>
                                <h3 class="mt-4 font-display text-[clamp(2rem,3.6vw,3.2rem)] leading-[0.92] font-extrabold tracking-[-0.035em] text-balance">{{ $item['name'] }}</h3>
                            </div>
                            <div class="bento-stage -mt-3 -mr-3">
                                <x-menu-art :item="$card['id']" data-art class="h-full w-auto transition-[rotate,scale] duration-700 ease-spring group-hover:-rotate-3 group-hover:scale-105" />
                            </div>
                        </div>
                        <p class="mt-4 max-w-md text-sm leading-relaxed opacity-80">{{ $item['description'] }}</p>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        @if (count($choices) > 1)
                            <label class="sr-only" for="bento-{{ $card['id'] }}">{{ $item['options']['label'] }}</label>
                            <select id="bento-{{ $card['id'] }}" data-choice class="bento-select">
                                @foreach ($choices as $choice => $choicePrice)
                                    <option value="{{ $choice }}">{{ $choice }}</option>
                                @endforeach
                            </select>
                        @endif
                        <p class="flex items-baseline gap-2">
                            <span data-price class="font-display text-4xl font-extrabold tracking-tight">₱{{ $price }}</span>
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
