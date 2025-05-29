# 🚀 SmartRest IoT Backend - PHP & Laravel Commands Documentation

## 📅 **Last Updated**: May 25, 2025

This comprehensive guide contains all PHP/Laravel commands used in the SmartRest IoT Backend project, organized by category for easy reference.

---

## 🏗️ **Project Setup & Installation**

### Install Dependencies

```powershell
composer install
```

### Install Development Dependencies

```powershell
composer install --dev
```

### Update Dependencies

```powershell
composer update
```

### Install Specific Package

```powershell
composer require package/name
```

### Install Development Package

```powershell
composer require --dev package/name
```

### Generate Application Key

```powershell
php artisan key:generate
```

### Create Storage Link

```powershell
php artisan storage:link
```

---

## 🗄️ **Database & Migrations**

### Run All Migrations

```powershell
php artisan migrate
```

### Rollback Last Migration Batch

```powershell
php artisan migrate:rollback
```

### Rollback Specific Number of Batches

```powershell
php artisan migrate:rollback --step=5
```

### Reset All Migrations

```powershell
php artisan migrate:reset
```

### Refresh Migrations (Reset + Migrate)

```powershell
php artisan migrate:refresh
```

### Fresh Migration (Drop All + Migrate)

```powershell
php artisan migrate:fresh
```

### Fresh Migration with Seeding

```powershell
php artisan migrate:fresh --seed
```

### Check Migration Status

```powershell
php artisan migrate:status
```

### Create New Migration

```powershell
php artisan make:migration create_table_name
```

### Create Migration for Existing Table

```powershell
php artisan make:migration add_column_to_table_name --table=table_name
```

### Create Migration with Model

```powershell
php artisan make:model ModelName -m
```

---

## 🌱 **Database Seeding**

### Run All Seeders

```powershell
php artisan db:seed
```

### Run Specific Seeder

```powershell
php artisan db:seed --class=SeederClassName
```

### Create New Seeder

```powershell
php artisan make:seeder SeederClassName
```

### Migrate and Seed

```powershell
php artisan migrate:fresh --seed
```

---

## 🏭 **Factories & Testing Data**

### Create Factory

```powershell
php artisan make:factory FactoryName
```

### Create Factory for Model

```powershell
php artisan make:factory FactoryName --model=ModelName
```

### Tinker with Factory Testing

```powershell
php artisan tinker
```

---

## 🎯 **Models & Eloquent**

### Create Model

```powershell
php artisan make:model ModelName
```

### Create Model with Migration

```powershell
php artisan make:model ModelName -m
```

### Create Model with Factory

```powershell
php artisan make:model ModelName -f
```

### Create Model with Controller

```powershell
php artisan make:model ModelName -c
```

### Create Model with All (Migration, Factory, Controller, Seeder)

```powershell
php artisan make:model ModelName -mfcs
```

### Create Model with Resource Controller

```powershell
php artisan make:model ModelName -mcr
```

---

## 🎮 **Controllers**

### Create Controller

```powershell
php artisan make:controller ControllerName
```

### Create Resource Controller

```powershell
php artisan make:controller ControllerName --resource
```

### Create API Resource Controller

```powershell
php artisan make:controller ControllerName --api
```

### Create Controller with Model

```powershell
php artisan make:controller ControllerName --model=ModelName
```

### Create Invokable Controller

```powershell
php artisan make:controller ControllerName --invokable
```

---

## 🛣️ **Routes & API**

### List All Routes

```powershell
php artisan route:list
```

### List API Routes Only

```powershell
php artisan route:list --path=api
```

### List Routes for Specific Method

```powershell
php artisan route:list --method=GET
```

### Clear Route Cache

```powershell
php artisan route:clear
```

### Cache Routes

```powershell
php artisan route:cache
```

---

## 🔐 **Authentication & Authorization**

### Create Auth Scaffolding

```powershell
php artisan make:auth
```

### Create Policy

```powershell
php artisan make:policy PolicyName
```

