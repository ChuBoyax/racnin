@props(['shop', 'staffLoginUrl' => null])

@php($address = $shop['address'])

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
                    <li class="pt-2 text-sm font-medium text-ink/75">{{ $address['street'] }}, {{ $address['area'] }}, {{ $address['city'] }} {{ $address['zip'] }}</li>
                </ul>
            </div>
        </div>

        {{-- Letters sit on one line with no whitespace between spans, so the tracking stays tight --}}
        <p class="footer-word mt-20 overflow-hidden pb-[0.12em] text-center font-display text-[clamp(3.2rem,13vw,12rem)] leading-[0.9] font-extrabold tracking-[-0.06em] whitespace-nowrap select-none" aria-hidden="true">@foreach (mb_str_split($shop['callout']) as $index => $letter)<span style="--i: {{ $index }}">{!! $letter === ' ' ? '&nbsp;' : e($letter) !!}</span>@endforeach</p>

        <div class="flex flex-col gap-4 border-t border-ink/15 py-6 text-sm text-ink/70 lg:flex-row lg:items-center lg:justify-between">
            <p>© {{ now()->year }} {{ $shop['name'] }}. All rights reserved.</p>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                <p>Prices and availability may change. {{ $shop['motto'] }}</p>
                @if ($staffLoginUrl)
                    <a href="{{ $staffLoginUrl }}" rel="nofollow" class="inline-flex shrink-0 items-center gap-2 self-start rounded-full bg-ink py-2 pr-4 pl-3 font-bold text-cream transition duration-300 hover:bg-cream hover:text-ink sm:self-auto">
                        <x-site-icon name="log-in" class="size-4" />
                        Staff login
                    </a>
                @endif
            </div>
        </div>
    </div>
</footer>
