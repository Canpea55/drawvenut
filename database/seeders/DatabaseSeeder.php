<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Root',
            'email' => 'root@mail.com',
            'isAdmin' => 1,
        ]);
        User::factory()->create([
            'name' => 'steve',
            'email' => 'steve@mail.com',
            'isAdmin' => 0,
        ]);

        Product::factory(count: 20)->create();
    }
}
