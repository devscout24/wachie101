<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use Illuminate\Support\Facades\File;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $uploadPath = public_path('uploads/teams');

        // Create folder if doesn't exist
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }

        // Copy placeholder images automatically
        $images = [
            '011.jpg',
            '012.jpg',
            '013.jpg',
            '014.jpg',
            '015.jpg',
        ];

        foreach ($images as $img) {
            $source = database_path('seeders/images/' . $img);
            $destination = $uploadPath . '/' . $img;

            if (File::exists($source) && !File::exists($destination)) {
                File::copy($source, $destination);
            }
        }

        // Now seed the database
        Team::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'John Doe',
                'designation' => 'Manager',
                'image' => 'uploads/teams/011.jpg',
                'bio' => 'John is an experienced manager with over 10 years in the industry.',
                'is_active' => 1
            ]
        );

        Team::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Jane Smith',
                'designation' => 'Developer',
                'image' => 'uploads/teams/012.jpg',
                'bio' => 'Jane is a skilled developer with a passion for coding.',
                'is_active' => 1
            ]
        );

        Team::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Mike Johnson',
                'designation' => 'Designer',
                'image' => 'uploads/teams/013.jpg',
                'bio' => 'Mike is a creative designer with a keen eye for detail.',
                'is_active' => 1
            ]
        );

        Team::updateOrCreate(
            ['id' => 4],
            [
                'name' => 'Emily Davis',
                'designation' => 'Marketing Specialist',
                'image' => 'uploads/teams/014.jpg',
                'bio' => 'Emily is a marketing specialist with expertise in digital marketing.',
                'is_active' => 1
            ]
        );

        Team::updateOrCreate(
            ['id' => 5],
            [
                'name' => 'David Wilson',
                'designation' => 'Sales Executive',
                'image' => 'uploads/teams/015.jpg',
                'bio' => 'David is a sales executive with a strong track record of success.',
                'is_active' => 1
            ]
        );
    }
}
