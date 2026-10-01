<?php

namespace Database\Seeders;

use App\Helpers\SeederFunc;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@dismac.com.bo'],
            [
                'name' => 'Admin',
                'password' => Hash::make('1q2w3e4r'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'consul@dismac.com.bo'],
            [
                'name' => 'Subadmin Consul',
                'password' => Hash::make('1q2w3e4r'),
                'role' => 'subadmin',
            ]
        );

        SeederFunc::loadData();
        $this->call(PrizeAndBranchSeeder::class);
    }
}
