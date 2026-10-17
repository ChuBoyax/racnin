{{-- Iced drinks, fruit sodas and frappes in a clear plastic cup. --}}
@php
    $art = $drawing->art;
    $ink = '#1f1510';
    $straw = $drawing->straw();
    $surface = $drawing->surface();
    $cup = $drawing->cupPath();
    $liquid = $drawing->liquidPath();
    $topColor = $art['layers'][array_key_last($art['layers'])][0];
    $isSoda = $art['type'] === 'soda';
    $isFrappe = $art['type'] === 'frappe';
@endphp

<ellipse cx="104" cy="207" rx="60" ry="10" fill="url(#ra-shadow)" />

{{-- Straw first, so the drink hides the part below its surface --}}
<g transform="translate({{ $straw['x'] }} {{ $straw['y'] }}) rotate({{ $straw['angle'] }})">
    <rect x="-4" width="8" height="{{ $straw['length'] }}" rx="4" fill="url(#ra-straw)" />
</g>

<path d="{{ $cup }}" fill="#ffffff" fill-opacity=".22" />

<g clip-path="url(#ra-cup)">
    <g filter="url(#ra-soft)" @if ($isSoda) opacity=".88" @endif>
        <path d="{{ $liquid }}" fill="{{ $topColor }}" />
        @foreach ($drawing->bands() as $band)
            <path d="{{ $band['d'] }}" fill="{{ $band['fill'] }}" />
        @endforeach
        @foreach ($drawing->tendrils() as $tendril)
            <path d="{{ $tendril['d'] }}" stroke="{{ $tendril['stroke'] }}" stroke-width="{{ $tendril['width'] }}" stroke-linecap="round" fill="none" opacity=".45" />
        @endforeach
    </g>

    <ellipse cx="{{ $surface['cx'] }}" cy="{{ $surface['cy'] }}" rx="{{ $surface['rx'] }}" ry="{{ $surface['ry'] }}" fill="{{ $surface['fill'] }}" @if ($isSoda) opacity=".85" @endif />
    <ellipse cx="{{ $surface['cx'] }}" cy="{{ $surface['cy'] }}" rx="{{ $surface['rx'] }}" ry="{{ $surface['ry'] }}" fill="url(#ra-sheen)" opacity=".5" />

    <path d="{{ $liquid }}" fill="url(#ra-cyl)" />
    <path d="{{ $liquid }}" fill="url(#ra-depth)" />
    @if ($isFrappe)
        <path d="{{ $liquid }}" fill="#ffffff" filter="url(#ra-grain)" opacity=".4" />
    @endif

    @foreach ($drawing->speckles() as $speck)
        <circle cx="{{ $speck['x'] }}" cy="{{ $speck['y'] }}" r="{{ $speck['r'] }}" fill="{{ $art['speckle'] }}" opacity=".85" />
    @endforeach

    @foreach ($drawing->bubbles() as $bubble)
        <circle cx="{{ $bubble['x'] }}" cy="{{ $bubble['y'] }}" r="{{ $bubble['r'] }}" fill="#ffffff" fill-opacity=".18" stroke="#ffffff" stroke-opacity=".8" stroke-width=".6" />
        <circle cx="{{ $bubble['x'] - $bubble['r'] * 0.35 }}" cy="{{ $bubble['y'] - $bubble['r'] * 0.35 }}" r="{{ max(0.3, $bubble['r'] * 0.3) }}" fill="#ffffff" />
    @endforeach

    @foreach ($drawing->ice() as $cube)
        @php($half = $cube['size'] / 2)
        <g transform="translate({{ $cube['x'] }} {{ $cube['y'] }}) rotate({{ $cube['rotate'] }})" opacity="{{ $cube['opacity'] }}">
            <rect x="{{ -$half }}" y="{{ -$half }}" width="{{ $cube['size'] }}" height="{{ $cube['size'] }}" rx="{{ $cube['size'] * 0.22 }}" fill="url(#ra-ice)" stroke="#ffffff" stroke-opacity=".9" stroke-width=".9" />
            <rect x="{{ 3 - $half }}" y="{{ 3 - $half }}" width="{{ $cube['size'] - 6 }}" height="{{ $cube['size'] - 6 }}" rx="{{ $cube['size'] * 0.16 }}" fill="none" stroke="#ffffff" stroke-opacity=".35" stroke-width=".8" />
            <path d="M{{ 3 - $half }} {{ 4 - $half }} q{{ $half * 0.6 }} -2.5 {{ $half * 1.2 }} 0" stroke="#ffffff" stroke-width="1.4" stroke-linecap="round" fill="none" />
        </g>
    @endforeach
