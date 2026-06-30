<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stagiaire;
use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Support\Str;

class StagiaireSeeder extends Seeder
{
    public function run(): void
    {
        $filieres = Filiere::all();
        $groupes  = Groupe::all();

        if ($filieres->isEmpty() || $groupes->isEmpty()) {
            return;
        }

        $prenoms = ['Ahmed', 'Youssef', 'Sara', 'Khadija', 'Omar', 'Imane', 'Salma', 'Hamza'];
        $noms    = ['El Amrani', 'Benali', 'Tazi', 'Haddad', 'Bennani', 'Khalfi', 'Alaoui'];

        for ($i = 1; $i <= 30; $i++) {

            $groupe = $groupes->random();
            $filiere = $groupe->filiere ?? $filieres->random();

            Stagiaire::create([
                'nom' => $noms[array_rand($noms)],
                'prenom' => $prenoms[array_rand($prenoms)],
                'date_naissance' => now()->subYears(rand(18, 25))->subDays(rand(1, 1000)),
                'cin' => strtoupper(Str::random(8)),
                'adresse' => 'Safi - Maroc',
                'telephone' => '06' . rand(10000000, 99999999),
                'responsable_telephone' => '06' . rand(10000000, 99999999),
                'filiere_id' => $filiere->id,
                'group_id' => $groupe->id,
            ]);
        }
    }
}