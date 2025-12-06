<header class="tb-header-fixed">
    <div class="tb-container">

        {{-- Logo --}}
        <div class="d-flex align-items-center mb-2" style="gap: 0.6rem;">
            <a href="{{ url('/') }}" class="d-inline-flex align-items-center" style="gap:0.5rem;">
                <img
                    src="{{ asset('images/theboys_logo.jpg') }}"
                    alt="The Boys Logo"
                    style="height:42px;width:auto;border-radius:0.4rem;object-fit:cover;background:#0f172a;"
                >
            </a>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:0.75rem;">

            {{-- LEFT: Search + Filter --}}
            <div class="d-flex flex-grow-1 align-items-center" style="gap:0.5rem;max-width:620px;min-width:260px;">

                {{-- Search --}}
                <form action="{{ url('/') }}" method="GET" class="d-flex flex-grow-1" style="gap:0.4rem;">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="tb-input-rounded"
                        placeholder="Search for products..."
                        style="
                            padding-left:1rem;
                            width: 480px;          /* fixed desktop width */
                            max-width: 100%;       /* shrink on smaller screens */
                        "
                    >

                    <button type="submit" class="tb-btn-primary d-inline-flex align-items-center">
                        <img
                            src="{{ asset('images/search_icon.png') }}"
                            alt="Search"
                            style="height:15px;width:15px;opacity:0.9;margin-right:0.3rem;"
                        >
                    </button>
                </form>

                {{-- Filter --}}
                @php
                    $showFilter = request()->routeIs(
                        'home',
                        'home.user',
                        'admin.user',
                        'cart',
                        'cart.admin'
                    );
                @endphp

                @if($showFilter)
                    <div class="dropdown">
                        <button
                            type="button"
                            class="tb-pill-link dropdown-toggle d-inline-flex align-items-center"
                            id="filterDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="border:1px solid rgba(148,163,184,0.5);white-space:nowrap;"
                        >
                            <img
                                src="{{ asset('images/filter_icon.png') }}"
                                alt="Filter"
                                style="height:16px;width:16px;opacity:0.85;margin-right:0.35rem;"
                            >
                            Categories
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            @php
                                $clearCategoryUrl = request('q')
                                    ? url('/?q=' . urlencode(request('q')))
                                    : url('/');
                            @endphp

                            @if(request('category'))
                                <li>
                                    <a class="dropdown-item text-danger fw-semibold" href="{{ $clearCategoryUrl }}">
                                        Clear
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif

                            @foreach(($recentCategories ?? []) as $cat)
                                @php
                                    $queryString = request('q') ? '&q=' . urlencode(request('q')) : '';
                                @endphp
                                <li>
                                    <a class="dropdown-item"
                                    href="{{ url('/?category=' . $cat['id'] . $queryString) }}">
                                        {{ ucfirst($cat['name']) }}
                                    </a>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                @endif
            </div>

            {{-- RIGHT: Navbar --}}
            <nav class="d-flex flex-wrap align-items-center justify-content-end" style="gap:0.4rem;">
                @php
                    $loggedIn = session('user_id') !== null;
                    $userSlug = $loggedIn ? \Illuminate\Support\Str::slug(session('name')) : null;
                @endphp

                {{-- HOME --}}
                @if($loggedIn)
                    <a href="{{ route('home.user', ['username' => $userSlug]) }}"
                    class="tb-pill-link d-inline-flex align-items-center"
                    style="gap:0.35rem;">
                        <img src="{{ asset('images/home_icon.png') }}" alt="Home" style="height:16px;width:16px;opacity:0.85;">
                        Home
                    </a>
                @else
                    <a href="{{ route('home') }}"
                    class="tb-pill-link d-inline-flex align-items-center"
                    style="gap:0.35rem;">
                        <img src="{{ asset('images/home_icon.png') }}" alt="Home" style="height:16px;width:16px;opacity:0.85;">
                        Home
                    </a>
                @endif

                {{-- CART (requires login to be useful, but can stay public for now) --}}
                <a href="{{ route('cart') }}" class="tb-pill-link d-inline-flex align-items-center" style="gap:0.35rem;">
                    <img src="{{ asset('images/cart_icon.png') }}" alt="Cart" style="height:16px;width:16px;opacity:0.85;">
                    Cart
                </a>

                {{-- ACCOUNT / LOGIN --}}
                @if($loggedIn)
                    <a href="{{ route('account') }}" class="tb-pill-link d-inline-flex align-items-center" style="gap:0.35rem;">
                        <img src="{{ asset('images/account_icon.png') }}" alt="Account" style="height:16px;width:16px;opacity:0.85;">
                        Account
                    </a>
                @else
                    <a href="{{ route('login') }}" class="tb-pill-link d-inline-flex align-items-center" style="gap:0.35rem;">
                        <img src="{{ asset('images/account_icon.png') }}" alt="Login" style="height:16px;width:16px;opacity:0.85;">
                        Login
                    </a>
                @endif

            </nav>
        </div>
    </div>
</header>