</g>

{{-- The real RacNin sticker, wrapped slightly around the cup --}}
<g transform="translate(100 139) scale(.92 1) translate(-100 -139)">
    <circle cx="100" cy="139" r="21.5" fill="#000000" opacity=".12" filter="url(#ra-blur)" />
    <image href="{{ $drawing->sticker() }}" x="79" y="118" width="42" height="42" />
</g>

<g clip-path="url(#ra-cup)">
    @foreach ($drawing->droplets() as $drop)
        @if ($drop['drip'] > 0)
            <path d="M{{ $drop['x'] - $drop['r'] }} {{ $drop['y'] }} Q{{ $drop['x'] - $drop['r'] }} {{ $drop['y'] + $drop['drip'] }} {{ $drop['x'] }} {{ $drop['y'] + $drop['drip'] + $drop['r'] }} Q{{ $drop['x'] + $drop['r'] }} {{ $drop['y'] + $drop['drip'] }} {{ $drop['x'] + $drop['r'] }} {{ $drop['y'] }} A{{ $drop['r'] }} {{ $drop['r'] }} 0 0 0 {{ $drop['x'] - $drop['r'] }} {{ $drop['y'] }} Z" fill="url(#ra-drop)" />
        @else
            <circle cx="{{ $drop['x'] }}" cy="{{ $drop['y'] }}" r="{{ $drop['r'] }}" fill="url(#ra-drop)" />
        @endif
        <circle cx="{{ $drop['x'] - $drop['r'] * 0.35 }}" cy="{{ $drop['y'] - $drop['r'] * 0.35 }}" r="{{ max(0.25, $drop['r'] * 0.26) }}" fill="#ffffff" fill-opacity=".65" />
    @endforeach
</g>

{{-- Clear plastic: brighter at the edges, with glossy streaks following the taper --}}
<path d="{{ $cup }}" fill="url(#ra-plastic)" />
<path d="M57 62 L64 62 L71.5 194 L66.5 194 Z" fill="url(#ra-gloss)" />
<path d="M76 66 L78 66 L83 188 L82 188 Z" fill="url(#ra-gloss)" opacity=".55" />
<path d="M141 62 L143.6 62 L135.6 194 L134 194 Z" fill="url(#ra-gloss)" opacity=".7" />
<path d="{{ $cup }}" fill="none" stroke="#ffffff" stroke-opacity=".75" stroke-width="1.2" />
<path d="{{ $cup }}" fill="none" stroke="{{ $ink }}" stroke-opacity=".14" stroke-width=".7" />
<path d="M63 202 A37 6.5 0 0 0 137 202" fill="none" stroke="#ffffff" stroke-opacity=".7" stroke-width="2" />

