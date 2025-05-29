# SmartRest IoT API Documentation

This document provides comprehensive details about the SmartRest IoT API endpoints, request/response formats, and TypeScript interfaces for frontend development.

## Table of Contents

1. [API Overview](#api-overview)
2. [Authentication](#authentication)
3. [User Management](#user-management)
4. [Product Catalog](#product-catalog)
5. [Sensor Data](#sensor-data)
6. [Messaging & Notifications](#messaging--notifications)
7. [Analytics & Reports](#analytics--reports)
8. [System & Device Management](#system--device-management)
9. [TypeScript Interfaces](#typescript-interfaces)

## API Overview

The API is organized into the following categories:

-   **Authentication**: User registration, login, and token management
-   **User Management**: CRUD operations for users
-   **Product Catalog**: Smart bed product information
-   **Sensor Data**: Collection and retrieval of sensor readings
-   **Messaging & Notifications**: Communication between users
-   **Analytics & Reports**: Sleep and health data analysis
-   **System & Device Management**: Smart bed status and management

All API endpoints are prefixed with `/api`.

## Authentication

### Register User

Creates a new patient or customer account.

-   **URL**: `/auth/register`
-   **Method**: `POST`
-   **Auth Required**: No

**Request Body**:

```typescript
{
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  password: string;
  password_confirmation: string;
  role: "patient" | "customer";  // Only these roles can self-register
}
```

**Response (201)**:

```typescript
{
    message: string;
    user: User;
    token: string;
}
```

### Verify Email

Verifies a user's email address using a signed URL.

-   **URL**: `/auth/verify-email/{id}/{hash}`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Response (200)**:

```typescript
{
    message: string; // "Email verified successfully" or "Email already verified"
}
```

### Login

Authenticates a user and provides an access token.

-   **URL**: `/auth/login`
-   **Method**: `POST`
-   **Auth Required**: No

**Request Body**:

```typescript
{
    email: string;
    password: string;
}
```

**Response (200)**:

```typescript
{
    message: string; // "Login successful"
    user: User;
    token: string;
}
```

### Logout

Invalidates the user's current access token.

-   **URL**: `/auth/logout`
-   **Method**: `POST`
-   **Auth Required**: Yes

**Response (200)**:

```typescript
{
    message: string; // "Logout successful"
}
```

### Get Current User Profile

Returns the authenticated user's profile with role-specific information.

-   **URL**: `/auth/me`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Response (200)**:

```typescript
User; // Full user object with appropriate profile data
```

### Refresh Token

Refreshes an authentication token.

-   **URL**: `/auth/refresh`
-   **Method**: `POST`
-   **Auth Required**: Yes

**Response (200)**:

```typescript
{
    token: string;
}
```

### Forgot Password

Initiates password reset process by sending an email.

-   **URL**: `/auth/forgot-password`
-   **Method**: `POST`
-   **Auth Required**: No

**Request Body**:

```typescript
{
    email: string;
}
```

**Response (200)**:

```typescript
{
    message: string; // "Password reset link sent"
}
```

### Reset Password

Resets a user's password with a valid token.

-   **URL**: `/auth/reset-password`
-   **Method**: `POST`
-   **Auth Required**: No

**Request Body**:

```typescript
{
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
}
```

**Response (200)**:

```typescript
{
    message: string; // "Password has been reset"
}
```

### Change Password

Changes the authenticated user's password.

-   **URL**: `/auth/change-password`
-   **Method**: `POST`
-   **Auth Required**: Yes

**Request Body**:

```typescript
{
    current_password: string;
    password: string;
    password_confirmation: string;
}
```

**Response (200)**:

```typescript
{
    message: string; // "Password changed successfully"
}
```

### Social Login

OAuth authentication with third-party providers. (Not fully implemented)

-   **URL**: `/auth/social-login`
-   **Method**: `POST`
-   **Auth Required**: No

**Response (501)**:

```typescript
{
    message: string; // "Social login is not implemented yet"
}
```

## User Management

### List Users

Returns a paginated list of users. Admin-only endpoint.

-   **URL**: `/users`
-   **Method**: `GET`
-   **Auth Required**: Yes (Admin)

**Query Parameters**:

-   `role`: Filter by user role (`patient`, `doctor`, `admin`, `customer`)
-   `search`: Search in name or email
-   `page`: Page number for pagination

**Response (200)**:

```typescript
{
  data: User[];
  current_page: number;
  total: number;
}
```

### Get User

Returns details for a specific user.

-   **URL**: `/users/{userId}`
-   **Method**: `GET`
-   **Auth Required**: Yes (Admin or self)

**Response (200)**:

```typescript
User; // Full user object with appropriate profile
```

### Create User

Creates a new user account. Admin-only endpoint.

-   **URL**: `/users`
-   **Method**: `POST`
-   **Auth Required**: Yes (Admin)

**Request Body**:

```typescript
{
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  password: string;
  role: "patient" | "doctor" | "customer" | "admin";
  // Role-specific fields
  license_no?: string;  // required for doctors
  specialty?: string;  // optional for doctors
  national_id?: string;  // optional for patients
  date_of_birth?: string;  // optional for patients, format YYYY-MM-DD
  sex?: "M" | "F" | "O";  // optional for patients
}
```

**Response (201)**:

```typescript
{
    message: string; // "User created successfully"
    user: User;
}
```

### Update User

Updates a user profile. Admin or self access only.

-   **URL**: `/users/{userId}`
-   **Method**: `PUT`
-   **Auth Required**: Yes (Admin or self)

**Request Body**:

```typescript
{
  first_name?: string;
  last_name?: string;
  email?: string;
  phone?: string;
  role?: "patient" | "doctor" | "customer" | "admin";  // Admin only
  // Role-specific fields
  license_no?: string;
  specialty?: string;
  national_id?: string;
  date_of_birth?: string;
  sex?: "M" | "F" | "O";
}
```

**Response (200)**:

```typescript
{
    message: string; // "User updated successfully"
    user: User;
}
```

### Delete User

Removes a user account. Admin-only endpoint.

-   **URL**: `/users/{userId}`
-   **Method**: `DELETE`
-   **Auth Required**: Yes (Admin)

**Response (200)**:

```typescript
{
    message: string; // "User deleted successfully"
}
```

## Product Catalog

### List Products

Returns a paginated list of products.

-   **URL**: `/products`
-   **Method**: `GET`
-   **Auth Required**: No

**Query Parameters**:

-   `page`: Page number for pagination

**Response (200)**:

```typescript
{
  data: Product[];
  current_page: number;
  total: number;
  per_page: number;
}
```

### Get Product

Returns details for a specific product.

-   **URL**: `/products/{productId}`
-   **Method**: `GET`
-   **Auth Required**: No

**Response (200)**:

```typescript
Product;
```

### Create Product

Adds a new product. Admin-only endpoint.

-   **URL**: `/products`
-   **Method**: `POST`
-   **Auth Required**: Yes (Admin)

**Request Body**:

```typescript
{
  name: string;
  description?: string;
  image_url?: string;
  firmware_version?: string;
  is_active?: boolean;
}
```

**Response (201)**:

```typescript
Product;
```

### Update Product

Updates a product. Admin-only endpoint.

-   **URL**: `/products/{productId}`
-   **Method**: `PUT`
-   **Auth Required**: Yes (Admin)

**Request Body**:

```typescript
{
  name?: string;
  description?: string;
  image_url?: string;
  firmware_version?: string;
  is_active?: boolean;
}
```

**Response (200)**:

```typescript
Product;
```

### Delete Product

Removes a product. Admin-only endpoint.

-   **URL**: `/products/{productId}`
-   **Method**: `DELETE`
-   **Auth Required**: Yes (Admin)

**Response (204)**:
No content

## Sensor Data

### Store Sensor Readings

Uploads batched sensor readings from smart bed.

-   **URL**: `/sensors/data`
-   **Method**: `POST`
-   **Auth Required**: No (uses device token)

**Request Body**:

```typescript
{
  patient_id: string;
  bed_id: string;
  readings: {
    sensor_type: "pressure" | "heart_rate" | "breathing_rate" | "temperature" |
                 "humidity" | "body_movement" | "posture" | "vibration" | "sleep_apnea";
    sensor_value: number;
    sensor_unit?: string;
    timestamp?: string;  // ISO format
    additional_metadata?: object;
  }[];
}
```

**Response (201)**:

```typescript
{
    message: string; // "Sensor data stored successfully"
    readings_count: number;
}
```

### Get Latest Sensor Readings

Returns the most recent readings for each sensor type for a patient.

-   **URL**: `/sensors/latest`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Query Parameters**:

-   `patient_id`: Patient ID (required for doctors, not for patients)

**Response (200)**:

```typescript
{
  patient_id: string;
  timestamp: string;  // ISO format
  readings: {
    heart_rate?: {
      value: number;
      unit: string;
      timestamp: string;  // ISO format
    };
    breathing_rate?: {
      value: number;
      unit: string;
      timestamp: string;  // ISO format
    };
    temperature?: {
      value: number;
      unit: string;
      timestamp: string;  // ISO format
    };
    // Other sensor types may also be included
  };
}
```

### Get Historical Sensor Data

Returns historical sensor readings with filtering options.

-   **URL**: `/sensors/history`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Query Parameters**:

-   `patient_id`: Patient ID (required for doctors, not for patients)
-   `type`: Filter by sensor type
-   `from`: Start date (YYYY-MM-DD)
-   `to`: End date (YYYY-MM-DD)
-   `limit`: Maximum number of readings to return

**Response (200)**:

```typescript
{
  patient_id: string;
  readings: {
    sensor_type: string;
    sensor_value: number;
    sensor_unit: string;
    timestamp: string;  // ISO format
    additional_metadata?: object;
  }[];
  period: {
    start: string;  // ISO format
    end: string;    // ISO format
  };
}
```

## Messaging & Notifications

### Send Message

Send a message to another user.

-   **URL**: `/messages`
-   **Method**: `POST`
-   **Auth Required**: Yes

**Request Body**:

```typescript
{
  recipient_id: string;
  title?: string;
  body: string;
  type: "alert" | "chat" | "promo";
}
```

**Response (201)**:

```typescript
{
    message: string; // "Message sent successfully"
    data: Message;
}
```

### Get Conversation Thread

Retrieve message thread between the current user and another user.

-   **URL**: `/messages/{conversationId}`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Response (200)**:

```typescript
{
  conversation_with: string;  // User ID
  messages: {
    message_id: string;
    sender_id: string;
    recipient_id: string;
    title?: string;
    body: string;
    type: "alert" | "chat" | "promo";
    is_read: boolean;
    sent_at: string;  // ISO format
  }[];
}
```

### Get Notifications

Retrieve unread notifications for the current user.

-   **URL**: `/notifications`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Response (200)**:

```typescript
{
  notifications: {
    message_id: string;
    sender_id: string;
    title?: string;
    body: string;
    type: "alert" | "chat" | "promo";
    sent_at: string;  // ISO format
  }[];
  unread_count: number;
}
```

### Acknowledge Notification

Mark a specific notification as read.

-   **URL**: `/notifications/{id}/acknowledge`
-   **Method**: `POST`
-   **Auth Required**: Yes

**Response (200)**:

```typescript
{
    message: string; // "Notification acknowledged"
}
```

## Analytics & Reports

### Get Sleep Report

Retrieve AI-generated sleep report for a specific date.

-   **URL**: `/analytics/sleep-report`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Query Parameters**:

-   `date`: Date for the report (YYYY-MM-DD)
-   `patient_id`: Patient ID (required for doctors/admins, not for patients)

**Response (200)**:

```typescript
{
  patient_id: string;
  date: string;  // YYYY-MM-DD
  sleep_duration: {
    total_hours: number;
    minutes: number;
  };
  sleep_stages: {
    awake: number;  // minutes
    light: number;  // minutes
    deep: number;   // minutes
    rem: number;    // minutes
  };
  sleep_score: number;
  posture_changes: number;
  breathing_events: number;
  avg_heart_rate: number;
  notes?: string;
}
```

### Get Health Summary

Retrieve health data summary for a specified time period.

-   **URL**: `/analytics/health-summary`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Query Parameters**:

-   `patient_id`: Patient ID (required for doctors/admins, not for patients)
-   `days`: Number of days to analyze (1-90, default: 7)

**Response (200)**:

```typescript
{
  patient_id: string;
  period: {
    start_date: string;  // YYYY-MM-DD
    end_date: string;    // YYYY-MM-DD
  };
  vital_trends: {
    heart_rate: {
      avg: number;
      min: number;
      max: number;
      trend: string;  // "improving", "declining", "stable"
    };
    breathing_rate: {
      avg: number;
      min: number;
      max: number;
      trend: string;
    };
    // Other vital signs may be included
  };
  sleep_quality: {
    avg_score: number;
    trend: string;
  };
  anomalies: {
    type: string;
    description: string;
    detected_at: string;  // ISO format
    severity: "low" | "medium" | "high";
  }[];
  recommendations: string[];
}
```

## System & Device Management

### Get System Status

Retrieve system status for a specific smart bed.

-   **URL**: `/system/status`
-   **Method**: `GET`
-   **Auth Required**: Yes

**Query Parameters**:

-   `bed_id`: Bed identifier

**Response (200)**:

```typescript
{
    bed_id: string;
    status: string; // "online", "offline", "maintenance"
    uptime: string; // e.g. "3d 7h 22m"
    firmware: {
        version: string;
        last_updated: string; // ISO format
    }
    battery: {
        level: number; // percentage
        estimated_remaining: string; // e.g. "4d 12h"
    }
    sensors: {
        type: string;
        status: string; // "ok", "warning", "error"
        last_reading: string; // ISO format
    }
    [];
    connectivity: {
        type: string; // "wifi", "cellular", "bluetooth"
        strength: string; // "excellent", "good", "fair", "poor"
        last_sync: string; // ISO format
    }
    recent_logs: {
        severity: string; // "INFO", "WARNING", "ERROR"
        message: string;
        logged_at: string; // ISO format
    }
    [];
}
```

### Reboot System

Trigger a remote restart of a smart bed system.

-   **URL**: `/system/reboot`
-   **Method**: `POST`
-   **Auth Required**: Yes (Admin only)

**Request Body**:

```typescript
{
    bed_id: string;
}
```

**Response (200)**:

```typescript
{
    message: string; // "Reboot command sent successfully"
    bed_id: string;
    reboot_initiated_at: string; // ISO format
    estimated_completion: string; // ISO format
}
```

## TypeScript Interfaces

### User

```typescript
interface User {
    user_id: string;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string;
    role: "patient" | "doctor" | "customer" | "admin";
    is_email_verified: boolean;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
    patientProfile?: PatientProfile;
    doctorProfile?: DoctorProfile;
}
```

### PatientProfile

```typescript
interface PatientProfile {
    patient_id: string;
    national_id?: string;
    date_of_birth?: string;
    sex?: "M" | "F" | "O";
    emergency_contact_name?: string;
    emergency_contact_phone?: string;
    health_conditions?: string;
    medications?: string;
}
```

### DoctorProfile

```typescript
interface DoctorProfile {
    doctor_id: string;
    license_no: string;
    specialty?: string;
    institution?: string;
    years_experience?: number;
}
```

### Product

```typescript
interface Product {
    product_id: string;
    name: string;
    description?: string;
    image_url?: string;
    firmware_version?: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}
```

### SensorReading

```typescript
interface SensorReading {
    reading_id: string;
    patient_id: string;
    bed_id: string;
    sensor_type:
        | "pressure"
        | "heart_rate"
        | "breathing_rate"
        | "temperature"
        | "humidity"
        | "body_movement"
        | "posture"
        | "vibration"
        | "sleep_apnea";
    sensor_value: number;
    sensor_unit?: string;
    timestamp: string;
    additional_metadata?: any;
}
```

### Message

```typescript
interface Message {
    message_id: string;
    sender_id: string;
    recipient_id: string;
    title?: string;
    body: string;
    type: "alert" | "chat" | "promo";
    is_read: boolean;
    sent_at: string;
}
```

### SystemLog

```typescript
interface SystemLog {
    log_id: string;
    bed_id: string;
    severity: string;
    message: string;
    logged_at: string;
}
```

---

This documentation provides a comprehensive overview of the SmartRest IoT API endpoints, including request/response formats and TypeScript interfaces. Frontend developers can use this to integrate with the backend services.
