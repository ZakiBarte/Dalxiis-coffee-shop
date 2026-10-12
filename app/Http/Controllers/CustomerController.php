<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyCard;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $customers = Customer::with('loyaltyCard')->withCount('orders')
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('loyaltyCard', fn ($c) => $c->where('card_number', 'like', "%{$search}%")
                      ->orWhere('tier', 'like', "%{$search}%"));
            }))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.form', ['customer' => new Customer()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'tier' => 'required|in:bronze,silver,gold',
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
        ]);

        // Every customer gets exactly one loyalty card (one-to-one)
        LoyaltyCard::create([
            'customer_id' => $customer->id,
            'card_number' => 'DX-' . str_pad($customer->id, 5, '0', STR_PAD_LEFT),
            'tier' => $data['tier'],
        ]);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer and loyalty card created.');
    }

    public function show(Customer $customer)
    {
        $customer->load('loyaltyCard');
        $orders = $customer->orders()->latest()->get();

        return view('customers.show', compact('customer', 'orders'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'tier' => 'required|in:bronze,silver,gold',
        ]);

        $customer->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
        ]);

        $customer->loyaltyCard()->updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'card_number' => $customer->loyaltyCard?->card_number ?? 'DX-' . str_pad($customer->id, 5, '0', STR_PAD_LEFT),
                'tier' => $data['tier'],
            ]
        );

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        // Loyalty card is deleted too; their past orders are kept as walk-in orders
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
