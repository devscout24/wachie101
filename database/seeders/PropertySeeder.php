<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'uploads/properties/001.jpg',
            'uploads/properties/002.jpg',
            'uploads/properties/003.jpg',
            'uploads/properties/004.jpg',
        ];

        Property::updateOrCreate(
            ['id' => 1],
            [
                'user_id'       => 1,
                'multiple_image'=> json_encode($images),
                'title'         => 'Luxury Beachside Villa',
                'location'      => 'Cox\'s Bazar, Bangladesh',
                'rating'        => 4.8,
                'total_reviews' => 0,
                'price'         => 350,
                'cleaning_fee'   => 50,
                'bedrooms'      => 4,
                'bathrooms'     => 3,
                'max_guests'    => 8,
                'amenity_id'    => 2,
                'description'   => 'A beautiful luxury villa with sea view and private pool.',
                'status'        => 1,
            ]
        );

        Property::updateOrCreate(
            ['id' => 2],
            [
                'user_id'       => 1,
                'multiple_image'=> json_encode($images),
                'title'         => 'Modern City Apartment',
                'location'      => 'Dhaka, Bangladesh',
                'rating'        => 4.5,
                'total_reviews' => 0,
                'price'         => 120,
                'cleaning_fee'   => 20,
                'bedrooms'      => 2,
                'bathrooms'     => 2,
                'max_guests'    => 4,
                'amenity_id'    => 3,
                'description'   => 'Comfortable apartment with modern facilities in the heart of the city.',
                'status'        => 1,
            ]
        );

        Property::updateOrCreate(
            ['id' => 3],
            [
                'user_id'       => 1,
                'multiple_image'=> json_encode($images),
                'title'         => 'Cozy Cottage in the Woods',
                'location'      => 'Chittagong, Bangladesh',
                'rating'        => 5,
                'total_reviews' => 0,
                'price'         => 80,
                'cleaning_fee'   => 15,
                'bedrooms'      => 1,
                'bathrooms'     => 1,
                'max_guests'    => 2,
                'amenity_id'    => 1,
                'description'   => 'A cozy cottage nestled in the woods, perfect for nature lovers.',
                'status'        => 1,
            ]
        );
    }
}
