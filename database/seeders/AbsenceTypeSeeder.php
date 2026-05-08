<?php

namespace Database\Seeders;

use App\Models\OptiHr\AbsenceType;
use Illuminate\Database\Seeder;

class AbsenceTypeSeeder extends Seeder
{
    /**
     * Exécuter les commandes de remplissage de la base de données.
     */
    public function run(): void
    {
        // Données fixes d'exemple
        $absenceTypes = [
            [
                'label' => 'annuel',
                'description' => 'Absence pour congés payés annuels',
                'status' => 'ACTIVATED',
                'is_deductible' => true,
            ],

            [
                'label' => 'maternité',
                'description' => 'Congé accordé pour les salariées enceintes',
                'status' => 'ACTIVATED',
                'type' => 'EXCEPTIONAL',

                'is_deductible' => false,
            ],

            [
                'label' => 'exceptionnel',
                'description' => 'Absence pour une raison spécifique',
                'status' => 'ACTIVATED',
                'type' => 'EXCEPTIONAL',
                'is_deductible' => false,

            ],
            [
                'label' => 'déductible',
                'description' => 'Absence pour une raison personnelle',
                'status' => 'ACTIVATED',
                'type' => 'NORMAL',
                'is_deductible' => true,
            ],
            [
                'label' => 'paternité',
                'description' => 'Absence pour les pères au foyer',
                'status' => 'ACTIVATED',
                'type' => 'EXCEPTIONAL',
                'is_deductible' => false,

            ],
        ];

        // Insérer les types d'absence fixes
        foreach ($absenceTypes as $type) {
            AbsenceType::create($type);
        }

        // Générer des types d'absence aléatoires (optionnel)
        // AbsenceType::factory()->count(5)->create();
    }
}
