<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'administrador']);
        Role::firstOrCreate(['name' => 'encargado']);
        Role::firstOrCreate(['name' => 'conductor']);
    }
}
