{{-- Siomai sa Tisa: four siomai in a bowl with chili oil and calamansi. --}}
<ellipse cx="102" cy="201" rx="76" ry="12" fill="url(#ra-shadow)" />
<ellipse cx="100" cy="122" rx="66" ry="14" fill="#e3dacb" />
<ellipse cx="100" cy="125" rx="58" ry="9" fill="#cfc4b2" />
@foreach ([[100, 102], [78, 114], [122, 114], [100, 126]] as [$x, $y])
    @include('menu-art.siomai', ['x' => $x, 'y' => $y])
@endforeach
@foreach ([[88, 96, 4.2, 2.2], [113, 92, 3.6, 2], [67, 107, 3.4, 1.8], [133, 108, 3.8, 2.1], [96, 119, 3.2, 1.7], [111, 123, 2.8, 1.5], [80, 112, 2.4, 1.3]] as [$x, $y, $rx, $ry])
    <ellipse cx="{{ $x }}" cy="{{ $y }}" rx="{{ $rx }}" ry="{{ $ry }}" fill="url(#ra-chili)" opacity=".92" />
    <ellipse cx="{{ $x - $rx * 0.3 }}" cy="{{ $y - $ry * 0.35 }}" rx="{{ $rx * 0.3 }}" ry="{{ $ry * 0.25 }}" fill="#ffffff" opacity=".7" />
@endforeach
@foreach ($drawing->points(10, 100, 108, 26, 12, 4) as $bit)
    <circle cx="{{ $bit['x'] }}" cy="{{ $bit['y'] }}" r="{{ 0.8 + $bit['size'] }}" fill="#d9a04a" />
@endforeach

<path d="M34 122 C36 160 52 198 100 200 C148 198 164 160 166 122 C150 136 50 136 34 122 Z" fill="url(#ra-bowl)" />
<path d="M34 122 C36 160 52 198 100 200 C148 198 164 160 166 122" fill="none" stroke="#000000" stroke-opacity=".08" stroke-width="1" />
<path d="M34 122 C50 136 150 136 166 122" fill="none" stroke="#ffffff" stroke-width="2.5" />
<path d="M41 152 C64 166 136 166 159 152" stroke="#ee7f1a" stroke-width="4.5" fill="none" stroke-linecap="round" opacity=".9" />
<path d="M46 132 C48 158 58 182 74 192" stroke="#ffffff" stroke-opacity=".75" stroke-width="3" fill="none" stroke-linecap="round" />

@foreach ([[156, 110, 0], [170, 126, 25]] as [$x, $y, $angle])
    <g transform="translate({{ $x }} {{ $y }}) rotate({{ $angle }})">
        <ellipse cy="3" rx="13" ry="4" fill="#000000" opacity=".15" filter="url(#ra-blur)" />
        <circle r="12.5" fill="#4f8a24" />
        <circle r="10.5" fill="url(#ra-lime)" />
        <path d="M0 -9 V9 M-9 0 H9 M-6.4 -6.4 L6.4 6.4 M6.4 -6.4 L-6.4 6.4" stroke="#c4d47a" stroke-width=".8" />
        <circle r="1.6" fill="#eef4c4" />
        <ellipse cx="-4" cy="-5" rx="3" ry="1.6" fill="#ffffff" opacity=".6" />
    </g>
@endforeach
