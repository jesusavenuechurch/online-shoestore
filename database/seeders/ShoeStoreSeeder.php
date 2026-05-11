<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShoeStoreSeeder extends Seeder
{
    public function run(): void
    {
        // --- Lookup tables ---

        $sneakersId = DB::table('categories')->insertGetId([
            'parent_id'  => null,
            'name'       => 'Sneakers',
            'slug'       => 'sneakers',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $size42 = DB::table('sizes')->insertGetId(['label' => '42', 'system' => 'EU', 'sort_order' => 42, 'created_at' => now(), 'updated_at' => now()]);
        $size44 = DB::table('sizes')->insertGetId(['label' => '44', 'system' => 'EU', 'sort_order' => 44, 'created_at' => now(), 'updated_at' => now()]);

        $red   = DB::table('colors')->insertGetId(['name' => 'Red',   'hex_code' => '#FF0000', 'created_at' => now(), 'updated_at' => now()]);
        $black = DB::table('colors')->insertGetId(['name' => 'Black', 'hex_code' => '#000000', 'created_at' => now(), 'updated_at' => now()]);

        DB::table('order_statuses')->insert([
            ['name' => 'Pending',   'color' => '#F59E0B', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paid',      'color' => '#3B82F6', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shipped',   'color' => '#8B5CF6', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Delivered', 'color' => '#10B981', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cancelled', 'color' => '#EF4444', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- Product ---

        $productId = DB::table('products')->insertGetId([
            'category_id' => $sneakersId,
            'name'        => 'Nike Air Max 90',
            'slug'        => 'nike-air-max-90',
            'description' => 'Classic comfort meets bold style.',
            'brand'       => 'Nike',
            'base_price'  => 1200.00,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // --- 4 Variants (same model, 2 sizes × 2 colors) ---

        DB::table('product_variants')->insert([
            [
                'product_id'     => $productId,
                'size_id'        => $size42,
                'color_id'       => $red,
                'sku'            => 'NK-AM90-42-RED',
                'price_override' => null,       // inherits base_price 1200.00
                'stock_quantity' => 3,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'product_id'     => $productId,
                'size_id'        => $size42,
                'color_id'       => $black,
                'sku'            => 'NK-AM90-42-BLK',
                'price_override' => null,
                'stock_quantity' => 5,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'product_id'     => $productId,
                'size_id'        => $size44,
                'color_id'       => $red,
                'sku'            => 'NK-AM90-44-RED',
                'price_override' => 1250.00,    // size 44 costs a little more
                'stock_quantity' => 2,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'product_id'     => $productId,
                'size_id'        => $size44,
                'color_id'       => $black,
                'sku'            => 'NK-AM90-44-BLK',
                'price_override' => 1250.00,
                'stock_quantity' => 1,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        // --- Simulate a customer buying 1 × EU42 Black ---

        $customerId = DB::table('customers')->insertGetId([
            'first_name' => 'Thabo',
            'last_name'  => 'Mokoena',
            'email'      => 'thabo@example.com',
            'phone'      => '+26650123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pendingStatus = DB::table('order_statuses')->where('name', 'Pending')->value('id');
        $variant       = DB::table('product_variants')->where('sku', 'NK-AM90-42-BLK')->first();
        $unitPrice     = $variant->price_override ?? DB::table('products')->where('id', $productId)->value('base_price');

        DB::transaction(function () use ($customerId, $pendingStatus, $variant, $unitPrice, $productId) {

            // 1. Decrement stock atomically
            $affected = DB::table('product_variants')
                ->where('id', $variant->id)
                ->where('stock_quantity', '>', 0)
                ->decrement('stock_quantity');

            if ($affected === 0) {
                throw new \RuntimeException("Out of stock: {$variant->sku}");
            }

            // 2. Create the order
            $orderId = DB::table('orders')->insertGetId([
                'customer_id'     => $customerId,
                'status_id'       => $pendingStatus,
                'subtotal'        => $unitPrice,
                'shipping_cost'   => 0,
                'total'           => $unitPrice,
                'shipping_address' => json_encode([
                    'name'        => 'Thabo Mokoena',
                    'line1'       => '12 Kingsway',
                    'city'        => 'Maseru',
                    'country'     => 'LS',
                    'postal_code' => '100',
                ]),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 3. Create the order item with frozen price
            DB::table('order_items')->insert([
                'order_id'   => $orderId,
                'variant_id' => $variant->id,
                'quantity'   => 1,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}