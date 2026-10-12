<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\LoyaltyCard;
use App\Models\Order;
use App\Models\Product;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Staff accounts (password for all: "password") ----------
        $admin = User::create(['name' => 'Dalxiis Admin', 'email' => 'admin@dalxiis.test', 'password' => 'password', 'role' => 'admin']);
        $manager = User::create(['name' => 'Cabdi Warsame', 'email' => 'manager@dalxiis.test', 'password' => 'password', 'role' => 'manager']);
        $employee1 = User::create(['name' => 'Ahmed Barista', 'email' => 'ahmed@dalxiis.test', 'password' => 'password', 'role' => 'employee']);
        $employee2 = User::create(['name' => 'Ayaan Cashier', 'email' => 'ayaan@dalxiis.test', 'password' => 'password', 'role' => 'employee']);

        // Staff profiles (one-to-one with the staff user)
        StaffProfile::create(['user_id' => $manager->id, 'phone' => '0634200001', 'job_title' => 'Shop Manager', 'hired_on' => '2024-03-01']);
        StaffProfile::create(['user_id' => $employee1->id, 'phone' => '0634200002', 'job_title' => 'Barista', 'hired_on' => '2025-06-15']);
        StaffProfile::create(['user_id' => $employee2->id, 'phone' => '0634200003', 'job_title' => 'Cashier', 'hired_on' => '2025-09-01']);

        $handlers = [$employee1, $employee2, $employee1, $employee2, $manager];

        // ---------- Menu ----------
        $menu = [
            'Hot Coffee' => [
                ['Espresso', 'Rich single shot', 2.50],
                ['Double Espresso', 'Two shots, bold and strong', 3.30],
                ['Americano', 'Espresso with hot water', 3.00],
                ['Cappuccino', 'Espresso with steamed milk foam', 3.80],
                ['Latte', 'Smooth espresso with steamed milk', 4.00],
                ['Flat White', 'Velvety microfoam over a double shot', 4.10],
                ['Mocha', 'Espresso, chocolate and milk', 4.50],
                ['Caramel Macchiato', 'Vanilla, milk, espresso and caramel', 4.80],
            ],
            'Cold Drinks' => [
                ['Iced Latte', 'Chilled espresso over milk and ice', 4.20],
                ['Cold Brew', 'Slow steeped for 18 hours', 4.00],
                ['Iced Mocha', 'Chocolate and espresso over ice', 4.70],
                ['Mango Smoothie', 'Fresh mango blended with ice', 4.80],
                ['Strawberry Lemonade', 'Sparkling and fruity', 3.90],
            ],
            'Tea' => [
                ['Green Tea', 'Light and refreshing', 2.20],
                ['Karak Tea', 'Spiced milk tea', 2.60],
                ['Mint Tea', 'Fresh mint leaves, lightly sweet', 2.40],
                ['Chai Latte', 'Spiced tea with steamed milk', 3.70],
            ],
            'Pastries' => [
                ['Butter Croissant', 'Baked fresh every morning', 2.90],
                ['Chocolate Muffin', 'Double chocolate chunk', 3.20],
                ['Cinnamon Roll', 'Warm with cream cheese glaze', 3.50],
                ['Blueberry Scone', 'Crumbly with real blueberries', 3.00],
            ],
            'Sandwiches' => [
                ['Chicken Club', 'Grilled chicken, lettuce and tomato', 6.50],
                ['Cheese Panini', 'Melted mozzarella and cheddar', 5.50],
                ['Egg & Avocado Toast', 'Sourdough with poached egg', 6.00],
                ['Tuna Melt', 'Tuna, cheese and pickles on toast', 6.20],
                ['Veggie Wrap', 'Hummus and roasted vegetables', 5.80],
            ],
        ];

        foreach ($menu as $categoryName => $items) {
            $category = Category::create([
                'name' => $categoryName,
                'description' => 'Our ' . strtolower($categoryName) . ' selection',
            ]);
            foreach ($items as [$name, $description, $price]) {
                $category->products()->create(['name' => $name, 'description' => $description, 'price' => $price]);
            }
        }

        // One item sold out, to show the "Unavailable" status
        Product::where('name', 'Blueberry Scone')->update(['is_available' => false]);

        // ---------- Customers + loyalty cards (one-to-one) ----------
        $people = [
            ['Amina Ali', '0634100101', 'amina.ali@example.com', 'gold'],
            ['Hassan Omar', '0634100102', 'hassan.omar@example.com', 'silver'],
            ['Fatima Yusuf', '0634100103', 'fatima.yusuf@example.com', 'bronze'],
            ['Abdirahman Jama', '0634100104', 'abdirahman.jama@example.com', 'gold'],
            ['Hodan Warsame', '0634100105', 'hodan.warsame@example.com', 'silver'],
            ['Mohamed Farah', '0634100106', 'mohamed.farah@example.com', 'bronze'],
            ['Sagal Ibrahim', '0634100107', 'sagal.ibrahim@example.com', 'silver'],
            ['Yasin Abdi', '0634100108', 'yasin.abdi@example.com', 'bronze'],
            ['Ifrah Hussein', '0634100109', 'ifrah.hussein@example.com', 'gold'],
            ['Liban Osman', '0634100110', 'liban.osman@example.com', 'bronze'],
            ['Deqa Mohamud', '0634100111', 'deqa.mohamud@example.com', 'silver'],
            ['Ahmed Duale', '0634100112', 'ahmed.duale@example.com', 'bronze'],
            ['Nimco Said', '0634100113', 'nimco.said@example.com', 'bronze'],
            ['Khadar Elmi', '0634100114', 'khadar.elmi@example.com', 'silver'],
            ['Sumaya Nur', '0634100115', 'sumaya.nur@example.com', 'bronze'],
        ];

        foreach ($people as $index => [$name, $phone, $email, $tier]) {
            // The first 4 customers also have a login account (role: customer)
            $account = $index < 4
                ? User::create(['name' => $name, 'email' => $email, 'password' => 'password', 'role' => 'customer'])
                : null;

            $customer = Customer::create([
                'user_id' => $account?->id,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
            ]);

            LoyaltyCard::create([
                'customer_id' => $customer->id,
                'card_number' => 'DX-' . str_pad($customer->id, 5, '0', STR_PAD_LEFT),
                'tier' => $tier,
            ]);
        }

        // ---------- Orders (many-to-many with quantity and price) ----------
        $products = Product::where('is_available', true)->get();
        $customers = Customer::all();
        $notes = [null, null, null, 'Extra hot', 'No sugar', 'Oat milk please', 'Take away', 'Less ice'];

        for ($i = 0; $i < 70; $i++) {
            $daysAgo = mt_rand(0, 30);
            $when = now()->subDays($daysAgo)->setTime(mt_rand(7, 20), mt_rand(0, 59));

            if ($daysAgo === 0) {
                $status = Arr::random(['pending', 'pending', 'preparing', 'completed']);
            } else {
                $status = mt_rand(1, 100) <= 92 ? 'completed' : 'cancelled';
            }

            $customer = mt_rand(1, 100) <= 75 ? $customers->random() : null; // 25% walk-in

            // Today's pending orders placed by customers have not been claimed yet
            $handler = ($status === 'pending' && $customer) ? null : Arr::random($handlers);

            $order = Order::create([
                'user_id' => $handler?->id,
                'customer_id' => $customer?->id,
                'status' => $status,
                'payment_method' => Arr::random(['cash', 'card', 'mobile']),
                'notes' => Arr::random($notes),
                'total' => 0,
            ]);

            $total = 0;
            foreach ($products->random(mt_rand(1, 4)) as $product) {
                $qty = mt_rand(1, 3);
                $order->products()->attach($product->id, ['quantity' => $qty, 'unit_price' => $product->price]);
                $total += $qty * $product->price;
            }

            $order->total = round($total, 2);
            $order->created_at = $when;
            $order->updated_at = $when;
            $order->save();
        }

        // Guarantee a few waiting orders so the "Claim" queue is never empty
        foreach ([1, 2, 3] as $customerId) {
            $order = Order::create([
                'user_id' => null,
                'customer_id' => $customerId,
                'status' => 'pending',
                'payment_method' => 'cash',
                'total' => 0,
            ]);
            $product = $products->random();
            $order->products()->attach($product->id, ['quantity' => 2, 'unit_price' => $product->price]);
            $order->update(['total' => 2 * $product->price]);
        }

        // Every customer account gets at least one of their own past orders
        foreach (Customer::whereNotNull('user_id')->get() as $customer) {
            if ($customer->orders()->count() === 0) {
                $order = Order::create([
                    'user_id' => $employee1->id,
                    'customer_id' => $customer->id,
                    'status' => 'completed',
                    'payment_method' => 'card',
                    'total' => 0,
                ]);
                $product = $products->random();
                $order->products()->attach($product->id, ['quantity' => 1, 'unit_price' => $product->price]);
                $order->update(['total' => $product->price]);
            }
        }
    }
}
