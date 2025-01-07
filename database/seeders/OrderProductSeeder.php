<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderProduct;

class OrderProductSeeder extends Seeder
{
    public function run()
    {
        OrderProduct::create([
            'OrderID' => 1,
            'ProductID' => 1,
            'Quantity' => 2,
            'TotalPrice' => 49.99,
        ]);

        OrderProduct::create([
            'OrderID' => 1,
            'ProductID' => 2,
            'Quantity' => 1,
            'TotalPrice' => 24.99,
        ]);

        OrderProduct::create([
            'OrderID' => 2,
            'ProductID' => 3,
            'Quantity' => 3,
            'TotalPrice' => 74.97,
        ]);
    }
}
