<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Homepage: list products with category + search filters.
     */
    public function home(Request $request)
    {
        $categories = collect([
            ['id' => 1, 'name' => 'electronics'],
            ['id' => 2, 'name' => 'toys'],
        ]);

        // Map: [id => name] for easy lookup
        $categoryNames = $categories->pluck('name', 'id');

        // Hardcoded products
        $allProducts = collect([
            [
                'id'          => 1,
                'name'        => 'Gaming Headset',
                'category_id' => 1,
                'price'       => 350000,
                'image'       => 'https://via.placeholder.com/400x260?text=Gaming+Headset',
            ],
            [
                'id'          => 2,
                'name'        => 'Mechanical Keyboard',
                'category_id' => 1,
                'price'       => 550000,
                'image'       => 'https://via.placeholder.com/400x260?text=Mechanical+Keyboard',
            ],
            [
                'id'          => 3,
                'name'        => '4K Monitor',
                'category_id' => 1,
                'price'       => 2500000,
                'image'       => 'https://via.placeholder.com/400x260?text=4K+Monitor',
            ],
            [
                'id'          => 4,
                'name'        => 'Action Figure – Hero',
                'category_id' => 2,
                'price'       => 150000,
                'image'       => 'https://via.placeholder.com/400x260?text=Action+Figure',
            ],
            [
                'id'          => 5,
                'name'        => 'RC Car',
                'category_id' => 2,
                'price'       => 280000,
                'image'       => 'https://via.placeholder.com/400x260?text=RC+Car',
            ],
            [
                'id'          => 6,
                'name'        => 'Building Blocks Set',
                'category_id' => 2,
                'price'       => 120000,
                'image'       => 'https://via.placeholder.com/400x260?text=Building+Blocks',
            ],
        ]);

        $categoryId = $request->input('category');
        $query      = $request->input('q');

        // Filter products by category_id and name
        $products = $allProducts
            ->when($categoryId, function ($c) use ($categoryId) {
                return $c->where('category_id', intval($categoryId));
            })
            ->when($query, function ($c) use ($query) {
                $q = mb_strtolower($query);
                return $c->filter(function ($p) use ($q) {
                    return str_contains(mb_strtolower($p['name']), $q);
                });
            });

        // All categories
        $allCategories = $categories;

        // Recent categories: selected category first, then others
        $recentCategories = $allCategories;
        $categoryIdInt = $categoryId ? intval($categoryId) : null;

        if ($categoryIdInt && $allCategories->pluck('id')->contains($categoryIdInt)) {
            $selected = $allCategories->firstWhere('id', $categoryIdInt);

            $recentCategories = collect([$selected])->merge(
                $allCategories->where('id', '!=', $categoryIdInt)
            );
        }

        // Limit to 3
        $recentCategories = $recentCategories->take(3);

        return view('home', [
            'products'         => $products,
            'categoryId'       => $categoryIdInt,
            'query'            => $query,
            'recentCategories' => $recentCategories,
            'categoryNames'    => $categoryNames,
        ]);
    }
}