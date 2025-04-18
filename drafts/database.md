# SmartRest AIoT – Database Explanation

This document describes the **initial PostgreSQL schema** that powers the SmartRest AIoT platform. It explains the purpose of every table, the meaning of each field, and the relationships that tie the data model together. The current schema is intentionally lean; additional columns (e.g., more detailed medical metadata, device calibration data) can be added **without breaking existing contracts**.

---

## 1. Enumerated Types

| Enum              | Allowed Values                                                                                                                | Purpose                                                                                                    |
| ----------------- | ----------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------- |
| **`user_role`**   | `patient`, `doctor`, `customer`, `admin`                                                                                      | Constrains the role of every user. A single‑column enum is simpler and faster than a separate roles table. |
| **`sensor_type`** | `pressure`, `heart_rate`, `breathing_rate`, `temperature`, `humidity`, `body_movement`, `posture`, `vibration`, `sleep_apnea` | Guarantees that sensor readings are typed correctly and queried efficiently.                               |

---

## 2. Table‑by‑Table Detail

### 2.1 `users`

Base account information shared by **all** personas (patients, doctors, retail customers, admins).

| Column              | Type                                      | Nullable | Description                                             |
| ------------------- | ----------------------------------------- | -------- | ------------------------------------------------------- |
| `user_id`           | `UUID` (PK, default `uuid_generate_v4()`) | No       | Globally unique identifier. Propagated to child tables. |
| `email`             | `VARCHAR(80)` **UNIQUE**                  | No       | Login and primary contact address.                      |
| `password_hash`     | `TEXT`                                    | No       | Argon2/BCrypt (never store raw passwords).              |
| `role`              | `user_role`                               | No       | Single enum value defining capabilities.                |
| `first_name`        | `VARCHAR(80)`                             | No       | Given name.                                             |
| `last_name`         | `VARCHAR(80)`                             | No       | Family name.                                            |
| `phone`             | `VARCHAR(20)`                             | Yes      | Optional mobile/WhatsApp contact.                       |
| `is_email_verified` | `BOOLEAN` default `false`                 | No       | Toggles once user clicks verification link.             |
| `created_at`        | `TIMESTAMPTZ` default `now()`             | No       | Record creation time (UTC).                             |
| `updated_at`        | `TIMESTAMPTZ` default `now()`             | No       | Auto‑updated by trigger `update_users_timestamp`.       |

> **Extending:** Add columns like `profile_picture_url`, `two_factor_secret`, or `locale` without affecting existing relations.

---

### 2.2 `patient_profiles`

One‑to‑one extension of `users` that stores **patient‑specific** data.

| Column          | Type                          | Nullable | Description                                                 |
| --------------- | ----------------------------- | -------- | ----------------------------------------------------------- |
| `patient_id`    | `UUID` (PK → `users.user_id`) | No       | Mirrors the user row. Cascades on delete.                   |
| `national_id`   | `CHAR(16)` **UNIQUE**         | Yes      | Rwanda 16‑digit ID; useful for hospital admissions.         |
| `date_of_birth` | `DATE`                        | Yes      | Enables age calculations.                                   |
| `sex`           | `CHAR(1)` (`M`,`F`,`O`)       | Yes      | Biological sex. ‘O’ stands for ‘Other / prefer not to say’. |
| `created_at`    | `TIMESTAMPTZ`                 | No       | Timestamp of profile creation.                              |

> **Future fields:** blood group, chronic conditions, insurance provider, emergency contacts, preferred language, etc.

---

### 2.3 `doctor_profiles`

Stores credentials for medical professionals.

| Column       | Type                          | Nullable | Description                           |
| ------------ | ----------------------------- | -------- | ------------------------------------- |
| `doctor_id`  | `UUID` (PK → `users.user_id`) | No       | Matches doctor entry in `users`.      |
| `license_no` | `VARCHAR(40)`                 | Yes      | Medical licence or registration code. |
| `specialty`  | `VARCHAR(60)`                 | Yes      | e.g., Pulmonology, Cardiology.        |
| `created_at` | `TIMESTAMPTZ`                 | No       | When the profile was first saved.     |

> **Future fields:** hospital affiliation, consultation hours, signature image for prescriptions.

---

### 2.4 `doctor_patients`

A **many‑to‑many junction** table linking doctors and the patients they oversee.

| Column        | Type                                   | Nullable | Description                                       |
| ------------- | -------------------------------------- | -------- | ------------------------------------------------- |
| `doctor_id`   | `UUID` → `doctor_profiles.doctor_id`   | No       | One side of the pairing.                          |
| `patient_id`  | `UUID` → `patient_profiles.patient_id` | No       | Other side of the pairing.                        |
| `assigned_at` | `TIMESTAMPTZ` default `now()`          | No       | When this doctor started monitoring this patient. |

Composite primary key `(doctor_id, patient_id)` prevents duplicates.

---

### 2.5 `products`

Catalogue of SmartRest mattresses and accessories (no e‑commerce at this stage).

