<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $user = \App\Models\User::create([
            'name' => "Testing User",
            'email' => "testuser@testmail.com",
            'phone_number' => "01111 111 111",
            'role' => 'admin',
            'password' => bcrypt('Pass123!'), // password
        ]);
        $user->save();
        $user2 = \App\Models\User::create([
            'name' => "Testing User 2",
            'email' => "testuser2@testmail.com",
            'phone_number' => "02222 222 222",
            'role' => 'user',
            'password' => bcrypt('Pass123!'), // password
        ]);
        $user2->save();
    }
}
