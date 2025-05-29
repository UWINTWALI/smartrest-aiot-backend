# SmartRest IoT Backend TypeScript Interfaces

This document provides TypeScript interfaces for the SmartRest IoT backend data models. These interfaces reflect the database structure and relationships between entities in the system.

## Core Types and Enums

```typescript
// Basic user roles in the system
export enum UserRole {
    PATIENT = "patient",
    DOCTOR = "doctor",
    CUSTOMER = "customer",
    ADMIN = "admin",
}

// Sensor types for smart bed readings
export enum SensorType {
    HEART_RATE = "heart_rate",
    PRESSURE = "pressure",
    TEMPERATURE = "temperature",
    HUMIDITY = "humidity",
    MOVEMENT = "movement",
    SLEEP_QUALITY = "sleep_quality",
}

// Biological sex options
export enum Sex {
    MALE = "M",
    FEMALE = "F",
    OTHER = "O",
}

// Message type classification
export enum MessageType {
    NOTIFICATION = "notification",
    ALERT = "alert",
    GENERAL = "general",
    MEDICAL = "medical",
    SYSTEM = "system",
}

// System log severity levels
export enum LogSeverity {
    DEBUG = "debug",
    INFO = "info",
    WARNING = "warning",
    ERROR = "error",
    CRITICAL = "critical",
}
```

## Core Interfaces

### User

```typescript
export interface User {
    user_id: string; // UUID
    first_name: string;
    last_name: string;
    email: string;
    phone?: string; // Optional
    role: UserRole;
    is_email_verified: boolean;
    email_verified_at?: string; // ISO date string
    created_at: string; // ISO date string
    updated_at: string; // ISO date string

    // Related profiles based on user role
    patientProfile?: PatientProfile; // Only exists if role is 'patient'
    doctorProfile?: DoctorProfile; // Only exists if role is 'doctor'
}
```

### PatientProfile

```typescript
export interface PatientProfile {
    patient_id: string; // UUID, same as user_id
    national_id?: string; // Optional
    date_of_birth?: string; // ISO date string, YYYY-MM-DD
    sex?: Sex; // M, F, or O
    emergency_contact_name?: string;
    emergency_contact_phone?: string;
    health_conditions?: string;
    medications?: string;

    // Relations
    user?: User; // The user this profile belongs to
    doctors?: DoctorPatient[]; // Assigned doctors with relationship metadata
}
```

### DoctorProfile

```typescript
export interface DoctorProfile {
    doctor_id: string; // UUID, same as user_id
    license_no: string;
    specialty?: string;
    institution?: string;
    years_experience?: number;

    // Relations
    user?: User; // The user this profile belongs to
    patients?: DoctorPatient[]; // Assigned patients with relationship metadata
}
```

### DoctorPatient Relationship

```typescript
export interface DoctorPatient {
    doctor_id: string; // UUID
    patient_id: string; // UUID
    assigned_at: string; // ISO date string

    // These can be populated when needed
    doctor?: DoctorProfile;
    patient?: PatientProfile;
}
```

### SensorReading

```typescript
export interface SensorReading {
    reading_id: string; // UUID
    patient_id: string; // UUID
    bed_id: string;
    sensor_type: SensorType;
    sensor_value: number;
    sensor_unit?: string;
    timestamp: string; // ISO datetime string
    additional_metadata?: Record<string, any>;
    notes?: string;

    // Relations
    patient?: PatientProfile;
}
```

### Message

```typescript
export interface Message {
    message_id: string; // UUID
    sender_id: string; // UUID
    recipient_id: string; // UUID
    title?: string;
    body: string;
    type?: MessageType;
    is_read: boolean;
    sent_at: string; // ISO datetime string

    // Relations
    sender?: User;
    recipient?: User;
}
```

### Product

```typescript
export interface Product {
    product_id: string; // UUID
    name: string;
    description?: string;
    image_url?: string;
    firmware_version?: string;
    is_active: boolean;
    created_at: string; // ISO datetime string
    updated_at: string; // ISO datetime string
}
```

### SystemLog

```typescript
export interface SystemLog {
    log_id: string; // UUID
    bed_id?: string;
    severity: LogSeverity;
    message: string;
    logged_at: string; // ISO datetime string
}
```

## Relationships Explanation

1. **User & Profiles**:

    - A User can have either a PatientProfile or a DoctorProfile based on their role
    - The user_id maps directly to patient_id or doctor_id in their respective profiles
    - These are one-to-one relationships

2. **Doctor-Patient Relationship**:

    - Doctors and Patients have a many-to-many relationship
    - The DoctorPatient interface represents this relationship with metadata like assigned_at
    - A doctor can have multiple patients, and a patient can have multiple doctors

3. **Sensor Readings**:

    - Each SensorReading belongs to a specific PatientProfile
    - Readings are associated with a specific bed_id
    - Multiple readings can exist for a single patient
    - Readings are categorized by sensor_type

4. **Messages**:

    - Messages connect two Users - a sender and a recipient
    - Both sender and recipient are references to User entities
    - This allows communication between any types of users in the system

5. **Products**:

    - Products represent the smart bed devices in the system
    - They include device information like firmware version
    - Products are independent entities that can be associated with patients or readings through other mechanisms

6. **System Logs**:
    - SystemLogs capture system events and errors
    - They can be optionally associated with a specific bed_id
    - They provide monitoring and troubleshooting capabilities

## Usage Notes

1. All relationships should be populated based on need
2. UUIDs are used as primary keys throughout the system
3. ISO date strings should be used for all date/time fields
4. Optional fields are marked with the `?` operator
5. Enum types should be used for fields with a defined set of possible values
6. Frontend should handle conditional display based on user role and access rights
