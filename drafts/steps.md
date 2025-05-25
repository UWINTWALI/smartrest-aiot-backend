# SmartRest AIoT Backend - Complete Setup Guide

## Project Overview

**SmartRest AIoT Backend** is a Laravel-based API system for an intelligent mattress that enhances sleep quality and monitors health using IoT, AI, and mobile/web applications. The system tracks vital signs like heart rate, breathing patterns, temperature, and movement through embedded sensors.

### Key Features

-   **Temperature Control**: Automatic mattress temperature adjustment
-   **Health Monitoring**: Real-time heart rate, breathing, and movement tracking
-   **Multi-Role System**: Supports patients, doctors, customers, and administrators
-   **IoT Integration**: Collects and processes sensor data from smart mattresses
-   **API Documentation**: Swagger/OpenAPI documentation
-   **Real-time Analytics**: Health reports and sleep pattern analysis

### Technology Stack

-   **Backend**: Laravel 12 (PHP 8.2+)
-   **Database**: PostgreSQL (primary), MySQL/SQLite supported
-   **Authentication**: Laravel Sanctum (JWT tokens)
-   **Documentation**: L5-Swagger (OpenAPI/Swagger)
-   **Queue System**: Database-based queues
-   **Caching**: Database/Redis
-   **Frontend Build**: Vite + TailwindCSS

### User Roles

-   **Patient**: Hospital in-patients using the mattress
-   **Doctor**: Clinicians supervising patients
-   **Customer**: Retail buyers with home mattresses
-   **Admin**: Hospital IT/company staff with full access

---

## Prerequisites

### Required Software

1. **PHP 8.2 or higher**

    ```powershell
    php --version
    ```

2. **Composer** (PHP dependency manager)

    ```powershell
    composer --version
    ```

3. **PostgreSQL** (recommended) or MySQL

    - PostgreSQL 12+ recommended
    - MySQL 8.0+ or MariaDB 10.4+ as alternatives

4. **Node.js & npm** (for frontend assets)

    ```powershell
    node --version
    npm --version
    ```

5. **Git** (for version control)
    ```powershell
    git --version
    ```

### Optional Tools

-   **Redis** (for caching and queues)
-   **Docker** (for containerized deployment)
-   **Postman** (API testing - collection included)

---

## Step-by-Step Setup Instructions

### 1. Clone and Navigate to Project

```powershell
git clone <repository-url>
cd smartrest-aiot-backend
```

### 2. Install PHP Dependencies

```powershell
composer install
```

### 3. Install Node.js Dependencies

```powershell
npm install
```

### 4. Environment Configuration

#### Copy Environment File

```powershell
copy .env.example .env
```

#### Configure Database Settings

Edit `.env` file with your database credentials:

```env
# Application Settings
APP_NAME="SmartRest AIoT"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smartrest_aiot
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Alternative: MySQL Configuration
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=smartrest_aiot
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Laravel Sanctum for API Authentication
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@smartrest.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Swagger Documentation
L5_SWAGGER_GENERATE_ALWAYS=true
```

### 5. Generate Application Key

```powershell
php artisan key:generate
```

### 6. Database Setup

#### Create Database

Create a new database in PostgreSQL:

```sql
CREATE DATABASE smartrest_aiot;
```

#### Run Migrations

```powershell
php artisan migrate
```

#### Seed Database with Sample Data

```powershell
php artisan db:seed
```

**Default Users Created:**

-   **Admin**: admin@smartrest.com / password123
-   **Doctor**: doctor@smartrest.com / password123
-   **Patient**: patient@smartrest.com / password123
-   **Customer**: customer@smartrest.com / password123

### 7. Generate API Documentation

```powershell
php artisan l5-swagger:generate
```

### 8. Create Storage Symlink

```powershell
php artisan storage:link
```

### 9. Build Frontend Assets

```powershell
npm run build
```

### 10. Start the Development Server

```powershell
php artisan serve
```

The application will be available at: http://localhost:8000

---

## Database Schema Overview

### Core Tables

-   **users**: Base user accounts (patients, doctors, customers, admins)
-   **patient_profiles**: Extended patient information
-   **doctor_profiles**: Medical professional credentials
-   **doctor_patients**: Doctor-patient relationships
-   **products**: Mattress models and specifications
-   **sensor_readings**: IoT sensor data (heart rate, temperature, etc.)
-   **messages**: Communication between users
-   **system_logs**: Device and system logging

### Key Data Types

-   **sensor_type**: pressure, heart_rate, breathing_rate, temperature, humidity, body_movement, posture, vibration, sleep_apnea
-   **user_role**: patient, doctor, customer, admin
-   **message_type**: alert, chat, promo

---

## API Documentation

### Access Swagger UI

Once the server is running, access the interactive API documentation at:

```
http://localhost:8000/api/documentation
```

### API Categories

#### 1. Authentication & Session Management (10 endpoints)

