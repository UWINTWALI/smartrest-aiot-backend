Collecting workspace information# SmartRest Database Seeders Explanation

Your SmartRest application uses Laravel's seeding mechanism to populate your database with test data. Here's an explanation of the current seeders:

## Current Seeder Structure

1. **[DatabaseSeeder.php](d:\ALL-GITHUB\smartrest-aiot-backend\database\seeders\DatabaseSeeder.php)**: The main seeder that calls other seeders.

    - Currently calls only `SmartRestSeeder`

2. **[SmartRestSeeder.php](d:\ALL-GITHUB\smartrest-aiot-backend\database\seeders\SmartRestSeeder.php)**: Creates comprehensive test data:

    - Creates admin user
    - Creates 5 doctors with profiles
    - Creates 20 patients with profiles
    - Creates 3 customers
    - Assigns patients to doctors
    - Creates 10 products
    - Creates sensor readings for patients (recent, historical, abnormal)
    - Creates messages between users
    - Creates system logs for 15 different beds

3. **[UsersTableSeeder.php](d:\ALL-GITHUB\smartrest-aiot-backend\database\seeders\UsersTableSeeder.php)**: Creates individual users:
    - Creates 1 admin user
    - Creates 1 doctor with profile
    - Creates 1 patient with profile
    - Creates 1 customer

## Enhanced Seeder for 100 Records

Here's an updated `UsersTableSeeder` that will create 100 records for each entity with the password `P@ssw0rd!`:

