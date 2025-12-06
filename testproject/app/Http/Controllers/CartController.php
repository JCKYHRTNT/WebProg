<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Show cart for logged-in user.
     */
    public function index()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login to view your cart.');
        }

        $userId = session('user_id');

        // Ensure cart exists
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );

        // Load items + products + categories
        $cart->load(['items.product.category']);

        $items = $cart->items;

        // Calculate total
        $total = $items->reduce(function ($carry, CartItem $item) {
            return $carry + ($item->product->price * $item->quantity);
        }, 0);

        return view('cart', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    /**
     * Add product to cart (quantity +1).
     */
    public function add(Product $product)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login to add items to cart.');
        }

        $userId = session('user_id');

        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );

        $item = CartItem::firstOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $product->id],
            ['quantity' => 0]
        );

        $item->quantity += 1;
        $item->save();

        return back()->with('success', 'Item added to cart.');
    }

    /**
     * Update quantity for a cart item.
     * If quantity = 0 => remove item.
     */
    public function update(Request $request, CartItem $item)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $qty = (int) $request->quantity;

        if ($qty <= 0) {
            $item->delete();
        } else {
            $item->quantity = $qty;
            $item->save();
        }

        return redirect()->route('cart')->with('success', 'Cart updated.');
    }
}