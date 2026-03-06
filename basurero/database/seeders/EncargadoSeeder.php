<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EncargadoSeeder extends Seeder
{
    public function run(): void
    {
        $encargado = User::firstOrCreate(
            ['email' => 'encargado@basurero.com'],
            [
                'name' => 'encarargado',
                'password' => Hash::make('encargado123'),
            ]
        );
        $encargado->assignRole('encargado');
    }
}