@props(['shop'])

@php($address = $shop['address'])

<section id="visit" class="relative py-24 sm:py-32" aria-labelledby="visit-title">
    <div class="mx-auto grid max-w-7xl gap-5 px-5 sm:px-8 lg:grid-cols-[0.9fr_1.1fr]">
        <div data-reveal class="flex flex-col justify-between rounded-[2.5rem] bg-white p-8 ring-1 ring-ink/8 sm:p-10">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="eyebrow text-brand-deep">Visit us</p>
                    <x-status-pill :label="$shop['hours']['label']" />
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
            <div class="pointer-events-none absolute top-5 right-5 flex items-center gap-3 rounded-full bg-cream py-2 pr-5 pl-2 shadow-xl ring-1 ring-ink/10">
                <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="40" height="40" loading="lazy" class="size-10 rounded-full">
                <div class="leading-tight">
                    <p class="text-sm font-extrabold">RacNin</p>
                    <p class="text-xs text-ink-soft">{{ $address['landmark'] }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
