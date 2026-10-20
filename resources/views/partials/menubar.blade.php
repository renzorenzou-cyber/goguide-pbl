<nav class="gg-menubar" aria-label="Menu utama">
    <a href="{{ route('home') }}#destinations" class="gg-mi">
        <i class="fa-solid fa-location-dot"></i> Destinations
    </a>
    <a href="{{ route('home') }}#guides" class="gg-mi">
        <i class="fa-solid fa-user-group"></i> Local Guides
    </a>
    <a href="{{ route('reviews.index') }}" class="gg-mi {{ request()->routeIs('reviews.*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-star"></i> Reviews
    </a>
    <a href="{{ route('home') }}#features" class="gg-mi">
        <i class="fa-solid fa-shield-halved"></i> Why Us
    </a>
</nav>