<?php

namespace Database\Seeders;

use App\Models\Charge;
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
            'name' => 'SuperVisor',
            'email' => 'jjoker2330@gmail.com',
            'role' => 'supervisor',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'FrontDesk User',
            'email' => 'balagot.jeric@dnsc.edu.ph',
            'role' => 'front-desk',
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
                ['item' => 'Adult / Extra Bed', 'price' => '700'],
                ['item' => 'Child / Extra Bed', 'price' => '350'],
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
                ['item' => 'Adult / Extra Bed', 'price' => '700'],
                ['item' => 'Child / Extra Bed', 'price' => '350'],
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
                ['item' => 'Adult / Extra Bed', 'price' => '700'],
                ['item' => 'Child / Extra Bed', 'price' => '350'],
            ],
        ]);

        Room::create([
            'name' => 'Function Hall',
            'image' => 'images/functionhall.jpg',
            'status' => 1,
            'available_rooms' => 4,
            'total_rooms' => 4,
            'items' => [
                ['item' => 'Basic Package - Option 1', 'price' => '13000'],
                ['item' => 'Basic Package - Option 2', 'price' => '18000'],
                ['item' => 'Standard Package - Option 1', 'price' => '23000'],
                ['item' => 'Standard Package - Option 2', 'price' => '28000'],
                ['item' => 'Premium Package - Option 1', 'price' => '33000'],
                ['item' => 'Premium Package - Option 2', 'price' => '38000'],
            ],
        ]);

        Charge::create([
            'name' => 'Check-Out Extension',
            'description' => 'Check-Out Extension',
            'amount' => 100,
        ]);

        Charge::create([
            'name' => 'Extend Charge',
            'description' => 'Extend Charge',
            'amount' => 120,
        ]);

        Charge::create([
            'name' => 'Function Hall Check-Out Extension',
            'description' => 'Function Hall Check-Out Extension',
            'amount' => 1000,
        ]);

        Charge::create([
            'name' => 'Function Hall Extend Charge',
            'description' => 'Function Hall Extend Charge',
            'amount' => 1000,
        ]);

        Charge::create([
            'name' => 'Function Hall Basic Package Person Charge',
            'description' => 'Function Hall Basic Package Person Charge',
            'amount' => 300,
        ]);

        Charge::create([
            'name' => 'Function Hall Standard Package Person Charge',
            'description' => 'Function Hall Standard Package Person Charge',
            'amount' => 400,
        ]);

        Charge::create([
            'name' => 'Function Hall Premium Package Person Charge',
            'description' => 'Function Hall Premium Package Person Charge',
            'amount' => 500,
        ]);

        Charge::create([
            'name' => 'Function Hall Corkages',
            'description' => 'Function Hall Corkages',
            'amount' => 1000,
        ]);

        $this->call([
            SuitRoomSeeder::class,
        ]);
    }
}
