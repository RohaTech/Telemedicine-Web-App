<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class PatientSeeder extends Seeder
{
    public function run()
    {
        $patients = [];

        for ($i = 1; $i <= 30; $i++) {
            $name = $this->generateEthiopianName($i);
            $email = "patient{$i}@example.com";
            $phone = $this->generatePhoneNumber();
            $address = $this->generateEthiopianAddress($i);

            $patients[] = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'password' => bcrypt('password123'), // default password
                'role' => 'patient',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all patients at once
        User::insert($patients);
    }

    private function generateEthiopianName($index)
    {
        $firstNames = [
            'Abebe',
            'Mulugeta',
            'Selam',
            'Teshome',
            'Alemu',
            'Fikirte',
            'Kebede',
            'Hirut',
            'Getachew',
            'Lily',
            'Dawit',
            'Mekdes',
            'Yared',
            'Saba',
            'Tesfaye',
            'Almaz',
            'Birhanu',
            'Rahel',
            'Solomon',
            'Mahi',
            'Tigist',
            'Haile',
            'Eleni',
            'Samuel',
            'Genet',
            'Fikru',
            'Marta',
            'Tesfu',
            'Sara',
            'Daniel'
        ];

        $lastNames = [
            'Bekele',
            'Tadesse',
            'Gebremariam',
            'Kebede',
            'Fikru',
            'Tesfaye',
            'Mekonnen',
            'Alemu',
            'Haile',
            'Yohannes',
            'Abraham',
            'Tsegaye',
            'Gebre',
            'Meles',
            'Hailu',
            'Fenta',
            'Girma',
            'Teshome',
            'Asfaw',
            'Negash',
            'Wolde',
            'Kassa',
            'Mamo',
            'Tiruneh',
            'Biru',
            'Desta',
            'Zewdu',
            'Fikadu',
            'Mulu',
            'Kifle'
        ];

        $firstName = $firstNames[($index - 1) % count($firstNames)];
        $lastName = $lastNames[($index - 1) % count($lastNames)];

        return "$firstName $lastName";
    }

    private function generateEthiopianAddress($index)
    {
        $addresses = [
            'Addis Abeba, Bole',
            'Addis Abeba, Arada',
            'Addis Abeba, Lideta',
            'Addis Abeba, Gullele',
            'Addis Abeba, Kirkos',
            'Adama, Central',
            'Mekelle, Hawelti',
            'Bahir Dar, Tana',
            'Dire Dawa, Sabian',
            'Hawassa, Tabor',
            'Dessie, Piassa',
            'Kombolcha, Bishager',
        ];

        return $addresses[($index - 1) % count($addresses)];
    }

    private function generatePhoneNumber()
    {
        $number = '09';
        for ($i = 0; $i < 8; $i++) {
            $number .= rand(0, 9);
        }
        return $number;
    }
}
