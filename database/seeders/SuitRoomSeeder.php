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
            ]);
        }

        for ($i = 0; $i < 7; $i++) {
            SuiteRoom::create([
                'room_id' => 2,
                'name' => "room 20$i",
                'is_active' => true,
                'is_occupied' => false,
            ]);
        }

        for ($i = 0; $i < 13; $i++) {
            SuiteRoom::create([
                'room_id' => 3,
                'name' => "room 30$i",
                'is_active' => true,
                'is_occupied' => false,
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
