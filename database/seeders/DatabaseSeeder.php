<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profil;
use App\Models\Competence;
use App\Models\Offre;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(2)->admin()->create();
        $competences = Competence::factory(10)->create();
        User::factory(5)->recruteur()->create()->each(function ($user) {
            Offre::factory(rand(2, 3))->create(['user_id' => $user->id]);
        });

        // 10 Candidats avec profil et compétences
        User::factory(10)->create()->each(function ($user) use ($competences) {
            $profil = Profil::factory()->create(['user_id' => $user->id]);

            $selected = $competences->random(rand(2, 4));
            foreach ($selected as $competence) {
                $profil->competences()->attach($competence->id, [
                    'niveau' => collect(['débutant', 'intermédiaire', 'expert'])->random()
                ]);
            }
        });
    }
}
