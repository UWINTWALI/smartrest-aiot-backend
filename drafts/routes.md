## User Roles the backend must support

| Role         | Typical persona                            | Key permissions                                                 |
| ------------ | ------------------------------------------ | --------------------------------------------------------------- |
| **Patient**  | Hospital in‑patient (mattress user)        | View own sensor data, messages, alerts                          |
| **Doctor**   | Clinician supervising one or more patients | View & comment on assigned patients’ data, send alerts/messages |
| **Customer** | Retail buyer who owns a mattress at home   | View own sensor data & product info                             |
| **Admin**    | Hospital IT / company staff                | Full CRUD on users, products, devices, system ops               |

---

## Category 1 • Authentication & Session Management (10)

1. **POST `/api/auth/register`** – create an account (Patient, Customer).
2. **GET `/api/auth/verify-email`** – confirm email via token.
3. **POST `/api/auth/login`** – issue **JWT + refresh** tokens.
4. **POST `/api/auth/logout`** – revoke current JWT/RT pair.
5. **GET `/api/auth/me`** – return authenticated user profile.
6. **POST `/api/auth/refresh`** – rotate refresh token, issue new JWT.
7. **POST `/api/auth/forgot-password`** – send reset link.
8. **POST `/api/auth/reset-password`** – set new password by token.
9. **POST `/api/auth/change-password`** – change password while logged in.
10. **POST `/api/auth/social-login`** – one‑tap OAuth (Google, Apple, etc.).  
    _Role:_ Public / All authenticated roles as appropriate.

---

## Category 2 • User Management (CRUD) (5)

11. **GET `/api/users`** – list users with filters (Admin).
12. **GET `/api/users/{userId}`** – fetch user by ID (Admin, or self).
13. **POST `/api/users`** – create user manually (Admin onboarding a Doctor).
14. **PUT `/api/users/{userId}`** – update profile / role (Admin; self‑update allowed on own record).
15. **DELETE `/api/users/{userId}`** – deactivate or hard‑delete user (Admin).

---

## Category 3 • Product Catalog (4)

16. **GET `/api/products`** – list mattress models & accessories (all roles).
17. **GET `/api/products/{productId}`** – detailed specs, manuals, pricing (all roles).
18. **POST `/api/products`** – add new product (Admin).
19. **PUT `/api/products/{productId}`** – update or retire product (Admin).

---

## Category 4 • Sensor Data Collection & Query (3)

20. **POST `/api/sensors/data`** – device uploads batched readings (Device→backend; authenticated with device token).
21. **GET `/api/sensors/latest`** – get latest snapshot for the requesting user/patient (Patient, Doctor).
22. **GET `/api/sensors/history`** – paged time‑series data with query params (`type`, `from`, `to`) (Patient, Doctor).

---

## Category 5 • Messaging & Notifications (4)

23. **POST `/api/messages`** – send message; supports Patient⇄Doctor or Customer⇄Support threads.
24. **GET `/api/messages/{conversationId}`** – fetch or poll a specific thread.
25. **GET `/api/notifications`** – unread alerts (health, system, promo) for current user.
26. **POST `/api/notifications/{id}/acknowledge`** – mark alert as read / handled.

---

## Category 6 • Analytics & Reports (2)

27. **GET `/api/analytics/sleep-report`** – AI‑generated nightly summary (sleep stages, posture map, score).
28. **GET `/api/analytics/health-summary`** – consolidated vitals trends & flagged anomalies (e.g., apnea index, HRV).  
    _Role:_ Patient / Doctor; Admin can access for system testing.

---

## Category 7 • System & Device Management (2)

29. **GET `/api/system/status`** – uptime, firmware version, sensor health of a mattress unit (Admin, Doctor for assigned patients).
30. **POST `/api/system/reboot`** – trigger remote restart / soft reset of device (Admin only).

---

### Why these 30?

-   They cover **core flows**: secure auth, user CRUD, catalog exposure, real‑time data ingest, two‑way comms, insights, and ops.
-   Everything else—advanced analytics slices, sprawling sensor micro‑endpoints, bulk exports—can be layered on later without breaking existing contracts.
