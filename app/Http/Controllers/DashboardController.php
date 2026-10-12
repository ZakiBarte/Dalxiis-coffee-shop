<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Customers have no dashboard; send them to the menu
        if ($user->isCustomer()) {
            $request->session()->reflash();
            return redirect()->route('home');
        }

        // Employees only see the orders they work on
        $mine = $user->isEmployee();
        $orders = fn () => Order::query()->when($mine, fn ($q) => $q->where('user_id', $user->id));

        $stats = [
            'orders' => $orders()->count(),
            'open' => $orders()->whereIn('status', ['pending', 'preparing'])->count(),
            'today' => $orders()->whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total'),
            'revenue' => $orders()->where('status', 'completed')->sum('total'),
            'products' => Product::count(),
            'customers' => Customer::count(),
            'unclaimed' => Order::whereNull('user_id')->where('status', 'pending')->count(),
        ];

        $recentOrders = $orders()->with('customer')->latest()->take(5)->get();

        $unclaimedOrders = Order::with('customer')
            ->whereNull('user_id')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $filter = fn ($q) => $q->where('orders.status', '!=', 'cancelled')
            ->when($mine, fn ($q2) => $q2->where('orders.user_id', $user->id));

        $bestSellers = Product::withSum(['orders as units_sold' => $filter], 'order_product.quantity')
            ->whereHas('orders', $filter)
            ->orderByDesc('units_sold')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentOrders', 'unclaimedOrders', 'bestSellers', 'mine'));
    }
}
