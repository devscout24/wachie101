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
                'ref_name'=> 'FREE_WIFI',
                'image' => 'default-image.png',
            ]
        );
        Amenity::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Swimming Pool',
                'ref_name'=> 'SWIMMING_POOL',
                'image' => 'default-image.png',
            ]
        );
        Amenity::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Fitness Center',
                'ref_name'=> 'FITNESS_CENTER',
                'image' => 'default-image.png',
            ]
        );
        Amenity::updateOrCreate(
            ['id' => 4],
            [
                'name' => '24/7 Reception',
                'ref_name'=> 'RECEPTION',
                'image' => 'default-image.png',
            ]
        );
    }
}
