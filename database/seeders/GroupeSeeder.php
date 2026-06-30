<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Groupe;
use App\Models\Niveau;
use App\Models\AnneeScolaire;

class GroupeSeeder extends Seeder
{
    public function run(): void
    {
        $annee = AnneeScolaire::first(); // أول année scolaire
        $niveaux = Niveau::all();

        if (!$annee || $niveaux->isEmpty()) {
            return;
        }

        $groupes = [
            [
                'nom' => 'Groupe A - INF1',
                'niveau_id' => $niveaux->first()->id,
                'filiere_id' => $niveaux->first()->filiere_id,
                'annee_scolaire_id' => $annee->id,
            ],
            [
                'nom' => 'Groupe B - INF1',
                'niveau_id' => $niveaux->first()->id,
                'filiere_id' => $niveaux->first()->filiere_id,
                'annee_scolaire_id' => $annee->id,
            ],
            [
                'nom' => 'Groupe A - INF2',
                'niveau_id' => $niveaux->skip(1)->first()->id ?? $niveaux->first()->id,
                'filiere_id' => $niveaux->skip(1)->first()->filiere_id ?? $niveaux->first()->filiere_id,
                'annee_scolaire_id' => $annee->id,
            ],
        ];

        foreach ($groupes as $groupe) {
            Groupe::create($groupe);
        }
    }
}