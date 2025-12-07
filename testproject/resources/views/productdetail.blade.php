@extends('layouts.master')

@section('title', $product->name . ' – The Boys')

@section('content')

@php
    use Illuminate\Support\Str;

    $loggedIn = session('user_id') !== null;
    $userSlug = $loggedIn ? Str::slug(session('name')) : null;
@endphp

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

            {{-- QUANTITY --}}
            <p style="font-size:0.9rem;font-weight:500;color:#111827;">
                Quantity: {{ $product->quantity }}
            </p>

            {{-- DESCRIPTION --}}
            <p style="font-size:0.9rem;color:var(--tb-gray-text);">
                {{ $product->description ?? 'No description available.' }}
            </p>

            {{-- ADD TO CART --}}
            @if($loggedIn)
                <form method="POST"
                    action="{{ route('cart.add', [
                        'username' => $userSlug,
                        'product'  => $product->id,
                    ]) }}"
                    class="d-inline-block tb-add-to-cart-form"
                    data-max="{{ $product->quantity }}">
                    @csrf
                    <input type="hidden" name="quantity" value="1">

                    {{-- State 1 --}}
                    <div class="d-flex tb-add-to-cart-inactive">
                        <button type="button" class="tb-btn-primary tb-add-to-cart-trigger">
                            Add to Cart
                        </button>
                    </div>

                    {{-- State 2 --}}
                    <div class="d-flex align-items-center tb-add-to-cart-active d-none" style="gap:0.4rem;">
                        <div class="d-flex align-items-center" style="gap:0.3rem;">
                            <button type="button"
                                    class="btn btn-sm"
                                    style="border-radius:999px;border:1px solid #d1d5db;padding:0.1rem 0.5rem;"
                                    data-role="qty-minus">
                                –
                            </button>
                            <div class="tb-qty-display" style="min-width:28px;text-align:center;font-weight:500;">
                                1
                            </div>
                            <button type="button"
                                    class="btn btn-sm"
                                    style="border-radius:999px;border:1px solid #d1d5db;padding:0.1rem 0.5rem;"
                                    data-role="qty-plus">
                                +
                            </button>
                        </div>

                        <button type="submit" class="tb-btn-primary">
                            Add to Cart
                        </button>
                    </div>
                </form>
            @else
                <a href="{{ route('login') }}"
                class="tb-btn-primary"
                style="display:inline-flex;align-items:center;justify-content:center;">
                    Add to Cart
                </a>
            @endif

            {{-- BACK --}}
            <a href="{{ $loggedIn ? route('home.user', ['username' => $userSlug]) : route('home') }}"
               class="d-inline-flex align-items-center"
               style="
                    gap:0.35rem;
                    margin-left:0.5rem;
                    padding:0.4rem 0.9rem;
                    border-radius:999px;
                    background:#9ca3af;
                    color:#ffffff;
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