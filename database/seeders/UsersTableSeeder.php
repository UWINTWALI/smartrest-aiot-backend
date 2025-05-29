<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use App\Models\Product;
use App\Models\SensorReading;
use App\Models\Message;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds with 100 records per entity.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $standardPassword = Hash::make('P@ssw0rd!');
        
        $this->command->info('🌱 Starting enhanced seeding with 100 records per entity...');
        
        // Create 1 super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@smartrest.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '+250700000000',
                'password' => $standardPassword,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        
        // Create 10 admin users
        $this->command->info('Creating 10 admin users...');
        $admins = [];
        for ($i = 1; $i <= 10; $i++) {
            $email = "admin{$i}@smartrest.com";
            $existingUser = User::where('email', $email)->first();
            
            if (!$existingUser) {
                $admins[] = User::create([
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $email,
                    'phone' => "+25070{$i}" . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'password' => $standardPassword,
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]);
            } else {
                $admins[] = $existingUser;
            }
        }
        
        // Create 30 doctors
        $this->command->info('Creating 30 doctors with profiles...');
        $doctors = [];
        $doctorProfiles = [];
        $specialties = ['Cardiology', 'Neurology', 'Pulmonology', 'Orthopedics', 'Pediatrics', 'Oncology', 'Dermatology', 'Psychiatry', 'Radiology', 'Urology'];
        
        for ($i = 1; $i <= 30; $i++) {
            $email = "doctor{$i}@smartrest.com";
            $existingUser = User::where('email', $email)->first();
            
            if (!$existingUser) {
                $doctor = User::create([
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $email,
                    'phone' => "+25071{$i}" . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'password' => $standardPassword,
                    'role' => 'doctor',
                    'email_verified_at' => now(),
                ]);
                
                $doctors[] = $doctor;
                
                $doctorProfile = DoctorProfile::create([
                    'doctor_id' => $doctor->user_id,
                    'license_no' => "MD" . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'specialty' => $specialties[array_rand($specialties)],
                    'institution' => $faker->company,
                    'years_experience' => $faker->numberBetween(1, 30),
                    'created_at' => now(),
                ]);
                
                $doctorProfiles[] = $doctorProfile;
            } else {
                $doctors[] = $existingUser;
                
                $existingProfile = DoctorProfile::where('doctor_id', $existingUser->user_id)->first();
                if ($existingProfile) {
                    $doctorProfiles[] = $existingProfile;
                } else {
                    $doctorProfile = DoctorProfile::create([
                        'doctor_id' => $existingUser->user_id,
                        'license_no' => "MD" . str_pad($i, 6, '0', STR_PAD_LEFT),
                        'specialty' => $specialties[array_rand($specialties)],
                        'institution' => $faker->company,
                        'years_experience' => $faker->numberBetween(1, 30),
                        'created_at' => now(),
                    ]);
                    
                    $doctorProfiles[] = $doctorProfile;
                }
            }
        }
        
        // Create 50 patients
        $this->command->info('Creating 50 patients with profiles...');
        $patients = [];
        $patientProfiles = [];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        for ($i = 1; $i <= 50; $i++) {
            $email = "patient{$i}@smartrest.com";
            $existingUser = User::where('email', $email)->first();
            
            if (!$existingUser) {
                $patient = User::create([
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $email,
                    'phone' => "+25072{$i}" . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'password' => $standardPassword,
                    'role' => 'patient',
                    'email_verified_at' => now(),
                ]);
                
                $patients[] = $patient;
                
                $patientProfile = PatientProfile::create([
                    'patient_id' => $patient->user_id,
                    'national_id' => $faker->regexify('[A-Z]{2}[0-9]{6}[A-Z]'),
                    'date_of_birth' => $faker->date('Y-m-d', '-30 years'),
                    'sex' => $faker->randomElement(['M', 'F']),
                    'emergency_contact_name' => $faker->name(),
                    'emergency_contact_phone' => $faker->phoneNumber,
                    'health_conditions' => $faker->optional(0.7)->sentence,
                    'medications' => $faker->optional(0.5)->sentence,
                    'created_at' => now(),
                ]);
                
                $patientProfiles[] = $patientProfile;
            } else {
                $patients[] = $existingUser;
                
                $existingProfile = PatientProfile::where('patient_id', $existingUser->user_id)->first();
                if ($existingProfile) {
                    $patientProfiles[] = $existingProfile;
                } else {
                    $patientProfile = PatientProfile::create([
                        'patient_id' => $existingUser->user_id,
                        'national_id' => $faker->regexify('[A-Z]{2}[0-9]{6}[A-Z]'),
                        'date_of_birth' => $faker->date('Y-m-d', '-30 years'),
                        'sex' => $faker->randomElement(['M', 'F']),
                        'emergency_contact_name' => $faker->name(),
                        'emergency_contact_phone' => $faker->phoneNumber,
                        'health_conditions' => $faker->optional(0.7)->sentence,
                        'medications' => $faker->optional(0.5)->sentence,
                        'created_at' => now(),
                    ]);
                    
                    $patientProfiles[] = $patientProfile;
                }
            }
        }
        
        // Create 20 customers
        $this->command->info('Creating 20 customers...');
        $customers = [];
        for ($i = 1; $i <= 20; $i++) {
            $email = "customer{$i}@smartrest.com";
            $existingUser = User::where('email', $email)->first();
            
            if (!$existingUser) {
                $customer = User::create([
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $email,
                    'phone' => "+25073{$i}" . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'password' => $standardPassword,
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]);
                
                $customers[] = $customer;
            } else {
                $customers[] = $existingUser;
            }
        }
        
        // Assign patients to doctors (each patient to 1-3 doctors)
        $this->command->info('Assigning patients to doctors...');
        foreach ($patientProfiles as $profile) {
            $docCount = rand(1, 3);
            $assignedDoctors = array_rand($doctorProfiles, $docCount);
            
            if (!is_array($assignedDoctors)) {
                $assignedDoctors = [$assignedDoctors];
            }
            
            foreach ($assignedDoctors as $index) {
                // Check if the relationship already exists
                $exists = \DB::table('doctor_patients')
                    ->where('doctor_id', $doctorProfiles[$index]->doctor_id)
                    ->where('patient_id', $profile->patient_id)
                    ->exists();
                    
                if (!$exists) {
                    \DB::table('doctor_patients')->insert([
                        'doctor_id' => $doctorProfiles[$index]->doctor_id,
                        'patient_id' => $profile->patient_id,
                        'assigned_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    ]);
                }
            }
        }
        
        // Create 100 products
        $this->command->info('Creating 100 products...');
        $products = [];
        $categories = ['Monitor', 'Sensor', 'Wearable', 'Gateway', 'Accessory'];
        
        for ($i = 1; $i <= 100; $i++) {
            $product = Product::create([
                'name' => $faker->words(3, true) . " " . $categories[array_rand($categories)],
                'description' => $faker->paragraph,
                'image_url' => $faker->imageUrl(640, 480, 'medical'),
                'firmware_version' => 'v' . $faker->randomDigit(),
                'is_active' => true,
            ]);
            
            $products[] = $product;
        }
        
        // Create sensor readings (100 for each patient)
        $this->command->info('Creating sensor readings for each patient (100 each)...');
        foreach ($patientProfiles as $profile) {
            // Create recent readings (last 24 hours)
            for ($i = 0; $i < 20; $i++) {
                $sensorType = $faker->randomElement(['humidity', 'body_movement', 'posture', 'vibration', 'sleep_apnea']);
                SensorReading::create([
                    'patient_id' => $profile->patient_id,
                    'bed_id' => 'BED-' . $faker->numerify('#####'),
                    'sensor_type' => $sensorType,
                    'sensor_value' => $faker->randomFloat(2, 60, 180), // Using a wide range for different sensor types
                    'sensor_unit' => $faker->randomElement(['bpm', 'mmHg', '°C', '%', 'mg/dL']),
                    'timestamp' => $faker->dateTimeBetween('-24 hours', 'now'),
                    'additional_metadata' => json_encode(['status' => $faker->randomElement(['normal', 'warning', 'critical'])]),
                ]);
            }
            
            // Create historical readings
            for ($i = 0; $i < 80; $i++) {
                $sensorType = $faker->randomElement(['humidity', 'body_movement', 'posture', 'vibration', 'sleep_apnea']);
                SensorReading::create([
                    'patient_id' => $profile->patient_id,
                    'bed_id' => 'BED-' . $faker->numerify('#####'),
                    'sensor_type' => $sensorType,
                    'sensor_value' => $faker->randomFloat(2, 60, 180),
                    'sensor_unit' => $faker->randomElement(['bpm', 'mmHg', '°C', '%', 'mg/dL']),
                    'timestamp' => $faker->dateTimeBetween('-1 year', '-24 hours'),
                    'additional_metadata' => json_encode(['status' => $faker->randomElement(['normal', 'warning', 'critical'])]),
                ]);
            }
        }
        
        // Create messages (100 between different users)
        $this->command->info('Creating 100 messages between users...');
        $messageTypes = ['notification', 'reminder', 'emergency', 'chat'];
        $allUsers = array_merge([$superAdmin], $admins, $doctors, $patients, $customers);
        
        for ($i = 0; $i < 100; $i++) {
            $sender = $allUsers[array_rand($allUsers)];
            $recipient = $allUsers[array_rand($allUsers)];
            
            // Avoid sending message to self
            while ($recipient->user_id === $sender->user_id) {
                $recipient = $allUsers[array_rand($allUsers)];
            }
            
            Message::create([
                'sender_id' => $sender->user_id,
                'recipient_id' => $recipient->user_id,
                'title' => $faker->sentence,
                'body' => $faker->paragraph,
                'type' => $messageTypes[array_rand($messageTypes)],
                'is_read' => $faker->boolean(70), // 70% chance of being read
                'sent_at' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
        }
        
        // Create system messages
        $this->command->info('Creating system messages for all users...');
        foreach ($allUsers as $user) {
            Message::create([
                'sender_id' => null, // System message
                'recipient_id' => $user->user_id,
                'title' => 'Welcome to SmartRest',
                'body' => $faker->paragraph,
                'type' => 'notification',
                'is_read' => $faker->boolean(50),
                'sent_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);
        }
        
        // Create 100 system logs
        $this->command->info('Creating 100 system logs...');
        $severityLevels = ['info', 'warning', 'error', 'critical'];
        $bedIds = array_map(function($i) {
            return 'BED-' . str_pad($i, 5, '0', STR_PAD_LEFT);
        }, range(1, 20));
        
        for ($i = 0; $i < 100; $i++) {
            SystemLog::create([
                'bed_id' => $faker->randomElement($bedIds),
                'severity' => $severityLevels[array_rand($severityLevels)],
                'message' => $faker->sentence,
                'logged_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);
        }
        
        $this->command->info('🎉 Enhanced seeding completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Admin Users: ' . (count($admins) + 1));
        $this->command->info('   - Doctors: ' . count($doctors));
        $this->command->info('   - Patients: ' . count($patients));
        $this->command->info('   - Customers: ' . count($customers));
        $this->command->info('   - Products: ' . Product::count());
        $this->command->info('   - Sensor Readings: ' . SensorReading::count());
        $this->command->info('   - Messages: ' . Message::count());
        $this->command->info('   - System Logs: ' . SystemLog::count());
    }
}