| Column                    | Type                     | Nullable | Description                              |
| ------------------------- | ------------------------ | -------- | ---------------------------------------- |
| `product_id`              | `VARCHAR(20)` (PK)       | No       | Human‑friendly ID (e.g., `SR‑PRO‑01`).   |
| `name`                    | `VARCHAR(120)`           | No       | Marketing title.                         |
| `description`             | `TEXT`                   | Yes      | Long‑form overview & specs.              |
| `image_url`               | `TEXT`                   | Yes      | CDN‑hosted hero image.                   |
| `firmware_version`        | `VARCHAR(20)`            | Yes      | Factory firmware shipped with the model. |
| `is_active`               | `BOOLEAN` default `true` | No       | Soft‑deactivation flag.                  |
| `created_at / updated_at` | `TIMESTAMPTZ`            | No       | Managed by code & trigger.               |

---

### 2.6 `sensor_readings`

High‑volume table that stores **raw IoT data** emitted by each mattress.

| Column                | Type                                   | Nullable | Description                                              |
| --------------------- | -------------------------------------- | -------- | -------------------------------------------------------- |
| `reading_id`          | `UUID` (PK)                            | No       | Unique for every upload.                                 |
| `patient_id`          | `UUID` → `patient_profiles.patient_id` | No       | Owner of the measurement.                                |
| `bed_id`              | `VARCHAR(64)`                          | No       | Serial/QR of mattress hardware.                          |
| `sensor_type`         | `sensor_type` ENUM                     | No       | Agrees with predefined list.                             |
| `sensor_value`        | `FLOAT`                                | No       | Raw number (e.g., `67.0`).                               |
| `sensor_unit`         | `VARCHAR(20)`                          | Yes      | bpm, °C, %, kg/m², etc.                                  |
| `timestamp`           | `TIMESTAMPTZ` default `now()`          | No       | Server ingestion time.                                   |
| `additional_metadata` | `JSONB`                                | Yes      | Optional extras – firmware, calibration, waveform slice. |

**Constraint:** `valid_pressure` verifies that pressure readings fall inside `0–100 kPa` range.

> **Performance notes:** create a TimescaleDB hypertable on `(timestamp)` for automatic partitioning; index on `(patient_id, timestamp DESC)` for quick dashboard queries.

---

### 2.7 `messages`

Unifies **1‑to‑1 chat** and **system alerts**.

| Column         | Type                                     | Nullable | Description                          |
| -------------- | ---------------------------------------- | -------- | ------------------------------------ |
| `message_id`   | `UUID` (PK)                              | No       | Unique per message.                  |
| `sender_id`    | `UUID` → `users.user_id`                 | No       | Can be a human or a service account. |
| `recipient_id` | `UUID` → `users.user_id`                 | No       | Target user.                         |
| `title`        | `TEXT`                                   | Yes      | Short headline for alerts/promos.    |
| `body`         | `TEXT`                                   | No       | Main content or chat text.           |
| `type`         | `VARCHAR(24)` (`alert`, `chat`, `promo`) | No       | Drives UI styling & push rules.      |
| `is_read`      | `BOOLEAN` default `false`                | No       | Toggles when opened.                 |
| `sent_at`      | `TIMESTAMPTZ` default `now()`            | No       | Creation time.                       |

> **Push notifications:** an application service can query unread alert rows and deliver mobile/web pushes.

---

### 2.8 `system_logs`

Device‑level diagnostics for engineering and support.

| Column      | Type                                                         | Nullable | Description                       |
| ----------- | ------------------------------------------------------------ | -------- | --------------------------------- |
| `log_id`    | `UUID` (PK)                                                  | No       | Unique per log line.              |
| `bed_id`    | `VARCHAR(64)`                                                | No       | Hardware source.                  |
| `severity`  | `VARCHAR(10)` (`DEBUG`, `INFO`, `WARN`, `ERROR`, `CRITICAL`) | Yes      | Log level.                        |
| `message`   | `TEXT`                                                       | No       | Human‑readable event description. |
| `logged_at` | `TIMESTAMPTZ` default `now()`                                | No       | Ingestion timestamp.              |

> **Indexing:** Indexes on `bed_id` and `logged_at` allow quick filtering by device and timeframe when troubleshooting.

---

## 3. Triggers & Audit Helpers

-   `update_timestamp()` keeps `updated_at` fresh on every `UPDATE` for `users` and `products`.
-   Consider enabling `pg_partman` or native declarative partitioning on `sensor_readings` once daily volume exceeds ~10 M rows.
-   Enable **`pgcrypto`** or **`pg_audit`** extensions if you require tamper‑evident audit logs (HIPAA/GDPR).

---

## 4. Future Evolution

-   **Patient & Doctor extras:** address info, insurance policy numbers, next‑of‑kin, preferred contact methods, medical certifications, departments.
-   **Role granularity:** if finer access control is needed, keep the `user_role` enum for top‑level UX flows but layer a policy/ACL table on top.
-   **Telemetry summarisation:** create nightly materialised views for sleep sessions to accelerate dashboard analytics.
