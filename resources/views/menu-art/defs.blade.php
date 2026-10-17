{{-- Gradients, clip paths and filters that the drawings share. --}}
<defs>
    {{-- Geometry --}}
    <clipPath id="ra-cup" clipPathUnits="userSpaceOnUse">
        <path d="{{ $drawing->cupPath() }}" />
    </clipPath>
    <clipPath id="ra-above-rim" clipPathUnits="userSpaceOnUse">
        <path d="M-20 -20 H220 V50 H149 A49 9 0 0 1 51 50 H-20 Z" />
    </clipPath>
    <clipPath id="ra-rice-mound" clipPathUnits="userSpaceOnUse">
        <path d="M30 158 C26 126 52 106 76 112 C96 100 124 112 122 146 C124 168 40 178 30 158 Z" />
    </clipPath>
    <clipPath id="ra-above-dome" clipPathUnits="userSpaceOnUse">
        <path d="M-20 -20 H220 V52 H150 C150 20 127 7 100 7 C73 7 50 20 50 52 H-20 Z" />
    </clipPath>

    {{-- Light and shadow --}}
    <radialGradient id="ra-shadow">
        <stop offset="0" stop-color="#1f1510" stop-opacity=".42" />
        <stop offset=".55" stop-color="#1f1510" stop-opacity=".16" />
        <stop offset="1" stop-color="#1f1510" stop-opacity="0" />
    </radialGradient>
    <linearGradient id="ra-cyl" x1="0" x2="1">
        <stop offset="0" stop-color="#000" stop-opacity=".34" />
        <stop offset=".1" stop-color="#000" stop-opacity=".08" />
        <stop offset=".28" stop-color="#fff" stop-opacity=".2" />
        <stop offset=".42" stop-color="#fff" stop-opacity="0" />
        <stop offset=".78" stop-color="#000" stop-opacity=".06" />
        <stop offset="1" stop-color="#000" stop-opacity=".38" />
    </linearGradient>
    <linearGradient id="ra-depth" x1="0" x2="0" y1="0" y2="1">
        <stop offset="0" stop-color="#000" stop-opacity="0" />
        <stop offset="1" stop-color="#000" stop-opacity=".22" />
    </linearGradient>
    <linearGradient id="ra-plastic" x1="0" x2="1">
        <stop offset="0" stop-color="#fff" stop-opacity=".55" />
        <stop offset=".08" stop-color="#fff" stop-opacity=".12" />
        <stop offset=".5" stop-color="#fff" stop-opacity=".04" />
        <stop offset=".92" stop-color="#fff" stop-opacity=".1" />
        <stop offset="1" stop-color="#fff" stop-opacity=".45" />
    </linearGradient>
    <linearGradient id="ra-gloss" x1="0" x2="0" y1="0" y2="1">
        <stop offset="0" stop-color="#fff" stop-opacity="0" />
        <stop offset=".18" stop-color="#fff" stop-opacity=".75" />
        <stop offset=".6" stop-color="#fff" stop-opacity=".35" />
        <stop offset="1" stop-color="#fff" stop-opacity="0" />
    </linearGradient>
    <radialGradient id="ra-drop" cx=".38" cy=".32" r=".7">
        <stop offset="0" stop-color="#fff" stop-opacity=".55" />
        <stop offset=".45" stop-color="#fff" stop-opacity=".08" />
        <stop offset=".85" stop-color="#000" stop-opacity=".16" />
        <stop offset="1" stop-color="#000" stop-opacity=".05" />
    </radialGradient>
    <linearGradient id="ra-ice" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0" stop-color="#fff" stop-opacity=".85" />
        <stop offset=".45" stop-color="#fff" stop-opacity=".25" />
        <stop offset=".8" stop-color="#e8f4ff" stop-opacity=".4" />
        <stop offset="1" stop-color="#fff" stop-opacity=".7" />
    </linearGradient>
    <linearGradient id="ra-straw" x1="0" x2="1">
        <stop offset="0" stop-color="#9a4705" />
        <stop offset=".3" stop-color="#ee7f1a" />
        <stop offset=".45" stop-color="#ffc58c" />
        <stop offset=".62" stop-color="#ee7f1a" />
        <stop offset="1" stop-color="#8f4004" />
    </linearGradient>
    <linearGradient id="ra-film" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0" stop-color="#fff" stop-opacity=".55" />
        <stop offset=".5" stop-color="#fff" stop-opacity=".12" />
        <stop offset="1" stop-color="#fff" stop-opacity=".4" />
    </linearGradient>
    <radialGradient id="ra-cream" cx=".35" cy=".25" r=".85">
        <stop offset="0" stop-color="#fff" stop-opacity=".75" />
        <stop offset=".45" stop-color="#fff" stop-opacity="0" />
        <stop offset="1" stop-color="#5b3a1e" stop-opacity=".22" />
    </radialGradient>
    <radialGradient id="ra-sheen" cx=".3" cy=".25" r=".9">
        <stop offset="0" stop-color="#fff" stop-opacity=".6" />
        <stop offset=".5" stop-color="#fff" stop-opacity="0" />
        <stop offset="1" stop-color="#000" stop-opacity=".2" />
    </radialGradient>

    {{-- Food surfaces --}}
    <radialGradient id="ra-plate" cx=".45" cy=".4" r=".65">
        <stop offset="0" stop-color="#ffffff" />
        <stop offset=".75" stop-color="#f3efe8" />
        <stop offset="1" stop-color="#d9d1c4" />
    </radialGradient>
    <radialGradient id="ra-plate-well" cx=".5" cy=".55" r=".6">
        <stop offset="0" stop-color="#fdfbf7" />
        <stop offset="1" stop-color="#e6ded1" />
    </radialGradient>
    <radialGradient id="ra-rice" cx=".42" cy=".3" r=".75">
        <stop offset="0" stop-color="#fffef9" />
        <stop offset=".65" stop-color="#f1e9da" />
        <stop offset="1" stop-color="#cfc2a9" />
    </radialGradient>
    <radialGradient id="ra-egg" cx=".45" cy=".4" r=".7">
        <stop offset="0" stop-color="#ffffff" />
        <stop offset=".8" stop-color="#f7f2e8" />
        <stop offset="1" stop-color="#e3cfa8" />
    </radialGradient>
    <radialGradient id="ra-yolk" cx=".38" cy=".32" r=".75">
        <stop offset="0" stop-color="#ffe08a" />
        <stop offset=".45" stop-color="#ffb21e" />
        <stop offset="1" stop-color="#d97806" />
    </radialGradient>
    <linearGradient id="ra-fried" x1="0" x2="0" y1="0" y2="1">
        <stop offset="0" stop-color="#8a4510" />
        <stop offset=".22" stop-color="#d98a2b" />
        <stop offset=".42" stop-color="#f7cd7a" />
        <stop offset=".65" stop-color="#d48526" />
        <stop offset="1" stop-color="#6a330a" />
    </linearGradient>
    <radialGradient id="ra-wrapper" cx=".38" cy=".3" r=".8">
        <stop offset="0" stop-color="#fff4d2" />
        <stop offset=".5" stop-color="#efcb85" />
        <stop offset="1" stop-color="#b98a3f" />
    </radialGradient>
    <radialGradient id="ra-pork" cx=".45" cy=".4" r=".7">
        <stop offset="0" stop-color="#e3b58a" />
        <stop offset="1" stop-color="#a86a3e" />
    </radialGradient>
    <linearGradient id="ra-chili" x1="0" x2="0" y1="0" y2="1">
        <stop offset="0" stop-color="#ef5a2a" />
        <stop offset="1" stop-color="#9e1f0c" />
    </linearGradient>
    <linearGradient id="ra-bowl" x1="0" x2="1">
        <stop offset="0" stop-color="#d8d1c6" />
        <stop offset=".25" stop-color="#ffffff" />
        <stop offset=".6" stop-color="#f6f2ec" />
        <stop offset="1" stop-color="#cbc3b6" />
    </linearGradient>
    <radialGradient id="ra-lime" cx=".4" cy=".35" r=".7">
        <stop offset="0" stop-color="#f6f9cf" />
        <stop offset=".75" stop-color="#d9e78f" />
        <stop offset="1" stop-color="#b9cf62" />
    </radialGradient>
    <linearGradient id="ra-fry" x1="0" x2="1">
        <stop offset="0" stop-color="#c98a24" />
        <stop offset=".35" stop-color="#f9d97a" />
        <stop offset=".7" stop-color="#f0bf4c" />
        <stop offset="1" stop-color="#b8761a" />
    </linearGradient>
    <linearGradient id="ra-carton" x1="0" x2="1">
        <stop offset="0" stop-color="#a8250f" />
        <stop offset=".3" stop-color="#e2462a" />
        <stop offset=".55" stop-color="#f26a40" />
        <stop offset="1" stop-color="#b52d18" />
    </linearGradient>
    <linearGradient id="ra-chip" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0" stop-color="#fbe08f" />
        <stop offset=".55" stop-color="#efbd4e" />
        <stop offset="1" stop-color="#c98a24" />
    </linearGradient>
    <linearGradient id="ra-cheese" x1="0" x2="0" y1="0" y2="1">
        <stop offset="0" stop-color="#ffc642" />
        <stop offset="1" stop-color="#e98300" />
    </linearGradient>
    <linearGradient id="ra-ceramic" x1="0" x2="1">
        <stop offset="0" stop-color="#5f6127" />
        <stop offset=".3" stop-color="#a8a754" />
        <stop offset=".5" stop-color="#c8c77e" />
        <stop offset="1" stop-color="#6f7030" />
    </linearGradient>
    <radialGradient id="ra-carbonara" cx=".45" cy=".4" r=".7">
        <stop offset="0" stop-color="#ffd99a" />
        <stop offset=".7" stop-color="#f4a24a" />
        <stop offset="1" stop-color="#d9772a" />
    </radialGradient>
    <linearGradient id="ra-black-bowl" x1="0" x2="1">
        <stop offset="0" stop-color="#050302" />
        <stop offset=".3" stop-color="#2b231e" />
        <stop offset=".45" stop-color="#4a3f38" />
        <stop offset=".6" stop-color="#1c1612" />
        <stop offset="1" stop-color="#050302" />
    </linearGradient>
    <linearGradient id="ra-wood" x1="0" x2="1">
        <stop offset="0" stop-color="#7a4a2b" />
        <stop offset=".45" stop-color="#d9a066" />
        <stop offset="1" stop-color="#8b5a3c" />
    </linearGradient>
    <linearGradient id="ra-pouch" x1="0" x2="1">
        <stop offset="0" stop-color="#3f5d1f" />
        <stop offset=".25" stop-color="#6f8f3a" />
        <stop offset=".4" stop-color="#a6c46a" />
        <stop offset=".55" stop-color="#6f8f3a" />
        <stop offset="1" stop-color="#405f20" />
    </linearGradient>

    {{-- Filters --}}
    <filter id="ra-soft" x="-10%" y="-10%" width="120%" height="120%">
        <feGaussianBlur stdDeviation="2.6" />
    </filter>
    <filter id="ra-blur" x="-20%" y="-20%" width="140%" height="140%">
        <feGaussianBlur stdDeviation="1" />
    </filter>
    <filter id="ra-blur-lg" x="-50%" y="-50%" width="200%" height="200%">
        <feGaussianBlur stdDeviation="4" />
    </filter>
    <filter id="ra-grain" x="0" y="0" width="100%" height="100%">
        <feTurbulence type="fractalNoise" baseFrequency="1.1" numOctaves="2" seed="7" result="noise" />
        <feColorMatrix in="noise" type="matrix" values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  2.4 0 0 0 -1.15" result="specks" />
        <feComposite in="specks" in2="SourceGraphic" operator="in" />
    </filter>
    <filter id="ra-grain-dark" x="0" y="0" width="100%" height="100%">
        <feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" seed="3" result="noise" />
        <feColorMatrix in="noise" type="matrix" values="0 0 0 0 .25  0 0 0 0 .12  0 0 0 0 .03  2.6 0 0 0 -1.3" result="specks" />
        <feComposite in="specks" in2="SourceGraphic" operator="in" />
    </filter>
</defs>
