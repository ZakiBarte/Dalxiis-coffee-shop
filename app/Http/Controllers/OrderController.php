<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Number of product rows shown on the order form (no JavaScript needed)
    private const ROWS = 6;

    private function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,preparing,completed,cancelled',
            'payment_method' => 'required|in:cash,card,mobile',
            'notes' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'nullable|integer|min:1|max:50',
        ];
    }

    private function formData(Order $order, array $rows = []): array
    {
        return [
            'order' => $order,
            'rows' => $rows,
            'rowCount' => self::ROWS,
            'customers' => Customer::orderBy('name')->get(),
            'staff' => User::whereIn('role', ['admin', 'manager', 'employee'])->orderBy('name')->get(),
            'products' => Product::with('category')->orderBy('name')->get(),
        ];
    }

    // Employees may only open their own orders (or unclaimed ones to look at).
    // Admin and manager may open everything.
    private function authorizeOrder(Order $order, bool $forWrite): void
    {
        $user = auth()->user();

        if (! $user->isEmployee()) {
            return;
        }

        $isMine = $order->user_id !== null && (int) $order->user_id === (int) $user->id;
        $isUnclaimed = $order->user_id === null;

        if (! $isMine && ! (! $forWrite && $isUnclaimed)) {
            abort(403, 'This order belongs to another staff member.');
        }
    }

    // Removes blank rows and merges duplicate products. Returns [productId => qty]
    private function cleanItems(array $items): array
    {
        $clean = [];
        foreach ($items as $row) {
            if (empty($row['product_id'])) {
                continue;
            }
            $qty = (int) ($row['quantity'] ?? 1) ?: 1;
            $clean[$row['product_id']] = ($clean[$row['product_id']] ?? 0) + $qty;
        }

        return $clean;
    }

    // Saves the pivot rows and the order total
    private function saveItems(Order $order, array $clean): void
    {
        $existing = $order->products->keyBy('id');
        $sync = [];
        $total = 0;

        foreach ($clean as $productId => $qty) {
            // Keep the historical price for products that were already on the order
            $price = $existing->has($productId)
                ? $existing[$productId]->pivot->unit_price
                : Product::find($productId)->price;

            $sync[$productId] = ['quantity' => $qty, 'unit_price' => $price];
            $total += $qty * $price;
        }

        $order->products()->sync($sync);
        $order->update(['total' => $total]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = trim((string) $request->query('q', ''));

        $orders = Order::with('customer', 'user')
            ->when($user->isEmployee(), function ($query) use ($user) {
                $query->where(function ($w) use ($user) {
                    $w->where('user_id', $user->id)->orWhereNull('user_id');
                });
            })
            ->when($search !== '', function ($query) use ($search) {
                $term = ltrim($search, '#');
                $query->where(function ($w) use ($search, $term) {
                    $w->where('orders.id', 'like', "%{$term}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhere('payment_method', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                    if (str_contains(strtolower($search), 'unclaimed')) {
                        $w->orWhereNull('user_id');
                    }
                    if (str_contains(strtolower($search), 'walk')) {
                        $w->orWhereNull('customer_id');
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.form', $this->formData(new Order(['status' => 'pending', 'payment_method' => 'cash'])));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $clean = $this->cleanItems($data['items']);

        if (empty($clean)) {
            return back()->withErrors(['items' => 'Please choose at least one product.'])->withInput();
        }

        $user = $request->user();
        $handlerId = $user->isEmployee() ? $user->id : ($data['user_id'] ?? $user->id);

        $order = DB::transaction(function () use ($data, $clean, $handlerId) {
            $order = Order::create([
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $handlerId,
                'status' => $data['status'],
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
                'total' => 0,
            ]);
            $this->saveItems($order, $clean);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order #' . $order->id . ' created successfully.');
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order, false);
        $order->load('customer.loyaltyCard', 'user', 'products');

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $this->authorizeOrder($order, true);

        $order->load('products');
        $rows = $order->products
            ->map(fn ($p) => ['product_id' => $p->id, 'quantity' => $p->pivot->quantity])
            ->values()
            ->all();

        return view('orders.form', $this->formData($order, $rows));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeOrder($order, true);

        $data = $request->validate($this->rules());
        $clean = $this->cleanItems($data['items']);

        if (empty($clean)) {
            return back()->withErrors(['items' => 'Please choose at least one product.'])->withInput();
        }

        $isEmployee = $request->user()->isEmployee();

        DB::transaction(function () use ($order, $data, $clean, $isEmployee) {
            $fields = [
                'customer_id' => $data['customer_id'] ?? null,
                'status' => $data['status'],
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
            ];

            // Only admin and manager can reassign an order to someone else
            if (! $isEmployee) {
                $fields['user_id'] = $data['user_id'] ?? null;
            }

            $order->update($fields);
            $order->load('products');
            $this->saveItems($order, $clean);
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order #' . $order->id . ' updated successfully.');
    }

    // A staff member takes an unclaimed order (for example one placed by a customer)
    public function claim(Request $request, Order $order)
    {
        if ($order->user_id !== null) {
            return back()->with('error', 'Order #' . $order->id . ' was already taken by ' . $order->user->name . '.');
        }

        $order->update([
            'user_id' => $request->user()->id,
            'status' => $order->status === 'pending' ? 'preparing' : $order->status,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'You are now handling order #' . $order->id . '.');
    }

    public function destroy(Order $order)
    {
        $id = $order->id;
        $order->delete(); // pivot rows are removed by the cascade in the migration

        return redirect()->route('orders.index')->with('success', 'Order #' . $id . ' deleted.');
    }
}
