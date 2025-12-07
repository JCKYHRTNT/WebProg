<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Str;

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

        return view('cart', [
            'items' => $items,
            'total' => $total,
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
            'quantity' => ['required', 'integer', 'min:1'],
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
     * If quantity = 0 => remove item.
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
}