{{-- A standalone drawing that MenuArtController serves as an image. --}}
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 220" width="200" height="220">
    @include('menu-art.defs')
    @include($drawing->partial())
</svg>
