<?php

namespace Database\Seeders;

use App\Models\Room;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@user.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        Room::create([
            'name' => 'Standard Suite',
            'image' => 'images/standard.jpg',
            'status' => 1,
            'available_rooms' => 7,
            'total_rooms' => 7,
        ]);

        Room::create([
            'name' => 'Deluxe Suite',
            'image' => 'images/deluxe.jpg',
            'status' => 1,
            'available_rooms' => 7,
            'total_rooms' => 7,
        ]);

        Room::create([
            'name' => 'Executive Suite',
            'image' => 'images/executive.jpg',
            'status' => 1,
            'available_rooms' => 7,
            'total_rooms' => 7,
        ]);
    }
}
