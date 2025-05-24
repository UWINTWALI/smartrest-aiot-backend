<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@smartrest.com',
            'phone' => '+250700000001',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create doctor user
        $doctor = User::create([
            'first_name' => 'Doctor',
            'last_name' => 'User',
            'email' => 'doctor@smartrest.com',
            'phone' => '+250700000002',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
        ]);
        
        // Create doctor profile
        DoctorProfile::create([
            'doctor_id' => $doctor->user_id,
            'license_no' => 'MD123456',
            'specialty' => 'Pulmonology',
            'created_at' => now(),
        ]);
        
        // Create patient user
        $patient = User::create([
            'first_name' => 'Patient',
            'last_name' => 'User',
            'email' => 'patient@smartrest.com',
            'phone' => '+250700000003',
            'password' => Hash::make('password123'),
            'role' => 'patient',
        ]);
        
        // Create patient profile
        $patientProfile = PatientProfile::create([
            'patient_id' => $patient->user_id,
            'national_id' => '1234567890123456',
            'date_of_birth' => '1985-05-15',
            'sex' => 'M',
            'created_at' => now(),
        ]);
        
        // Assign patient to doctor
        \DB::table('doctor_patients')->insert([
            'doctor_id' => $doctor->user_id,
            'patient_id' => $patient->user_id,
            'assigned_at' => now(),
        ]);

        // Create customer user
        $customer = User::create([
            'first_name' => 'Customer',
            'last_name' => 'User',
            'email' => 'customer@smartrest.com',
            'phone' => '+250700000004',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);
    }
}
