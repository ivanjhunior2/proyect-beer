<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {

        $faker = Faker::create();
        $password = Hash::make('password');

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => $password,
            'role' => 'admin',
            
        ]);

        for($i= 0; $i < 10; $i++){
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => $password,
                'role' => 'customer',
            ]);
        }
    }
}