-   `POST /api/auth/register` - Create new account
-   `POST /api/auth/login` - User authentication
-   `GET /api/auth/me` - Get current user profile
-   `POST /api/auth/logout` - End session
-   `POST /api/auth/refresh` - Refresh token
-   `POST /api/auth/forgot-password` - Password reset request
-   `POST /api/auth/reset-password` - Reset password
-   `POST /api/auth/change-password` - Change password
-   `GET /api/auth/verify-email` - Email verification
-   `POST /api/auth/social-login` - OAuth login

#### 2. User Management (5 endpoints)

-   `GET /api/users` - List users (Admin)
-   `GET /api/users/{userId}` - Get user details
-   `POST /api/users` - Create user (Admin)
-   `PUT /api/users/{userId}` - Update user
-   `DELETE /api/users/{userId}` - Delete user (Admin)

#### 3. Product Catalog (4 endpoints)

-   `GET /api/products` - List mattress models
-   `GET /api/products/{productId}` - Product details
-   `POST /api/products` - Add product (Admin)
-   `PUT /api/products/{productId}` - Update product (Admin)

#### 4. Sensor Data Collection (3 endpoints)

-   `POST /api/sensors/data` - Upload sensor readings
-   `GET /api/sensors/latest` - Get latest sensor data
-   `GET /api/sensors/history` - Historical sensor data

#### 5. Messaging & Notifications (4 endpoints)

-   `POST /api/messages` - Send message
-   `GET /api/messages/{conversationId}` - Get conversation
-   `GET /api/notifications` - Get notifications
-   `POST /api/notifications/{id}/acknowledge` - Mark as read

#### 6. Analytics & Reports (2 endpoints)

-   `GET /api/analytics/sleep-report` - Sleep analysis
-   `GET /api/analytics/health-summary` - Health trends

#### 7. System & Device Management (2 endpoints)

-   `GET /api/system/status` - Device status
-   `POST /api/system/reboot` - Remote device restart

---

## Testing the API

### Using Postman

Import the included Postman collection:

```powershell
# File location: SmartRest-API.postman_collection.json
```

### Basic API Testing

#### 1. Register a new user:

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "patient"
  }'
```

#### 2. Login:

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### 3. Access protected endpoint:

```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Development Workflow

### Running in Development Mode

```powershell
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Watch for file changes (if needed)
npm run dev

# Terminal 3: Process queues
php artisan queue:work
```

### Database Operations

#### Fresh Migration (Reset Database)

```powershell
php artisan migrate:fresh --seed
```

#### Create New Migration

```powershell
php artisan make:migration create_new_table
```

#### Create New Model

```powershell
php artisan make:model ModelName -mcr
```

### Clearing Caches

```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Queue Management

```powershell
# Start queue worker
php artisan queue:work

# Process specific queue
php artisan queue:work --queue=high,default

# Restart queue workers
php artisan queue:restart
```

---

## Production Deployment

### Environment Setup

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Configure production database
4. Set up proper mail configuration
5. Configure Redis for caching/queues (recommended)

### Optimization Commands

```powershell
# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate application key
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Generate API docs
php artisan l5-swagger:generate
```

### Using Docker

```powershell
# Build and run with Docker
docker build -t smartrest-aiot .
docker run -p 8000:80 smartrest-aiot
```

---

## Troubleshooting

### Common Issues

#### 1. Database Connection Error

-   Verify database credentials in `.env`
-   Ensure database exists
-   Check PostgreSQL/MySQL service is running

#### 2. Permission Errors

```powershell
# Windows: Ensure proper folder permissions
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T
```

#### 3. Migration Errors

```powershell
# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Reset and re-run migrations
php artisan migrate:fresh
```

#### 4. API Documentation Not Generating

```powershell
# Clear cache and regenerate
php artisan config:clear
php artisan l5-swagger:generate
```

#### 5. Authentication Issues

-   Verify Sanctum configuration
-   Check token generation
-   Ensure proper headers in requests

### Log Files

Check logs for detailed error information:

-   `storage/logs/laravel.log` - General application logs
-   `storage/logs/api.log` - API-specific logs
-   `storage/logs/auth.log` - Authentication logs
-   `storage/logs/sensors.log` - Sensor data logs

---

## Additional Resources

### Documentation Files

-   `drafts/database.md` - Detailed database schema documentation
-   `drafts/routes.md` - Complete API routes documentation
-   `README.md` - Project overview

### API Testing

-   Swagger UI: http://localhost:8000/api/documentation
-   Postman Collection: `SmartRest-API.postman_collection.json`

### Development Tools

-   Laravel Tinker: `php artisan tinker` (Interactive PHP shell)
-   Database migrations: `php artisan migrate`
-   Queue monitoring: `php artisan queue:work --verbose`

---

## Next Steps

1. **Test Core Functionality**: Use the provided test users to explore the API
2. **Customize Configuration**: Adjust settings based on your environment
3. **Integrate IoT Devices**: Configure sensor data endpoints
4. **Set Up Monitoring**: Implement logging and error tracking
5. **Scale Infrastructure**: Configure Redis, load balancers as needed

For support and contributions, refer to the project documentation and issue tracker.
