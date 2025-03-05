<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker =Faker::create();

        Product::create([
            'name' => 'Producto 1',
            'description' => 'Descripción del producto 1',
            'price' => 100,
            'stock' => 50,
        ]);


        for($i = 0 ; $i < 20 ; $i ++){
            Product::create([
                'name' => $faker->word,
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2, 1, 10),
                'stock' => $faker->numberBetween(10, 100),
                'image' => $faker->imageUrl(200, 200, 'beer'),
            ]);
        }

    }
}
