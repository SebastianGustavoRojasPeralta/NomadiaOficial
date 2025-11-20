<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ExperienceSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('experiencias')->insert([
            [
                'guia_id' => null,
                'titulo' => 'Tarabuco Market Immersion',
                'descripcion' => 'Explore the colorful Tarabuco market with a local guide and learn about traditional crafts and culture.',
                'precio' => 75.00,
                'categoria' => 'Cultural',
                'duracion' => 180,
                'estado' => 'published',
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'guia_id' => null,
                'titulo' => 'La Paz Street Food Tour',
                'descripcion' => 'Taste the best street food in La Paz while walking through lively neighborhoods.',
                'precio' => 45.00,
                'categoria' => 'Food',
                'duracion' => 120,
                'estado' => 'published',
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
