<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending',    'color' => '#F59E0B', 'sort_order' => 1],
            ['name' => 'Confirmed',  'color' => '#3B82F6', 'sort_order' => 2],
            ['name' => 'Shipped',    'color' => '#8B5CF6', 'sort_order' => 3],
            ['name' => 'Delivered',  'color' => '#10B981', 'sort_order' => 4],
            ['name' => 'Cancelled',  'color' => '#EF4444', 'sort_order' => 5],
        ];

        foreach ($statuses as $status) {
            OrderStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}