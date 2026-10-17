@php
    $ink = '#2a1d15';
    $type = $art['type'];
@endphp

@if ($type === 'combo')
    <div {{ $attributes->merge(['class' => 'menu-art menu-art-combo']) }} aria-hidden="true">
        @foreach ($art['parts'] as $part)
            <x-menu-art :art="$part" />
        @endforeach
    </div>
@else
    <svg viewBox="0 0 200 220" {{ $attributes->merge(['class' => 'menu-art']) }} aria-hidden="true" focusable="false">
        @if ($isDrink())
            {{-- Straw runs behind the cup so the liquid tints it --}}
            @php($straw = $type === 'frappe' ? [128, 2, 106, 196, 124, 36] : [132, 4, 108, 196, 125, 60])
            <line x1="{{ $straw[0] }}" y1="{{ $straw[1] }}" x2="{{ $straw[2] }}" y2="{{ $straw[3] }}" stroke="{{ $ink }}" stroke-width="11" stroke-linecap="round" />
            <line x1="{{ $straw[0] }}" y1="{{ $straw[1] }}" x2="{{ $straw[2] }}" y2="{{ $straw[3] }}" stroke="#ee7f1a" stroke-width="5.5" stroke-linecap="round" />

            <path d="M52 64 L61.5 196 Q62 204 69 204 L131 204 Q138 204 138.5 196 L148 64 Z" fill="#ffffff" fill-opacity=".5" />

            @foreach ($liquid() as $band)
                <path d="{{ $band['d'] }}" fill="{{ $band['fill'] }}" />
            @endforeach

            @foreach ($speckles() as [$x, $y])
                <circle cx="{{ $x }}" cy="{{ $y }}" r="2.3" fill="{{ $art['speckle'] }}" />
            @endforeach

            @if ($type !== 'frappe')
                <g fill="#ffffff" fill-opacity=".45" stroke="#ffffff" stroke-opacity=".9" stroke-width="2">
                    <rect x="64" y="90" width="26" height="24" rx="6" transform="rotate(-12 77 102)" />
                    <rect x="95" y="86" width="27" height="25" rx="6" transform="rotate(9 108 98)" />
                    @if ($type === 'iced')
                        <rect x="113" y="110" width="21" height="21" rx="5" transform="rotate(-6 123 120)" />
                        <rect x="72" y="114" width="20" height="20" rx="5" transform="rotate(14 82 124)" />
                    @endif
                </g>
            @endif

            @if ($type === 'soda')
                <g fill="none" stroke="#ffffff" stroke-opacity=".85" stroke-width="1.6">
                    <circle cx="70" cy="178" r="3.2" />
                    <circle cx="84" cy="134" r="2.4" />
                    <circle cx="124" cy="166" r="3.6" />
                    <circle cx="114" cy="124" r="2" />
                    <circle cx="130" cy="140" r="2.6" />
                    <circle cx="96" cy="188" r="2" />
                    <circle cx="66" cy="142" r="1.8" />
                    <circle cx="132" cy="186" r="1.8" />
                </g>
            @endif

            <path d="M63.5 94 L69 186" stroke="#ffffff" stroke-opacity=".55" stroke-width="5" stroke-linecap="round" />

            {{-- A tiny version of the logo, like the sticker on every real cup --}}
            <g transform="translate(100 156)">
                <circle r="17" fill="#fff8ef" stroke="{{ $ink }}" stroke-width="2.5" />
                <circle r="13" fill="none" stroke="#ee7f1a" stroke-width="1.8" />
                <path d="M-9 5 A9 9 0 0 1 9 5 Z" fill="#a8a754" />
                <path d="M-4 -2 L4 -2 L3 7 L-3 7 Z" fill="#f7a826" stroke="{{ $ink }}" stroke-width="1.2" stroke-linejoin="round" />
                <path d="M-4.6 -2 C-4.6 -9 4.6 -9 4.6 -2 Z" fill="#f3d9bd" stroke="{{ $ink }}" stroke-width="1.2" stroke-linejoin="round" />
            </g>

            <path d="M52 64 L61.5 196 Q62 204 69 204 L131 204 Q138 204 138.5 196 L148 64" fill="none" stroke="{{ $ink }}" stroke-width="3.5" stroke-linejoin="round" />

            @if ($type === 'frappe')
                <path d="M50 68 C42 56 56 44 68 50 C66 38 80 31 90 38 C94 25 112 23 116 35 C126 29 141 37 136 50 C148 47 158 58 150 68 Z" fill="{{ $art['whip'] }}" stroke="{{ $ink }}" stroke-width="3" stroke-linejoin="round" />
                <path d="M68 53 C78 47 88 51 96 47 M90 41 C100 37 108 41 114 37 M116 53 C124 49 132 53 137 51" fill="none" stroke="{{ $ink }}" stroke-opacity=".3" stroke-width="2" stroke-linecap="round" />
                <path d="M58 60 L71 47 L80 59 L92 41 L100 57 L112 38 L118 56 L130 45 L141 61" fill="none" stroke="{{ $art['drizzle'] }}" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M44 68 C44 30 70 14 100 14 C130 14 156 30 156 68" fill="none" stroke="{{ $ink }}" stroke-width="3.5" />
                <path d="M57 50 C59 36 71 26 85 22" fill="none" stroke="#ffffff" stroke-opacity=".8" stroke-width="3" stroke-linecap="round" />
                <rect x="40" y="62" width="120" height="10" rx="5" fill="#fff8ef" stroke="{{ $ink }}" stroke-width="3.5" />
            @else
                <rect x="43" y="54" width="114" height="13" rx="6.5" fill="#fff8ef" stroke="{{ $ink }}" stroke-width="3.5" />
            @endif

            <line x1="{{ $straw[0] }}" y1="{{ $straw[1] }}" x2="{{ $straw[4] }}" y2="{{ $straw[5] }}" stroke="{{ $ink }}" stroke-width="11" stroke-linecap="round" />
            <line x1="{{ $straw[0] }}" y1="{{ $straw[1] }}" x2="{{ $straw[4] }}" y2="{{ $straw[5] }}" stroke="#ee7f1a" stroke-width="5.5" stroke-linecap="round" />
        @elseif ($type === 'plate')
            <ellipse cx="100" cy="158" rx="92" ry="46" fill="#ffffff" stroke="{{ $ink }}" stroke-width="3.5" />
            <ellipse cx="100" cy="154" rx="72" ry="32" fill="#fff8ef" stroke="{{ $ink }}" stroke-opacity=".12" stroke-width="2" />

            <path d="M30 160 C24 128 52 108 74 116 C92 104 120 116 118 146 C120 166 40 176 30 160 Z" fill="#ffffff" stroke="{{ $ink }}" stroke-width="3" stroke-linejoin="round" />
            <path d="M48 132 l5 -2 M66 126 l5 1 M84 128 l4 -3 M58 146 l5 -1 M78 142 l5 2 M98 136 l4 -2 M94 152 l5 1 M46 152 l4 -2" stroke="{{ $ink }}" stroke-opacity=".22" stroke-width="2" stroke-linecap="round" />
            <g fill="#e9b44c">
                <circle cx="56" cy="124" r="2" />
                <circle cx="88" cy="120" r="2" />
                <circle cx="72" cy="152" r="2" />
                <circle cx="104" cy="144" r="2" />
                <circle cx="44" cy="142" r="2" />
            </g>

            <path d="M112 112 C116 90 156 86 170 104 C186 124 166 142 144 138 C124 144 106 132 112 112 Z" fill="#ffffff" stroke="{{ $ink }}" stroke-width="3" stroke-linejoin="round" />
            <circle cx="142" cy="114" r="14" fill="#f7a826" stroke="{{ $ink }}" stroke-width="3" />
            <ellipse cx="137" cy="109" rx="4" ry="2.6" fill="#ffffff" fill-opacity=".85" />

            @if (($art['side'] ?? null) === 'lumpia')
                <g transform="rotate(-10 106 162)">
                    <rect x="58" y="151" width="94" height="22" rx="11" fill="#d98a2b" stroke="{{ $ink }}" stroke-width="3" />
                    <path d="M82 154 l-6 16 M102 154 l-6 16 M122 154 l-6 16 M142 154 l-6 16" stroke="#a35d12" stroke-width="2.5" stroke-linecap="round" />
                </g>
                <g transform="rotate(-4 112 178)">
                    <rect x="66" y="167" width="92" height="22" rx="11" fill="#e8a142" stroke="{{ $ink }}" stroke-width="3" />
                    <path d="M90 170 l-6 16 M110 170 l-6 16 M130 170 l-6 16 M150 170 l-6 16" stroke="#a35d12" stroke-width="2.5" stroke-linecap="round" />
                </g>
            @else
                @foreach ([[78, 182], [112, 188], [146, 178]] as [$x, $y])
                    <g transform="translate({{ $x }} {{ $y }})">
                        <path d="M-17 2 C-19 -14 -10 -22 0 -22 C10 -22 19 -14 17 2 C10 8 -10 8 -17 2 Z" fill="#ecc57c" stroke="{{ $ink }}" stroke-width="3" stroke-linejoin="round" />
                        <path d="M-9 -12 C-6 -8 -4 -8 -3 -4 M9 -12 C6 -8 4 -8 3 -4" stroke="{{ $ink }}" stroke-opacity=".35" stroke-width="2" stroke-linecap="round" fill="none" />
                        <ellipse cy="-17" rx="6" ry="3.5" fill="#ee7f1a" stroke="{{ $ink }}" stroke-width="2" />
                    </g>
                @endforeach
            @endif
        @elseif ($type === 'tisa')
            <ellipse cx="100" cy="122" rx="66" ry="14" fill="#f3e6d4" stroke="{{ $ink }}" stroke-width="3.5" />
            @foreach ([[100, 102], [78, 114], [122, 114], [100, 126]] as [$x, $y])
                <g transform="translate({{ $x }} {{ $y }})">
                    <path d="M-17 2 C-19 -14 -10 -22 0 -22 C10 -22 19 -14 17 2 C10 8 -10 8 -17 2 Z" fill="#ecc57c" stroke="{{ $ink }}" stroke-width="3" stroke-linejoin="round" />
                    <path d="M-9 -12 C-6 -8 -4 -8 -3 -4 M9 -12 C6 -8 4 -8 3 -4" stroke="{{ $ink }}" stroke-opacity=".35" stroke-width="2" stroke-linecap="round" fill="none" />
                    <ellipse cy="-17" rx="6" ry="3.5" fill="#ee7f1a" stroke="{{ $ink }}" stroke-width="2" />
                </g>
            @endforeach
            <g fill="#c9361e">
                <circle cx="88" cy="98" r="2.6" />
                <circle cx="112" cy="94" r="2.2" />
                <circle cx="68" cy="108" r="2" />
                <circle cx="132" cy="110" r="2.4" />
                <circle cx="96" cy="120" r="2" />
                <circle cx="110" cy="124" r="1.8" />
            </g>
            <path d="M34 122 C36 160 52 198 100 200 C148 198 164 160 166 122 C150 136 50 136 34 122 Z" fill="#ffffff" stroke="{{ $ink }}" stroke-width="3.5" stroke-linejoin="round" />
            <path d="M40 150 C62 164 138 164 160 150" stroke="#ee7f1a" stroke-width="5" fill="none" stroke-linecap="round" />
            <g transform="translate(160 106)">
                <circle r="14" fill="#7fb241" stroke="{{ $ink }}" stroke-width="3" />
                <circle r="9" fill="#e6efa0" />
                <path d="M0 -9 V9 M-9 0 H9 M-6 -6 L6 6 M6 -6 L-6 6" stroke="#b8c96a" stroke-width="1.5" />
            </g>
        @elseif ($type === 'fries')
            <g fill="#f9c74f" stroke="{{ $ink }}" stroke-width="2.5" stroke-linejoin="round">
                <rect x="60" y="56" width="12" height="80" rx="3" transform="rotate(-10 66 96)" />
                <rect x="76" y="40" width="12" height="90" rx="3" transform="rotate(-4 82 85)" />
                <rect x="124" y="54" width="12" height="80" rx="3" transform="rotate(12 130 94)" />
                <rect x="108" y="38" width="12" height="90" rx="3" transform="rotate(7 114 83)" />
                <rect x="92" y="30" width="12" height="96" rx="3" transform="rotate(2 98 78)" />
                <rect x="84" y="52" width="12" height="80" rx="3" transform="rotate(-14 90 92)" />
                <rect x="104" y="50" width="12" height="80" rx="3" transform="rotate(14 110 90)" />
            </g>
            <g fill="#ee7f1a">
                <circle cx="62" cy="62" r="1.8" />
                <circle cx="81" cy="46" r="1.8" />
                <circle cx="98" cy="36" r="1.8" />
                <circle cx="116" cy="44" r="1.8" />
                <circle cx="133" cy="62" r="1.8" />
                <circle cx="87" cy="60" r="1.8" />
                <circle cx="111" cy="57" r="1.8" />
            </g>
            <path d="M46 112 Q100 134 154 112 L140 204 Q100 210 60 204 Z" fill="#ee7f1a" stroke="{{ $ink }}" stroke-width="3.5" stroke-linejoin="round" />
            <path d="M51 142 Q100 160 149 142" stroke="#fff8ef" stroke-width="5" fill="none" stroke-linecap="round" />
            <g transform="translate(100 178)">
                <circle r="15" fill="#fff8ef" stroke="{{ $ink }}" stroke-width="2.5" />
                <path d="M-8 5 A8 8 0 0 1 8 5 Z" fill="#a8a754" />
                <path d="M-3.5 -1 L3.5 -1 L2.6 7 L-2.6 7 Z" fill="#f7a826" stroke="{{ $ink }}" stroke-width="1.1" stroke-linejoin="round" />
            </g>
        @elseif ($type === 'nachos')
            <g fill="#f2c04b" stroke="{{ $ink }}" stroke-width="2.5" stroke-linejoin="round">
                <path d="M46 122 L70 64 L100 118 Z" />
                <path d="M84 118 L110 52 L134 116 Z" />
                <path d="M116 122 L152 72 L162 126 Z" />
                <path d="M62 126 L92 84 L114 128 Z" />
                <path d="M102 128 L130 88 L148 130 Z" />
            </g>
            <path d="M58 116 C64 98 82 106 94 98 C106 90 120 102 132 96 C146 92 152 106 148 118 Z" fill="#f7a826" stroke="{{ $ink }}" stroke-width="2.5" stroke-linejoin="round" />
            <path d="M74 104 c0 8 4 10 4 14 M118 100 c0 8 4 12 3 18" stroke="#f7a826" stroke-width="5" stroke-linecap="round" fill="none" />
            <path d="M96 92 C98 82 114 82 116 92 C112 98 100 98 96 92 Z" fill="#ffffff" stroke="{{ $ink }}" stroke-width="2.2" />
            <g stroke="{{ $ink }}" stroke-width="2">
                <circle cx="80" cy="102" r="5.5" fill="#7fb241" />
                <circle cx="132" cy="104" r="5" fill="#7fb241" />
                <circle cx="106" cy="108" r="4.5" fill="#c9361e" />
            </g>
            <path d="M30 118 Q100 130 170 118 C164 170 136 202 100 202 C64 202 36 170 30 118 Z" fill="#a8a754" stroke="{{ $ink }}" stroke-width="3.5" stroke-linejoin="round" />
            <path d="M38 146 Q100 162 162 146" stroke="#fff8ef" stroke-opacity=".75" stroke-width="4" fill="none" stroke-linecap="round" />
        @elseif ($type === 'noodles')
            <g stroke="{{ $ink }}" stroke-opacity=".25" stroke-width="3" fill="none" stroke-linecap="round">
                <path d="M78 64 C70 54 86 46 78 34" />
                <path d="M98 58 C90 48 106 40 98 28" />
            </g>
            <g stroke-linecap="round">
                <line x1="128" y1="16" x2="102" y2="120" stroke="{{ $ink }}" stroke-width="9" />
                <line x1="128" y1="16" x2="102" y2="120" stroke="#c58a4a" stroke-width="4.5" />
                <line x1="150" y1="24" x2="114" y2="120" stroke="{{ $ink }}" stroke-width="9" />
                <line x1="150" y1="24" x2="114" y2="120" stroke="#c58a4a" stroke-width="4.5" />
            </g>
            <ellipse cx="100" cy="118" rx="74" ry="20" fill="#f4b860" stroke="{{ $ink }}" stroke-width="3.5" />
            <g stroke="#e07b27" stroke-width="3" fill="none" stroke-linecap="round">
                <path d="M44 116 c6 -7 12 7 18 0 s12 7 18 0 s12 7 18 0 s12 7 18 0 s12 7 18 0 s12 7 18 0" />
                <path d="M58 106 c6 -7 12 7 18 0 s12 7 18 0 s12 7 18 0 s12 7 18 0" />
                <path d="M56 126 c6 -7 12 7 18 0 s12 7 18 0 s12 7 18 0 s12 7 18 0 s12 7 18 0" />
            </g>
            <g fill="#c9361e">
                <rect x="70" y="110" width="4" height="4" rx="1" transform="rotate(20 72 112)" />
                <rect x="112" y="104" width="4" height="4" rx="1" transform="rotate(-15 114 106)" />
                <rect x="128" y="120" width="4" height="4" rx="1" />
                <rect x="88" y="124" width="4" height="4" rx="1" transform="rotate(35 90 126)" />
            </g>
            <path d="M76 100 l14 4 M120 126 l12 -3" stroke="#2f3b1f" stroke-width="4" stroke-linecap="round" />
            <path d="M26 118 C28 168 60 204 100 204 C140 204 172 168 174 118 C160 138 40 138 26 118 Z" fill="#2a1d15" stroke="{{ $ink }}" stroke-width="3.5" stroke-linejoin="round" />
            <path d="M36 150 C60 170 140 170 164 150" stroke="#ee7f1a" stroke-width="5" fill="none" stroke-linecap="round" />
            <path d="M100 172 c-8 6 -9 14 -3 19 c-1 -5 2 -8 5 -9 c-1 4 1 7 4 9 c5 -6 2 -13 -6 -19 Z" fill="#e8501e" />
        @elseif ($type === 'pouch')
            <path d="M56 44 L144 44 L152 196 C120 206 80 206 48 196 Z" fill="#6f8f3a" stroke="{{ $ink }}" stroke-width="3.5" stroke-linejoin="round" />
            <path d="M56 44 L144 44 L145 62 L55 62 Z" fill="#5a7a2c" stroke="{{ $ink }}" stroke-width="3" stroke-linejoin="round" />
            <path d="M56 44 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6" fill="#5a7a2c" stroke="{{ $ink }}" stroke-width="2.5" stroke-linejoin="round" />
            <text x="100" y="90" text-anchor="middle" font-family="'Bricolage Grotesque', sans-serif" font-weight="800" font-size="19" fill="#fff8ef">CRISPY</text>
            <text x="100" y="106" text-anchor="middle" font-family="'Instrument Sans', sans-serif" font-weight="700" font-size="9" letter-spacing="1" fill="#fff8ef" fill-opacity=".85">FRUITS &amp; VEGGIES</text>
            <ellipse cx="100" cy="152" rx="34" ry="32" fill="#fff8ef" stroke="{{ $ink }}" stroke-width="2.5" />
            <g stroke="{{ $ink }}" stroke-width="1.6">
                <rect x="78" y="138" width="14" height="8" rx="3" fill="#ee7f1a" transform="rotate(-20 85 142)" />
                <rect x="98" y="132" width="12" height="9" rx="3" fill="#7fb241" transform="rotate(15 104 136)" />
                <circle cx="118" cy="148" r="6" fill="#f6c343" />
                <rect x="86" y="156" width="13" height="8" rx="3" fill="#d33a5a" transform="rotate(25 92 160)" />
                <circle cx="106" cy="166" r="5" fill="#8a5bb0" />
                <rect x="112" y="158" width="12" height="7" rx="3" fill="#ee7f1a" transform="rotate(-30 118 161)" />
                <circle cx="80" cy="162" r="4.5" fill="#7fb241" />
                <rect x="98" y="148" width="10" height="7" rx="3" fill="#f6c343" transform="rotate(-8 103 151)" />
            </g>
        @endif
    </svg>
@endif
