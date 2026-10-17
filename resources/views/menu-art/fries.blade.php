{{-- Flavored fries in a RacNin carton. --}}
<ellipse cx="102" cy="207" rx="62" ry="10" fill="url(#ra-shadow)" />
@foreach ([[60, 58, 82, -10], [130, 62, 76, 14], [74, 42, 92, -5], [118, 48, 86, 10], [88, 30, 98, 1], [104, 36, 94, 6], [82, 54, 84, -15], [98, 50, 86, 12], [112, 44, 90, -3]] as [$x, $y, $height, $angle])
    <g transform="rotate({{ $angle }} {{ $x + 5.5 }} {{ $y + $height / 2 }})">
        <rect x="{{ $x }}" y="{{ $y }}" width="11" height="{{ $height }}" rx="2.5" fill="url(#ra-fry)" />
        <rect x="{{ $x }}" y="{{ $y }}" width="11" height="8" rx="2.5" fill="#9a5a14" opacity=".35" />
        <rect x="{{ $x + 3 }}" y="{{ $y + 4 }}" width="2" height="{{ $height - 12 }}" rx="1" fill="#ffffff" opacity=".35" />
        @foreach ([3, 7, 12] as $offset)
            <circle cx="{{ $x + ($offset % 9) + 1.5 }}" cy="{{ $y + $offset + 2 }}" r=".9" fill="#e4651a" opacity=".75" />
        @endforeach
    </g>
@endforeach

<path d="M44 112 Q100 134 156 112 L141 204 Q100 211 59 204 Z" fill="url(#ra-carton)" />
<path d="M100 124 L100 209" stroke="#000000" stroke-opacity=".08" stroke-width="7" />
<path d="M44 112 Q100 134 156 112" stroke="#ffb199" stroke-opacity=".8" stroke-width="2" fill="none" />
<path d="M53 124 L62 200" stroke="#ffffff" stroke-opacity=".3" stroke-width="5" stroke-linecap="round" />
<circle cx="100" cy="170" r="20.5" fill="#000000" opacity=".15" filter="url(#ra-blur)" />
<image href="{{ $drawing->sticker() }}" x="80" y="149" width="40" height="40" />
