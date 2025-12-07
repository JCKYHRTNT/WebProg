<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Electronics'],   // id = 1
            ['name' => 'Toys'],          // id = 2
            ['name' => 'Books'],         // id = 3
            ['name' => 'Clothes'],       // id = 4
            ['name' => 'Kitchenware'],   // id = 5
            ['name' => 'Food'],          // id = 6
            ['name' => 'Accessories'],   // id = 7
            ['name' => 'Furniture'],     // id = 8
        ]);
    }
}