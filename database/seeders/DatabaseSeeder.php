<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
   

        // 2. CREATE USERS
        $admin = User::updateOrCreate(
            ['id' => 1],
            [
                'name'     => 'Admin User',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('12345678'),
            ]
        );

        $guest = User::updateOrCreate(
            ['id' => 2],
            [
                'name'     => 'Guest User',
                'email'    => 'guest@guest.com',
                'password' => Hash::make('12345678'),
            ]
        );

        $general = User::updateOrCreate(
            ['id' => 3],
            [
                'name'     => 'General User',
                'email'    => 'user@user.com',
                'password' => Hash::make('12345678'),
            ]
        );



        // 4. SEED OTHER TABLES
        $this->call([
            SystemSettingSeeder::class,
            AmenitySeeder::class,
            TeamSeeder::class,
            // PropertySeeder::class,
            // PropertyAmenitySeeder::class,
            // PropertyImageSeeder::class,
        ]);
    }
}
