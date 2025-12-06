@extends('layouts.master')

@section('title', 'The Boys – Home')

@section('content')

    {{-- Hero / intro --}}
    <section class="mb-4">
        <div class="tb-card p-4 p-md-4" style="
            background: radial-gradient(circle at top left, var(--tb-yellow) 0, var(--tb-blue) 45%, var(--tb-black) 100%);
            color: #f9fafb;
            border: none;
        ">
            <div class="row align-items-end gy-3">
                <div class="col-md-8">
                    <span class="badge rounded-pill" style="background:#facc15;color:#111827;font-size:0.7rem;letter-spacing:0.08em;">
                        MARKETPLACE
                    </span>
                    <h1 class="mt-2 mb-2" style="font-size:1.6rem;font-weight:600;">
                        Welcome to The Boys
                    </h1>
                    <p class="mb-0" style="font-size:0.9rem;max-width:420px;">
                        Browse electronics and toys in a clean.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Category filter row --}}
    <section class="mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-2" style="gap:0.5rem;">
            <h2 class="mb-0" style="font-size:1rem;font-weight:600;">Categories</h2>

            <div class="d-flex flex-wrap" style="gap:0.5rem;">

                @php
                    $isAllActive = is_null($categoryId);
                @endphp

                {{-- All --}}
                <a
                    href="{{ url('/').($query ? '?q='.urlencode($query) : '') }}"
                    class="tb-pill-link"
                    style="
                        background: {{ $isAllActive ? 'var(--tb-blue)' : 'rgba(15,23,42,0.06)' }};
                        color: {{ $isAllActive ? '#f9fafb' : '#111827' }};
                        font-size:0.8rem;
                        padding:0.4rem 0.9rem;
                    "
                >
                    All
                </a>

                {{-- Recent/default categories (max 3) --}}
                @foreach($recentCategories as $cat)
                    @php
                        $isActive = $categoryId === $cat['id'];
                        $catUrl = url('/?category='.$cat['id'].($query ? '&q='.urlencode($query) : ''));
                    @endphp

                    <a
                        href="{{ $catUrl }}"
                        class="tb-pill-link"
                        style="
                            background: {{ $isActive ? 'var(--tb-blue)' : 'rgba(15,23,42,0.06)' }};
                            color: {{ $isActive ? '#f9fafb' : '#111827' }};
                            font-size:0.8rem;
                            padding:0.4rem 0.9rem;
                        "
                    >
                        {{ ucfirst($cat['name']) }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(!is_null($categoryId) || $query)
            <div style="font-size:0.8rem;color:var(--tb-gray-text);">
                @if(!is_null($categoryId))
                    <span>
                        Filter:
                        <strong>{{ ucfirst($categoryNames[$categoryId] ?? 'Unknown') }}</strong>
                    </span>
                @endif

                @if(!is_null($categoryId) && $query)
                    <span> · </span>
                @endif

                @if($query)
                    <span>Search: <strong>{{ $query }}</strong></span>
                @endif
            </div>
        @endif

    </section>

    {{-- Product grid --}}
    <section>
        @if($products->isEmpty())
            <div class="tb-card p-4">
                <p class="mb-0" style="font-size:0.9rem;color:var(--tb-gray-text);">
                    No products found for this filter/search.
                </p>
            </div>
        @else
            <div class="row g-3">
                @foreach($products as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="tb-card h-100 overflow-hidden">

                            {{-- Clickable image --}}
                            <a href="{{ url('/products/'.$product['id']) }}" class="ratio ratio-4x3 d-block">
                                <img
                                    src="{{ $product['image'] }}"
                                    alt="{{ $product['name'] }}"
                                    class="w-100 h-100"
                                    style="object-fit:cover;"
                                >
                            </a>

                            <div class="p-2 p-md-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    @php
                                        $catId = $product['category_id'];

                                        if (!is_null($catId)) {
                                            $catUrl   = url('/?category=' . $catId . ($query ? '&q=' . urlencode($query) : ''));
                                            $catLabel = ucfirst($categoryNames[$catId] ?? 'Unknown');
                                        } else {
                                            $catUrl   = null;
                                            $catLabel = 'Uncategorized';
                                        }
                                    @endphp

                                    @if(!is_null($catId))
                                        <a
                                            href="{{ $catUrl }}"
                                            class="badge rounded-pill"
                                            style="background:#facc15;color:#111827;font-size:0.7rem;text-decoration:none;cursor:pointer;"
                                        >
                                            {{ $catLabel }}
                                        </a>
                                    @else
                                        <span
                                            class="badge rounded-pill"
                                            style="background:#9ca3af;color:#111827;font-size:0.7rem;"
                                        >
                                            {{ $catLabel }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Clickable name --}}
                                <h3 class="mb-1" style="font-size:0.9rem;font-weight:600;">
                                    <a href="{{ url('/products/'.$product['id']) }}" style="color:inherit;text-decoration:none;">
                                        {{ $product['name'] }}
                                    </a>
                                </h3>

                                <p class="mb-2" style="font-size:0.9rem;font-weight:600;color:var(--tb-blue);">
                                    Rp{{ number_format($product['price'], 0, ',', '.') }}
                                </p>

                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('cart.add', $product['id']) }}" class="flex-fill">
                                        @csrf
                                        <button type="submit" class="tb-btn-primary w-100">
                                            Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

@endsection