### Create Policy for Model

```powershell
php artisan make:policy PolicyName --model=ModelName
```

### Create Middleware

```powershell
php artisan make:middleware MiddlewareName
```

### Create Request

```powershell
php artisan make:request RequestName
```

---

## 📧 **Mail & Notifications**

### Create Mailable

```powershell
php artisan make:mail MailableName
```

### Create Notification

```powershell
php artisan make:notification NotificationName
```

### Create Mail with Markdown

```powershell
php artisan make:mail MailableName --markdown=emails.name
```

---

## 🔄 **Queue & Jobs**

### Create Job

```powershell
php artisan make:job JobName
```

### Run Queue Worker

```powershell
php artisan queue:work
```

### Run Queue Worker for Specific Connection

```powershell
php artisan queue:work database
```

### Process Single Job

```powershell
php artisan queue:work --once
```

### List Failed Jobs

```powershell
php artisan queue:failed
```

### Retry Failed Job

```powershell
php artisan queue:retry job_id
```

### Retry All Failed Jobs

```powershell
php artisan queue:retry all
```

### Clear Failed Jobs

```powershell
php artisan queue:flush
```

### Create Queue Table

```powershell
php artisan queue:table
```

### Create Failed Jobs Table

```powershell
php artisan queue:failed-table
```

---

## 🗂️ **Resources & API Resources**

### Create Resource

```powershell
php artisan make:resource ResourceName
```

### Create Resource Collection

```powershell
php artisan make:resource ResourceName --collection
```

### Create API Resource

```powershell
php artisan make:resource Api/ResourceName
```

---

## 🧪 **Testing**

### Run All Tests

```powershell
php artisan test
```

### Run Specific Test

```powershell
php artisan test --filter=TestMethodName
```

### Create Test

```powershell
php artisan make:test TestName
```

### Create Unit Test

```powershell
php artisan make:test TestName --unit
```

### Create Feature Test

```powershell
php artisan make:test TestName --feature
```

### Run Tests with Coverage

```powershell
php artisan test --coverage
```

### Run Tests in Parallel

```powershell
php artisan test --parallel
```

---

## 🎨 **Frontend & Assets**

### Compile Assets

```powershell
npm run dev
```

### Watch Assets for Changes

```powershell
npm run watch
```

### Production Build

```powershell
npm run production
```

### Install NPM Dependencies

```powershell
npm install
```

---

## 🧹 **Cache Management**

### Clear Application Cache

```powershell
php artisan cache:clear
```

### Clear Configuration Cache

```powershell
php artisan config:clear
```

### Clear Route Cache

```powershell
php artisan route:clear
```

### Clear View Cache

```powershell
php artisan view:clear
```

### Clear All Caches

```powershell
php artisan optimize:clear
```

### Cache Configuration

```powershell
php artisan config:cache
```

### Cache Routes

```powershell
php artisan route:cache
```

### Cache Views

```powershell
php artisan view:cache
```

### Optimize Application (Cache All)

```powershell
php artisan optimize
```

---

## 🔧 **Development & Debugging**

### Start Development Server

```powershell
php artisan serve
```

### Start Server on Specific Port

```powershell
php artisan serve --port=8080
```

### Start Server on Specific Host

```powershell
php artisan serve --host=0.0.0.0
```

### Enter Tinker (REPL)

```powershell
php artisan tinker
```

### Show Application Information

```powershell
php artisan about
```

### List All Artisan Commands

```powershell
php artisan list
```

### Get Help for Specific Command

```powershell
php artisan help command:name
```

### Enable Debug Mode

```powershell
php artisan down --render="errors::503"
```

### Disable Maintenance Mode

```powershell
php artisan up
```

---

## 📊 **Database Operations**

### Create Database Backup

```powershell
php artisan db:backup
```

### Show Database Schema

```powershell
php artisan schema:dump
```

### Truncate Specific Table

```powershell
php artisan db:wipe --table=table_name
```

---

## 🔍 **Code Generation & Inspection**

