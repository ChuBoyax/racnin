{{-- Buldak carbonara, steaming in a black bowl with chopsticks. --}}
<ellipse cx="102" cy="208" rx="72" ry="10" fill="url(#ra-shadow)" />
<g filter="url(#ra-blur-lg)" stroke="#ffffff" stroke-opacity=".75" stroke-width="5" fill="none" stroke-linecap="round">
    <path d="M78 76 C68 62 88 52 78 36" />
    <path d="M100 70 C92 56 108 46 100 30" />
</g>
@foreach ([[128, 14, 14.5], [150, 22, 20.5]] as [$x, $y, $angle])
    <g transform="translate({{ $x }} {{ $y }}) rotate({{ $angle }})">
        <rect x="-3" width="6" height="110" rx="3" fill="url(#ra-wood)" />
    </g>
@endforeach

<ellipse cx="100" cy="118" rx="74" ry="20" fill="url(#ra-carbonara)" />
<g fill="none" stroke-linecap="round">
    @foreach ([[44, 116], [56, 106], [52, 126], [70, 111], [64, 121]] as [$x, $y])
        <path d="M{{ $x }} {{ $y }} c6 -6 12 6 18 0 s12 6 18 0 s12 6 18 0 s12 6 18 0 s12 6 18 0" stroke="#e98f3a" stroke-width="3.2" />
        <path d="M{{ $x }} {{ $y - 0.8 }} c6 -6 12 6 18 0 s12 6 18 0 s12 6 18 0 s12 6 18 0 s12 6 18 0" stroke="#ffe3b5" stroke-width="1" opacity=".8" />
    @endforeach
</g>
@foreach ($drawing->points(14, 100, 116, 56, 13, 3) as $flake)
    <rect x="{{ $flake['x'] }}" y="{{ $flake['y'] }}" width="{{ 1.6 + $flake['size'] * 2 }}" height="{{ 1.2 + $flake['size'] }}" rx=".5" transform="rotate({{ $flake['rotate'] }} {{ $flake['x'] }} {{ $flake['y'] }})" fill="{{ $flake['size'] > 0.6 ? '#b8200e' : '#2f3b1f' }}" />
@endforeach
@foreach ($drawing->points(10, 100, 114, 50, 12, 6) as $seed)
    <ellipse cx="{{ $seed['x'] }}" cy="{{ $seed['y'] }}" rx="1.4" ry=".7" transform="rotate({{ $seed['rotate'] }} {{ $seed['x'] }} {{ $seed['y'] }})" fill="#fff6dc" />
@endforeach

<path d="M26 118 C28 168 60 206 100 206 C140 206 172 168 174 118 C160 138 40 138 26 118 Z" fill="url(#ra-black-bowl)" />
<path d="M26 118 C40 138 160 138 174 118" fill="none" stroke="#6b5d55" stroke-width="2" />
<path d="M42 140 C46 166 62 186 82 196" stroke="#ffffff" stroke-opacity=".22" stroke-width="5" fill="none" stroke-linecap="round" />
<path d="M38 150 C62 170 138 170 162 150" stroke="#c9361e" stroke-width="2.5" fill="none" />
