<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Unite;
use App\Models\Formateur;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $unites = Unite::all();
        $formateurs = Formateur::all();

        if ($unites->isEmpty() || $formateurs->isEmpty()) {
            return;
        }

        $modulesData = [
            ['titre' => 'Algorithmique', 'duree' => 60],
            ['titre' => 'Base de données', 'duree' => 50],
            ['titre' => 'Développement Web', 'duree' => 80],
            ['titre' => 'Réseaux informatiques', 'duree' => 45],
            ['titre' => 'Sécurité informatique', 'duree' => 40],
        ];

        foreach ($modulesData as $data) {

            $unite = $unites->random();
            $formateur = $formateurs->random();

            Module::create([
                'titre' => $data['titre'],
                'description' => 'Module de ' . $data['titre'],
                'duree' => $data['duree'],
                'nombre_cc' => rand(2, 4),
                'formateur_id' => $formateur->id,
                'unite_id' => $unite->id,
            ]);
        }
    }
}