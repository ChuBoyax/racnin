@props(['menu'])

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

        {{-- Nearly opaque instead of a backdrop blur, which would redraw on every scrolled frame --}}
        <div role="toolbar" aria-label="Filter the menu" class="no-scrollbar sticky top-20 z-30 -mx-5 mt-10 flex gap-2 overflow-x-auto bg-cream/95 px-5 py-3 sm:mx-0 sm:rounded-full sm:px-2">
            <button type="button" data-filter="all" aria-pressed="true" class="chip">
                All <span class="chip-count">{{ $menu->count() }}</span>
            </button>
            @foreach ($menu->categories() as $category => $label)
                <button type="button" data-filter="{{ $category }}" aria-pressed="false" class="chip">
                    {{ $label }} <span class="chip-count">{{ $menu->count($category) }}</span>
                </button>
            @endforeach
        </div>

        <div data-menu-grid class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($menu->items() as $id => $item)
                <x-menu-card :id="$id" :item="$item" :category="$menu->categories()[$item['category']]" />
            @endforeach
        </div>

        <div data-menu-empty hidden class="mt-8 rounded-[2rem] bg-paper px-6 py-16 text-center">
            <p class="font-display text-3xl font-extrabold tracking-tight">Nothing matches that.</p>
            <p class="mt-2 text-ink-soft">Try "matcha", "siomai" or "frappe".</p>
        </div>
    </div>
</section>
