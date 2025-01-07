<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserting categories into the 'category' table
        DB::table('category')->insert([
            [
                'Name' => 'Electronics',
                'Description' => 'Devices and gadgets like phones, laptops, etc.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Name' => 'Clothing',
                'Description' => 'Apparel and fashion items.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Name' => 'Home Appliances',
                'Description' => 'Machines and tools for household use.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more categories as needed
        ]);
    }
}
