@extends('layouts.master')

@section('title', $product->name . ' – Admin – The Boys')

@php
    use Illuminate\Support\Str;
    $adminSlug = Str::slug(session('name'));
@endphp

@section('content')

<style>
    .tb-btn-secondary {
        display:inline-flex;align-items:center;justify-content:center;
        background:var(--tb-blue);color:#fff;border-radius:999px;
        padding:0.4rem 0.9rem;font-size:0.85rem;font-weight:500;
        border:none;cursor:pointer;text-decoration:none;
    }
    .tb-btn-secondary:hover { filter:brightness(1.12); }

    .tb-btn-danger {
        display:inline-flex;align-items:center;justify-content:center;
        background:#dc2626;color:#fff;border-radius:999px;
        padding:0.4rem 0.9rem;font-size:0.85rem;font-weight:500;
        border:none;cursor:pointer;text-decoration:none;
    }
    .tb-btn-danger:hover { background:#ef4444; }

    .tb-btn-ghost {
        display:inline-flex;align-items:center;justify-content:center;
        gap:0.35rem;
        background:#9ca3af;color:#ffffff;border-radius:999px;
        padding:0.4rem 0.9rem;font-size:0.85rem;font-weight:500;
        text-decoration:none;border:none;cursor:pointer;
        transition:background 0.2s ease;
    }
    .tb-btn-ghost:hover { background:#000000; }
</style>

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

        {{-- DETAILS + EDIT FORM --}}
        <div class="col-md-7">

            {{-- CATEGORY --}}
            @if($product->category)
                <span class="badge rounded-pill"
                      style="background:#facc15;color:#111827;font-size:0.75rem;">
                    {{ strtoupper($product->category->name) }}
                </span>
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
            <p style="font-size:1rem;font-weight:600;color:var(--tb-blue);margin-bottom:0.25rem;">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </p>

            {{-- STOCK --}}
            <p style="font-size:0.9rem;font-weight:500;color:#111827;margin-bottom:0.25rem;">
                Stock: {{ $product->quantity }}
            </p>

            {{-- DESCRIPTION --}}
            <p style="font-size:0.9rem;color:var(--tb-gray-text);  white-space:pre-line;">
                {{ $product->description ?? 'No description available.' }}
            </p>

            <hr class="my-3">
            {{-- EDIT FORM --}}
            <h2 style="font-size:1rem;font-weight:600;margin-bottom:0.75rem;">Edit Product</h2>

            <form method="POST"
                  action="{{ route('admin.products.update', ['username' => $adminSlug, 'product' => $product->id]) }}">
                @csrf
                @method('PUT')

                <div class="mb-2">
                    <label class="form-label" for="name">Name</label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $product->name) }}"
                           required>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="price">Price (Rp)</label>
                    <input type="number"
                           id="price"
                           name="price"
                           class="form-control"
                           min="0"
                           value="{{ old('price', $product->price) }}"
                           required>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ (old('category_id', $product->category_id) == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="image">Image URL</label>
                    <input type="text"
                           id="image"
                           name="image"
                           class="form-control"
                           value="{{ old('image', $product->image) }}">
                </div>

                {{-- Quantity --}}
                <div class="mb-2">
                    <label class="form-label d-block">Quantity</label>
                    <div class="d-flex align-items-center" style="gap:0.4rem;">
                        <button type="button"
                                class="btn btn-sm"
                                style="border-radius:999px;border:1px solid #d1d5db;padding:0.1rem 0.6rem;"
                                id="qty-minus">
                            –
                        </button>

                        <input type="number"
                               id="quantity"
                               name="quantity"
                               class="form-control"
                               style="max-width:80px;text-align:center;"
                               min="0"
                               value="{{ old('quantity', $product->quantity) }}">

                        <button type="button"
                                class="btn btn-sm"
                                style="border-radius:999px;border:1px solid #d1d5db;padding:0.1rem 0.6rem;"
                                id="qty-plus">
                            +
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>

                <button type="submit" class="tb-btn-secondary">
                    Save Changes
                </button>
            </form>

            {{-- DELETE --}}
            <form method="POST"
                  action="{{ route('admin.products.destroy', ['username' => $adminSlug, 'product' => $product->id]) }}"
                  class="mt-3"
                  onsubmit="return confirm('Delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="tb-btn-danger">
                    Delete Product
                </button>
            </form>

            {{-- BACK TO ADMIN HOME --}}
            <a href="{{ route('admin.user', ['username' => $adminSlug]) }}"
               class="tb-btn-ghost mt-3">
                <img
                    src="{{ asset('images/home_icon.png') }}"
                    alt="Admin Home"
                    style="height:16px;width:16px;opacity:0.85;">
                Admin Home
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const minus = document.getElementById('qty-minus');
        const plus  = document.getElementById('qty-plus');
        const input = document.getElementById('quantity');

        if (minus && plus && input) {
            minus.addEventListener('click', function () {
                const v = parseInt(input.value || '0', 10);
                input.value = Math.max(v - 1, 0);
            });

            plus.addEventListener('click', function () {
                const v = parseInt(input.value || '0', 10);
                input.value = v + 1;
            });
        }
    });
</script>

@endsection