<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unite;

class UniteSeeder extends Seeder
{
    public function run(): void
    {
        Unite::create([
            'nom'         => 'Sciences Fondamentales',
            'code'        => 'UF01',
            'heures'      => 120,
            'coefficient' => 2,
            'niveau_id'   => 1,
        ]);

        Unite::create([
            'nom'         => 'Soins Infirmiers',
            'code'        => 'UF02',
            'heures'      => 180,
            'coefficient' => 4,
            'niveau_id'   => 1,
        ]);

        Unite::create([
            'nom'         => 'Santé Communautaire',
            'code'        => 'UF03',
            'heures'      => 100,
            'coefficient' => 3,
            'niveau_id'   => 2,
        ]);

        Unite::create([
            'nom'         => 'Urgences et Réanimation',
            'code'        => 'UF04',
            'heures'      => 160,
            'coefficient' => 5,
            'niveau_id'   => 2,
        ]);

        Unite::create([
            'nom'         => 'Stage Professionnel',
            'code'        => 'UF05',
            'heures'      => 200,
            'coefficient' => 6,
            'niveau_id'   => 3,
        ]);
    }
}