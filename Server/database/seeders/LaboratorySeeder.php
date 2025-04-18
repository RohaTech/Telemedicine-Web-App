<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LaboratorySeeder extends Seeder
{
    public function run()
    {
        $tests = [
            "Complete Blood Count" => [
                "description" => "Measures red and white blood cells, hemoglobin, and platelets.",
                "provided" => false,
            ],
            "Basic Metabolic Panel" => [
                "description" => "Tests blood sugar, electrolytes, and kidney function.",
                "provided" => false,
            ],
            "Comprehensive Metabolic Panel" => [
                "description" => "Includes liver function tests like albumin and bilirubin.",
                "provided" => false,
            ],
            "Lipid Panel" => [
                "description" => "Measures cholesterol levels to assess cardiovascular risk.",
                "provided" => false,
            ],
            "Hemoglobin A1c" => [
                "description" => "Monitors average blood sugar levels over 2-3 months.",
                "provided" => false,
            ],
            "Thyroid Function Tests" => [
                "description" => "Measures TSH, T3, and T4 to evaluate thyroid activity.",
                "provided" => false,
            ],
            "Liver Function Tests" => [
                "description" => "Assesses liver health through enzymes like ALT and AST.",
                "provided" => false,
            ],
            "Kidney Function Tests" => [
                "description" => "Evaluates kidney performance by measuring waste products.",
                "provided" => false,
            ],
            "Urinalysis" => [
                "description" => "Analyzes urine for signs of infection or kidney disease.",
                "provided" => false,
            ],
            "C-Reactive Protein" => [
                "description" => "Measures inflammation levels in the body.",
                "provided" => false,
            ],
            "Erythrocyte Sedimentation Rate" => [
                "description" => "Assesses inflammation by red blood cell settling rate.",
                "provided" => false,
            ],
            "Blood Glucose Test" => [
                "description" => "Checks blood sugar levels to diagnose diabetes.",
                "provided" => false,
            ],
            "Prothrombin Time" => [
                "description" => "Evaluates blood clotting ability.",
                "provided" => false,
            ],
            "Activated Partial Thromboplastin Time" => [
                "description" => "Assesses blood clotting pathways.",
                "provided" => false,
            ],
            "Electrolyte Panel" => [
                "description" => "Measures sodium, potassium, chloride, and bicarbonate.",
                "provided" => false,
            ],
            "Iron Studies" => [
                "description" => "Evaluates iron levels to diagnose anemia.",
                "provided" => false,
            ],
            "Vitamin D Test" => [
                "description" => "Measures vitamin D levels for bone health.",
                "provided" => false,
            ],
            "Blood Culture" => [
                "description" => "Identifies bacteria or fungi in the blood.",
                "provided" => false,
            ],
            "Stool Occult Blood Test" => [
                "description" => "Detects hidden blood in stool for GI issues.",
                "provided" => false,
            ],
            "Serum Uric Acid Test" => [
                "description" => "Measures uric acid levels to diagnose gout.",
                "provided" => false,
            ],
        ];

        $laboratories = [];

        for ($i = 1; $i <= 10; $i++) {
            $laboratories[] = [
                'name' => "Lab $i",
                'email' => "lab{$i}@example.com",
                'password' => bcrypt('password123'),
                'phone' => '09' . rand(10000000, 99999999),
                'address' => "Addis Ababa, Area $i",
                'tests' => json_encode($tests),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('laboratories')->insert($laboratories);
    }
}