```php
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
        $superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@smartrest.com',
            'phone' => '+250700000000',
            'password' => $standardPassword,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create 10 admin users
        $this->command->info('Creating 10 admin users...');
        $admins = [];
        for ($i = 1; $i <= 10; $i++) {
            $admins[] = User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "admin{$i}@smartrest.com",
                'phone' => "+25070{$i}" . str_pad($i, 6, '0', STR_PAD_LEFT),
                'password' => $standardPassword,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        // Create 30 doctors
        $this->command->info('Creating 30 doctors with profiles...');
        $doctors = [];
        $doctorProfiles = [];
        $specialties = ['Cardiology', 'Neurology', 'Pulmonology', 'Orthopedics', 'Pediatrics', 'Oncology', 'Dermatology', 'Psychiatry', 'Radiology', 'Urology'];

        for ($i = 1; $i <= 30; $i++) {
            $doctor = User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "doctor{$i}@smartrest.com",
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
                'years_experience' => $faker->numberBetween(1, 30),
                'bio' => $faker->paragraph,
                'created_at' => now(),
            ]);

            $doctorProfiles[] = $doctorProfile;
        }

        // Create 50 patients
        $this->command->info('Creating 50 patients with profiles...');
        $patients = [];
        $patientProfiles = [];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        for ($i = 1; $i <= 50; $i++) {
            $patient = User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "patient{$i}@smartrest.com",
                'phone' => "+25072{$i}" . str_pad($i, 6, '0', STR_PAD_LEFT),
                'password' => $standardPassword,
                'role' => 'patient',
                'email_verified_at' => now(),
            ]);

            $patients[] = $patient;

            $patientProfile = PatientProfile::create([
                'patient_id' => $patient->user_id,
                'national_id' => $faker->numerify('##############'),
                'date_of_birth' => $faker->date('Y-m-d', '-80 years'),
                'sex' => $faker->randomElement(['M', 'F']),
                'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                'emergency_contact' => $faker->phoneNumber,
                'medical_conditions' => $faker->optional(0.7)->paragraph,
                'allergies' => $faker->optional(0.5)->sentence,
                'created_at' => now(),
            ]);

            $patientProfiles[] = $patientProfile;
        }

        // Create 20 customers
        $this->command->info('Creating 20 customers...');
        $customers = [];
        for ($i = 1; $i <= 20; $i++) {
            $customer = User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "customer{$i}@smartrest.com",
                'phone' => "+25073{$i}" . str_pad($i, 6, '0', STR_PAD_LEFT),
                'password' => $standardPassword,
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);

            $customers[] = $customer;
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
                \DB::table('doctor_patients')->insert([
                    'doctor_id' => $doctorProfiles[$index]->doctor_id,
                    'patient_id' => $profile->patient_id,
                    'assigned_at' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
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
                'category' => $categories[array_rand($categories)],
                'sku' => "SKU-" . str_pad($i, 6, '0', STR_PAD_LEFT),
                'price' => $faker->randomFloat(2, 50, 5000),
                'stock_quantity' => $faker->numberBetween(0, 500),
                'created_at' => now(),
            ]);

            $products[] = $product;
        }

        // Create sensor readings (100 for each patient)
        $this->command->info('Creating sensor readings for each patient (100 each)...');
        foreach ($patientProfiles as $profile) {
            // Create recent readings (last 24 hours)
            for ($i = 0; $i < 20; $i++) {
                SensorReading::create([
                    'patient_id' => $profile->patient_id,
                    'sensor_type' => $faker->randomElement(['heart_rate', 'blood_pressure', 'temperature', 'oxygen', 'glucose']),
                    'value' => $faker->randomFloat(2, 60, 180), // Using a wide range for different sensor types
                    'unit' => $faker->randomElement(['bpm', 'mmHg', '°C', '%', 'mg/dL']),
                    'timestamp' => $faker->dateTimeBetween('-24 hours', 'now'),
                    'status' => $faker->randomElement(['normal', 'warning', 'critical']),
                    'created_at' => now(),
                ]);
            }

            // Create historical readings
            for ($i = 0; $i < 80; $i++) {
                SensorReading::create([
                    'patient_id' => $profile->patient_id,
                    'sensor_type' => $faker->randomElement(['heart_rate', 'blood_pressure', 'temperature', 'oxygen', 'glucose']),
                    'value' => $faker->randomFloat(2, 60, 180),
                    'unit' => $faker->randomElement(['bpm', 'mmHg', '°C', '%', 'mg/dL']),
                    'timestamp' => $faker->dateTimeBetween('-1 year', '-24 hours'),
                    'status' => $faker->randomElement(['normal', 'warning', 'critical']),
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
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
                'subject' => $faker->sentence,
                'content' => $faker->paragraph,
                'type' => $messageTypes[array_rand($messageTypes)],
                'is_read' => $faker->boolean(70), // 70% chance of being read
                'sent_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'created_at' => now(),
            ]);
        }

        // Create system messages
        $this->command->info('Creating system messages for all users...');
        foreach ($allUsers as $user) {
            Message::create([
                'sender_id' => null, // System message
                'recipient_id' => $user->user_id,
                'subject' => 'Welcome to SmartRest',
                'content' => $faker->paragraph,
                'type' => 'notification',
                'is_read' => $faker->boolean(50),
                'sent_at' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
            ]);
        }

        // Create 100 system logs
        $this->command->info('Creating 100 system logs...');
        $logLevels = ['info', 'warning', 'error', 'critical'];
        $bedIds = array_map(function($i) {
            return 'BED-' . str_pad($i, 5, '0', STR_PAD_LEFT);
        }, range(1, 20));

        for ($i = 0; $i < 100; $i++) {
            SystemLog::create([
                'device_id' => $faker->randomElement($bedIds),
                'event_type' => $faker->randomElement(['startup', 'shutdown', 'error', 'connection', 'data', 'calibration']),
                'level' => $logLevels[array_rand($logLevels)],
                'message' => $faker->sentence,
                'details' => $faker->optional(0.7)->paragraph,
                'timestamp' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
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
```

Now update your [DatabaseSeeder.php](d:\ALL-GITHUB\smartrest-aiot-backend\database\seeders\DatabaseSeeder.php) to use the enhanced UsersTableSeeder:

```php
<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class, // Use the enhanced seeder
        ]);
    }
}
```

To run this seeder:

```sh
php artisan db:seed
```

This will create 100+ records across all your main tables with `P@ssw0rd!` as the standard password for all users.

Similar code found with 2 license types
