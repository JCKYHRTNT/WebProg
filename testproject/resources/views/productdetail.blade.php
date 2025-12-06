@extends('layouts.master')

@section('title', $product->name . ' – The Boys')

@section('content')

<div class="tb-card p-4">

    <div class="row g-3">

        {{-- IMAGE --}}
        <div class="col-md-5">
            <div class="ratio ratio-4x3">
                <img
                    src="{{ $product->image }}"
                    alt="{{ $product->name }}"
                    class="w-100 h-100"
                    style="object-fit:cover;"
                >
            </div>
        </div>

        {{-- DETAILS --}}
        <div class="col-md-7">

            {{-- CATEGORY --}}
            @if($product->category_id && $product->category)
                <a href="{{ url('/?category=' . $product->category_id) }}">
                    <span class="badge rounded-pill"
                        style="background:#facc15;color:#111827;font-size:0.75rem;">
                        {{ strtoupper($product->category->name) }}
                    </span>
                </a>
            @else
                <span class="badge rounded-pill"
                    style="background:#9ca3af;color:#111827;font-size:0.75rem;">
                    UNCATEGORIZED
                </span>
            @endif

            {{-- NAME --}}
            <h1 class="mt-2 mb-2" style="font-size:1.4rem;font-weight:600;">
                {{ $product->name }}
            </h1>

            {{-- PRICE --}}
            <p style="font-size:1rem;font-weight:600;color:var(--tb-blue);">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </p>

            {{-- DESCRIPTION --}}
            <p style="font-size:0.9rem;color:var(--tb-gray-text);">
                {{ $product->description ?? 'No description available.' }}
            </p>

            {{-- ADD TO CART --}}
            <form method="POST" action="{{ route('cart.add', $product->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="tb-btn-primary">
                    Add to Cart
                </button>
            </form>

            {{-- BACK --}}
            <a href="{{ route('home') }}"
                class="d-inline-flex align-items-center"
                style="
                        gap:0.35rem;
                        margin-left:0.5rem;
                        padding:0.4rem 0.9rem;
                        border-radius:999px;
                        background:#9ca3af;     /* gray background */
                        color:#ffffff;          /* white text */
                        font-size:0.85rem;
                        font-weight:500;
                        text-decoration:none;
                        transition:background 0.2s ease;
                "
                onmouseover="this.style.background='#000000'"
                onmouseout="this.style.background='#9ca3af'">

                    <img
                        src="{{ asset('images/home_icon.png') }}"
                        alt="Home"
                        style="height:16px;width:16px;opacity:0.85;"
                    >

                    Back To Home
                </a>
        </div>
    </div>

</div>

@endsection