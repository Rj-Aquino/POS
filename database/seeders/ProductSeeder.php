<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserting products into the 'product' table
        DB::table('product')->insert([
            [
                'CategoryID' => 1, // Assuming category with ID 1 exists
                'Name' => 'Smartphone',
                'Price' => 799.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'CategoryID' => 2, // Assuming category with ID 2 exists
                'Name' => 'T-shirt',
                'Price' => 19.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'CategoryID' => 3, // Assuming category with ID 3 exists
                'Name' => 'Microwave',
                'Price' => 129.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more products as needed
        ]);
    }
}
