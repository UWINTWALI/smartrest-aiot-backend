# SmartRest API - Database Factories Documentation

## Why We Need Factories

Database factories in Laravel serve several critical purposes:

### 1. **Testing**

-   Generate realistic test data for unit and feature tests
-   Create consistent data patterns for reliable testing
-   Enable testing of edge cases and various scenarios

### 2. **Development**

-   Quickly populate local databases with sample data
-   Test features with realistic data during development
-   Debug and validate relationships between models

### 3. **Database Seeding**

-   Populate staging environments with sample data
-   Create demo data for presentations and client demos
-   Ensure consistent data across different environments

### 4. **Relationship Testing**

-   Test complex model relationships easily
-   Validate foreign key constraints and data integrity
-   Create interconnected data graphs for comprehensive testing

## Available Factories

### 1. UserFactory

**Purpose**: Creates users for the SmartRest IoT application with different roles.

**Usage Examples**:

```php
// Basic user creation
$user = User::factory()->create();

// Create specific role users
$patient = User::factory()->patient()->create();
$doctor = User::factory()->doctor()->create();
$admin = User::factory()->admin()->create();
$customer = User::factory()->customer()->create();

// Create unverified user
$unverifiedUser = User::factory()->unverified()->create();

// Create multiple users
$users = User::factory()->count(10)->create();

// Create user with specific attributes
$user = User::factory()->create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
]);
```

**Generated Fields**:

-   `first_name`: Random first name
-   `last_name`: Random last name
-   `email`: Unique email address
-   `phone`: Random phone number
-   `role`: Random role (patient, doctor, customer, admin)
-   `email_verified_at`: Current timestamp
-   `password`: Hashed 'password'
-   `remember_token`: Random token

**Available States**:

-   `patient()`: Creates a patient user
-   `doctor()`: Creates a doctor user
-   `admin()`: Creates an admin user
-   `customer()`: Creates a customer user
-   `unverified()`: Creates user with null email_verified_at

### 2. PatientProfileFactory

**Purpose**: Creates patient profiles linked to users with role 'patient'.

**Usage Examples**:

```php
// Create patient profile with user
$patientProfile = PatientProfile::factory()->create();

// Create profile for existing user
$user = User::factory()->patient()->create();
$profile = PatientProfile::factory()->forUser($user)->create();

// Create specific gender profiles
$malePatient = PatientProfile::factory()->male()->create();
$femalePatient = PatientProfile::factory()->female()->create();

// Create patient with health conditions
$patient = PatientProfile::factory()
    ->withHealthConditions('Hypertension, Sleep Apnea')
    ->withMedications('Lisinopril, CPAP therapy')
    ->create();
```

**Generated Fields**:

-   `patient_id`: Links to User with UUID
-   `national_id`: Unique format (e.g., "AB123456C")
-   `date_of_birth`: Date (18+ years old)
-   `sex`: Random 'M', 'F', or 'O'
-   `emergency_contact_name`: Random name
-   `emergency_contact_phone`: Random phone
-   `health_conditions`: Optional sentence
-   `medications`: Optional sentence

**Available States**:

-   `forUser(User $user)`: Links to specific user
-   `male()`: Sets sex to 'M'
-   `female()`: Sets sex to 'F'
-   `withHealthConditions(string)`: Sets specific conditions
-   `withMedications(string)`: Sets specific medications

### 3. DoctorProfileFactory

**Purpose**: Creates doctor profiles linked to users with role 'doctor'.

**Usage Examples**:

```php
// Create doctor profile with user
$doctorProfile = DoctorProfile::factory()->create();

// Create profile for existing user
$user = User::factory()->doctor()->create();
$profile = DoctorProfile::factory()->forUser($user)->create();

// Create specialists
$sleepSpecialist = DoctorProfile::factory()->sleepSpecialist()->create();
$cardiologist = DoctorProfile::factory()->specialty('Cardiology')->create();

// Create by experience level
$seniorDoctor = DoctorProfile::factory()->experienced()->create();
$juniorDoctor = DoctorProfile::factory()->junior()->create();
```

**Generated Fields**:

-   `doctor_id`: Links to User with UUID
-   `license_no`: Format "MED123456"
-   `specialty`: Medical specialty (Sleep Medicine, Cardiology, etc.)
-   `institution`: Healthcare institution name
-   `years_experience`: 1-40 years

**Available States**:

