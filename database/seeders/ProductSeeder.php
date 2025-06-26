<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Kemeja Lengan Panjang',
            'slug' => Str::slug('Kemeja Lengan Panjang'),
            'description' => 'Kemeja pria dengan bahan katun, cocok untuk formal maupun casual.',
            'price' => 175000,
            'stock' => 20,
            'image' => 'products/kemeja1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);

        Product::create([
            'name' => 'Kaos Polos Hitam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => 'Klos Hitam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => 'Ka Hitam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => 'Kaos Polo',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => 'Ktam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => 'Kaam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => 'Hitam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => 'Kaos  Hitam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
        Product::create([
            'name' => ' Polos Hitam',
            'slug' => Str::slug('Kaos Polos Hitam'),
            'description' => 'Kaos polos dengan bahan premium, nyaman dipakai sehari-hari.',
            'price' => 95000,
            'stock' => 50,
            'image' => 'products/kaos1.jpg',
            'status' => true,
            'category_id' => rand(1, 5),
        ]);
    }
}
