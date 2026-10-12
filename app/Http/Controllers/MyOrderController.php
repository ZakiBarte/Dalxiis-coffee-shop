<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MyOrderController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->user()->ensureCustomer();
        $orders = $customer->orders()->latest()->paginate(10);

        return view('my-orders.index', compact('customer', 'orders'));
    }

    public function show(Request $request, Order $order)
    {
        $this->ownOrder($request, $order);
        $order->load('products', 'user');

        return view('my-orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        $this->ownOrder($request, $order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled. Your order is already ' . $order->status . '.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('my-orders.show', $order)->with('success', 'Order #' . $order->id . ' cancelled.');
    }

    // A customer may only open their own orders
    private function ownOrder(Request $request, Order $order): void
    {
        $customer = $request->user()->ensureCustomer();
        abort_unless($order->customer_id === $customer->id, 403, 'This is not your order.');
    }
}
