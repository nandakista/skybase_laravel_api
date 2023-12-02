<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sub_categories')->insert([
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Artifical Intelligence',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Software Engineering',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'name' => 'Design',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'Saham',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'category_id' => 2,
                'name' => 'Kripto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'category_id' => 2,
                'name' => 'Reksadana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'category_id' => 3,
                'name' => 'Personal Healthy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'category_id' => 3,
                'name' => 'Bio medicine',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'category_id' => 3,
                'name' => 'Vaksinasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
