@props(['id', 'item', 'categories'])

@php
    $choices = $item['options']['choices'];
    $hasChoices = count($choices) > 1;
    $badges = [
        'bestseller' => ['label' => 'Bestseller', 'icon' => 'star', 'class' => 'bg-ink text-cream'],
        'new' => ['label' => 'New', 'icon' => 'sparkles', 'class' => 'bg-brand text-ink'],
        'spicy' => ['label' => 'Spicy', 'icon' => 'flame', 'class' => 'bg-chili text-white'],
        'signature' => ['label' => 'Signature', 'icon' => 'cup', 'class' => 'bg-olive-deep text-cream'],
        'sulit' => ['label' => 'Sulit', 'icon' => 'tag', 'class' => 'bg-amber text-ink'],
    ];
@endphp

<article
    data-item="{{ $id }}"
    data-category="{{ $item['category'] }}"
    data-search="{{ Str::lower(implode(' ', [$item['name'], $item['description'], $categories[$item['category']]])) }}"
    data-reveal
    data-sheen
    style="--tint: {{ $item['tint'] }}; view-transition-name: item-{{ $id }}"
    class="menu-card group relative flex flex-col overflow-hidden rounded-[2rem] bg-white shadow-[0_1px_0_rgb(31_21_16/0.04),0_24px_48px_-28px_rgb(31_21_16/0.4)] ring-1 ring-ink/8 transition-[translate,box-shadow] duration-500 ease-expo hover:-translate-y-1.5 hover:shadow-[0_1px_0_rgb(31_21_16/0.04),0_40px_60px_-30px_rgb(31_21_16/0.5)]"
>
    <div class="menu-card-stage relative aspect-[5/4] overflow-hidden">
        <x-menu-art :art="$item['art']" data-art class="relative z-10 mx-auto h-full w-auto py-4 transition-[rotate,scale] duration-700 ease-spring group-hover:-rotate-3 group-hover:scale-[1.06]" />

        @if ($item['badges'])
            <div class="absolute top-4 left-4 z-20 flex flex-wrap gap-1.5">
                @foreach ($item['badges'] as $badge)
                    <span class="badge-pill {{ $badges[$badge]['class'] }}">
                        <x-icon :name="$badges[$badge]['icon']" class="size-3" />
                        {{ $badges[$badge]['label'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-4 p-5 pt-4">
        <div>
            <h3 class="font-display text-xl leading-tight font-bold tracking-tight">{{ $item['name'] }}</h3>
            <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">{{ $item['description'] }}</p>
        </div>

        <div class="mt-auto flex flex-col gap-4">
            @if ($hasChoices)
                <fieldset>
                    <legend class="sr-only">{{ $item['options']['label'] }}</legend>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($choices as $choice => $price)
                            <label class="choice">
                                <input type="radio" class="sr-only" name="choice-{{ $id }}" value="{{ $choice }}" @checked($loop->first)>
                                <span>{{ $choice }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            <div class="flex items-center justify-between gap-3">
                <p class="flex items-baseline gap-2">
                    <span data-price class="font-display text-2xl font-extrabold tracking-tight">₱{{ reset($choices) }}</span>
                    @isset($item['regular_price'])
                        <s class="text-sm font-semibold text-ink-soft/70">₱{{ $item['regular_price'] }}</s>
                    @endisset
                </p>
                <button type="button" data-add="{{ $id }}" class="add-btn" aria-label="Add {{ $item['name'] }} to order">
                    <x-icon name="plus" class="size-4" />
                    <span>Add</span>
                </button>
            </div>
        </div>
    </div>
</article>
