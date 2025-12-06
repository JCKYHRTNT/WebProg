<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Show admin dashboard with lists of products and categories.
     */
    public function index()
    {
        // This expects products and categories tables to exist
        $products   = Product::with('category')->orderBy('id')->get();
        $categories = Category::orderBy('id')->get();

        return view('admin', [
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Admin dashboard with username in URL: /a/{username}
     */
    public function indexForUser(Request $request, string $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $sessionName  = session('name');
        $expectedSlug = Str::slug($sessionName);

        if ($username !== $expectedSlug) {
            return redirect()->route('admin.user', [
                'username' => $expectedSlug,
            ] + $request->query());
        }

        return $this->index();
    }

    /**
     * Store a new product.
     */
    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image'       => ['nullable', 'string', 'max:255'], // path or URL
        ]);

        Product::create($data);

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Product created.');
    }

    /**
     * Edit product.
     * Route: /admin/products/{product}/edit
     * {product} is the product ID (implicit binding).
     */
    public function editProduct(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin_edit_product', [
            'product'    => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update product.
     * Route: PUT /admin/products/{product}
     */
    public function updateProduct(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        $product->update($data);

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Product updated.');
    }

    /**
     * Delete product.
     * Route: DELETE /admin/products/{product}
     */
    public function destroyProduct(Product $product)
    {
        $product->delete();

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Product deleted.');
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::create($data);

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Category created.');
    }

    /**
     * Edit category.
     * Route: /admin/categories/{category}/edit
     * {category} is the category ID.
     */
    public function editCategory(Category $category)
    {
        return view('admin_edit_category', [
            'category' => $category,
        ]);
    }

    /**
     * Update category name.
     * Route: PUT /admin/categories/{category}
     */
    public function updateCategory(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,' . $category->id,
            ],
        ]);

        $category->update($data);

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Category updated.');
    }

    /**
     * Delete category.
     * Route: DELETE /admin/categories/{category}
     *
     * Note: if products reference this category and you set FK onDelete('cascade'),
     * deleting the category will also delete those products.
     */
    public function destroyCategory(Category $category)
    {
        $category->delete();

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Category deleted.');
    }
}