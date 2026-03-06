<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Camion;
use App\Models\Ruta;

class DatosInicialesSeeder extends Seeder
{
    public function run(): void
    {
        Camion::firstOrCreate(
            ['placa' => 'EJHI234'],
            ['codigo' => 'camion-01', 'estado' => 'activo']
        );

        Ruta::firstOrCreate(
            ['nombre' => 'Ruta Centro'],
            [
                'estado' => 'activa',
                'tolerancia_metros' => 50,
                'geometria_geojson' => '{"type":"LineString","coordinates":[[-63.1800,-17.7800],[-63.1810,-17.7810]]}'
            ]
        );
    }
}
