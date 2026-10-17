@use('App\View\Components\MenuArt')

@php
    $type = $art['type'];
    $logo = asset('images/logo-mark.webp');
    $ink = '#1f1510';
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
            @php
                $straw = $straw();
                $surface = $surface();
                $cup = MenuArt::cupPath();
                $liquid = $liquidPath();
                $topColor = $art['layers'][array_key_last($art['layers'])][0];
                $isFrappe = $type === 'frappe';
            @endphp

            <ellipse cx="104" cy="207" rx="60" ry="10" fill="url(#ra-shadow)" />

            {{-- Straw first, so the drink hides the part below its surface --}}
            <g transform="translate({{ $straw['x'] }} {{ $straw['y'] }}) rotate({{ $straw['angle'] }})">
                <rect x="-4" width="8" height="{{ $straw['length'] }}" rx="4" fill="url(#ra-straw)" />
            </g>

            <path d="{{ $cup }}" fill="#ffffff" fill-opacity=".22" />

            <g clip-path="url(#ra-cup)">
                <g filter="url(#ra-soft)" @if ($type === 'soda') opacity=".88" @endif>
                    <path d="{{ $liquid }}" fill="{{ $topColor }}" />
                    @foreach ($bands() as $band)
                        <path d="{{ $band['d'] }}" fill="{{ $band['fill'] }}" />
                    @endforeach
                    @foreach ($tendrils() as $tendril)
                        <path d="{{ $tendril['d'] }}" stroke="{{ $tendril['stroke'] }}" stroke-width="{{ $tendril['width'] }}" stroke-linecap="round" fill="none" opacity=".45" />
                    @endforeach
                </g>

                <ellipse cx="{{ $surface['cx'] }}" cy="{{ $surface['cy'] }}" rx="{{ $surface['rx'] }}" ry="{{ $surface['ry'] }}" fill="{{ $surface['fill'] }}" @if ($type === 'soda') opacity=".85" @endif />
                <ellipse cx="{{ $surface['cx'] }}" cy="{{ $surface['cy'] }}" rx="{{ $surface['rx'] }}" ry="{{ $surface['ry'] }}" fill="url(#ra-sheen)" opacity=".5" />

                <path d="{{ $liquid }}" fill="url(#ra-cyl)" />
                <path d="{{ $liquid }}" fill="url(#ra-depth)" />
                @if ($isFrappe)
                    <path d="{{ $liquid }}" fill="#ffffff" filter="url(#ra-grain)" opacity=".4" />
                @endif

                @foreach ($speckles() as $speck)
                    <circle cx="{{ $speck['x'] }}" cy="{{ $speck['y'] }}" r="{{ $speck['r'] }}" fill="{{ $art['speckle'] }}" opacity=".85" />
                @endforeach

                @foreach ($bubbles() as $bubble)
                    <circle cx="{{ $bubble['x'] }}" cy="{{ $bubble['y'] }}" r="{{ $bubble['r'] }}" fill="#ffffff" fill-opacity=".18" stroke="#ffffff" stroke-opacity=".8" stroke-width=".6" />
                    <circle cx="{{ $bubble['x'] - $bubble['r'] * 0.35 }}" cy="{{ $bubble['y'] - $bubble['r'] * 0.35 }}" r="{{ max(0.3, $bubble['r'] * 0.3) }}" fill="#ffffff" />
                @endforeach

                @foreach ($ice() as $cube)
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
                <image href="{{ $logo }}" x="79" y="118" width="42" height="42" />
            </g>

            <g clip-path="url(#ra-cup)">
                @foreach ($droplets() as $drop)
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
                    @foreach ($points(26, 100, 36, 40, 16, 5) as $crumb)
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
        @elseif ($type === 'plate')
            <ellipse cx="102" cy="190" rx="98" ry="18" fill="url(#ra-shadow)" />
            <ellipse cx="100" cy="160" rx="94" ry="44" fill="url(#ra-plate)" />
            <ellipse cx="100" cy="157" rx="73" ry="31" fill="url(#ra-plate-well)" />
            <path d="M27 157 A73 31 0 0 1 173 157" fill="none" stroke="#000000" stroke-opacity=".06" stroke-width="2" />
            <path d="M8 163 A94 44 0 0 0 192 163" fill="none" stroke="#ffffff" stroke-width="1.6" />

            {{-- Garlic rice --}}
            <ellipse cx="76" cy="160" rx="44" ry="9" fill="#000000" opacity=".1" filter="url(#ra-blur)" />
            <path d="M30 158 C26 126 52 106 76 112 C96 100 124 112 122 146 C124 168 40 178 30 158 Z" fill="url(#ra-rice)" />
            <g clip-path="url(#ra-rice-mound)">
                @foreach ($points(110, 76, 140, 48, 32, 1) as $grain)
                    <ellipse cx="{{ $grain['x'] }}" cy="{{ $grain['y'] }}" rx="2.6" ry="1.15" transform="rotate({{ $grain['rotate'] }} {{ $grain['x'] }} {{ $grain['y'] }})" fill="#fffefa" stroke="#d9cdb8" stroke-opacity=".55" stroke-width=".35" />
                @endforeach
                @foreach ($points(14, 76, 138, 34, 20, 2) as $bit)
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

            @if (($art['side'] ?? null) === 'lumpia')
                @foreach ([['angle' => -10, 'cx' => 106, 'cy' => 162, 'x' => 58, 'y' => 151, 'w' => 94], ['angle' => -4, 'cx' => 112, 'cy' => 178, 'x' => 66, 'y' => 167, 'w' => 92]] as $index => $roll)
                    <g transform="rotate({{ $roll['angle'] }} {{ $roll['cx'] }} {{ $roll['cy'] }})">
                        <ellipse cx="{{ $roll['x'] + $roll['w'] / 2 }}" cy="{{ $roll['y'] + 22 }}" rx="{{ $roll['w'] / 2 }}" ry="4" fill="#000000" opacity=".16" filter="url(#ra-blur)" />
                        <rect x="{{ $roll['x'] }}" y="{{ $roll['y'] }}" width="{{ $roll['w'] }}" height="22" rx="11" fill="url(#ra-fried)" />
                        <rect x="{{ $roll['x'] }}" y="{{ $roll['y'] }}" width="{{ $roll['w'] }}" height="22" rx="11" fill="#ffffff" filter="url(#ra-grain)" opacity=".35" />
                        @foreach ($points(16, $roll['x'] + $roll['w'] / 2, $roll['y'] + 10, $roll['w'] / 2 - 9, 7, 10 + $index) as $blister)
                            <ellipse cx="{{ $blister['x'] }}" cy="{{ $blister['y'] }}" rx="{{ 1.5 + $blister['size'] * 2 }}" ry="{{ 1 + $blister['size'] }}" fill="{{ $blister['size'] > 0.55 ? '#fbe2a0' : '#7a3a0b' }}" opacity=".45" />
                        @endforeach
                        <ellipse cx="{{ $roll['x'] + 4 }}" cy="{{ $roll['y'] + 11 }}" rx="4" ry="10" fill="#6a330a" opacity=".3" />
                        <ellipse cx="{{ $roll['x'] + $roll['w'] - 4 }}" cy="{{ $roll['y'] + 11 }}" rx="4" ry="10" fill="#6a330a" opacity=".3" />
                    </g>
                @endforeach
            @else
                @foreach ([[78, 182], [112, 188], [146, 178]] as [$x, $y])
                    <x-menu-art-siomai :x="$x" :y="$y" />
                @endforeach
            @endif
        @elseif ($type === 'tisa')
            <ellipse cx="102" cy="201" rx="76" ry="12" fill="url(#ra-shadow)" />
            <ellipse cx="100" cy="122" rx="66" ry="14" fill="#e3dacb" />
            <ellipse cx="100" cy="125" rx="58" ry="9" fill="#cfc4b2" />
            @foreach ([[100, 102], [78, 114], [122, 114], [100, 126]] as [$x, $y])
                <x-menu-art-siomai :x="$x" :y="$y" />
            @endforeach
            @foreach ([[88, 96, 4.2, 2.2], [113, 92, 3.6, 2], [67, 107, 3.4, 1.8], [133, 108, 3.8, 2.1], [96, 119, 3.2, 1.7], [111, 123, 2.8, 1.5], [80, 112, 2.4, 1.3]] as [$x, $y, $rx, $ry])
                <ellipse cx="{{ $x }}" cy="{{ $y }}" rx="{{ $rx }}" ry="{{ $ry }}" fill="url(#ra-chili)" opacity=".92" />
                <ellipse cx="{{ $x - $rx * 0.3 }}" cy="{{ $y - $ry * 0.35 }}" rx="{{ $rx * 0.3 }}" ry="{{ $ry * 0.25 }}" fill="#ffffff" opacity=".7" />
            @endforeach
            @foreach ($points(10, 100, 108, 26, 12, 4) as $bit)
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
        @elseif ($type === 'fries')
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
            <image href="{{ $logo }}" x="80" y="149" width="40" height="40" />
        @elseif ($type === 'nachos')
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
        @elseif ($type === 'noodles')
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
            @foreach ($points(14, 100, 116, 56, 13, 3) as $flake)
                <rect x="{{ $flake['x'] }}" y="{{ $flake['y'] }}" width="{{ 1.6 + $flake['size'] * 2 }}" height="{{ 1.2 + $flake['size'] }}" rx=".5" transform="rotate({{ $flake['rotate'] }} {{ $flake['x'] }} {{ $flake['y'] }})" fill="{{ $flake['size'] > 0.6 ? '#b8200e' : '#2f3b1f' }}" />
            @endforeach
            @foreach ($points(10, 100, 114, 50, 12, 6) as $seed)
                <ellipse cx="{{ $seed['x'] }}" cy="{{ $seed['y'] }}" rx="1.4" ry=".7" transform="rotate({{ $seed['rotate'] }} {{ $seed['x'] }} {{ $seed['y'] }})" fill="#fff6dc" />
            @endforeach

            <path d="M26 118 C28 168 60 206 100 206 C140 206 172 168 174 118 C160 138 40 138 26 118 Z" fill="url(#ra-black-bowl)" />
            <path d="M26 118 C40 138 160 138 174 118" fill="none" stroke="#6b5d55" stroke-width="2" />
            <path d="M42 140 C46 166 62 186 82 196" stroke="#ffffff" stroke-opacity=".22" stroke-width="5" fill="none" stroke-linecap="round" />
            <path d="M38 150 C62 170 138 170 162 150" stroke="#c9361e" stroke-width="2.5" fill="none" />
        @elseif ($type === 'pouch')
            <ellipse cx="100" cy="206" rx="64" ry="9" fill="url(#ra-shadow)" />
            <path d="M56 44 L144 44 L152 198 C120 208 80 208 48 198 Z" fill="url(#ra-pouch)" />
            <path d="M56 44 L144 44 L145 62 L55 62 Z" fill="#3f5d1f" opacity=".55" />
            <path d="M56 44 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6 l5.5 -6 l5.5 6" fill="#56782c" />
            <path d="M66 66 L60 192" stroke="#ffffff" stroke-opacity=".28" stroke-width="7" stroke-linecap="round" />
            <path d="M136 66 L140 190" stroke="#ffffff" stroke-opacity=".15" stroke-width="3" stroke-linecap="round" />
            <image href="{{ $logo }}" x="88" y="64" width="24" height="24" />
            <text x="100" y="104" text-anchor="middle" font-family="'Bricolage Grotesque', sans-serif" font-weight="800" font-size="18" fill="#fff8ef">CRISPY</text>
            <text x="100" y="117" text-anchor="middle" font-family="'Instrument Sans', sans-serif" font-weight="700" font-size="7.5" letter-spacing="1" fill="#fff8ef" fill-opacity=".85">FRUITS &amp; VEGGIES</text>
            <ellipse cx="100" cy="158" rx="33" ry="30" fill="#fff8ef" opacity=".95" />
            <ellipse cx="100" cy="158" rx="33" ry="30" fill="none" stroke="#000000" stroke-opacity=".12" stroke-width="1.5" />
            @foreach ([[84, 146, 14, 8, -20, '#ee7f1a'], [104, 140, 12, 9, 15, '#7fb241'], [118, 156, 11, 11, 0, '#f6c343'], [90, 166, 13, 8, 25, '#d33a5a'], [108, 172, 10, 10, 0, '#8a5bb0'], [116, 164, 12, 7, -30, '#ee7f1a'], [80, 160, 9, 9, 0, '#7fb241'], [100, 154, 10, 7, -8, '#f6c343']] as [$x, $y, $w, $h, $angle, $color])
                <g transform="rotate({{ $angle }} {{ $x }} {{ $y }})">
                    <rect x="{{ $x - $w / 2 }}" y="{{ $y - $h / 2 }}" width="{{ $w }}" height="{{ $h }}" rx="3" fill="{{ $color }}" />
                    <rect x="{{ $x - $w / 2 }}" y="{{ $y - $h / 2 }}" width="{{ $w }}" height="{{ $h }}" rx="3" fill="url(#ra-sheen)" />
                </g>
            @endforeach
            <path d="M74 140 C80 132 90 129 98 129" stroke="#ffffff" stroke-width="2" stroke-linecap="round" fill="none" opacity=".8" />
        @endif
    </svg>
@endif
