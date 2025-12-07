@extends('layouts.master')

@section('title', $product->name . ' – Admin – The Boys')

@section('content')

@php
    use Illuminate\Support\Str;
    $adminSlug = Str::slug(session('name'));
@endphp

<style>
/* EDIT BUTTON (blue) – same as admin.blade.php */
.tb-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--tb-blue);
    color: #ffffff;
    border-radius: 999px;
    padding: 0.4rem 0.9rem;
    font-size: 0.85rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    text-decoration: none;
}
.tb-btn-secondary:hover {
    filter: brightness(1.12);
}

/* DELETE BUTTON (red) – same as admin.blade.php */
.tb-btn-danger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #dc2626;
    color: #ffffff;
    border-radius: 999px;
    padding: 0.4rem 0.9rem;
    font-size: 0.85rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
}
.tb-btn-danger:hover {
    background: #ef4444;
}

/* ADMIN HOME (gray) */
.tb-btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    background: #9ca3af;
    color: #ffffff;
    border-radius: 999px;
    padding: 0.4rem 0.9rem;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background 0.2s ease;
}
.tb-btn-ghost:hover {
    background: #000000;
}
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

            {{-- ADMIN ACTIONS: all in one row --}}
            <div class="mt-3 d-flex flex-wrap align-items-center" style="gap:0.5rem;">

                {{-- Edit --}}
                <a href="{{ route('admin.products.edit', [
                        'username' => $adminSlug,
                        'product'  => $product->id,
                    ]) }}"
                   class="tb-btn-secondary">
                    Edit
                </a>

                {{-- Delete --}}
                <form action="{{ route('admin.products.destroy', [
                            'username' => $adminSlug,
                            'product'  => $product->id,
                        ]) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this product?');"
                      class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="tb-btn-danger">
                        Delete
                    </button>
                </form>

                {{-- Admin Home --}}
                <a href="{{ route('admin.user', ['username' => $adminSlug]) }}"
                   class="tb-btn-ghost">
                    <img
                        src="{{ asset('images/home_icon.png') }}"
                        alt="Admin Home"
                        style="height:16px;width:16px;opacity:0.85;"
                    >
                    Admin Home
                </a>
            </div>
        </div>
    </div>
</div>

@endsection