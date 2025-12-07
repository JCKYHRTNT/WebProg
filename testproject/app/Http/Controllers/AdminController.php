<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Admin dashboard (product list) – used by /a/{username}
     */
    public function index(Request $request)
    {
        $categoryId = $request->filled('category')
            ? (int) $request->input('category')
            : null;

        $query = $request->input('q');

        // Categories
        $categories    = Category::orderBy('name')->get();
        $categoryNames = $categories->pluck('name', 'id')->toArray();

        // Base product query
        $productsQuery = Product::with('category');

        if (!is_null($categoryId)) {
            $productsQuery->where('category_id', $categoryId);
        }

        if ($query) {
            $q = strtolower($query);
            $productsQuery->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"]);
        }

        $products = $productsQuery->orderBy('id')->get();

        // Recent categories (max 3)
        $recentCategories = $categories;

        if (!is_null($categoryId) && $categories->pluck('id')->contains($categoryId)) {
            $selected = $categories->firstWhere('id', $categoryId);

            $recentCategories = collect([$selected])->merge(
                $categories->where('id', '!=', $categoryId)
            );
        }

        $recentCategories = $recentCategories
            ->take(3)
            ->map(fn ($cat) => ['id' => $cat->id, 'name' => $cat->name])
            ->values()
            ->all();

        return view('admin.home', [
            'products'         => $products,
            'categories'       => $categories,
            'categoryId'       => $categoryId,
            'query'            => $query,
            'recentCategories' => $recentCategories,
            'categoryNames'    => $categoryNames,
        ]);
    }

    /**
     * /a/{username}
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

        return $this->index($request);
    }

    /**
     * Admin product detail.
     * Route: /a/{username}/products/{product}
     */
    public function productDetail(Request $request, string $username, Product $product)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $expectedSlug = Str::slug(session('name'));

        if ($username !== $expectedSlug) {
            return redirect()->route('admin.products.show', [
                'username' => $expectedSlug,
                'product'  => $product->id,
            ]);
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.productdetail', [
            'product'    => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Admin CRUD hub: /a/{username}/admin
     */
    public function crud(Request $request, string $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $sessionName  = session('name');
        $expectedSlug = Str::slug($sessionName);

        if ($username !== $expectedSlug) {
            return redirect()->route('admin.crud', [
                'username' => $expectedSlug,
            ] + $request->query());
        }

        $productCount  = Product::count();
        $categoryCount = Category::count();
        $adminCount    = User::where('role', 'admin')->count();

        return view('admin.crud', [
            'productCount'  => $productCount,
            'categoryCount' => $categoryCount,
            'adminCount'    => $adminCount,
            'categories'    => Category::orderBy('id')->get(),
        ]);
    }

    /**
     * Register a new admin from the CRUD hub.
     */
    public function registerAdmin(Request $request, string $username)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:4', 'confirmed'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'admin',
            'profpic'  => null,
        ]);

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.crud', ['username' => $slug])
            ->with('success', 'New admin account created.');
    }

    // ===== PRODUCT CRUD =====

    public function storeProduct(Request $request, string $username)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        Product::create($data);

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Product created.');
    }

    // editing from anywhere just reuses productDetail + same Blade
    public function editProduct(Request $request, string $username, Product $product)
    {
        return $this->productDetail($request, $username, $product);
    }

    public function updateProduct(Request $request, string $username, Product $product)
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
            ->route('admin.products.show', ['username' => $slug, 'product' => $product->id])
            ->with('status', 'Product updated.');
    }

    public function destroyProduct(string $username, Product $product)
    {
        $product->delete();

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.user', ['username' => $slug])
            ->with('status', 'Product deleted.');
    }

    // ===== CATEGORY CRUD =====
    public function storeCategory(Request $request, string $username)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::create($data);

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.crud', ['username' => $slug])  // <-- go back to CRUD page
            ->with('status', 'Category created.');
    }

    public function editCategory(string $username, Category $category)
    {
        return view('admin_edit_category', [
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, string $username, Category $category)
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
            ->route('admin.crud', ['username' => $slug])  // <-- CRUD page
            ->with('status', 'Category updated.');
    }

    public function destroyCategory(string $username, Category $category)
    {
        $category->delete();

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('admin.crud', ['username' => $slug])  // <-- CRUD page
            ->with('status', 'Category deleted.');
    }
}