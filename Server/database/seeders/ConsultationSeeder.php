<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Consultation;
use App\Models\Appointment;
use App\Models\Prescription;

class ConsultationSeeder extends Seeder
{
    public function run()
    {
        // Find or create a patient
        $patient = User::where('email', 'patient@example.com')->first();
        if (!$patient) {
            $patient = User::create([
                'name' => 'John Doe',
                'email' => 'patient@example.com',
                'password' => bcrypt('password'),
                'phone' => '1234567890',
                'role' => 'patient',
                'gender' => 'male',
                'age' => 35
            ]);
        }

        // Find or create doctors
        $doctorUser1 = User::where('email', 'doctor1@example.com')->first();
        if (!$doctorUser1) {
            $doctorUser1 = User::create([
                'name' => 'Dr. Sarah Johnson',
                'email' => 'doctor1@example.com',
                'password' => bcrypt('password'),
                'phone' => '1234567891',
                'role' => 'doctor',
                'gender' => 'female',
                'age' => 45
            ]);

            Doctor::create([
                'user_id' => $doctorUser1->id,
                'medical_license_number' => 'MD123456',
                'specialty' => 'Cardiologist',
                'qualification' => 'MD, FACC',
                'experience_years' => 15,
                'university_attended' => 'Harvard Medical School',
                'license_issue_date' => '2008-01-01',
                'license_expiry_date' => '2028-01-01',
                'status' => 'active',
                'payment' => 150.00,
                'location' => 'New York, NY',
                'languages' => json_encode(['English', 'Spanish']),
                'overview' => 'Experienced cardiologist specializing in heart disease.',
                'available' => true,
            ]);
        }

        $doctorUser2 = User::where('email', 'doctor2@example.com')->first();
        if (!$doctorUser2) {
            $doctorUser2 = User::create([
                'name' => 'Dr. Michael Anderson',
                'email' => 'doctor2@example.com',
                'password' => bcrypt('password'),
                'phone' => '1234567892',
                'role' => 'doctor',
                'gender' => 'male',
                'age' => 50
            ]);

            Doctor::create([
                'user_id' => $doctorUser2->id,
                'medical_license_number' => 'MD789012',
                'specialty' => 'Internal Medicine',
                'qualification' => 'MD, FACP',
                'experience_years' => 20,
                'university_attended' => 'Johns Hopkins Medical School',
                'license_issue_date' => '2003-01-01',
                'license_expiry_date' => '2023-01-01',
                'status' => 'active',
                'payment' => 120.00,
                'location' => 'Boston, MA',
                'languages' => json_encode(['English']),
                'overview' => 'Internal medicine specialist with focus on preventive care.',
                'available' => true,
            ]);
        }

        // Create appointments
        $appointment1 = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorUser1->id,
            'appointment_date' => '2024-05-15',
            'appointment_time' => '10:00:00',
            'appointment_type' => 'Follow-up Consultation',
            'status' => 'completed',
            'notes' => 'Follow-up for blood pressure management'
        ]);

        $appointment2 = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorUser2->id,
            'appointment_date' => '2024-03-20',
            'appointment_time' => '14:00:00',
            'appointment_type' => 'Initial Consultation',
            'status' => 'completed',
            'notes' => 'Initial consultation for hypertension'
        ]);

        // Create prescriptions
        $prescription1 = Prescription::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorUser1->id,
            'medications' => json_encode(['Lisinopril 10mg daily', 'Aspirin 81mg daily']),
            'instructions' => 'Take medications as prescribed. Monitor blood pressure daily.',
            'date_prescribed' => '2024-05-15',
        ]);

        $prescription2 = Prescription::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorUser2->id,
            'medications' => json_encode(['Lisinopril 5mg daily']),
            'instructions' => 'Start with 5mg daily, lifestyle modifications recommended.',
            'date_prescribed' => '2024-03-20',
        ]);

        // Create consultations
        Consultation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorUser1->id,
            'appointment_id' => $appointment1->id,
            'prescription_id' => $prescription1->id,
            'consultation_date' => '2024-05-15',
            'notes' => 'Patient reported significant improvement in chest pain symptoms. Blood pressure well controlled on current medication. Recommended continuation of Lisinopril 10mg daily. Patient advised to maintain low-sodium diet and regular exercise routine. Follow-up in 3 months or sooner if symptoms return.',
        ]);

        Consultation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorUser2->id,
            'appointment_id' => $appointment2->id,
            'prescription_id' => $prescription2->id,
            'consultation_date' => '2024-03-20',
            'notes' => 'Patient presented with complaints of intermittent chest discomfort and elevated blood pressure readings at home. Physical examination revealed no acute distress. Heart sounds regular, no murmurs. Lungs clear bilaterally. Recommended lifestyle modifications and initiated antihypertensive therapy.',
        ]);

        $this->command->info('Sample consultation data created successfully!');
    }
}
