<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ⚠️ دير seeders ديالك هنا بالترتيب الصحيح

            GroupeSeeder::class,

            UniteSeeder::class,
            ModuleSeeder::class,

            StagiaireSeeder::class,

          
        ]);
    }
}