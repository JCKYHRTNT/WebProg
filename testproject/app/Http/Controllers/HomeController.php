<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Guest + user home.
     */
    public function home(Request $request)
    {
        $categoryId = $request->filled('category')
            ? (int) $request->input('category')
            : null;

        $query = $request->input('q');

        // Load categories
        $categories = Category::orderBy('name')->get();

        $categoryNames = $categories
            ->pluck('name', 'id')
            ->toArray();

        $productsQuery = Product::query();

        // FILTER BY CATEGORY
        if (!is_null($categoryId)) {
            $productsQuery->where('category_id', $categoryId);
        }

        // FILTER BY SEARCH
        if ($query) {
            $q = strtolower($query);
            $productsQuery->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"]);
        }

        $products = $productsQuery->get();

        // RECENT CATEGORIES (max 3)
        $recentCategories = $categories;

        if (!is_null($categoryId) && $categories->pluck('id')->contains($categoryId)) {
            $selected = $categories->firstWhere('id', $categoryId);

            $recentCategories = collect([$selected])
                ->merge($categories->where('id', '!=', $categoryId));
        }

        $recentCategories = $recentCategories
            ->take(3)
            ->map(fn ($cat) => ['id' => $cat->id, 'name' => $cat->name])
            ->values()
            ->all();

        return view('home', [
            'products'         => $products,
            'categoryId'       => $categoryId,
            'query'            => $query,
            'recentCategories' => $recentCategories,
            'categories'       => $categories,
            'categoryNames'    => $categoryNames,
        ]);
    }

    /**
     * User home: /u/{username}
     */
    public function homeForUser(Request $request, string $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $sessionName  = session('name');
        $expectedSlug = Str::slug($sessionName);

        // Prevent accessing another user’s URL
        if ($username !== $expectedSlug) {
            return redirect()->route('home.user', [
                'username' => $expectedSlug,
            ] + $request->query());
        }

        return $this->home($request);
    }

    /**
     * Guest / user product detail
     */
    public function productDetail($usernameOrId, $maybeId = null)
    {
        $id = $maybeId ?? $usernameOrId;

        $product = Product::with('category')->findOrFail((int) $id);

        return view('productdetail', [
            'product' => $product,
        ]);
    }
}