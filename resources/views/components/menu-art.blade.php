@if (count($pictures) === 1)
    <img src="{{ $pictures[0]['src'] }}" alt="" width="{{ $pictures[0]['width'] }}" height="{{ $pictures[0]['height'] }}" loading="{{ $loading }}" decoding="async" {{ $attributes->class($pictures[0]['class']) }}>
@else
    <div {{ $attributes->class('menu-art-combo') }}>
        @foreach ($pictures as $picture)
            <img src="{{ $picture['src'] }}" alt="" width="{{ $picture['width'] }}" height="{{ $picture['height'] }}" loading="{{ $loading }}" decoding="async" class="{{ $picture['class'] }}">
        @endforeach
    </div>
@endif
