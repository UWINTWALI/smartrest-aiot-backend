<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use App\Models\Product;
use App\Models\SensorReading;
use App\Models\Message;
use App\Models\SystemLog;
use Illuminate\Database\Seeder;

class SmartRestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding SmartRest application data...');        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@smartrest.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin',
                'email_verified_at' => now(),
                'password' => \Hash::make('password'),
            ]
        );
        $this->command->info('✅ Created admin user');

        // Create doctors
        $doctors = User::factory()
            ->doctor()
            ->count(5)
            ->create();

        $doctorProfiles = collect();
        foreach ($doctors as $doctor) {
            $profile = DoctorProfile::factory()
                ->forUser($doctor)
                ->create();
            $doctorProfiles->push($profile);
        }
        $this->command->info('✅ Created 5 doctors with profiles');

        // Create patients
        $patients = User::factory()
            ->patient()
            ->count(20)
            ->create();

        $patientProfiles = collect();
        foreach ($patients as $patient) {
            $profile = PatientProfile::factory()
                ->forUser($patient)
                ->create();
            $patientProfiles->push($profile);
        }
        $this->command->info('✅ Created 20 patients with profiles');

        // Create customers
        $customers = User::factory()
            ->customer()
            ->count(3)
            ->create();
        $this->command->info('✅ Created 3 customers');

        // Assign patients to doctors
        foreach ($patientProfiles as $index => $patientProfile) {
            $doctor = $doctorProfiles->get($index % $doctorProfiles->count());
            $doctor->patients()->attach($patientProfile->patient_id, [
                'assigned_at' => now(),
            ]);
        }
        $this->command->info('✅ Assigned patients to doctors');

        // Create products
        Product::factory()->count(10)->create();
        $this->command->info('✅ Created 10 products');

        // Create sensor readings for each patient
        foreach ($patientProfiles as $patientProfile) {
            // Create recent readings (last 24 hours)
            SensorReading::factory()
                ->forPatient($patientProfile)
                ->recent()
                ->count(rand(10, 30))
                ->create();

            // Create some historical readings
            SensorReading::factory()
                ->forPatient($patientProfile)
                ->count(rand(50, 100))
                ->create();

            // Create some abnormal readings for testing alerts
            SensorReading::factory()
                ->forPatient($patientProfile)
                ->abnormal()
                ->count(rand(1, 5))
                ->create();
        }
        $this->command->info('✅ Created sensor readings for all patients');

        // Create messages between users
        // Doctor to patient messages
        foreach ($doctorProfiles as $doctorProfile) {
            $assignedPatients = $doctorProfile->patients;
            foreach ($assignedPatients as $patient) {
                // Send some notifications and reminders
                Message::factory()
                    ->between($doctorProfile->user, $patient->user)
                    ->notification()
                    ->count(rand(1, 3))
                    ->create();

                Message::factory()
                    ->between($doctorProfile->user, $patient->user)
                    ->reminder()
                    ->count(rand(0, 2))
                    ->create();
            }
        }

        // System messages to all users
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            Message::factory()
                ->fromSystem()
                ->state(['recipient_id' => $user->user_id])
                ->count(rand(1, 3))
                ->create();
        }

        // Create some emergency messages (unread)
        Message::factory()
            ->emergency()
            ->unread()
            ->count(5)
            ->create();

        $this->command->info('✅ Created messages between users');

        // Create system logs
        $bedIds = collect(range(1, 15))->map(fn($i) => 'BED-' . str_pad($i, 5, '0', STR_PAD_LEFT));

        foreach ($bedIds as $bedId) {
            // Create info logs
            SystemLog::factory()
                ->forBed($bedId)
                ->info()
                ->count(rand(20, 50))
                ->create();

            // Create warning logs
            SystemLog::factory()
                ->forBed($bedId)
                ->warning()
                ->count(rand(5, 15))
                ->create();

            // Create some error logs
            SystemLog::factory()
                ->forBed($bedId)
                ->error()
                ->count(rand(1, 5))
                ->create();

            // Create critical logs (rare)
            SystemLog::factory()
                ->forBed($bedId)
                ->critical()
                ->count(rand(0, 2))
                ->create();

            // Create recent logs
            SystemLog::factory()
                ->forBed($bedId)
                ->recent()
                ->count(rand(5, 15))
                ->create();
        }
        $this->command->info('✅ Created system logs for all beds');

        $this->command->info('🎉 SmartRest seeding completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Users: ' . User::count());
        $this->command->info('   - Doctor Profiles: ' . DoctorProfile::count());
        $this->command->info('   - Patient Profiles: ' . PatientProfile::count());
        $this->command->info('   - Products: ' . Product::count());
        $this->command->info('   - Sensor Readings: ' . SensorReading::count());
        $this->command->info('   - Messages: ' . Message::count());
        $this->command->info('   - System Logs: ' . SystemLog::count());
    }
}
