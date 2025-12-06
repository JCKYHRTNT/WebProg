@extends('layouts.master')

@section('title', 'Your Cart – The Boys')

@section('content')

<div class="tb-card p-3 p-md-4 mb-3">
    <h2 style="font-size:1.2rem;font-weight:600;margin-bottom:1rem;">Shopping Cart</h2>

    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2">
            {{ session('error') }}
        </div>
    @endif

    @if($items->isEmpty())
        <p style="font-size:0.95rem;color:var(--tb-gray-text);">Your cart is empty.</p>
    @else

        {{-- Cart items list --}}
        <div class="d-flex flex-column" style="gap:0.75rem;">
            @foreach($items as $item)
                @php
                    $product = $item->product;
                    $categoryName = $product->category->name ?? 'Uncategorized';
                    $lineTotal = $product->price * $item->quantity;
                @endphp

                <div class="d-flex flex-wrap align-items-center"
                     style="gap:0.75rem;padding:0.75rem;border-radius:0.75rem;border:1px solid var(--tb-gray-border);background:#f9fafb;">

                    {{-- IMAGE --}}
                    <div style="flex:0 0 90px;">
                        <div class="ratio ratio-1x1">
                            <img src="{{ $product->image }}"
                                 alt="{{ $product->name }}"
                                 class="w-100 h-100"
                                 style="object-fit:cover;border-radius:0.5rem;">
                        </div>
                    </div>

                    {{-- NAME + CATEGORY --}}
                    <div class="flex-grow-1">
                        <div style="font-size:0.95rem;font-weight:600;">
                            {{ $product->name }}
                        </div>
                        <div style="font-size:0.8rem;color:var(--tb-gray-text);">
                            {{ ucfirst($categoryName) }}
                        </div>
                    </div>

                    {{-- PRICE --}}
                    <div style="min-width:110px;text-align:right;">
                        <div style="font-size:0.85rem;color:var(--tb-gray-text);">Price</div>
                        <div style="font-weight:600;">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- QUANTITY CONTROLS --}}
                    <div style="min-width:150px;">
                        <div style="font-size:0.85rem;color:var(--tb-gray-text);text-align:center;">Quantity</div>

                        <div class="d-flex align-items-center justify-content-center" style="gap:0.4rem;">

                            {{-- - button --}}
                            <form method="POST" action="{{ route('cart.item.update', $item->id) }}">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ max($item->quantity - 1, 0) }}">
                                <button type="submit"
                                        class="btn btn-sm"
                                        style="border-radius:999px;border:1px solid #d1d5db;padding:0.1rem 0.5rem;">
                                    –
                                </button>
                            </form>

                            {{-- current quantity (readonly) --}}
                            <div style="min-width:32px;text-align:center;font-weight:500;">
                                {{ $item->quantity }}
                            </div>

                            {{-- + button --}}
                            <form method="POST" action="{{ route('cart.item.update', $item->id) }}">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                <button type="submit"
                                        class="btn btn-sm"
                                        style="border-radius:999px;border:1px solid #d1d5db;padding:0.1rem 0.5rem;">
                                    +
                                </button>
                            </form>

                        </div>
                    </div>

                    {{-- LINE TOTAL --}}
                    <div style="min-width:130px;text-align:right;">
                        <div style="font-size:0.85rem;color:var(--tb-gray-text);">Total</div>
                        <div style="font-weight:600;color:var(--tb-blue);">
                            Rp{{ number_format($lineTotal, 0, ',', '.') }}
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- CART TOTAL + PAYMENT --}}
        <div class="d-flex justify-content-end mt-3">
            <div style="min-width:260px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div style="font-size:0.95rem;font-weight:500;">Total Price</div>
                    <div style="font-size:1.05rem;font-weight:700;color:var(--tb-blue);">
                        Rp{{ number_format($total, 0, ',', '.') }}
                    </div>
                </div>

                <button
                    type="button"
                    class="tb-btn-primary w-100"
                    onclick="alert('Payment Successful');">
                    Payment
                </button>
            </div>
        </div>

    @endif
</div>

@endsection