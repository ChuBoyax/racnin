{{-- Rice meals: garlic rice and a fried egg, with lumpia or siomai on the side. --}}
<ellipse cx="102" cy="190" rx="98" ry="18" fill="url(#ra-shadow)" />
<ellipse cx="100" cy="160" rx="94" ry="44" fill="url(#ra-plate)" />
<ellipse cx="100" cy="157" rx="73" ry="31" fill="url(#ra-plate-well)" />
<path d="M27 157 A73 31 0 0 1 173 157" fill="none" stroke="#000000" stroke-opacity=".06" stroke-width="2" />
<path d="M8 163 A94 44 0 0 0 192 163" fill="none" stroke="#ffffff" stroke-width="1.6" />

{{-- Garlic rice --}}
<ellipse cx="76" cy="160" rx="44" ry="9" fill="#000000" opacity=".1" filter="url(#ra-blur)" />
<path d="M30 158 C26 126 52 106 76 112 C96 100 124 112 122 146 C124 168 40 178 30 158 Z" fill="url(#ra-rice)" />
<g clip-path="url(#ra-rice-mound)">
    @foreach ($drawing->points(110, 76, 140, 48, 32, 1) as $grain)
        <ellipse cx="{{ $grain['x'] }}" cy="{{ $grain['y'] }}" rx="2.6" ry="1.15" transform="rotate({{ $grain['rotate'] }} {{ $grain['x'] }} {{ $grain['y'] }})" fill="#fffefa" stroke="#d9cdb8" stroke-opacity=".55" stroke-width=".35" />
    @endforeach
    @foreach ($drawing->points(14, 76, 138, 34, 20, 2) as $bit)
        <circle cx="{{ $bit['x'] }}" cy="{{ $bit['y'] }}" r="{{ 0.8 + $bit['size'] }}" fill="{{ $bit['size'] > 0.5 ? '#c98a34' : '#e3ad55' }}" />
    @endforeach
    <path d="M30 158 C26 126 52 106 76 112 C96 100 124 112 122 146 C124 168 40 178 30 158 Z" fill="url(#ra-sheen)" opacity=".45" />
</g>

{{-- Sunny-side-up egg --}}
<path d="M110 112 C114 88 158 84 172 102 C190 124 166 144 144 140 C122 146 104 134 110 112 Z" fill="url(#ra-egg)" />
<path d="M110 112 C114 88 158 84 172 102 C190 124 166 144 144 140 C122 146 104 134 110 112 Z" fill="none" stroke="#c4843a" stroke-opacity=".28" stroke-width="2.2" filter="url(#ra-blur)" />
<ellipse cx="143" cy="124" rx="13" ry="3.5" fill="#000000" opacity=".08" filter="url(#ra-blur)" />
<circle cx="142" cy="113" r="15" fill="url(#ra-yolk)" />
<ellipse cx="136.5" cy="107" rx="5" ry="3" fill="#ffffff" opacity=".85" />

@if (($drawing->art['side'] ?? null) === 'lumpia')
    @foreach ([['angle' => -10, 'cx' => 106, 'cy' => 162, 'x' => 58, 'y' => 151, 'w' => 94], ['angle' => -4, 'cx' => 112, 'cy' => 178, 'x' => 66, 'y' => 167, 'w' => 92]] as $index => $roll)
        <g transform="rotate({{ $roll['angle'] }} {{ $roll['cx'] }} {{ $roll['cy'] }})">
            <ellipse cx="{{ $roll['x'] + $roll['w'] / 2 }}" cy="{{ $roll['y'] + 22 }}" rx="{{ $roll['w'] / 2 }}" ry="4" fill="#000000" opacity=".16" filter="url(#ra-blur)" />
            <rect x="{{ $roll['x'] }}" y="{{ $roll['y'] }}" width="{{ $roll['w'] }}" height="22" rx="11" fill="url(#ra-fried)" />
            <rect x="{{ $roll['x'] }}" y="{{ $roll['y'] }}" width="{{ $roll['w'] }}" height="22" rx="11" fill="#ffffff" filter="url(#ra-grain)" opacity=".35" />
            @foreach ($drawing->points(16, $roll['x'] + $roll['w'] / 2, $roll['y'] + 10, $roll['w'] / 2 - 9, 7, 10 + $index) as $blister)
                <ellipse cx="{{ $blister['x'] }}" cy="{{ $blister['y'] }}" rx="{{ 1.5 + $blister['size'] * 2 }}" ry="{{ 1 + $blister['size'] }}" fill="{{ $blister['size'] > 0.55 ? '#fbe2a0' : '#7a3a0b' }}" opacity=".45" />
            @endforeach
            <ellipse cx="{{ $roll['x'] + 4 }}" cy="{{ $roll['y'] + 11 }}" rx="4" ry="10" fill="#6a330a" opacity=".3" />
            <ellipse cx="{{ $roll['x'] + $roll['w'] - 4 }}" cy="{{ $roll['y'] + 11 }}" rx="4" ry="10" fill="#6a330a" opacity=".3" />
        </g>
    @endforeach
@else
    @foreach ([[78, 182], [112, 188], [146, 178]] as [$x, $y])
        @include('menu-art.siomai', ['x' => $x, 'y' => $y])
    @endforeach
@endif
