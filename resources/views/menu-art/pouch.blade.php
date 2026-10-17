{{-- Crispy dried veggies in a printed pouch with a window. --}}
<ellipse cx="100" cy="206" rx="64" ry="9" fill="url(#ra-shadow)" />
<path d="M56 44 L144 44 L152 198 C120 208 80 208 48 198 Z" fill="url(#ra-pouch)" />
<path d="M56 44 L144 44 L145 62 L55 62 Z" fill="#3f5d1f" opacity=".55" />
<path d="M56 44 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6" fill="#56782c" />
<path d="M66 66 L60 192" stroke="#ffffff" stroke-opacity=".28" stroke-width="7" stroke-linecap="round" />
<path d="M136 66 L140 190" stroke="#ffffff" stroke-opacity=".15" stroke-width="3" stroke-linecap="round" />
<image href="{{ $drawing->sticker() }}" x="88" y="64" width="24" height="24" />
{{-- An SVG image can't use the page's web fonts, so the label falls back to a bold system face. --}}
<text x="100" y="104" text-anchor="middle" font-family="'Bricolage Grotesque', 'Arial Black', Arial, sans-serif" font-weight="800" font-size="18" fill="#fff8ef">CRISPY</text>
<text x="100" y="117" text-anchor="middle" font-family="'Instrument Sans', Arial, sans-serif" font-weight="700" font-size="7.5" letter-spacing="1" fill="#fff8ef" fill-opacity=".85">FRUITS &amp; VEGGIES</text>
<ellipse cx="100" cy="158" rx="33" ry="30" fill="#fff8ef" opacity=".95" />
<ellipse cx="100" cy="158" rx="33" ry="30" fill="none" stroke="#000000" stroke-opacity=".12" stroke-width="1.5" />
@foreach ([[84, 146, 14, 8, -20, '#ee7f1a'], [104, 140, 12, 9, 15, '#7fb241'], [118, 156, 11, 11, 0, '#f6c343'], [90, 166, 13, 8, 25, '#d33a5a'], [108, 172, 10, 10, 0, '#8a5bb0'], [116, 164, 12, 7, -30, '#ee7f1a'], [80, 160, 9, 9, 0, '#7fb241'], [100, 154, 10, 7, -8, '#f6c343']] as [$x, $y, $w, $h, $angle, $color])
    <g transform="rotate({{ $angle }} {{ $x }} {{ $y }})">
        <rect x="{{ $x - $w / 2 }}" y="{{ $y - $h / 2 }}" width="{{ $w }}" height="{{ $h }}" rx="3" fill="{{ $color }}" />
        <rect x="{{ $x - $w / 2 }}" y="{{ $y - $h / 2 }}" width="{{ $w }}" height="{{ $h }}" rx="3" fill="url(#ra-sheen)" />
    </g>
@endforeach
<path d="M74 140 C80 132 90 129 98 129" stroke="#ffffff" stroke-width="2" stroke-linecap="round" fill="none" opacity=".8" />
