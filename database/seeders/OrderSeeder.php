<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->insert([
            [
                'OrderDate' => '2024-12-01',
                'Subtotal' => 50.75,
                'Total' => 60.00,
            ],
            [
                'OrderDate' => '2024-12-10',
                'Subtotal' => 100.50,
                'Total' => 110.00,
            ],
            [
                'OrderDate' => '2024-12-15',
                'Subtotal' => 75.25,
                'Total' => 80.00,
            ],
        ]);
    }
}
