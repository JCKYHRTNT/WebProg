<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Admin dashboard (product list)
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

        $admin        = User::findOrFail(session('user_id'));
        $expectedSlug = $admin->slug;

        if ($username !== $expectedSlug) {
            return redirect()->route('admin.user', [
                'username' => $expectedSlug,
            ] + $request->query());
        }

        return $this->index($request);
    }

    /**
     * Admin product detail: /a/{username}/products/{product}
     */
    public function productDetail(Request $request, string $username, Product $product)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $admin        = User::findOrFail(session('user_id'));
        $expectedSlug = $admin->slug;

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

        $admin        = User::findOrFail(session('user_id'));
        $expectedSlug = $admin->slug;

        if ($username !== $expectedSlug) {
            return redirect()->route('admin.crud', [
                'username' => $expectedSlug,
            ] + $request->query());
        }

        $productCount  = Product::count();
        $categoryCount = Category::count();
        $adminCount    = User::where('role', 'admin')->count();

        // Users that can be promoted to admin
        $eligibleUsers = User::where('role', '!=', 'admin')->get();

        // Admins that can be demoted
        $demotableAdmins = User::where('role', 'admin')
            ->where('id', '!=', session('user_id'))
            ->get();

        return view('admin.crud', [
            'productCount'    => $productCount,
            'categoryCount'   => $categoryCount,
            'adminCount'      => $adminCount,
            'categories'      => Category::orderBy('id')->get(),
            'eligibleUsers'   => $eligibleUsers,
            'demotableAdmins' => $demotableAdmins,
        ]);
    }

    /**
     * Promote: admin.crud.promote
     */
    public function promoteAdmin(Request $request, string $username)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $currentAdmin = User::findOrFail(session('user_id'));
        $expectedSlug = $currentAdmin->slug;

        if ($username !== $expectedSlug) {
            return redirect()->route('admin.crud', [
                'username' => $expectedSlug,
            ] + $request->query());
        }

        $data = $request->validate([
            'user_id'          => ['required', 'exists:users,id'],
            'current_password' => ['required', 'string'],
        ]);

        // Verify current admin password
        if (!Hash::check($data['current_password'], $currentAdmin->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        $user = User::findOrFail($data['user_id']);

        if ($user->role === 'admin') {
            return back()->with('error', 'Selected user is already an admin.');
        }

        $user->role = 'admin';
        $user->save();

        return redirect()
            ->route('admin.crud', ['username' => $currentAdmin->slug])
            ->with('success', 'User promoted to admin.');
    }

    /**
     * Demote: admin.crud.demote
     */
    public function demoteAdmin(Request $request, string $username)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $currentAdmin = User::findOrFail(session('user_id'));
        $expectedSlug = $currentAdmin->slug;

        if ($username !== $expectedSlug) {
            return redirect()->route('admin.crud', [
                'username' => $expectedSlug,
            ] + $request->query());
        }

        $data = $request->validate([
            'user_id'          => ['required', 'exists:users,id'],
            'current_password' => ['required', 'string'],
        ]);

        // Verify current admin password
        if (!Hash::check($data['current_password'], $currentAdmin->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        $user = User::findOrFail($data['user_id']);

        if ($user->role !== 'admin') {
            return back()->with('error', 'Selected user is not an admin.');
        }

        if ($user->id === $currentAdmin->id) {
            return back()->with('error', 'You cannot demote yourself here.');
        }

        $user->role = 'user';
        $user->save();

        return redirect()
            ->route('admin.crud', ['username' => $currentAdmin->slug])
            ->with('success', 'Admin has been demoted to user.');
    }

    // ===== PRODUCT CRUD =====
    public function storeProduct(Request $request, string $username)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $admin = User::findOrFail(session('user_id'));

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        Product::create($data);

        return redirect()
            ->route('admin.user', ['username' => $admin->slug])
            ->with('status', 'Product created.');
    }

    public function editProduct(Request $request, string $username, Product $product)
    {
        return $this->productDetail($request, $username, $product);
    }

    public function updateProduct(Request $request, string $username, Product $product)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $admin = User::findOrFail(session('user_id'));

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        $product->update($data);

        return redirect()
            ->route('admin.products.show', ['username' => $admin->slug, 'product' => $product->id])
            ->with('status', 'Product updated.');
    }

    public function destroyProduct(string $username, Product $product)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $admin = User::findOrFail(session('user_id'));

        $product->delete();

        return redirect()
            ->route('admin.user', ['username' => $admin->slug])
            ->with('status', 'Product deleted.');
    }

    // ===== CATEGORY CRUD =====
    public function storeCategory(Request $request, string $username)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $admin = User::findOrFail(session('user_id'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::create($data);

        return redirect()
            ->route('admin.crud', ['username' => $admin->slug])
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
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $admin = User::findOrFail(session('user_id'));

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,' . $category->id,
            ],
        ]);

        $category->update($data);

        return redirect()
            ->route('admin.crud', ['username' => $admin->slug])
            ->with('status', 'Category updated.');
    }

    public function destroyCategory(string $username, Category $category)
    {
        if (!session('user_id') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $admin = User::findOrFail(session('user_id'));

        $category->delete();

        return redirect()
            ->route('admin.crud', ['username' => $admin->slug])
            ->with('status', 'Category deleted.');
    }
}