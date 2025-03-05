<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Beras & Biji-bijian
            [
                'category' => 'Beras & Biji-bijian',
                'products' => [
                    [
                        'name' => 'Beras Pandan Wangi',
                        'description' => 'Beras premium dengan aroma pandan, bersih dari kulit padi dan bebas pemutih',
                        'price' => 75000,
                        'stock' => 100,
                        'weight' => 5000, // 5kg
                        'height' => 40,
                        'width' => 25,
                        'length' => 10
                    ],
                    [
                        'name' => 'Beras Merah Organik',
                        'description' => 'Beras merah organik kaya serat dan vitamin B, hasil pertanian organik',
                        'price' => 89000,
                        'stock' => 50,
                        'weight' => 2500, // 2.5kg
                        'height' => 35,
                        'width' => 20,
                        'length' => 8
                    ]
                ]
            ],
            // Minyak & Mentega
            [
                'category' => 'Minyak & Mentega',
                'products' => [
                    [
                        'name' => 'Minyak Goreng Sawit',
                        'description' => 'Minyak goreng sawit berkualitas, hasil penyaringan 2x, bebas kolesterol',
                        'price' => 45000,
                        'stock' => 150,
                        'weight' => 2000, // 2L
                        'height' => 30,
                        'width' => 12,
                        'length' => 12
                    ],
                    [
                        'name' => 'Mentega Tawar',
                        'description' => 'Mentega tawar untuk memasak dan membuat kue',
                        'price' => 25000,
                        'stock' => 80,
                        'weight' => 500,
                        'height' => 10,
                        'width' => 8,
                        'length' => 8
                    ]
                ]
            ],
            // Gula & Pemanis
            [
                'category' => 'Gula & Pemanis',
                'products' => [
                    [
                        'name' => 'Gula Pasir Putih',
                        'description' => 'Gula pasir putih berkualitas, hasil penyaringan sempurna',
                        'price' => 15000,
                        'stock' => 200,
                        'weight' => 1000,
                        'height' => 20,
                        'width' => 15,
                        'length' => 8
                    ],
                    [
                        'name' => 'Gula Aren Bubuk',
                        'description' => 'Gula aren bubuk alami, tanpa bahan pengawet',
                        'price' => 35000,
                        'stock' => 75,
                        'weight' => 500,
                        'height' => 18,
                        'width' => 12,
                        'length' => 7
                    ]
                ]
            ],
            // Telur & Susu
            [
                'category' => 'Telur & Susu',
                'products' => [
                    [
                        'name' => 'Telur Ayam Negeri',
                        'description' => 'Telur ayam segar pilihan, ukuran besar',
                        'price' => 28000,
                        'stock' => 100,
                        'weight' => 1000, // 1kg ±16 butir
                        'height' => 15,
                        'width' => 25,
                        'length' => 30
                    ],
                    [
                        'name' => 'Susu Ultra UHT Full Cream',
                        'description' => 'Susu segar full cream dengan proses UHT',
                        'price' => 18000,
                        'stock' => 120,
                        'weight' => 1000, // 1L
                        'height' => 20,
                        'width' => 10,
                        'length' => 7
                    ]
                ]
            ],
            // Bumbu Dapur
            [
                'category' => 'Bumbu Dapur',
                'products' => [
                    [
                        'name' => 'Garam Meja Beryodium',
                        'description' => 'Garam halus beryodium untuk keperluan memasak sehari-hari',
                        'price' => 5000,
                        'stock' => 300,
                        'weight' => 250,
                        'height' => 12,
                        'width' => 8,
                        'length' => 5
                    ],
                    [
                        'name' => 'Merica Bubuk',
                        'description' => 'Merica bubuk berkualitas, praktis untuk bumbu masak',
                        'price' => 8000,
                        'stock' => 150,
                        'weight' => 100,
                        'height' => 10,
                        'width' => 7,
                        'length' => 4
                    ]
                ]
            ]
        ];

        foreach ($products as $categoryProducts) {
            $category = Category::where('name', $categoryProducts['category'])->first();

            foreach ($categoryProducts['products'] as $product) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'slug' => Product::generateUniqueSlug($product['name']),
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'is_active' => true,
                    'image' => [],
                    'weight' => $product['weight'],
                    'height' => $product['height'],
                    'width' => $product['width'],
                    'length' => $product['length']
                ]);
            }
        }
    }
}
