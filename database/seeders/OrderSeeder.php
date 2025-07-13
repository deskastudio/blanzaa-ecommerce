<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Skipping order seeding.');
            return;
        }

        // Create sample orders
        for ($i = 1; $i <= 20; $i++) {
            $customer = $customers->random();
            $orderProducts = $products->random(rand(1, 4));
            
            $totalAmount = 0;
            $shippingCost = 15000;
            
            // Calculate total
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $totalAmount += $product->price * $quantity;
            }
            
            $totalAmount += $shippingCost;
            
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'status' => $this->randomStatus(),
                'payment_status' => $this->randomPaymentStatus(),
                'total_amount' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'tax_amount' => 0,
                'discount_amount' => 0,
                
                // Customer info
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?? '081234567890',
                
                // Shipping info
                'shipping_address' => $customer->address ?? 'Jl. Sample Address No. ' . rand(1, 100),
                'shipping_city' => $customer->city ?? 'Jakarta',
                'shipping_postal_code' => $customer->postal_code ?? '12345',
                'shipping_province' => $customer->province ?? 'DKI Jakarta',
                
                // Payment
                'payment_method' => 'bank_transfer',
                'payment_proof' => rand(0, 1) ? 'payment-proofs/sample-' . $i . '.jpg' : null,
                
                'notes' => rand(0, 1) ? 'Sample order notes for testing.' : null,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
            
            // Create order items
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $price * $quantity,
                ]);
            }
        }
    }

    private function randomStatus(): string
    {
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $weights = [30, 20, 15, 15, 15, 5]; // Higher chance for pending
        
        return $this->weightedRandom($statuses, $weights);
    }

    private function randomPaymentStatus(): string
    {
        $statuses = ['pending', 'paid', 'failed', 'refunded'];
        $weights = [40, 50, 8, 2]; // Higher chance for pending and paid
        
        return $this->weightedRandom($statuses, $weights);
    }

    private function weightedRandom(array $values, array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($values as $index => $value) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $value;
            }
        }
        
        return $values[0];
    }
}