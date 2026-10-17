{{-- Nachos with cheese sauce in a ceramic bowl. --}}
<ellipse cx="102" cy="206" rx="70" ry="11" fill="url(#ra-shadow)" />
{{-- Tortilla chips, with corners rounded by a stroke in the same gradient --}}
@foreach (['M40 124 L66 66 L98 120 Z', 'M116 124 L156 72 L166 128 Z', 'M66 100 L94 58 L112 104 Z', 'M110 100 L140 62 L150 108 Z', 'M80 118 L108 48 L138 116 Z', 'M58 128 L86 84 L114 132 Z', 'M98 132 L128 88 L152 134 Z'] as $chip)
    <path d="{{ $chip }}" fill="url(#ra-chip)" stroke="url(#ra-chip)" stroke-width="5" stroke-linejoin="round" />
    <path d="{{ $chip }}" fill="#ffffff" filter="url(#ra-grain-dark)" opacity=".55" />
    <path d="{{ $chip }}" fill="none" stroke="#b97a22" stroke-opacity=".35" stroke-width=".8" stroke-linejoin="round" />
@endforeach

{{-- Cheese sauce drizzled over the top, with drips and a wet shine --}}
@php($cheese = 'M48 114 C60 100 72 118 86 104 S108 96 118 106 S140 98 154 112')
<path d="{{ $cheese }}" stroke="url(#ra-cheese)" stroke-width="10" stroke-linecap="round" fill="none" />
<path d="M70 90 C80 82 90 98 102 86 S122 82 134 92" stroke="url(#ra-cheese)" stroke-width="7" stroke-linecap="round" fill="none" />
<path d="M64 110 c1 6 -1 10 0 14 M100 102 c1 7 -1 11 1 16 M138 104 c1 5 0 9 -1 12 M86 92 c1 4 0 7 0 9" stroke="#f08c00" stroke-width="4" stroke-linecap="round" fill="none" />
<path d="M52 110 C62 99 72 114 84 102 M104 98 C112 96 116 102 120 101 M70 88 C78 83 86 92 94 87" stroke="#ffffff" stroke-opacity=".6" stroke-width="1.4" stroke-linecap="round" fill="none" />
<path d="M96 92 C98 81 115 81 117 92 C113 99 100 99 96 92 Z" fill="#fdfaf3" />
<path d="M99 90 C102 85 109 85 111 88" stroke="#ffffff" stroke-width="1.6" fill="none" stroke-linecap="round" />
@foreach ([[78, 106, 6], [134, 104, 5.4], [110, 98, 4.8]] as [$x, $y, $r])
    <circle cx="{{ $x }}" cy="{{ $y }}" r="{{ $r }}" fill="#3f7a1c" />
    <circle cx="{{ $x }}" cy="{{ $y }}" r="{{ $r * 0.72 }}" fill="#a6cf63" />
    <circle cx="{{ $x }}" cy="{{ $y }}" r="{{ $r * 0.3 }}" fill="#eef4cf" />
@endforeach

<path d="M30 118 Q100 130 170 118 C164 170 136 204 100 204 C64 204 36 170 30 118 Z" fill="url(#ra-ceramic)" />
<path d="M30 118 Q100 130 170 118" stroke="#e4e3a8" stroke-width="2.4" fill="none" />
<path d="M44 132 C48 160 62 182 80 194" stroke="#ffffff" stroke-opacity=".35" stroke-width="4" fill="none" stroke-linecap="round" />
