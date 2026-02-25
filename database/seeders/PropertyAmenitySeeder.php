<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyAmenitySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Property 1 Amenities
            [
                'property_id' => 1,
                'amenity_id'  => 1,
            ],
            [
                'property_id' => 1,
                'amenity_id'  => 2,
            ],

            // Property 2 Amenities
            [
                'property_id' => 2,
                'amenity_id'  => 1,
            ],
            [
                'property_id' => 2,
                'amenity_id'  => 3,
            ],
        ];

        foreach ($data as $item) {
            DB::table('property_amenity')->updateOrInsert(
                [
                    'property_id' => $item['property_id'],
                    'amenity_id'  => $item['amenity_id'],
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