### Create Custom Command

```powershell
php artisan make:command CommandName
```

### Create Event

```powershell
php artisan make:event EventName
```

### Create Listener

```powershell
php artisan make:listener ListenerName
```

### Create Observer

```powershell
php artisan make:observer ObserverName
```

### Create Provider

```powershell
php artisan make:provider ProviderName
```

### Create Rule (Validation)

```powershell
php artisan make:rule RuleName
```

### Create Cast

```powershell
php artisan make:cast CastName
```

### Create Component

```powershell
php artisan make:component ComponentName
```

---

## 📚 **API Documentation (L5-Swagger)**

### Generate API Documentation

```powershell
php artisan l5-swagger:generate
```

### Publish Swagger Config

```powershell
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```

---

## 🔄 **Package Management**

### Publish Package Configuration

```powershell
php artisan vendor:publish
```

### Publish Specific Provider

```powershell
php artisan vendor:publish --provider="Provider\Name"
```

### Publish Specific Tag

```powershell
php artisan vendor:publish --tag=config
```

---

## 🚀 **Production & Deployment**

### Generate Optimized Autoloader

```powershell
composer dump-autoload --optimize
```

### Generate Optimized Autoloader for Production

```powershell
composer dump-autoload --optimize --no-dev
```

### Install Production Dependencies Only

```powershell
composer install --no-dev --optimize-autoloader
```

### Clear and Cache Everything for Production

```powershell
php artisan optimize
```

### Check Environment Configuration

```powershell
php artisan env
```

---

## 🛠️ **SmartRest IoT Specific Commands**

### Seed SmartRest Data

```powershell
php artisan db:seed --class=SmartRestSeeder
```

### Fresh Install with SmartRest Data

```powershell
php artisan migrate:fresh --seed
```

### Generate SmartRest API Documentation

```powershell
php artisan l5-swagger:generate
```

### Test Factory Data Generation

```powershell
php artisan tinker
# Then in tinker:
# User::factory()->count(10)->create()
# PatientProfile::factory()->count(5)->create()
# SensorReading::factory()->count(100)->create()
```

### Check SmartRest Models

```powershell
php artisan tinker
# Then test model relationships:
# User::with('patientProfile')->first()
# PatientProfile::with('user')->first()
# SensorReading::with('patient')->first()
```

---

## 🔧 **Environment & Configuration**

### Check Current Environment

```powershell
php artisan env
```

### List All Configuration

```powershell
php artisan config:show
```

### Show Specific Configuration

```powershell
php artisan config:show database
```

---

## 📈 **Performance & Monitoring**

### Profile Application Performance

```powershell
php artisan profile:start
```

### Monitor Queue Performance

```powershell
php artisan queue:monitor
```

### Check Application Health

```powershell
php artisan health:check
```

---

## 🐛 **Debugging & Troubleshooting**

### Clear All Caches and Restart

```powershell
php artisan optimize:clear && php artisan optimize
```

### Check Laravel Version

```powershell
php artisan --version
```

### Check PHP Version

```powershell
php --version
```

### Validate Composer Dependencies

```powershell
composer validate
```

### Check for Security Vulnerabilities

```powershell
composer audit
```

### Update Composer Itself

```powershell
composer self-update
```

---

## 💡 **Quick Development Workflows**

### Fresh Start Development Workflow

```powershell
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan serve
```

### Production Deployment Workflow

```powershell
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan queue:restart
```

### Testing Workflow

```powershell
php artisan migrate:fresh --seed --env=testing
php artisan test
```

### Factory Testing Workflow

```powershell
php artisan migrate:fresh
php artisan tinker
# Test factories interactively
```

---

_📝 Note: Replace `ModelName`, `ControllerName`, `TableName`, etc. with actual names relevant to your SmartRest IoT project._

_🔧 Always run commands from the project root directory: `d:\ALL-GITHUB\smartrest-aiot-backend`_

_⚠️ Use `--force` flag carefully in production environments for destructive operations._
