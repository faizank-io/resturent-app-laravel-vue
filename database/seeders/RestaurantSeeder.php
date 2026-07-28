<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Categories
        $categoriesData = [
            ['name' => 'Burgers & Wraps', 'icon' => 'sandwich'],
            ['name' => 'Pizza & Pasta', 'icon' => 'pizza'],
            ['name' => 'Asian & Rice', 'icon' => 'bowl-food'],
            ['name' => 'Beverages', 'icon' => 'cup-soda'],
            ['name' => 'Desserts', 'icon' => 'cake-slice'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
            ]);
        }

        // 2. Seed Menu Items / Dishes
        $dishes = [
            // Burgers
            [
                'category_id' => $categories['Burgers & Wraps']->id,
                'name' => 'Truffle Angus Smash Burger',
                'description' => 'Double double-smash beef patties, white cheddar, caramelized onions & black truffle aioli on brioche.',
                'price' => 14.99,
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 12,
            ],
            [
                'category_id' => $categories['Burgers & Wraps']->id,
                'name' => 'Crispy Nashville Hot Chicken Wrap',
                'description' => 'Fried chicken tenders tossed in cayenne hot oil, dill pickles, house slaw & ranch sauce.',
                'price' => 11.50,
                'image' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 10,
            ],
            // Pizza & Pasta
            [
                'category_id' => $categories['Pizza & Pasta']->id,
                'name' => 'Neapolitan Artisan Pepperoni Pizza',
                'description' => 'Wood-fired sourdough crust, San Marzano tomato sauce, fresh mozzarella & cupped pepperoni.',
                'price' => 17.00,
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 18,
            ],
            [
                'category_id' => $categories['Pizza & Pasta']->id,
                'name' => 'Creamy Garlic Butter Fettuccine Alfredo',
                'description' => 'Handmade fettuccine pasta tossed in rich parmesan cream, garlic butter & fresh parsley.',
                'price' => 15.50,
                'image' => 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 15,
            ],
            // Asian & Rice
            [
                'category_id' => $categories['Asian & Rice']->id,
                'name' => 'Flame-Grilled Wagyu Beef Bowl (Donburi)',
                'description' => 'Sliced Wagyu beef over steamed jasmine rice, topped with soft poached egg & teriyaki glaze.',
                'price' => 19.99,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 14,
            ],
            [
                'category_id' => $categories['Asian & Rice']->id,
                'name' => 'Authentic Thai Pad Thai Shrimp',
                'description' => 'Stir-fried rice noodles with jumbo tiger prawns, crushed peanuts, bean sprouts & tamarind sauce.',
                'price' => 16.25,
                'image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 15,
            ],
            // Beverages
            [
                'category_id' => $categories['Beverages']->id,
                'name' => 'Iced Passionfruit Mint Fizz',
                'description' => 'Fresh passionfruit pulp, lime juice, fresh mint leaves & sparkling soda.',
                'price' => 5.99,
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 5,
            ],
            [
                'category_id' => $categories['Beverages']->id,
                'name' => 'Cold Brew Nitro Espresso',
                'description' => 'Smooth 16-hour steeped cold brew infused with nitrogen for a creamy velvety head.',
                'price' => 4.50,
                'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 3,
            ],
            // Desserts
            [
                'category_id' => $categories['Desserts']->id,
                'name' => 'Warm Chocolate Lava Molten Cake',
                'description' => 'Dark Belgian chocolate cake with a gooey molten center served with vanilla bean ice cream.',
                'price' => 8.99,
                'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=600&q=80',
                'is_available' => true,
                'prep_time_minutes' => 10,
            ],
        ];

        $createdMenuItems = [];
        foreach ($dishes as $dish) {
            $createdMenuItems[] = MenuItem::create($dish);
        }

        // 3. Seed Dining Tables
        $tablesData = [
            ['table_number' => 'Table 01', 'capacity' => 2, 'location' => 'Main Dining', 'status' => 'occupied'],
            ['table_number' => 'Table 02', 'capacity' => 4, 'location' => 'Main Dining', 'status' => 'occupied'],
            ['table_number' => 'Table 03', 'capacity' => 4, 'location' => 'Main Dining', 'status' => 'available'],
            ['table_number' => 'Table 04', 'capacity' => 6, 'location' => 'Window Side', 'status' => 'reserved'],
            ['table_number' => 'Table 05', 'capacity' => 2, 'location' => 'Patio', 'status' => 'available'],
            ['table_number' => 'Table 06', 'capacity' => 8, 'location' => 'VIP Lounge', 'status' => 'occupied'],
            ['table_number' => 'Table 07', 'capacity' => 4, 'location' => 'Patio', 'status' => 'available'],
            ['table_number' => 'Table 08', 'capacity' => 6, 'location' => 'Rooftop', 'status' => 'available'],
        ];

        $createdTables = [];
        foreach ($tablesData as $tbl) {
            $createdTables[] = Table::create($tbl);
        }

        // 4. Seed Historical Orders (Last 7 Days) for Chart Analytics
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $orderCount = rand(5, 12);

            for ($j = 0; $j < $orderCount; $j++) {
                $statusList = ['completed', 'completed', 'completed', 'completed', 'cancelled'];
                $status = ($i === 0)
                    ? ['pending', 'preparing', 'ready', 'completed'][rand(0, 3)]
                    : $statusList[rand(0, 4)];

                $orderType = ['dine_in', 'takeaway', 'delivery'][rand(0, 2)];
                $tableId = ($orderType === 'dine_in') ? $createdTables[rand(0, 7)]->id : null;

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                    'table_id' => $tableId,
                    'customer_name' => ['Ahmad Khan', 'Sarah Jenkins', 'Michael Chen', 'Usman Ali', 'Fatima Zahra', 'John Doe'][rand(0, 5)],
                    'order_type' => $orderType,
                    'status' => $status,
                    'total_amount' => 0,
                    'created_at' => $date->copy()->addHours(rand(10, 22))->addMinutes(rand(0, 59)),
                ]);

                // Random 1 to 3 items
                $total = 0;
                $itemCount = rand(1, 3);
                $randomDishes = collect($createdMenuItems)->random($itemCount);

                foreach ($randomDishes as $dishItem) {
                    $qty = rand(1, 2);
                    $subtotal = $dishItem->price * $qty;
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $dishItem->id,
                        'quantity' => $qty,
                        'unit_price' => $dishItem->price,
                        'subtotal' => $subtotal,
                        'created_at' => $order->created_at,
                    ]);
                }

                $order->update(['total_amount' => $total]);
            }
        }

        // 5. Seed Staff Members
        $staffsData = [
            [
                'name' => 'Alex Rodriguez',
                'email' => 'alex.r@restaurant.com',
                'phone' => '+1 (555) 234-5678',
                'role' => 'Manager',
                'status' => 'on_shift',
                'shift' => 'Full-Day',
                'hourly_rate' => 28.50,
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => 'Chef Marco Pierre',
                'email' => 'marco.chef@restaurant.com',
                'phone' => '+1 (555) 876-5432',
                'role' => 'Head Chef',
                'status' => 'on_shift',
                'shift' => 'Morning',
                'hourly_rate' => 32.00,
                'avatar' => 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => 'Sophia Chen',
                'email' => 'sophia.c@restaurant.com',
                'phone' => '+1 (555) 345-6789',
                'role' => 'Chef',
                'status' => 'active',
                'shift' => 'Evening',
                'hourly_rate' => 22.00,
                'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => 'David Kim',
                'email' => 'david.k@restaurant.com',
                'phone' => '+1 (555) 456-7890',
                'role' => 'Waiter',
                'status' => 'on_shift',
                'shift' => 'Morning',
                'hourly_rate' => 16.50,
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => 'Emma Watson',
                'email' => 'emma.w@restaurant.com',
                'phone' => '+1 (555) 567-8901',
                'role' => 'Waitress',
                'role' => 'Waiter',
                'status' => 'on_shift',
                'shift' => 'Evening',
                'hourly_rate' => 16.50,
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => 'Lucas Miller',
                'email' => 'lucas.m@restaurant.com',
                'phone' => '+1 (555) 678-9012',
                'role' => 'Cashier',
                'status' => 'on_shift',
                'shift' => 'Morning',
                'hourly_rate' => 18.00,
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => 'Olivia Taylor',
                'email' => 'olivia.t@restaurant.com',
                'phone' => '+1 (555) 789-0123',
                'role' => 'Bartender',
                'status' => 'off_duty',
                'shift' => 'Night',
                'hourly_rate' => 20.00,
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80',
            ]
        ];

        foreach ($staffsData as $staff) {
            Staff::create($staff);
        }
    }
}
