<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ConductorSeeder extends Seeder
{
    public function run(): void
    {
        // Conductor 1
        $conductor1 = User::firstOrCreate(
            ['email' => 'conductor@basurero.com'],
            [
                'name' => 'conductor',
                'password' => Hash::make('conductor123'),
            ]
        );
        $conductor1->assignRole('conductor');
    }
       
}