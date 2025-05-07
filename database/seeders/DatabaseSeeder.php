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

        User::factory()->create([
            'name' => 'Test User1',
            'email' => 'user1@user.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Test User2',
            'email' => 'user2@user.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Test User3',
            'email' => 'user3@user.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Test User4',
            'email' => 'user4@user.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Test User5',
            'email' => 'user5@user.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'FrontDesk User',
            'email' => 'frontdesk@user.com',
            'role' => 'front-desk',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@user.com',
            'role' => 'staff',
            'password' => bcrypt('password'),
        ]);

        Room::create([
            'name' => 'Standard Suite',
            'image' => 'images/standard.jpg',
            'status' => 1,
            'available_rooms' => 7,
            'total_rooms' => 7,
            'items' => [
                ['item' => '3 hours stay', 'price' => '300'],
                ['item' => '6 hours stay', 'price' => '500'],
                ['item' => '12 hours stat', 'price' => '800'],
                ['item' => 'Overnight Stay', 'price' => '1200'],
                ['item' => 'Extension / hr', 'price' => '100'],
                ['item' => 'Extra Person / Extra Bed', 'price' => '700'],
            ],
        ]);

        Room::create([
            'name' => 'Deluxe Suite',
            'image' => 'images/deluxe.jpg',
            'status' => 1,
            'available_rooms' => 7,
            'total_rooms' => 7,
            'items' => [
                ['item' => '3 hours stay', 'price' => '350'],
                ['item' => '6 hours stay', 'price' => '550'],
                ['item' => '12 hours stay', 'price' => '850'],
                ['item' => 'Overnight Stay', 'price' => '1400'],
                ['item' => 'Extension/ hour', 'price' => '100'],
                ['item' => 'Extra bed / person', 'price' => '700'],
            ],
        ]);

        Room::create([
            'name' => 'Executive Suite',
            'image' => 'images/executive.jpg',
            'status' => 1,
            'available_rooms' => 13,
            'total_rooms' => 13,
            'items' => [
                ['item' => '3 hours stay', 'price' => '400'],
                ['item' => '6 hours stay', 'price' => '600'],
                ['item' => '12 hours stay', 'price' => '900'],
                ['item' => 'Overnight Stay', 'price' => '1600'],
                ['item' => 'Extension / hour', 'price' => '150'],
                ['item' => 'Extra bed / Extra person', 'price' => '700'],
            ],
        ]);

        Room::create([
            'name' => 'Function Hall',
            'image' => 'images/functionhall.jpg',
            'status' => 1,
            'available_rooms' => 4,
            'total_rooms' => 4,
        ]);

        $this->call([
            SuitRoomSeeder::class,
        ]);
    }
}
