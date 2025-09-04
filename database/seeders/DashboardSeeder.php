<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Feedback;
use App\Models\Role;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // users
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $users[] = User::firstOrCreate([
                'email' => "demo{$i}@example.com"
            ], [
                'name' => "Demo User {$i}",
                'password' => bcrypt('password'),
                'role_id' => Role::CUSTOMER,
            ]);
        }

        // categories
        $categories = [];
        $categoryNames = ['Bedroom', 'Living Room', 'Dining', 'Kitchen', 'Office'];
        foreach ($categoryNames as $name) {
            $categories[] = Category::firstOrCreate(['name' => $name], [
                'image' => 'images/default-category.jpg'
            ]);
        }

        // products
        $products = [];
        $productNames = [
            'Luxury Bed Frame', 'Comfortable Sofa', 'Dining Table Set', 
            'Kitchen Cabinet', 'Office Chair', 'Wardrobe', 'Coffee Table',
            'Bookshelf', 'Desk Lamp', 'Storage Box'
        ];
        
        foreach ($productNames as $index => $name) {
            $products[] = Product::firstOrCreate(['name' => $name], [
                'price' => rand(500000, 5000000),
                'description' => "Demo {$name} for dashboard analytics",
                'stock' => rand(10, 100),
                'category_id' => $categories[$index % count($categories)]->category_id,
                'image' => 'images/default-product.jpg'
            ]);
        }

        // orders, order_items, and feedbacks in last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $orderCounts = [3, 7, 5, 12, 8, 15, 10];
            $ordersToday = $orderCounts[6 - $i];
            
            for ($j = 0; $j < $ordersToday; $j++) {
                $user = $users[array_rand($users)];
                
                $order = Order::create([
                    'customer_id' => $user->id,
                    'order_date' => $date->format('Y-m-d'),
                    'total_cost' => 0,
                    'status' => 'approved',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalCost = 0;
                $itemCount = rand(1, 3);
                for ($k = 0; $k < $itemCount; $k++) {
                    $product = $products[array_rand($products)];
                    $quantity = rand(1, 3);
                    $price = $product->price;
                    
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $product->product_id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                    
                    $totalCost += $price * $quantity;
                }
                
                $order->update(['total_cost' => $totalCost]);
            }
            
            // feedbacks
            $feedbackCounts = [4, 8, 6, 15, 11, 18, 12]; // number of feedbacks for each day
            $feedbacksToday = $feedbackCounts[6 - $i];
            $randomDate = $date->copy()->addMinutes(rand(0, 1440));

            for ($j = 0; $j < $feedbacksToday; $j++) {
                $user = $users[array_rand($users)];
                $product = $products[array_rand($products)];
                
                Feedback::create([
                    'user_id' => $user->id,
                    'product_id' => $product->product_id,
                    'rating' => rand(3, 5),
                    'comment' => "Demo feedback for analytics dashboard",
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
            }
        }
    }
}
