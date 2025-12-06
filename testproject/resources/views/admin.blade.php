@extends('layouts.master')

@section('title', 'The Boys – Home')

@php
    use Illuminate\Support\Str;

    $currentCategoryId = request()->filled('category') ? (int) request('category') : null;
    $currentQuery      = request('q');
    $allActive         = is_null($currentCategoryId);

    // Logged-in admin slug based on name in session
    $adminSlug = Str::slug(session('name'));
@endphp

@section('content')

<style>
/* EDIT BUTTON (blue) */
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

/* DELETE BUTTON (red) */
.tb-btn-danger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #dc2626; /* red-600 */
    color: #ffffff;
    border-radius: 999px;
    padding: 0.4rem 0.9rem;
    font-size: 0.85rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
}
.tb-btn-danger:hover {
    background: #ef4444; /* red-500 */
}
</style>

    {{-- Hero / intro --}}
    <section class="mb-4">
        <div class="tb-card p-4 p-md-4" style="
            background: radial-gradient(circle at top left, var(--tb-yellow) 0, var(--tb-blue) 45%, var(--tb-black) 100%);
            color: #f9fafb;
            border: none;
        ">
            <div class="row align-items-end gy-3">
                <div class="col-md-8">
                    <span class="badge rounded-pill"
                          style="background:#facc15;color:#111827;font-size:0.7rem;letter-spacing:0.08em;">
                        MARKETPLACE
                    </span>
                    <h1 class="mt-2 mb-2" style="font-size:1.6rem;font-weight:600;">
                        Welcome to The Boys
                    </h1>
                </div>
            </div>
        </div>
    </section>

    {{-- Category filter row --}}
    <section class="mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-2" style="gap:0.5rem;">
            <h2 class="mb-0" style="font-size:1rem;font-weight:600;">Categories</h2>

            <div class="d-flex flex-wrap" style="gap:0.4rem;">

                {{-- All --}}
                @php
                    $allUrl = $currentQuery
                        ? url('/a/' . $adminSlug . '?q=' . urlencode($currentQuery))
                        : url('/a/' . $adminSlug);
                @endphp

                <a
                    href="{{ $allUrl }}"
                    class="tb-pill-link"
                    style="
                        background: {{ $allActive ? 'var(--tb-blue)' : 'transparent' }};
                        color: {{ $allActive ? '#f9fafb' : '#e5e7eb' }};
                    "
                >
                    All
                </a>

                {{-- First 5 categories --}}
                @foreach($categories->take(5) as $cat)
                    @php
                        $active  = $currentCategoryId === $cat->id;
                        $qParam  = $currentQuery ? '&q=' . urlencode($currentQuery) : '';
                        $catUrl  = url('/a/' . $adminSlug . '?category=' . $cat->id . $qParam);
                        $bgColor = $active ? 'var(--tb-blue)' : 'transparent';
                        $textColor = $active ? '#f9fafb' : '#e5e7eb';
                    @endphp

                    <a
                        href="{{ $catUrl }}"
                        class="tb-pill-link"
                        style="background: {{ $bgColor }}; color: {{ $textColor }};"
                    >
                        {{ ucfirst($cat->name) }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Active filter summary --}}
        @if($currentCategoryId || $currentQuery)
            <div style="font-size:0.8rem;color:var(--tb-gray-text);">
                @if($currentCategoryId)
                    @php
                        $selectedCategory = $categories->firstWhere('id', $currentCategoryId);
                    @endphp
                    @if($selectedCategory)
                        <span>Filter: <strong>{{ ucfirst($selectedCategory->name) }}</strong></span>
                    @endif
                @endif

                @if($currentCategoryId && $currentQuery)
                    <span> · </span>
                @endif

                @if($currentQuery)
                    <span>Search: <strong>{{ $currentQuery }}</strong></span>
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

                            {{-- Image --}}
                            <a href="{{ route('products.show', $product->id) }}" class="ratio ratio-4x3 d-block">
                                <img
                                    src="{{ $product->image }}"
                                    alt="{{ $product->name }}"
                                    class="w-100 h-100"
                                    style="object-fit:cover;"
                                >
                            </a>

                            <div class="p-2 p-md-3">

                                {{-- Category badge (goes to public home, not required to change) --}}
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    @if($product->category)
                                        @php
                                            $qParam = $currentQuery ? '&q=' . urlencode($currentQuery) : '';
                                            $catUrl = url('/?category=' . $product->category_id . $qParam);
                                        @endphp
                                        <a href="{{ $catUrl }}">
                                            <span class="badge rounded-pill"
                                                  style="background:#facc15;color:#111827;font-size:0.7rem;">
                                                {{ ucfirst($product->category->name) }}
                                            </span>
                                        </a>
                                    @endif
                                </div>

                                {{-- Name --}}
                                <h3 class="mb-1" style="font-size:0.9rem;font-weight:600;">
                                    <a href="{{ route('products.show', $product->id) }}" style="color:inherit;text-decoration:none;">
                                        {{ $product->name }}
                                    </a>
                                </h3>

                                {{-- Price --}}
                                <p class="mb-2" style="font-size:0.9rem;font-weight:600;color:var(--tb-blue);">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </p>

                                {{-- Edit + Delete --}}
                                <div class="d-flex gap-2">
                                    @php
                                        $adminSlug = Str::slug(session('name'));
                                    @endphp

                                    <a href="{{ route('admin.products.edit', [
                                            'username' => $adminSlug,
                                            'product'  => $product->id,
                                        ]) }}"
                                    class="tb-btn-secondary flex-fill text-center">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.products.destroy', [
                                                'username' => $adminSlug,
                                                'product'  => $product->id,
                                            ]) }}"
                                        method="POST"
                                        class="flex-fill"
                                        onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tb-btn-danger w-100">
                                            Delete
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.products.destroy', $product->id) }}"
                                          method="POST"
                                          class="flex-fill"
                                          onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="tb-btn-danger w-100">
                                            Delete
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