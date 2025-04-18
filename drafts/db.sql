-- Enable UUID extension if not already enabled
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- User roles ENUM
CREATE TYPE user_role AS ENUM ('patient', 'doctor', 'customer', 'admin');

-- 1. Users Table (Base table for all accounts)
CREATE TABLE users (
    user_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email VARCHAR(80) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role user_role NOT NULL,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    phone VARCHAR(20),
    is_email_verified BOOLEAN NOT NULL DEFAULT false,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 2. Patient Profiles (Extends users)
CREATE TABLE patient_profiles (
    patient_id UUID PRIMARY KEY REFERENCES users(user_id) ON DELETE CASCADE,
    national_id CHAR(16) UNIQUE,
    date_of_birth DATE,
    sex CHAR(1) CHECK (sex IN ('M', 'F', 'O')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 3. Doctor Profiles (Extends users)
CREATE TABLE doctor_profiles (
    doctor_id UUID PRIMARY KEY REFERENCES users(user_id) ON DELETE CASCADE,
    license_no VARCHAR(40),
    specialty VARCHAR(60),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 4. Doctor-Patient Relationship (Many-to-Many)
CREATE TABLE doctor_patients (
    doctor_id UUID NOT NULL REFERENCES doctor_profiles(doctor_id) ON DELETE CASCADE,
    patient_id UUID NOT NULL REFERENCES patient_profiles(patient_id) ON DELETE CASCADE,
    assigned_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY (doctor_id, patient_id)
);

-- 5. Products Table
CREATE TABLE products (
    product_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description TEXT,
    image_url TEXT,
    firmware_version VARCHAR(20),
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Sensor types ENUM
CREATE TYPE sensor_type AS ENUM (
    'pressure',
    'heart_rate',
    'breathing_rate',
    'temperature',
    'humidity',
    'body_movement',
    'posture',
    'vibration',
    'sleep_apnea'
);

-- 6. Sensor Readings (IoT Data)
CREATE TABLE sensor_readings (
    reading_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    patient_id UUID NOT NULL REFERENCES patient_profiles(patient_id) ON DELETE CASCADE,
    bed_id VARCHAR(64) NOT NULL,
    sensor_type sensor_type NOT NULL,
    sensor_value FLOAT NOT NULL,
    sensor_unit VARCHAR(20),
    timestamp TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    additional_metadata JSONB,
CONSTRAINT valid_pressure CHECK (
    (sensor_type = 'pressure' AND sensor_value >= 0 AND sensor_value <= 100) OR
    (sensor_type != 'pressure')
)
);

-- 7. Messages (Chat + Alerts)
CREATE TABLE messages (
    message_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    sender_id UUID NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    recipient_id UUID NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    title TEXT,
    body TEXT NOT NULL,
    type VARCHAR(24) NOT NULL CHECK (type IN ('alert', 'chat', 'promo')),
    is_read BOOLEAN NOT NULL DEFAULT false,
    sent_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 8. System Logs
CREATE TABLE system_logs (
    log_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    bed_id VARCHAR(64) NOT NULL,
    severity VARCHAR(10) CHECK (severity IN ('DEBUG', 'INFO', 'WARN', 'ERROR', 'CRITICAL')),
    message TEXT NOT NULL,
    logged_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Indexes for performance
CREATE INDEX idx_sensor_readings_patient ON sensor_readings(patient_id);
CREATE INDEX idx_sensor_readings_timestamp ON sensor_readings(timestamp);
CREATE INDEX idx_sensor_readings_bed ON sensor_readings(bed_id);
CREATE INDEX idx_messages_recipient ON messages(recipient_id);
CREATE INDEX idx_messages_sent ON messages(sent_at);
CREATE INDEX idx_system_logs_bed ON system_logs(bed_id);

-- Update timestamp trigger
CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Apply trigger to tables with updated_at
CREATE TRIGGER update_users_timestamp
BEFORE UPDATE ON users
FOR EACH ROW EXECUTE FUNCTION update_timestamp();

CREATE TRIGGER update_products_timestamp
BEFORE UPDATE ON products
FOR EACH ROW EXECUTE FUNCTION update_timestamp();