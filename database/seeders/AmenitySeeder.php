<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Amenity::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Free Wi-Fi',
                'image' => 'default-image.png',
            ]
        );
        Amenity::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Swimming Pool',
                'image' => 'default-image.png',
            ]
        );
        Amenity::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Fitness Center',
                'image' => 'default-image.png',
            ]
        );
        Amenity::updateOrCreate(
            ['id' => 4],
            [
                'name' => '24/7 Reception',
                'image' => 'default-image.png',
            ]
        );
    }
}