-   `forUser(User $user)`: Links to specific user
-   `specialty(string)`: Sets specific specialty
-   `sleepSpecialist()`: Sets Sleep Medicine specialty
-   `experienced()`: 15-40 years experience
-   `junior()`: 1-5 years experience

### 4. SensorReadingFactory

**Purpose**: Creates IoT sensor readings from smart beds.

**Usage Examples**:

```php
// Create sensor reading
$reading = SensorReading::factory()->create();

// Create for specific patient
$patient = PatientProfile::factory()->create();
$reading = SensorReading::factory()->forPatient($patient)->create();

// Create for specific bed
$reading = SensorReading::factory()->forBed('BED-12345')->create();

// Create specific sensor types
$humidityReading = SensorReading::factory()->humidity()->create();
$movementReading = SensorReading::factory()->movement()->create();
$apneaReading = SensorReading::factory()->sleepApnea()->create();

// Create recent readings
$recentReadings = SensorReading::factory()->recent()->count(10)->create();

// Create abnormal readings for alerts
$alertReadings = SensorReading::factory()->abnormal()->count(5)->create();
```

**Generated Fields**:

-   `patient_id`: Links to PatientProfile
-   `bed_id`: Format "BED-12345"
-   `sensor_type`: humidity, body_movement, posture, vibration, sleep_apnea
-   `sensor_value`: Realistic values based on sensor type
-   `sensor_unit`: Appropriate unit (%, units/min, degrees, Hz, events/hour)
-   `timestamp`: Random timestamp within last month
-   `additional_metadata`: JSON with device info and calibration data

**Available States**:

-   `forPatient(PatientProfile)`: Links to specific patient
-   `forBed(string)`: Links to specific bed
-   `humidity()`: Creates humidity sensor readings
-   `movement()`: Creates movement sensor readings
-   `sleepApnea()`: Creates sleep apnea readings
-   `recent()`: Creates readings from last 24 hours
-   `abnormal()`: Creates readings that would trigger alerts

### 5. MessageFactory

**Purpose**: Creates messages between users and system notifications.

**Usage Examples**:

```php
// Create message between users
$sender = User::factory()->doctor()->create();
$recipient = User::factory()->patient()->create();
$message = Message::factory()->between($sender, $recipient)->create();

// Create specific message types
$alert = Message::factory()->alert()->create();
$emergency = Message::factory()->emergency()->unread()->create();
$reminder = Message::factory()->reminder()->create();
$notification = Message::factory()->notification()->create();

// Create system messages
$systemMessage = Message::factory()->fromSystem()->create([
    'recipient_id' => $user->user_id
]);

// Create recent messages
$recentMessages = Message::factory()->recent()->count(5)->create();
```

**Generated Fields**:

-   `sender_id`: Links to User (nullable for system messages)
-   `recipient_id`: Links to User
-   `title`: Contextual title based on message type
-   `body`: Detailed message content based on type
-   `type`: info, alert, reminder, notification, emergency
-   `is_read`: Boolean (30% chance of being read)
-   `sent_at`: Random timestamp within last month

**Available States**:

-   `between(User $sender, User $recipient)`: Creates message between specific users
-   `unread()`: Sets is_read to false
-   `read()`: Sets is_read to true
-   `alert()`: Creates alert-type message
-   `emergency()`: Creates emergency message (defaults to unread)
-   `reminder()`: Creates reminder message
-   `notification()`: Creates notification message
-   `recent()`: Creates message from last 24 hours
-   `fromSystem()`: Creates system message (sender_id = null)

### 6. SystemLogFactory

**Purpose**: Creates system logs for monitoring bed devices and operations.

**Usage Examples**:

```php
// Create system log
$log = SystemLog::factory()->create();

// Create for specific bed
$log = SystemLog::factory()->forBed('BED-12345')->create();

// Create by severity level
$infoLog = SystemLog::factory()->info()->create();
$warningLog = SystemLog::factory()->warning()->create();
$errorLog = SystemLog::factory()->error()->create();
$criticalLog = SystemLog::factory()->critical()->create();

// Create recent logs
$recentLogs = SystemLog::factory()->recent()->count(10)->create();

// Create themed logs
$sensorLogs = SystemLog::factory()->sensorRelated()->count(5)->create();
$maintenanceLogs = SystemLog::factory()->maintenance()->count(3)->create();
$connectivityLogs = SystemLog::factory()->connectivity()->count(4)->create();
```

**Generated Fields**:

