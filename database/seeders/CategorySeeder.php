<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Beras & Biji-bijian',
                'image' => null
            ],
            [
                'name' => 'Minyak & Mentega',
                'image' => null
            ],
            [
                'name' => 'Gula & Pemanis',
                'image' => null
            ],
            [
                'name' => 'Telur & Susu',
                'image' => null
            ],
            [
                'name' => 'Bumbu Dapur',
                'image' => null
            ]
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Category::generateUniqueSlug($category['name']),
                'image' => $category['image']
            ]);
        }
    }
}
