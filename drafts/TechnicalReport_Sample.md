# Technical Report: SmartRest AIoT Application

**Version:** 1.0
**Date:** YYYY-MM-DD
**Author:** GitHub Copilot (Generated Document)

## Table of Contents

1.  [Chapter 1: Introduction](#chapter-1-introduction)
    1.1. [Historical Background of the Case Study](#historical-background-of-the-case-study)
    1.2. [Problem Statement](#problem-statement)
    1.3. [Proposed Solution: SmartRest AIoT](#proposed-solution-smartrest-aiot)
    1.4. [Purpose](#purpose)
    1.5. [Objectives](#objectives)
2.  [Chapter 2: System Analysis and Design](#chapter-2-system-analysis-and-design)
    2.1. [System Analysis](#system-analysis)
    2.2. [Functional Requirements](#functional-requirements)
    2.2.1. [Authentication and Session Management](#authentication-and-session-management)
    2.2.2. [User Management (CRUD)](#user-management-crud)
    2.2.3. [Product Catalog Management](#product-catalog-management)
    2.2.4. [Sensor Data Collection and Query](#sensor-data-collection-and-query)
    2.2.5. [Messaging and Notifications](#messaging-and-notifications)
    2.2.6. [Analytics and Reports](#analytics-and-reports)
    2.2.7. [System and Device Management](#system-and-device-management)
    2.3. [Users of the Project](#users-of-the-project)
    2.4. [System Design](#system-design)
    2.4.1. [Architectural Overview](#architectural-overview)
    2.4.2. [UI Design Considerations (for consuming applications)](#ui-design-considerations-for-consuming-applications)
    2.4.3. [Database Design](#database-design)
    2.4.3.1. [Enumerated Types](#enumerated-types)
    2.4.3.2. [Table Structures and Relationships](#table-structures-and-relationships)
    2.4.3.3. [Indexes and Triggers](#indexes-and-triggers)
3.  [Chapter 3: Implementation](#chapter-3-implementation)
    3.1. [Introduction to Implementation](#introduction-to-implementation)
    3.2. [Tools and Technologies Used](#tools-and-technologies-used)
    3.3. [API Interaction Examples (Illustrative)](#api-interaction-examples-illustrative)
    3.3.1. [User Registration Example](#user-registration-example)
    3.3.2. [Fetching Latest Sensor Data Example](#fetching-latest-sensor-data-example)
4.  [Chapter 4: Conclusion and Recommendation](#chapter-4-conclusion-and-recommendation)
    4.1. [State of Implementation](#state-of-implementation)
    4.2. [Recommendations for Future Work](#recommendations-for-future-work)
5.  [Appendix](#appendix)
    5.1. [Full API Documentation (Reference)](#full-api-documentation-reference)
    5.2. [Detailed Database Schema (SQL)](#detailed-database-schema-sql)

---

## Chapter 1: Introduction

### 1.1. Historical Background of the Case Study

The healthcare and wellness industries are increasingly leveraging technology to provide personalized and proactive care. Traditional mattresses offer passive comfort, but the advent of IoT (Internet of Things) and AI (Artificial Intelligence) presents an opportunity to transform beds into intelligent health monitoring and enhancement systems. The need for continuous, non-invasive monitoring of vital signs, sleep patterns, and environmental factors has driven the development of smart bedding solutions. These solutions aim to improve sleep quality, detect potential health issues early, and provide actionable insights to users and healthcare providers. The SmartRest AIoT project emerges from this context, seeking to create a robust backend system for an intelligent mattress, as outlined in the project's `README.md`.

### 1.2. Problem Statement

Modern lifestyles often lead to sleep-related issues and an increased risk of health problems that could be mitigated with early detection and intervention. Key challenges include:

-   **Lack of Continuous Health Monitoring:** Periodic health check-ups may miss transient or nocturnal health events.
-   **Suboptimal Sleep Quality:** Many individuals suffer from poor sleep due to environmental factors, stress, or undiagnosed conditions, impacting overall well-being and productivity.
-   **Delayed Intervention for Health Issues:** Conditions like sleep apnea, irregular heartbeats, or changes in breathing patterns can go unnoticed until they become severe.
-   **Generic Comfort Solutions:** Standard mattresses do not adapt to individual physiological needs or environmental conditions in real-time.
-   **Data Silos:** Health data collected by various devices often remains isolated, preventing a holistic view of an individual's health status.

The SmartRest AIoT system aims to address these problems by providing a platform for an intelligent mattress that actively monitors and adapts to user needs.

### 1.3. Proposed Solution: SmartRest AIoT

The proposed solution is the **SmartRest AIoT Backend**, a comprehensive API-driven system built using the Laravel framework. This system serves as the central nervous system for an intelligent mattress designed to enhance sleep quality and monitor user health through a network of embedded sensors. It integrates IoT capabilities to collect data, AI for analysis and insights, and provides interfaces for mobile and web applications.

The system is designed to:

-   Collect and process data from various sensors embedded in the mattress (e.g., heart rate, breathing patterns, temperature, movement).
-   Offer features like automatic mattress temperature adjustment based on user comfort and ambient conditions.
-   Provide real-time health monitoring and alerts for anomalies.
-   Support multiple user roles, including patients in hospitals, doctors supervising them, retail customers using the mattress at home, and administrators managing the system.
-   Generate analytics and reports on sleep patterns and health trends.

### 1.4. Purpose

The primary purpose of the SmartRest AIoT Backend project is to develop a scalable, secure, and reliable server-side application that manages all aspects of the SmartRest intelligent mattress ecosystem. This includes user authentication, data acquisition from IoT devices, data processing and storage, providing access to data and insights for various user roles, and facilitating communication and system management. This aligns with the project overview in `README.md`.

### 1.5. Objectives

The key objectives of the SmartRest AIoT Backend are, as derived from `README.md` and API specifications:

-   **Develop a Secure Authentication System:** Implement robust user registration, login, and access control mechanisms for different user roles (Patient, Doctor, Customer, Admin) using Laravel Sanctum.
-   **Enable IoT Data Integration:** Design and implement API endpoints for smart mattresses to securely transmit sensor data (heart rate, breathing rate, temperature, movement, etc.).
-   **Implement Comprehensive User Management:** Provide functionalities for administrators to manage user accounts and for users to manage their profiles.
-   **Manage Product Information:** Allow administrators to manage the catalog of smart mattress models and their specifications.
-   **Facilitate Real-time Data Access:** Enable users (Patients, Doctors, Customers) to access their latest and historical sensor data.
-   **Support Messaging and Notifications:** Implement a system for communication between users (e.g., Patient-Doctor) and for delivering system alerts and health notifications.
-   **Provide Data Analytics and Reporting:** Develop capabilities to generate sleep reports and health summaries based on collected sensor data.
-   **Offer System and Device Management Tools:** Allow administrators and relevant users to monitor device status and perform basic management tasks.
-   **Ensure Scalability and Maintainability:** Build the system using best practices and a robust technology stack (Laravel, PostgreSQL) to support future growth and ease of maintenance.
-   **Deliver Comprehensive API Documentation:** Provide clear and interactive API documentation (using L5-Swagger) for frontend and mobile application developers.

---

## Chapter 2: System Analysis and Design

### 2.1. System Analysis

The SmartRest AIoT system is a multi-faceted platform designed to bridge the gap between IoT-enabled hardware (the smart mattress) and end-user applications (web/mobile interfaces for patients, doctors, and customers). The backend acts as the central hub, responsible for:

-   **Data Ingestion:** Receiving and validating sensor data streams from potentially numerous smart mattresses.
-   **Data Storage:** Securely storing user profiles, product information, sensor readings, messages, and system logs in a structured PostgreSQL database.
-   **Data Processing & Logic:** Implementing business rules, such as user authentication, authorization, data aggregation for analytics, and alert generation.
-   **API Provisioning:** Exposing a well-defined set of RESTful API endpoints for client applications to interact with the system's functionalities and data, as detailed in `api.php` and `api_documentation.md`.
-   **Security:** Ensuring data privacy and integrity through authentication (Laravel Sanctum), authorization, and secure communication protocols.

The system must be designed to handle concurrent requests, manage large volumes of time-series sensor data, and provide responsive interactions for users. It caters to distinct user roles, each with specific needs and access permissions.

### 2.2. Functional Requirements

The functional requirements define the specific operations the SmartRest AIoT system must perform. These are primarily exposed through its API endpoints, as documented in `api_documentation.md`, `routes.md`, and `routes_and_return.md`.

#### 2.2.1. Authentication and Session Management

This category covers how users access the system securely. Based on `AuthController.php` and API documentation:

-   **User Registration (`POST /api/auth/register`):**
    -   Allows new Patients or Customers to create an account.
    -   Requires `first_name`, `last_name`, `email`, `password`, `password_confirmation`, and `role` (`patient` or `customer`).
    -   Returns a success message, user object, and an authentication token.
-   **Email Verification (`GET /api/auth/verify-email/{id}/{hash}`):**
    -   Allows users to verify their email address using a signed URL.
    -   Requires authentication.
    -   Returns a message indicating verification status.
-   **User Login (`POST /api/auth/login`):**
    -   Authenticates existing users.
    -   Requires `email` and `password`.
    -   Returns a success message, user object, and an authentication token.
-   **User Logout (`POST /api/auth/logout`):**
    -   Invalidates the current user's session and access token.
    -   Requires authentication.
-   **Get Current User Profile (`GET /api/auth/me`):**
    -   Retrieves the profile of the authenticated user.
    -   Requires authentication.
    -   Returns the user object with role-specific data.
-   **Refresh Token (`POST /api/auth/refresh`):**
    -   Allows an authenticated user to obtain a new access token.
    -   Requires authentication.
-   **Forgot Password (`POST /api/auth/forgot-password`):**
    -   Initiates password reset.
    -   Requires `email`.
    -   Sends a reset link.
-   **Reset Password (`POST /api/auth/reset-password`):**
    -   Allows password reset using a token.
    -   Requires `token`, `email`, `password`, `password_confirmation`.
-   **Change Password (`POST /api/auth/change-password`):**
    -   Allows an authenticated user to change their password.
    -   Requires `current_password`, new `password`, `password_confirmation`.
-   **Social Login (`POST /api/auth/social-login`):**
    -   Placeholder for OAuth login (Google, Apple, etc.). Currently returns "not implemented".

#### 2.2.2. User Management (CRUD)

Operations for managing user accounts, primarily by administrators, based on `UserController.php` and API documentation:

-   **List Users (`GET /api/users`):**
    -   Retrieves a paginated list of users. Admin-only.
    -   Supports filtering by `role` and searching.
-   **Get User Details (`GET /api/users/{userId}`):**
    -   Fetches details for a specific user. Admin or self-access.
-   **Create User (`POST /api/users`):**
    -   Admin creates a new user (e.g., Doctor).
    -   Requires user details and `role`.
-   **Update User (`PUT /api/users/{userId}`):**
    -   Updates user profile. Admin or self-access.
-   **Delete User (`DELETE /api/users/{userId}`):**
    -   Removes a user account. Admin-only.

#### 2.2.3. Product Catalog Management

Managing smart mattress models, based on `ProductController.php` and API documentation:

-   **List Products (`GET /api/products`):**
    -   Retrieves a paginated list of products. Publicly accessible.
-   **Get Product Details (`GET /api/products/{productId}`):**
    -   Fetches details for a specific product. Publicly accessible.
-   **Create Product (`POST /api/products`):**
    -   Admin adds a new product.
    -   Requires product details like `name`, `description`.
-   **Update Product (`PUT /api/products/{productId}`):**
    -   Admin updates an existing product.
-   **Delete Product (`DELETE /api/products/{productId}`):**
    -   Admin removes a product. Returns 204 No Content.

#### 2.2.4. Sensor Data Collection and Query

Handling data from smart mattresses, based on `SensorController.php` and API documentation:

-   **Store Sensor Readings (`POST /api/sensors/data`):**
    -   Device uploads batched sensor readings. Authenticated with a device token.
    -   Requires `patient_id`, `bed_id`, and an array of `readings` (each with `sensor_type`, `sensor_value`, etc.).
    -   Supported `sensor_type` values: `pressure`, `heart_rate`, `breathing_rate`, `temperature`, `humidity`, `body_movement`, `posture`, `vibration`, `sleep_apnea`.
-   **Get Latest Sensor Data (`GET /api/sensors/latest`):**
    -   Retrieves the most recent sensor data for the authenticated user (Patient/Doctor).
-   **Get Historical Sensor Data (`GET /api/sensors/history`):**
    -   Retrieves paginated time-series sensor data. Supports filtering. Authenticated access.

#### 2.2.5. Messaging and Notifications

Communication and alerts, based on `MessageController.php` and API documentation:

-   **Send Message (`POST /api/messages`):**
    -   Users send messages (Patient-Doctor, Customer-Support).
    -   Requires `recipient_id`, `body`, `type` (`alert`, `chat`, `promo`).
-   **Get Conversation/Thread (`GET /api/messages/{conversationId}`):**
    -   Fetches messages for a specific conversation.
-   **Get Notifications (`GET /api/notifications`):**
    -   Retrieves unread alerts and notifications for the authenticated user.
-   **Acknowledge Notification (`POST /api/notifications/{id}/acknowledge`):**
    -   Marks a notification as read/handled.

#### 2.2.6. Analytics and Reports

Insights from sensor data, based on `AnalyticsController.php` and API documentation:

-   **Get Sleep Report (`GET /api/analytics/sleep-report`):**
    -   Generates AI-summary of nightly sleep (stages, posture, score). Patient/Doctor access.
-   **Get Health Summary (`GET /api/analytics/health-summary`):**
    -   Consolidated vitals trends and anomalies. Patient/Doctor access.

#### 2.2.7. System and Device Management

Operational aspects of smart mattresses, based on `SystemController.php` and API documentation:

-   **Get System/Device Status (`GET /api/system/status`):**
    -   Retrieves operational status of a mattress (uptime, firmware, sensor health). Admin/Doctor access.
-   **Reboot Device (`POST /api/system/reboot`):**
    -   Admin triggers remote device restart.

### 2.3. Users of the Project

The SmartRest AIoT system caters to several user roles, as defined in `README.md` and `routes.md`:

-   **Patient:**
    -   **Persona:** Hospital in-patient or home user under monitoring.
    -   **Key Permissions:** View own sensor data, reports, messages; manage profile.
-   **Doctor:**
    -   **Persona:** Clinician supervising patients.
    -   **Key Permissions:** View assigned patients' data/reports; comment; send messages/alerts; monitor patient mattresses; manage profile.
-   **Customer:**
    -   **Persona:** Retail buyer of a home mattress.
    -   **Key Permissions:** View own sensor data/reports; access product info; receive updates; manage profile; contact support.
-   **Admin:**
    -   **Persona:** Hospital IT / SmartRest company staff.
    -   **Key Permissions:** Full CRUD on users, products; monitor system/devices; system management tasks; access logs/analytics.

Role-based access control is enforced throughout the API.

### 2.4. System Design

#### 2.4.1. Architectural Overview

The SmartRest AIoT backend follows a monolithic application architecture using the Laravel PHP framework, adhering to the Model-View-Controller (MVC) pattern. The "View" component primarily serves JSON API responses.

-   **API Layer:** RESTful endpoints defined in `routes/api.php` serve as the interface for client applications and IoT devices.
-   **Controller Layer:** Classes like `AuthController`, `UserController`, etc., (in `app/Http/Controllers`) handle request validation, business logic execution (often delegating to services or models), and response formatting.
-   **Model Layer:** Eloquent models (in `app/Models`) represent database entities (`User`, `PatientProfile`, `SensorReading`, etc.) and manage database interactions.
-   **Database Layer:** PostgreSQL is the primary relational database, storing all application data.
-   **Authentication:** Laravel Sanctum handles API token-based authentication.
-   **API Documentation:** L5-Swagger generates OpenAPI documentation, accessible via `/api/documentation`.

This architecture promotes separation of concerns and leverages Laravel's features for rapid development and maintainability.

#### 2.4.2. UI Design Considerations (for consuming applications)

While the backend is API-focused, its design anticipates the needs of client UIs (web/mobile):

-   **Common UI Elements:** Secure login/registration, profile management, password recovery.
-   **Patient/Customer UI:** Dashboards for latest sensor data, historical data charts, sleep/health reports, messaging, notifications.
-   **Doctor UI:** Patient list, detailed patient views (data, reports), commenting tools, messaging, alert dashboards.
-   **Admin UI:** User/product management consoles, system status dashboards, log viewers, configuration panels.
    The API provides paginated lists, consistent response structures, and clear error messages to support these UIs.

#### 2.4.3. Database Design

The PostgreSQL database schema is central to the system, detailed in `db.sql` and `database.md`.

##### 2.4.3.1. Enumerated Types

Defined for data consistency and efficiency:

-   **`user_role`**: (`patient`, `doctor`, `customer`, `admin`) - Constrains `users.role`.
-   **`sensor_type`**: (`pressure`, `heart_rate`, `breathing_rate`, `temperature`, `humidity`, `body_movement`, `posture`, `vibration`, `sleep_apnea`) - Categorizes `sensor_readings.sensor_type`.

##### 2.4.3.2. Table Structures and Relationships

Key tables and their purposes:

1.  **`users`**: Base account information.

    -   `user_id` (UUID, PK), `email` (UNIQUE), `password_hash`, `role` (user_role), `first_name`, `last_name`, `phone`, `is_email_verified`, `created_at`, `updated_at`.
    -   Parent to `patient_profiles`, `doctor_profiles`. Referenced by `messages`.

2.  **`patient_profiles`**: Patient-specific data.

    -   `patient_id` (UUID, PK, FK to `users.user_id`), `national_id` (UNIQUE), `date_of_birth`, `sex`, `created_at`.
    -   One-to-one with `users`. Referenced by `doctor_patients`, `sensor_readings`.

3.  **`doctor_profiles`**: Doctor-specific data.

    -   `doctor_id` (UUID, PK, FK to `users.user_id`), `license_no`, `specialty`, `created_at`.
    -   One-to-one with `users`. Referenced by `doctor_patients`.

4.  **`doctor_patients`**: Many-to-many link between doctors and patients.

    -   `doctor_id` (UUID, PK, FK), `patient_id` (UUID, PK, FK), `assigned_at`.

5.  **`products`**: Smart mattress models.

    -   `product_id` (VARCHAR(20), PK), `name`, `description`, `image_url`, `firmware_version`, `is_active`, `created_at`, `updated_at`.

6.  **`sensor_readings`**: Raw IoT data.

    -   `reading_id` (UUID, PK), `patient_id` (FK), `bed_id`, `sensor_type` (sensor_type), `sensor_value` (FLOAT), `sensor_unit`, `timestamp`, `additional_metadata` (JSONB).
    -   Constraint `valid_pressure` (0-100 kPa). High-volume table.

7.  **`messages`**: User communication and alerts.

    -   `message_id` (UUID, PK), `sender_id` (FK), `recipient_id` (FK), `title`, `body`, `type` (VARCHAR(24) - `alert`, `chat`, `promo`), `is_read`, `sent_at`.

8.  **`system_logs`**: Device/system diagnostics.
    -   `log_id` (UUID, PK), `bed_id`, `severity` (VARCHAR(10) - `DEBUG`, `INFO`, etc.), `message`, `logged_at`.

##### 2.4.3.3. Indexes and Triggers

For performance and data integrity:

-   **Indexes**: On foreign keys and frequently queried columns (e.g., `sensor_readings.patient_id`, `sensor_readings.timestamp`, `messages.recipient_id`).
-   **Triggers**: `update_timestamp()` function and associated triggers on `users` and `products` tables automatically update the `updated_at` column on modifications.

---

## Chapter 3: Implementation

### 3.1. Introduction to Implementation

The SmartRest AIoT backend is implemented as a RESTful API using Laravel 12 and PHP 8.2+. It provides a secure, scalable, and maintainable foundation for the smart mattress ecosystem. The implementation translates the system design and functional requirements into working code, organized within the Laravel framework structure.

### 3.2. Tools and Technologies Used

The project leverages a modern technology stack, identified from `composer.json` and `README.md`:

-   **Backend Framework: Laravel 12** (PHP 8.2+)
    -   Provides MVC structure, Eloquent ORM, routing, middleware, Artisan console.
-   **Database: PostgreSQL**
    -   Primary relational database. Schema uses ENUMs, JSONB, UUIDs.
-   **API Authentication: Laravel Sanctum (^4.1)**
    -   Lightweight token-based authentication for APIs.
-   **API Documentation: L5-Swagger (^9.0)** (via `darkaonline/l5-swagger`)
    -   Generates interactive OpenAPI documentation.
-   **Dependency Management: Composer**
    -   Manages PHP packages.
-   **Development & Testing Tools:**
    -   `fakerphp/faker`: Generates fake data for seeding/testing.
    -   `laravel/pail`: Tails application logs.
    -   `laravel/pint`: PHP code style fixer.
    -   `laravel/sail`: Docker-based development environment.
    -   `mockery/mockery`: Mock object framework for testing.
    -   `nunomaduro/collision`: Error reporting for CLI.
    -   `pestphp/pest` & `pestphp/pest-plugin-laravel`: PHP testing framework.
-   **Frontend Build Tool: Vite** (with TailwindCSS, as per `README.md`)
    -   Used for frontend assets if an admin panel or companion web app is part of the project. `npm run dev` script in `composer.json`.
-   **Queue System: Database-based queues**
    -   Laravel Queues for deferring long-running tasks.
-   **Caching: Database/Redis**
    -   For performance improvement by caching frequently accessed data.
-   **Version Control: Git**
    -   Source code management.

### 3.3. API Interaction Examples (Illustrative)

Based on `README.md` and `api_documentation.md`.

#### 3.3.1. User Registration Example

A new patient registers.

**Request:**

-   Method: `POST`
-   URL: `http://localhost:8000/api/auth/register`
-   Headers: `Content-Type: application/json`, `Accept: application/json`
-   Body:

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe.patient@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "patient"
}
```

**Successful Response (201 Created):**

```json
{
    "message": "User registered successfully. Please check your email to verify your account.",
    "user": {
        /* ... user object ... */
    },
    "token": "YOUR_API_TOKEN"
}
```

**Explanation:** The client sends registration details. The server validates, creates `users` and `patient_profiles` records, hashes the password, and returns the user object and an API token.

#### 3.3.2. Fetching Latest Sensor Data Example

An authenticated patient requests their latest sensor data.

**Request:**

-   Method: `GET`
-   URL: `http://localhost:8000/api/sensors/latest`
-   Headers: `Authorization: Bearer YOUR_API_TOKEN`, `Accept: application/json`

**Successful Response (200 OK):**

```json
{
    "latest_readings": [
        {
            "sensor_type": "heart_rate",
            "sensor_value": 68.0,
            "sensor_unit": "bpm",
            "timestamp": "..."
        },
        {
            "sensor_type": "temperature",
            "sensor_value": 36.6,
            "sensor_unit": "°C",
            "timestamp": "..."
        }
    ],
    "summary": {
        /* ... summary data ... */
    }
}
```

**Explanation:** The authenticated client requests latest data. The server identifies the user via token, retrieves relevant `sensor_readings`, and returns them.

---

## Chapter 4: Conclusion and Recommendation

### 4.1. State of Implementation

The SmartRest AIoT backend has a well-defined API structure covering core functionalities for an intelligent mattress platform.

-   **API Endpoints:** Approximately 30 endpoints are defined across Authentication, User Management, Product Catalog, Sensor Data, Messaging, Analytics, and System Management.
-   **Authentication:** Laravel Sanctum provides secure user and device authentication.
-   **Database:** A detailed PostgreSQL schema is implemented, supporting all entities and relationships.
-   **Core Features:** User CRUD, product management, sensor data submission and retrieval, basic messaging, and placeholders for analytics reports are established.
-   **Technology Stack:** Built on Laravel 12, PHP 8.2, and PostgreSQL, providing a stable and modern foundation.
-   **API Documentation:** L5-Swagger integration ensures discoverable and usable API documentation.

The foundational elements are largely in place or clearly specified. Features like Social Login are noted as "not implemented yet," and the AI/ML algorithms for analytics represent a significant ongoing or future development area.

### 4.2. Recommendations for Future Work

1.  **Advanced Analytics & AI/ML:**
    -   Implement sophisticated algorithms for predictive health alerts, personalized sleep coaching.
    -   Establish an MLOps pipeline for AI/ML model management.
2.  **Enhanced Security:**
    -   Implement Two-Factor Authentication (2FA).
    -   Ensure comprehensive data encryption (at rest, in transit).
    -   Develop detailed audit trails for sensitive operations.
    -   Conduct regular security audits and penetration testing.
3.  **Scalability & Performance:**
    -   Expand use of Laravel Queues for all long-running tasks.
    -   Implement database partitioning for `sensor_readings` (e.g., TimescaleDB).
    -   Refine caching strategies.
4.  **Expanded IoT Device Management:**
    -   Implement Over-the-Air (OTA) firmware updates.
    -   Streamline device provisioning.
    -   Enhance device diagnostics capabilities.
5.  **Richer Communication:**
    -   Implement WebSocket-based real-time chat.
    -   Support secure file attachments in messages.
6.  **Third-Party Integrations:**
    -   Explore EHR system integration (HL7 FHIR).
    -   Allow data integration from other health wearables.
    -   Integrate with smart home platforms.
7.  **Compliance & Certification:**
    -   Pursue relevant certifications (e.g., HIPAA, GDPR) if handling sensitive health information or marketing as a medical device.
8.  **Complete Pending Features:**
    -   Finalize Social Login implementation.
9.  **Comprehensive Testing:**
    -   Expand unit, integration, and end-to-end test coverage.

Addressing these recommendations will further enhance the SmartRest AIoT platform's capabilities, security, and user value.

---

## Appendix

### 5.1. Full API Documentation (Reference)

For the complete and interactive API documentation, please refer to the Swagger UI endpoint, typically available at:
`http://localhost:8000/api/documentation`
(This endpoint is active when the Laravel development server is running and L5-Swagger is correctly configured).

The detailed structure of API requests, responses, and data models can also be found in the `d:\ALL-GITHUB\smartrest-aiot-backend\drafts\api_documentation.md` file within the project.

### 5.2. Detailed Database Schema (SQL)

The following SQL script defines the PostgreSQL database schema for the SmartRest AIoT application, as provided in `d:\ALL-GITHUB\smartrest-aiot-backend\drafts\db.sql`:

```sql
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
```

For a more descriptive explanation of the schema, refer to `d:\ALL-GITHUB\smartrest-aiot-backend\drafts\database.md`.
