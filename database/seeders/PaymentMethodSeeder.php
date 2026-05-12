<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash on Delivery', 'code' => 'cash',    'sort_order' => 1],
            ['name' => 'M-Pesa',           'code' => 'mpesa',   'sort_order' => 2],
            ['name' => 'EcoCash',          'code' => 'ecocash', 'sort_order' => 3],
            ['name' => 'Bank Transfer',    'code' => 'bank',    'sort_order' => 4],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['code' => $method['code']], $method);
        }
    }
}