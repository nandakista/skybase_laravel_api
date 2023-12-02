<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\SubCategorySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConstantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SubCategorySeeder::class,
        ]);
    }
}