-   `bed_id`: Format "BED-12345"
-   `severity`: info, warning, error, critical
-   `message`: Contextual message based on severity level
-   `logged_at`: Random timestamp within last month

**Available States**:

-   `forBed(string)`: Creates log for specific bed
-   `info()`: Creates info-level log
-   `warning()`: Creates warning-level log
-   `error()`: Creates error-level log
-   `critical()`: Creates critical-level log
-   `recent()`: Creates log from last 24 hours
-   `lastHour()`: Creates log from last hour
-   `sensorRelated()`: Creates sensor-related logs
-   `maintenance()`: Creates maintenance logs
-   `connectivity()`: Creates network/connectivity logs

### 7. ProductFactory

**Purpose**: Creates IoT products/devices for the system.

**Usage Examples**:

```php
// Create product
$product = Product::factory()->create();

// Create multiple products
$products = Product::factory()->count(10)->create();

// Create with specific attributes
$product = Product::factory()->create([
    'name' => 'SmartBed Pro',
    'description' => 'Advanced sleep monitoring bed',
]);
```

**Generated Fields**:

-   `name`: 3-word product name
-   `description`: Product description sentence
-   `image_url`: Fake image URL
-   `firmware_version`: Format "v1", "v2", etc.
-   `is_active`: Boolean (defaults to true)

## Model Inconsistencies Fixed

During the factory creation process, several inconsistencies were identified and resolved:

### 1. **UserFactory Outdated Fields**

-   **Issue**: Factory used `'name'` field instead of `'first_name'` and `'last_name'`
-   **Fix**: Updated to use proper field names matching the User model

### 2. **Missing Fillable Properties**

-   **Issue**: PatientProfile and DoctorProfile had incomplete fillable arrays
-   **Fix**: Added missing fields like `emergency_contact_name`, `emergency_contact_phone`, `health_conditions`, `medications`, `institution`, `years_experience`

### 3. **Database Schema Mismatch**

-   **Issue**: Model factories referenced fields that didn't exist in database
-   **Fix**: Created migration to add missing columns to profile tables

### 4. **UUID Trait Inconsistency**

-   **Issue**: Some models with UUID primary keys didn't use `HasUuids` trait
-   **Fix**: Added `HasUuids` trait to DoctorProfile and PatientProfile models

### 5. **Message Sender ID Constraint**

-   **Issue**: System messages needed null sender_id but database didn't allow it
-   **Fix**: Created migration to make `sender_id` nullable with proper foreign key handling

### 6. **Timestamp Inconsistencies**

-   **Issue**: Some models disabled timestamps but included `created_at` in fillable
-   **Fix**: Aligned fillable arrays with actual timestamp usage

## Using the Comprehensive Seeder

The `SmartRestSeeder` demonstrates best practices for using all factories together:

```bash
# Run the seeder
php artisan db:seed --class=SmartRestSeeder

# Or run via DatabaseSeeder
php artisan db:seed
```

The seeder creates:

-   1 Admin user
-   5 Doctors with profiles
-   20 Patients with profiles
-   3 Customers
-   10 Products
-   Patient-doctor assignments
-   Thousands of sensor readings
-   Hundreds of messages
-   Hundreds of system logs

## Best Practices

### 1. **Use Factories in Tests**

```php
public function test_patient_can_view_sensor_readings()
{
    $patient = User::factory()->patient()->create();
    $patientProfile = PatientProfile::factory()->forUser($patient)->create();
    $readings = SensorReading::factory()
        ->forPatient($patientProfile)
        ->count(5)
        ->create();

    // Test code here
}
```

### 2. **Create Realistic Relationships**

```php
$doctor = User::factory()->doctor()->create();
$doctorProfile = DoctorProfile::factory()->forUser($doctor)->create();

$patient = User::factory()->patient()->create();
$patientProfile = PatientProfile::factory()->forUser($patient)->create();

// Assign patient to doctor
$doctorProfile->patients()->attach($patientProfile->patient_id);
```

### 3. **Use States for Specific Scenarios**

```php
// Test emergency scenarios
$emergencyMessage = Message::factory()
    ->emergency()
    ->unread()
    ->create();

// Test abnormal sensor readings
$abnormalReading = SensorReading::factory()
    ->abnormal()
    ->sleepApnea()
    ->create();
```

This comprehensive factory system ensures that your SmartRest IoT application has robust testing capabilities and can generate realistic data for development and demonstration purposes.
