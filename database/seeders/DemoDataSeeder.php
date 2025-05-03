<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $hotel = Hotel::create([
            'name' => 'Seaside Hotel',
            'location' => 'Goa',
            'description' => 'A beach hotel',
            'created_by' => $admin->id
        ]);

        Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => '101',
            'room_type' => 'Deluxe',
            'price_per_night' => 120.00,
            'max_guests' => 2,
        ]);
    }
}
