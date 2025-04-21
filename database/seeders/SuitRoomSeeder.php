<?php

namespace Database\Seeders;

use App\Models\SuiteRoom;
use Illuminate\Database\Seeder;

class SuitRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 0; $i < 7; $i++) {
            SuiteRoom::create([
                'room_id' => 1,
                'name' => "room 10$i",
                'is_active' => true,
                'is_occupied' => false,
                'items' => [
                    ['item' => '3 hours stay', 'price' => '300'],
                    ['item' => '6 hours stay', 'price' => '500'],
                    ['item' => '12 hours stat', 'price' => '800'],
                    ['item' => 'Overnight Stay', 'price' => '1200'],
                    ['item' => 'Extension / hr', 'price' => '100'],
                    ['item' => 'Extra Person / Extra Bed', 'price' => '700'],
                ],
            ]);
        }

        for ($i = 0; $i < 7; $i++) {
            SuiteRoom::create([
                'room_id' => 2,
                'name' => "room 20$i",
                'is_active' => true,
                'is_occupied' => false,
                'items' => [
                    ['item' => '3 hours stay', 'price' => '350'],
                    ['item' => '6 hours stay', 'price' => '550'],
                    ['item' => '12 hours stay', 'price' => '850'],
                    ['item' => 'Overnight Stay', 'price' => '1400'],
                    ['item' => 'Extension/ hour', 'price' => '100'],
                    ['item' => 'Extra bed / person', 'price' => '700'],
                ],
            ]);
        }

        for ($i = 0; $i < 13; $i++) {
            SuiteRoom::create([
                'room_id' => 3,
                'name' => "room 30$i",
                'is_active' => true,
                'is_occupied' => false,
                'items' => [
                    ['item' => '3 hours stay', 'price' => '400'],
                    ['item' => '6 hours stay', 'price' => '600'],
                    ['item' => '12 hours stay', 'price' => '900'],
                    ['item' => 'Overnight Stay', 'price' => '1600'],
                    ['item' => 'Extension / hour', 'price' => '150'],
                    ['item' => 'Extra bed / Extra person', 'price' => '700'],
                ],
            ]);
        }

        $hall = ['Cowboy Hall', 'Señorito Hall', 'Daisy Hall', 'All Halls'];
        $hallPrice = [3500, 3500, 4500, 10000];

        foreach ($hall as $key => $value) {
            SuiteRoom::create([
                'room_id' => 4,
                'name' => $value,
                'price' => $hallPrice[$key],
                'is_active' => true,
                'is_occupied' => false,
            ]);
        }
    }
}