@if ($isFrappe)
    {{-- Whipped cream under a clear dome --}}
    @foreach ([
        'M52 56 C46 46 56 38 66 41 C68 33 80 30 88 35 C94 27 108 27 114 34 C122 29 136 34 136 42 C146 40 154 48 148 56 C132 62 68 62 52 56 Z',
        'M64 42 C60 33 70 26 80 29 C84 21 98 20 104 26 C112 21 126 26 124 34 C132 34 136 42 130 46 C112 50 80 50 64 42 Z',
        'M80 30 C78 22 86 16 94 18 C96 10 106 6 110 12 C114 8 120 12 118 18 C124 20 124 28 118 31 C106 35 90 35 80 30 Z',
    ] as $tier)
        <path d="{{ $tier }}" fill="{{ $art['whip'] }}" />
        <path d="{{ $tier }}" fill="url(#ra-cream)" stroke="#5b3a1e" stroke-opacity=".14" stroke-width=".8" />
    @endforeach
    <path d="M68 52 C78 48 90 52 100 48 M88 38 C96 35 104 38 112 35 M114 52 C124 48 132 51 140 49 M96 24 C100 22 106 23 110 21" stroke="#5b3a1e" stroke-opacity=".13" stroke-width="1.3" stroke-linecap="round" fill="none" />

    @isset($art['speckle'])
        @foreach ($drawing->points(26, 100, 36, 40, 16, 5) as $crumb)
            <circle cx="{{ $crumb['x'] }}" cy="{{ $crumb['y'] }}" r="{{ 0.7 + $crumb['size'] * 1.3 }}" fill="{{ $art['speckle'] }}" />
        @endforeach
    @endisset

    @php($drizzle = 'M57 52 C65 41 73 54 82 44 S97 46 104 36 S120 45 128 38 S141 50 144 50 M70 35 C79 29 85 41 94 29 S109 30 116 22')
    <path d="{{ $drizzle }}" fill="none" stroke="{{ $art['drizzle'] }}" stroke-width="3" stroke-linecap="round" />
    <path d="M83 45 q1.5 7 -.5 12 M121 41 q1 6 0 10" fill="none" stroke="{{ $art['drizzle'] }}" stroke-width="2.4" stroke-linecap="round" />
    <path d="{{ $drizzle }}" fill="none" stroke="#ffffff" stroke-opacity=".5" stroke-width=".9" stroke-linecap="round" transform="translate(-.5 -.8)" />

    <path d="M50 52 C50 20 73 7 100 7 C127 7 150 20 150 52" fill="#ffffff" fill-opacity=".1" stroke="#ffffff" stroke-opacity=".9" stroke-width="1.8" />
    <path d="M50 52 C50 20 73 7 100 7 C127 7 150 20 150 52" fill="none" stroke="{{ $ink }}" stroke-opacity=".1" stroke-width=".7" />
    <path d="M59 42 C61 27 73 16 88 12" fill="none" stroke="#ffffff" stroke-opacity=".9" stroke-width="3" stroke-linecap="round" />
    <path d="M140 38 C138 29 132 22 125 18" fill="none" stroke="#ffffff" stroke-opacity=".55" stroke-width="1.5" stroke-linecap="round" />
    <path d="M50 52 A50 9 0 0 0 150 52" fill="none" stroke="#ffffff" stroke-opacity=".95" stroke-width="3.6" />
    <path d="M50 53.5 A50 9.5 0 0 0 150 53.5" fill="none" stroke="{{ $ink }}" stroke-opacity=".12" stroke-width=".7" />

    <g clip-path="url(#ra-above-dome)">
        <g transform="translate({{ $straw['x'] }} {{ $straw['y'] }}) rotate({{ $straw['angle'] }})">
            <rect x="-4" width="8" height="{{ $straw['length'] }}" rx="4" fill="url(#ra-straw)" />
        </g>
    </g>
@else
    {{-- Sealed film lid, pierced by the straw --}}
    <ellipse cx="100" cy="50" rx="49" ry="9" fill="url(#ra-film)" />
    <path d="M62 46 Q80 42 98 43" stroke="#ffffff" stroke-opacity=".85" stroke-width="1.6" fill="none" stroke-linecap="round" />
    <ellipse cx="100" cy="50" rx="49" ry="9" fill="none" stroke="#ffffff" stroke-opacity=".95" stroke-width="2.4" />
    <ellipse cx="100" cy="50.6" rx="49.6" ry="9.5" fill="none" stroke="{{ $ink }}" stroke-opacity=".14" stroke-width=".7" />

    <g clip-path="url(#ra-above-rim)">
        <g transform="translate({{ $straw['x'] }} {{ $straw['y'] }}) rotate({{ $straw['angle'] }})">
            <rect x="-4" width="8" height="{{ $straw['length'] }}" rx="4" fill="url(#ra-straw)" />
        </g>
        <ellipse cx="122.5" cy="49" rx="6" ry="1.6" fill="{{ $ink }}" opacity=".15" />
    </g>
@endif
