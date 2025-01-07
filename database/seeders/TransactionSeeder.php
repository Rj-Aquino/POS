<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        Transaction::create([
            'OrderID' => 1,
            'LoyaltyCardID' => 1,
            'TotalPointsUsed' => 100,
            'PointsEarned' => 50,
            'TransactionDate' => now(),
        ]);

        Transaction::create([
            'OrderID' => 2,
            'LoyaltyCardID' => 2,
            'TotalPointsUsed' => 200,
            'PointsEarned' => 100,
            'TransactionDate' => now(),
        ]);
    }
}
