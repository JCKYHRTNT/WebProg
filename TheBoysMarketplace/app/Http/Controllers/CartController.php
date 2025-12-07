<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Show cart for logged-in user.
     */
    public function index($username = null)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login to view your cart.');
        }

        $userId = session('user_id');

        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );

        $cart->load(['items.product.category']);

        $items = $cart->items;

        $total = $items->reduce(function ($carry, CartItem $item) {
            return $carry + ($item->product->price * $item->quantity);
        }, 0);

        $categories = Category::orderBy('name')->get();

        return view('cart', [
            'items'      => $items,
            'total'      => $total,
            'categories' => $categories,
        ]);
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, $username, Product $product)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login to add items to cart.');
        }

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $product->quantity],
        ]);

        $requestedQty = (int) $request->input('quantity', 1);
        $userId       = session('user_id');

        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );

        $item = CartItem::firstOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $product->id],
            ['quantity' => 0]
        );

        $currentQty = $item->quantity;
        $newQty     = $currentQty + $requestedQty;

        // Enforce stock
        if ($newQty > $product->quantity) {
            $maxAddable = max($product->quantity - $currentQty, 0);

            $slug = Str::slug(session('name'));

            if ($maxAddable <= 0) {
                return redirect()
                    ->route('cart', ['username' => $slug])
                    ->with('error', 'Not enough stock for this product.');
            }

            return redirect()
                ->route('cart', ['username' => $slug])
                ->with('error', 'You can only add up to ' . $maxAddable . ' more of this item.');
        }

        $item->quantity = $newQty;
        $item->save();

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('cart', ['username' => $slug])
            ->with('success', 'Item added to cart.');
    }

    /**
     * Update quantity for a cart item.
     */
    public function update(Request $request, $username, CartItem $item)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $qty = (int) $request->quantity;

        $product = $item->product;

        if ($product) {
            $stock = (int) $product->quantity;

            // If stock is 0 or below, remove item from cart
            if ($stock <= 0) {
                $item->delete();

                $slug = Str::slug(session('name'));

                return redirect()
                    ->route('cart', ['username' => $slug])
                    ->with('error', 'This product is out of stock and has been removed from your cart.');
            }

            if ($qty > $stock) {
                $qty = $stock;
            }
        }

        if ($qty <= 0) {
            $item->delete();
        } else {
            $item->quantity = $qty;
            $item->save();
        }

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('cart', ['username' => $slug])
            ->with('success', 'Cart updated.');
    }

    /**
     * Checkout: confirm with password, update product stock, clear cart, redirect home.
     */
    public function checkout(Request $request, $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Validate password input
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $userId = session('user_id');

        // Get user and check password
        $user = User::find($userId);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()
                ->back()
                ->with('error', 'Invalid password.');
        }

        // Get user's cart
        $cart = Cart::with(['items.product'])
            ->where('user_id', $userId)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Your cart is empty.');
        }

        DB::transaction(function () use ($cart) {
            foreach ($cart->items as $item) {
                $product = $item->product;

                if (!$product) {
                    continue;
                }

                // Decrease product stock
                $currentStock = (int) $product->quantity;
                $newStock     = $currentStock - (int) $item->quantity;

                if ($newStock < 0) {
                    $newStock = 0;
                }

                $product->quantity = $newStock;
                $product->save();
            }

            // Clear cart items after successful stock update
            $cart->items()->delete();
        });

        $slug = Str::slug(session('name'));

        return redirect()
            ->route('home.user', ['username' => $slug])
            ->with('success', 'Payment successful. Thank you for your purchase.');
    }
}