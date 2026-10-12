<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // The cart is kept in the session as [product_id => quantity]
    public function index()
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        $lines = [];
        $total = 0;
        foreach ($cart as $productId => $qty) {
            if (! $products->has($productId)) {
                continue;
            }
            $product = $products[$productId];
            $subtotal = $product->price * $qty;
            $total += $subtotal;
            $lines[] = ['product' => $product, 'quantity' => $qty, 'subtotal' => $subtotal];
        }

        return view('cart.index', compact('lines', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:20']);

        if (! $product->is_available) {
            return back()->with('error', $product->name . ' is not available right now.');
        }

        $cart = session('cart', []);
        $cart[$product->id] = min(20, ($cart[$product->id] ?? 0) + $data['quantity']);
        session(['cart' => $cart]);

        return back()->with('success', $product->name . ' added to your cart.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:20']);

        $cart = session('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id] = $data['quantity'];
            session(['cart' => $cart]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', $product->name . ' removed from your cart.');
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:cash,card,mobile',
            'notes' => 'nullable|string|max:255',
        ]);

        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->where('is_available', true)->get();

        if ($products->isEmpty()) {
            return redirect()->route('home')->with('error', 'Your cart is empty. Choose something from the menu first.');
        }

        $customer = $request->user()->ensureCustomer();

        $order = DB::transaction(function () use ($data, $cart, $products, $customer) {
            $order = Order::create([
                'customer_id' => $customer->id,
                'user_id' => null, // staff will claim it
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($products as $product) {
                $qty = $cart[$product->id];
                $order->products()->attach($product->id, ['quantity' => $qty, 'unit_price' => $product->price]);
                $total += $qty * $product->price;
            }
            $order->update(['total' => $total]);

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('my-orders.show', $order)->with('success', 'Thank you! Your order #' . $order->id . ' has been placed.');
    }
}
