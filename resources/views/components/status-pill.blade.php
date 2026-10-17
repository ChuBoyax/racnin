@props(['label'])

{{-- shop.js swaps the label for "Open now" or "Closed" in the shop's own timezone --}}
<span data-open-status {{ $attributes->class('status-pill') }}>
    <span class="status-dot"></span>
    <span data-open-label>{{ $label }}</span>
</span>
