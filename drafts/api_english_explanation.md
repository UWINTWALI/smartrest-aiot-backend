### 1. Welcome Message (`GET /`)

**Purpose and Functionality:**
This endpoint serves as the most basic entry point to the API. When a user or system sends a `GET` request to the root URL of the API (e.g., `http://smartrest.com/`), it returns a simple welcome message. Its primary purpose is to confirm that the API is running and accessible. It's often used for health checks by monitoring services or as a first quick test during development to ensure the server is responsive. It doesn't perform any complex operations or require any input data, acting as a digital handshake to confirm the API's presence and basic operational status. This simplicity makes it an ideal target for automated uptime checks.

**Accepted Request Data:**
None. This endpoint does not accept any parameters or request body. It is designed to be called without any specific input.

**Expected Response (Typically `200 OK`):**
A JSON object containing a simple message:

```json
{
    "message": "Welcome to SmartRest IoT API"
}
```

This response is standardized to allow for easy parsing by automated tools.

**Underlying Process (What Happens Behind the Scenes):**
When a request reaches this endpoint, the API's routing mechanism identifies it as the root path. A predefined, static response, usually a small JSON object with a welcome message, is immediately constructed and sent back to the client with an HTTP `200 OK` status. There's no database interaction, authentication check, or business logic involved. It's a lightweight way to signal API availability, consuming minimal server resources while providing essential feedback.

### 2. API Version Information (`GET /v1` or `GET /api`)

**Purpose and Functionality:**
Similar to the root welcome message, this endpoint (often `/api` or `/api/v1`, with `/v1` explicitly defined in `api.php` for SmartRest) provides information about the specific version of the API being accessed. This is crucial in systems where multiple API versions might coexist, allowing clients to verify they are interacting with the expected version. It helps in managing API evolution, allowing for new features or breaking changes to be introduced in new versions while older versions remain operational for a transition period. For the SmartRest IoT API, accessing `/v1` confirms that the client is interacting with Version 1 of the API, ensuring compatibility.

**Accepted Request Data:**
None. This endpoint does not require any input parameters or a request body. It is a purely informational GET request.

**Expected Response (Typically `200 OK`):**
A JSON object indicating the API version and its status:

```json
{
    "message": "SmartRest IoT API v1 - Ready to serve your requests"
}
```

This message clearly states the API version and its readiness.

**Underlying Process (What Happens Behind the Scenes):**
Upon receiving a `GET` request to this version-specific path (e.g., `/v1`), the API router directs it to a handler that returns a predefined JSON response. This response contains a message confirming the API version and its operational status. Like the root endpoint, this involves no complex processing, authentication, or database lookups. It's a straightforward informational endpoint designed for quick verification of API version and availability, helping client applications ensure they are using the correct interface.

## Test Endpoints

### 1. Test Email Sending (`GET /test-email` or `GET /api/test/email`)

**Purpose and Functionality:**
This endpoint is specifically designed for development and testing phases to verify the email sending functionality of the SmartRest system. When a developer or an automated test accesses this endpoint (via a `GET` request to `/test-email` or `/api/test/email`), it triggers the system to send a pre-configured test email. This allows developers to confirm that the mail server settings (like SMTP host, port, credentials) are correctly configured in the application's environment and that the email transmission pipeline is working as expected. It's a vital tool for debugging email-related features such as registration confirmations, password reset emails, or system notifications, without needing to go through the full user workflow or affect real user data.

**Accepted Request Data:**
Typically, this endpoint does not require specific request data if the test recipient and email content are hardcoded or configured within the development environment (e.g., in the `.env` file). The primary goal is to test the sending mechanism itself. The route definition suggests a simple `GET` request without explicit parameters.

**Expected Response (Typically `200 OK` on success, or an error status like `500 Internal Server Error` on failure):**

-   If the email is dispatched successfully by the system, the API usually returns a JSON response with a success message, for example:
    ```json
    {
        "message": "Test email sent successfully. Please check the configured test inbox."
    }
    ```
-   If there's an issue with the mail configuration (e.g., incorrect SMTP credentials, mail server unreachable) or an error during the sending process, it would return an error response, often with an HTTP `500` status code, and potentially an error message detailing the problem encountered.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Request Reception:** The API receives the `GET` request at the designated test email path.
2.  **Controller Action:** The request is routed to the `TestController`'s `testEmail` method.
3.  **Email Construction:** Inside this method, a sample email is constructed. This might involve using a specific test email template, a predefined subject line (e.g., "SmartRest System - Test Email"), and sample body content.
4.  **Recipient Determination:** The recipient email address for this test email is typically fetched from the application's configuration (e.g., a `MAIL_FROM_ADDRESS` or a dedicated test email address specified in the `.env` file).
5.  **Mail Dispatch Attempt:** The system uses Laravel's configured mail driver (e.g., SMTP, Log, Mailgun, Sendgrid) to attempt to send the constructed email to the determined recipient. This involves connecting to the mail service, authenticating if required, and transmitting the email data.
6.  **Response Generation:** If the mail system reports success in queuing or sending the email, the API returns a `200 OK` response with a success message. If any part of the process fails (e.g., mail configuration error, connection timeout, authentication failure with the mail server), an error is usually logged internally, and an appropriate error response (e.g., `500 Internal Server Error`) is sent back to the client, often with an error message indicating the failure point. This allows developers to quickly diagnose and fix issues related to the email subsystem.

## Authentication Endpoints

### 1. Register User (`POST /auth/register`)

**Purpose and Functionality:**
The "Register User" endpoint is the primary gateway for new individuals to create an account within the SmartRest IoT ecosystem. Specifically designed for self-registration, it allows users to sign up with either a "patient" role, typically for those under medical supervision using a SmartRest bed in a healthcare facility, or a "customer" role, for individuals who have purchased a SmartRest bed for personal home use. This endpoint facilitates the initial onboarding process, collecting essential user information and establishing their identity within the system. Successful registration is a prerequisite for accessing most other features and functionalities of the SmartRest platform. The system ensures that only designated roles can self-register, maintaining a level of control over account creation for more privileged roles like doctors or administrators, which are likely managed through a separate administrative interface. This initial step is crucial for personalizing the user experience and ensuring data privacy and security from the outset.

**Accepted Request Data:**
To register, a user must send data in the body of their `POST` request, typically as a JSON object. This data must include:

-   `first_name` (string): The user's first name. This is a mandatory field for personal identification.
-   `last_name` (string): The user's last name, also mandatory for identification. (Note: `api_documentation.md` only lists `first_name` and `role`, but a full registration typically requires more. `api.php` points to `AuthController@register` which would handle the full logic, likely expecting email and password as well).
-   `email` (string): A unique email address that will serve as the primary username and the main channel for important communications like verification and notifications. The system validates this for correct email format and uniqueness.
-   `password` (string): A password chosen by the user for securing their account. The backend typically enforces complexity requirements (e.g., minimum length, inclusion of uppercase, lowercase, numbers, and special characters) to ensure strong passwords.
-   `password_confirmation` (string): A repetition of the chosen password. The backend compares this with the `password` field to prevent typos during password entry.
-   `role` (string, enum: "patient" or "customer"): This field explicitly defines the type of user account being created. The system is designed to restrict self-registration to only these two roles.

**Expected Response (Typically `201 Created`):**
Upon successful registration, the API responds with a JSON object containing:

-   `message` (string): A clear confirmation message, often indicating success and guiding the user on the next steps, for example, "Registration successful. Please check your email to verify your account."
-   `user` (object): An object containing details of the newly created user, such as their ID, first name, last name, email, and role.
-   `token` (string): An authentication token (commonly a JSON Web Token - JWT). This token allows the user to immediately make further requests to protected parts of the API, such as initiating the email verification process.

**Underlying Process (What Happens Behind the Scenes):**
When a registration request is received, several actions occur:

1.  **Input Validation:** The system validates all incoming data (e.g., presence of required fields, email format, password strength, role eligibility).
2.  **Duplicate Email Check:** It checks if the provided email already exists in the `users` table.
3.  **User Creation:** If valid and unique, a new user record is created. The password is securely hashed (e.g., using bcrypt) before being stored. The `is_email_verified` field is set to `false`.
4.  **Profile Creation:** Depending on the `role`, an associated profile (e.g., `patient_profiles`) might be created.
5.  **Email Verification Trigger:** A unique verification token/link is generated, and an email is sent to the user's address.
6.  **Token Generation:** An access token (JWT) is generated for the new user.
7.  **Response:** The API returns a success message, user details, and the access token. If any step fails, an appropriate error (e.g., `422 Unprocessable Entity`, `409 Conflict`) is returned.

### 2. Verify Email (`GET /auth/verify-email/{id}/{hash}`)

**Purpose and Functionality:**
This endpoint confirms a user's email address after registration. When a user registers, they receive an email with a unique verification link. This link, when clicked, directs the user to this endpoint. The `id` in the URL identifies the user, and the `hash` is a security token to validate the request's authenticity. Successfully verifying the email often activates the user's account or enables full functionality. This step is crucial for ensuring the user owns the email address, preventing spam, and enabling reliable communication. The endpoint is protected by `auth:sanctum` (user must be logged in, likely with the token from registration) and `signed` (URL integrity) middleware.

**Accepted Request Data:**
The data is passed via URL path segments:

-   `id` (e.g., UUID or integer): The unique identifier of the user.
-   `hash` (string): A cryptographic hash or token ensuring the link's validity.
    No request body is needed for this `GET` request.

**Expected Response (Typically `200 OK`):**

-   Success: `{"message": "Email verified successfully"}` or `{"message": "Email already verified"}`.
-   Failure (invalid link, expired, etc.): Error responses like `401 Unauthorized`, `403 Forbidden`, or `404 Not Found`, with messages such as `{"message": "Invalid verification link."}`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Middleware Execution:** The `signed` middleware validates the URL's signature and expiration. The `auth:sanctum` middleware ensures an authenticated user is making the request.
2.  **User Lookup:** The system retrieves the user based on the `id`.
3.  **Hash Validation:** The provided `hash` is validated against the user's details and the expected signature.
4.  **Verification Status Update:** If valid and not already verified, the user's `is_email_verified` status in the database is set to `true`, and often an `email_verified_at` timestamp is recorded.
5.  **Event Dispatch (Optional):** An `EmailVerified` event might be triggered for other system actions.
6.  **Response:** A success or error message is returned. This secure flow ensures only legitimate verifications occur.

### 3. Login (`POST /auth/login`)

**Purpose and Functionality:**
The "Login" endpoint is the cornerstone of user authentication, allowing registered users to securely access their accounts and the protected features of the SmartRest IoT system. By submitting their credentials (email and password), users prove their identity. If the credentials are valid, the system issues an authentication token (typically a JWT), which acts as a digital key for subsequent interactions with the API. This process ensures that sensitive data and functionalities are only accessible to authorized individuals. A successful login establishes a session (represented by the token) that usually has a limited duration for security reasons, after which the user might need to refresh the token or log in again. This endpoint is heavily used and critical for the application's security.

**Accepted Request Data:**
A `POST` request with a JSON body containing the user's credentials:

-   `email` (string): The email address used during registration. This field is mandatory and must be a valid email format.
-   `password` (string): The user's password. This field is mandatory. It is sent over HTTPS to ensure it's encrypted in transit.

**Expected Response (Typically `200 OK` on success, `401 Unauthorized` or `422 Unprocessable Entity` on failure):**

-   **Successful Login:**
    -   `message` (string): A confirmation, e.g., `"Login successful"`.
    -   `token` (string): A new JWT access token. The client application must store this token securely and include it in the `Authorization` header (as a Bearer token) for all future requests to protected endpoints.
    -   `user` (object, optional): Sometimes, basic details of the logged-in user (ID, name, role) are returned for convenience.
-   **Failed Login:**
    -   Invalid credentials: HTTP `401 Unauthorized` with `{"message": "Invalid credentials"}` or `{"error": "These credentials do not match our records."}`.
    -   Input validation errors (e.g., missing email): HTTP `422 Unprocessable Entity` with error details.
    -   Account issues (e.g., not verified, locked): HTTP `403 Forbidden` with an explanatory message.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Input Validation:** The system first validates that `email` and `password` are provided and the email is correctly formatted. Failure leads to a `422` response.
2.  **User Lookup:** The database is queried for a user matching the provided `email`. If no user is found, a generic `401` error is returned to prevent attackers from guessing valid email addresses.
3.  **Account Status Check:** If a user is found, the system may check if the account is active, verified (if required for login), or not locked. If there's an issue, a `403` error might be returned.
4.  **Password Verification:** The plain-text `password` from the request is hashed using the same algorithm (e.g., bcrypt) and parameters as when the user's original password was stored. This new hash is then compared to the stored password hash in the database. This is a critical security step; plain passwords are never stored or compared directly.
5.  **Attempt Logging:** Login attempts (both successful and failed) are often logged for security auditing and to detect suspicious activities like brute-force attacks. Rate limiting might be applied based on failed attempts.
6.  **Token Generation:** If password verification succeeds, a new JWT access token is generated. This token contains claims like user ID, roles, and an expiration time, and is digitally signed by the server.
7.  **Update Last Login (Optional):** The user's `last_login_at` timestamp in the database might be updated.
8.  **Response:** A `200 OK` response with the success message and the access token is sent. The client is then responsible for managing this token for the session.

### 4. Logout (`POST /auth/logout`)

**Purpose and Functionality:**
The "Logout" endpoint allows an authenticated user to terminate their current session with the SmartRest IoT system. When a user logs out, the authentication token they have been using should be invalidated on the server-side if possible (especially for stateful tokens or by using a token blocklist mechanism for JWTs). This is a crucial security feature, ensuring that a compromised token cannot be used indefinitely if, for example, a user's device is lost or they are using a shared computer. This endpoint requires the user to be authenticated, meaning they must provide their current valid access token with the request.

**Accepted Request Data:**
This endpoint typically does not require a request body. The authentication is handled via the JWT token sent in the `Authorization` header of the `POST` request.

**Expected Response (Typically `200 OK`):**

-   A JSON response with a success message, such as:
    ```json
    {
        "message": "Logout successful"
    }
    ```
-   If the user is not authenticated or the token is invalid, a `401 Unauthorized` response is usually returned.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication Check:** The `auth:sanctum` middleware verifies the JWT token provided in the request's `Authorization` header. If the token is invalid or missing, a `401` error is returned.
2.  **Token Invalidation:** This is the core of the logout process. For Laravel Sanctum using tokens, the current access token used for the request is typically deleted from the `personal_access_tokens` table (or equivalent storage for tokens). This effectively revokes the token, so it can no longer be used to access protected resources. If using JWTs in a stateless manner, true server-side invalidation can be more complex and might involve a token blocklist (e.g., storing the token ID in a cache like Redis with its expiry time until it naturally expires).
3.  **Session Cleanup (If Applicable):** If there are any server-side session data associated with the token (though JWTs are often used for stateless sessions), it might be cleared.
4.  **Response Generation:** The API returns a `200 OK` response with a success message, confirming that the user has been logged out. The client application should then discard the stored token and clear any user-specific data from its local state.

### 5. Get Current User Profile (`GET /auth/me`)

**Purpose and Functionality:**
The "Get Current User Profile" endpoint (`/auth/me`) allows an authenticated user to retrieve their own profile information. After a user logs in and obtains an access token, they can use this endpoint to fetch details associated with their account, such as their name, email, role, and any role-specific information (e.g., patient ID for a patient, specialty for a doctor). This is commonly used by client applications to display user-specific information, personalize the user interface, or make decisions based on the user's role and permissions. It ensures that users can only access their own data, maintaining privacy and security.

**Accepted Request Data:**
No request body is needed. The user's identity is determined from the valid authentication token (JWT) sent in the `Authorization` header of the `GET` request.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing the authenticated user's full profile information. The structure of this object (`User` type) will vary based on the user's role and the data model. For example:
    ```json
    // Example for a patient
    {
        "user_id": "uuid-goes-here",
        "email": "patient@example.com",
        "role": "patient",
        "first_name": "John",
        "last_name": "Doe",
        "phone": "123-456-7890",
        "is_email_verified": true,
        "profile": {
            // Patient-specific profile data
            "national_id": "NATIONALID123",
            "date_of_birth": "1990-01-01",
            "sex": "M"
        },
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
    ```
-   If the user is not authenticated or the token is invalid, a `401 Unauthorized` response is returned.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication:** The `auth:sanctum` middleware validates the access token from the `Authorization` header. If invalid, it returns a `401` error.
2.  **User Retrieval:** If the token is valid, the system extracts the user identifier (e.g., user ID) from the token.
3.  **Data Fetching:** The system queries the database (e.g., `users` table and related profile tables like `patient_profiles` or `doctor_profiles`) to retrieve all relevant information for that user. This often involves eager loading related models to include all necessary profile details based on the user's role.
4.  **Data Formatting/Serialization:** The retrieved user data is formatted into a structured JSON response. Sensitive information not meant for the user profile (like password hashes) is excluded.
5.  **Response:** The API returns the user's profile data.

### 6. Refresh Token (`POST /auth/refresh`)

**Purpose and Functionality:**
The "Refresh Token" endpoint (`/auth/refresh`) is used to obtain a new access token when the current one is about to expire or has just expired. Access tokens are typically short-lived for security reasons. Instead of forcing the user to log in again frequently, a refresh mechanism allows client applications to request a new access token, usually by presenting a valid, longer-lived refresh token (though Sanctum's default token behavior might differ, often relying on the existing valid token to issue a new one or using session-based refresh). This endpoint helps maintain a seamless user experience by extending the session without requiring re-authentication with credentials, provided the refresh condition is met.

**Accepted Request Data:**
This endpoint requires the user to be authenticated, meaning a valid (or recently expired but still refreshable, depending on the strategy) access token must be sent in the `Authorization` header of the `POST` request. Some systems might require a separate refresh token to be sent in the request body, but the `api_documentation.md` implies it works with the existing auth token.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing a new access token:
    ```json
    {
        "token": "new_jwt_access_token_string"
        // Optionally, may also include new token's expiry time
    }
    ```
-   If the refresh attempt fails (e.g., token is too old, revoked, or user is not authenticated), an error response like `401 Unauthorized` or `403 Forbidden` is returned, often requiring the user to log in again.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication/Token Validation:** The `auth:sanctum` middleware (or custom logic) validates the incoming token. The system checks if the token is valid for refresh (e.g., within a specific grace period after expiry, or if it's a special refresh token).
2.  **Old Token Invalidation (Optional but Recommended):** If a new token is issued, the old access token (and potentially the refresh token, if a new one is also issued) might be invalidated or marked as used to prevent replay attacks or misuse. Sanctum might handle this by simply issuing a new token and the old one expires naturally or is deleted if it was a personal access token being re-issued.
3.  **New Token Generation:** If the refresh criteria are met, the system generates a brand new access token with a new expiration time. This new token will contain the same user identity and claims as the original, but with updated issuance (`iat`) and expiration (`exp`) timestamps.
4.  **Response:** The API returns a `200 OK` response with the new access token. The client application should then replace its stored access token with this new one and use it for future API requests. This process allows for extended user sessions without compromising security by frequently rotating the short-lived access tokens.

### 7. Forgot Password (`POST /auth/forgot-password`)

**Purpose and Functionality:**
The "Forgot Password" endpoint (`/auth/forgot-password`) initiates the process for a user who has forgotten their password to securely reset it. When a user provides their registered email address to this endpoint, the system typically generates a unique, time-sensitive password reset token (or link) and sends it to that email address. This process does not immediately change the password but provides the user with a secure way to prove their identity (by accessing their email) and proceed to set a new password via a separate "Reset Password" endpoint. This is a standard and essential feature for account recovery.

**Accepted Request Data:**
A `POST` request with a JSON body containing:

-   `email` (string): The email address of the user who wishes to reset their password. This email must be registered in the system.

**Expected Response (Typically `200 OK` or `202 Accepted`):**

-   A JSON response with a message indicating that if the email exists, a password reset link has been sent. For security reasons, the message is often generic to avoid confirming whether an email address is registered or not. Example:
    ```json
    {
        "message": "If your email address exists in our system, you will receive a password reset link shortly."
        // Or from api_documentation.md: { status: 'We have emailed your password reset link!' }
    }
    ```
-   If input validation fails (e.g., email not provided or invalid format), a `422 Unprocessable Entity` response with error details.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Input Validation:** The system validates that the provided `email` is in a correct format.
2.  **User Lookup:** It checks if a user account exists with the given `email` address in the `users` database.
3.  **Token Generation (If User Exists):** If the user exists and their account is active:
    a. A unique, cryptographically secure password reset token is generated.
    b. This token is typically stored in a dedicated database table (e.g., `password_resets`) associated with the user's email and an expiration timestamp (e.g., valid for 1 hour).
4.  **Email Dispatch:** An email is composed containing a link to the "Reset Password" page/endpoint of the application. This link includes the generated reset token as a parameter (e.g., `https://your-app.com/reset-password?token=RESET_TOKEN_HERE&email=USER_EMAIL`).
5.  **Response:** The API returns a success message. Crucially, even if the email address is not found, the system often returns a similar generic success message. This is a security measure to prevent attackers from using this endpoint to discover which email addresses are registered in the system (user enumeration). The actual email is only sent if the user exists.

### 8. Reset Password (`POST /auth/reset-password`)

**Purpose and Functionality:**
The "Reset Password" endpoint (`/auth/reset-password`) is the second part of the password recovery process. After a user has requested a password reset via the "Forgot Password" endpoint and received an email with a reset token, they use this endpoint to set a new password. The user provides their email, the reset token they received, and their new desired password (along with its confirmation). The system validates the token and, if valid, updates the user's password in the database with the new one. This allows users to regain access to their accounts after forgetting their original password.

**Accepted Request Data:**
A `POST` request with a JSON body containing:

-   `token` (string): The password reset token received by the user via email.
-   `email` (string): The user's email address (often required to match against the token).
-   `password` (string): The new password the user wants to set. This should meet the application's password complexity requirements.
-   `password_confirmation` (string): A confirmation of the new password, which must match the `password` field.

**Expected Response (Typically `200 OK` on success, error on failure):**

-   **Successful Password Reset:**
    ```json
    {
        "message": "Password reset successfully. You can now login with your new password."
        // Or from api_documentation.md: { status: 'Password has been reset.' }
    }
    ```
-   **Failed Password Reset:**
    -   Invalid or expired token: `400 Bad Request` or `422 Unprocessable Entity` with a message like `{"message": "Invalid or expired password reset token."}` or `{"email": ["This password reset token is invalid."]}`.
    -   Password validation failure (e.g., too short, no match): `422 Unprocessable Entity` with details on password errors.
    -   Email not found or doesn't match token: Error indicating the mismatch.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Input Validation:** The system validates all inputs: presence of `token`, `email`, `password`, `password_confirmation`; email format; password complexity and confirmation match.
2.  **Token Validation:** The system checks the `password_resets` table (or equivalent) for an entry matching the provided `email` and `token`. It also verifies that the token has not expired. If no valid token is found, an error is returned.
3.  **User Lookup:** The system retrieves the user account associated with the email address.
4.  **Password Update:** If the token is valid and the user exists:
    a. The new `password` is securely hashed (e.g., using bcrypt).
    b. The user's record in the `users` table is updated with this new hashed password.
5.  **Token Invalidation/Deletion:** After a successful password reset, the used password reset token is deleted or marked as invalid in the `password_resets` table to prevent it from being used again.
6.  **Event Dispatch/Notification (Optional):** An event might be dispatched, or the user might be sent an email notification about the password change for security awareness.
7.  **Response:** A success message is returned. If any step fails, an appropriate error response is generated.

### 9. Change Password (`POST /auth/change-password`)

**Purpose and Functionality:**
The "Change Password" endpoint (`/auth/change-password`) allows an authenticated (already logged-in) user to change their current password to a new one. This is different from "Reset Password" because it requires the user to know their current password as a security measure. It's a common feature for users who want to update their password proactively for security reasons or if they suspect their current password might have been compromised. This endpoint is protected and requires a valid authentication token.

**Accepted Request Data:**
A `POST` request, made by an authenticated user, with a JSON body containing:

-   `current_password` (string): The user's existing (old) password.
-   `new_password` (string): The new password the user wishes to set. This must meet complexity requirements.
-   `new_password_confirmation` (string): Confirmation of the new password, must match `new_password`.

**Expected Response (Typically `200 OK` on success, error on failure):**

-   **Successful Password Change:**
    ```json
    {
        "message": "Password changed successfully."
    }
    ```
-   **Failed Password Change:**
    -   Incorrect `current_password`: `401 Unauthorized` or `422 Unprocessable Entity` with a message like `{"message": "Current password does not match."}`.
    -   `new_password` validation failure (e.g., too weak, doesn't match confirmation): `422 Unprocessable Entity` with error details.
    -   User not authenticated: `401 Unauthorized`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication:** The `auth:sanctum` middleware verifies the user's access token.
2.  **Input Validation:** The system validates that `current_password`, `new_password`, and `new_password_confirmation` are provided, and that `new_password` meets complexity rules and matches its confirmation.
3.  **Current Password Verification:** The system retrieves the authenticated user's record. The provided `current_password` is hashed and compared against the user's stored password hash. If they do not match, the request is rejected. If they do match, the system proceeds to validate the `new_password`: it checks that `new_password` and `new_password_confirmation` are identical and that the `new_password` meets complexity requirements. If these checks also pass, the `new_password` is securely hashed, and this new hash updates the `password_hash` field in the `users` table for the authenticated user. As a security measure, after a successful password change, the system might invalidate all other active sessions or tokens for that user, forcing them to log in again on all devices with the new password.
4.  **Response:** A success message is returned. If any validation or verification fails, an appropriate error response is generated.

### 10. Social Login (`POST /auth/social-login`)

**Purpose and Functionality:**
The "Social Login" endpoint (`/auth/social-login`) enables users to register or log in to the SmartRest IoT system using their existing accounts from third-party social providers like Google, Facebook, Apple, etc. This offers a convenient and often faster alternative to traditional email/password registration and login. The user authenticates with the social provider, which then provides an authorization code or access token to the client application. The client sends this token to the `/auth/social-login` endpoint. The backend then verifies this token with the social provider, retrieves user information (like name and email), and either creates a new user account or logs in an existing user linked to that social identity.

**Accepted Request Data:**
A `POST` request with a JSON body, typically containing:

-   `provider` (string): The name of the social provider (e.g., "google", "facebook").
-   `token` (string): An access token or authorization code obtained from the social provider after the user authenticated with them. (The exact token type depends on the OAuth flow used).
-   Optionally, other provider-specific data might be required.

**Expected Response (Typically `200 OK` on success, error on failure):**

-   **Successful Social Login/Registration:**
    -   `message` (string): A confirmation message, e.g., `"Login successful"`.
    -   `token` (string): A new JWT access token. The client application must store this token securely and include it in the `Authorization` header (as a Bearer token) for all future requests to protected endpoints.
    -   `user` (object, optional): Details of the logged-in or newly created user.
-   **Failed Social Login:**
    -   Invalid provider token: `401 Unauthorized` or `422 Unprocessable Entity` with a message like `{"message": "Invalid social provider token."}`.
    -   Provider error: Error response indicating issues communicating with or validating with the social provider.
    -   Email already exists with a different login method: `409 Conflict` if the social email is tied to a password-based account, potentially prompting the user to link accounts.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Input Validation:** The system validates the `provider` name and the presence of the `token`.
2.  **Provider Interaction (Token Verification):** The backend uses a library (like Laravel Socialite) to communicate with the specified social `provider`. It sends the `token` (provider's access token) to the provider's API to verify its authenticity and retrieve the user's profile information (e.g., unique ID, name, email).
3.  **User Lookup/Creation:**
    a. The system checks if a user in the `users` table is already associated with the unique ID from the social provider. If yes, that user is considered logged in.
    b. If no existing user is found by provider ID, it may check if the email address retrieved from the social provider already exists in the `users` table.
    i. If the email exists (and was perhaps registered via password), the system might link the social account to the existing user or return an error/prompt for account linking.
    ii. If the email does not exist, a new user record is created in the `users` table using the information from the social provider (name, email). A placeholder or randomly generated password hash might be stored as direct password login is not used for this social identity. The `is_email_verified` field is often set to `true` as social providers usually verify emails.
4.  **SmartRest API Token Generation:** Once the user is identified or created, a new JWT access token for the SmartRest IoT API is generated for this session.
5.  **Response:** A success message, the SmartRest API token, and user details are returned. If any step fails (e.g., provider token invalid, provider API error), an appropriate error response is generated.

## User Management Endpoints

These endpoints are typically protected and require administrator privileges or specific user roles to access, ensuring that user data is managed securely and appropriately. They follow standard CRUD (Create, Read, Update, Delete) operations.

### 1. List Users (`GET /users`)

**Purpose and Functionality:**
The "List Users" endpoint (`GET /users`) is designed to retrieve a collection of user records from the system. This is typically an administrative function, allowing authorized personnel (like administrators) to view a list of all users or a paginated subset. It can be used for user auditing, management dashboards, or searching for specific users. The response usually includes key information for each user but might exclude highly sensitive details. Filters and pagination parameters are often supported to manage large datasets.

**Accepted Request Data:**
This `GET` request typically doesn't require a body. It might accept optional query parameters for pagination or filtering, such as:

-   `page` (integer): For pagination, specifying the page number to retrieve.
-   `per_page` (integer): For pagination, specifying the number of users per page.
-   `role` (string): To filter users by a specific role (e.g., "patient", "doctor").
-   `search` (string): A search term to find users by name, email, etc.
    Authentication (e.g., admin role) is required via an access token in the `Authorization` header.

**Expected Response (Typically `200 OK`):**

-   A JSON array of user objects. If paginated, the response is often wrapped in an object containing the user data array and pagination metadata (total users, current page, per page, links to next/previous pages).
    ```json
    {
      "data": [
        { "user_id": "uuid1", "first_name": "Admin", "last_name": "User", "email": "admin@example.com", "role": "admin", ... },
        { "user_id": "uuid2", "first_name": "Doctor", "last_name": "Who", "email": "doctor@example.com", "role": "doctor", ... }
      ],
      "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
      "meta": { "current_page": 1, "from": 1, "last_page": 5, "path": "...", "per_page": 15, "to": 15, "total": 75 }
    }
    ```
-   If not authorized, a `403 Forbidden` or `401 Unauthorized` response.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** The `auth:sanctum` middleware and potentially role-based authorization checks ensure the requesting user is logged in and has permission (e.g., is an admin) to list users.
2.  **Query Parameter Processing:** Any provided query parameters (for pagination, filtering, searching) are parsed and validated.
3.  **Database Query:** The system queries the `users` table (and possibly related profile tables) to fetch the list of users according to the applied filters and pagination. Search queries might involve `LIKE` clauses on multiple fields.
4.  **Data Serialization:** The retrieved user data is formatted into JSON. Sensitive data (like password hashes) is excluded.
5.  **Response:** The API returns the list of users.

### 2. Get User Details (`GET /users/{userId}`)

**Purpose and Functionality:**
The "Get User Details" endpoint (`GET /users/{userId}`) retrieves the complete profile information for a specific user, identified by their `userId`. This is typically used by administrators to view the details of a particular user or by users to view their own extended profile if this endpoint is also used for that (though `/auth/me` is more common for self-profile viewing). It provides a comprehensive look at a single user's data stored in the system.

**Accepted Request Data:**

-   `userId` (path parameter, e.g., UUID): The unique identifier of the user whose details are being requested.
    Authentication (e.g., admin role, or if the user is requesting their own data) is required via an access token.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing the full details of the specified user, similar in structure to the response from `/auth/me` but potentially with more administrative-level information if accessed by an admin.
    ```json
    {
        "user_id": "specific-uuid",
        "email": "user@example.com",
        "role": "patient",
        "first_name": "Specific",
        "last_name": "User"
        // ... other user fields and profile details
    }
    ```
-   If the user is not found: `404 Not Found`.
-   If not authorized: `403 Forbidden` or `401 Unauthorized`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** The system verifies the requester's token and checks if they have permission to view the specified user's details (e.g., admin, or the user themselves).
2.  **User Lookup:** The system queries the database for a user with the given `userId`. If not found, a `404` error is returned.
3.  **Data Fetching & Serialization:** If the user is found, their data (from `users` table and any associated profile tables) is retrieved and formatted into a JSON response, excluding sensitive fields like password hashes.
4.  **Response:** The API returns the user's detailed profile.

### 3. Create User (`POST /users`)

**Purpose and Functionality:**
The "Create User" endpoint (`POST /users`) allows authorized personnel (typically administrators) to create new user accounts. This is different from the public `/auth/register` endpoint as it might allow creating users with any role (including 'doctor' or 'admin') and potentially bypass certain self-registration steps like immediate email verification (though an admin might trigger that separately). This is used for managed onboarding of users who do not self-register.

**Accepted Request Data:**
A `POST` request with a JSON body containing the new user's details:

-   `first_name` (string): First name.
-   `last_name` (string): Last name.
-   `email` (string): Unique email address.
-   `password` (string): Password for the new user (admin might set an initial one).
-   `password_confirmation` (string): Confirmation of the password.
-   `role` (string, enum: "patient", "customer", "doctor", "admin"): The role for the new user.
-   Other role-specific fields might be included (e.g., `license_no` for a doctor).

**Expected Response (Typically `201 Created`):**

-   A JSON object containing the details of the newly created user, similar to the registration response.
    ```json
    {
        "message": "User created successfully.",
        "user": {
            /* ... new user details ... */
        }
    }
    ```
-   If validation fails (e.g., email exists, invalid role): `422 Unprocessable Entity`.
-   If not authorized: `403 Forbidden` or `401 Unauthorized`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Ensures the requester is an authorized admin.
2.  **Input Validation:** Validates all provided data (required fields, email uniqueness, password strength, valid role).
3.  **User Creation:** If valid, a new user record is created in the `users` table (password is hashed).
4.  **Profile Creation:** Associated profiles (e.g., `doctor_profiles`) are created based on the role.
5.  **Notification (Optional):** The newly created user might be sent an email with their account details and a temporary password or a link to set their password.
6.  **Response:** Returns a success message and the created user's data.

### 4. Update User (`PUT /users/{userId}`)

**Purpose and Functionality:**
The "Update User" endpoint (`PUT /users/{userId}`) allows authorized personnel (administrators, or users updating their own profiles if permissions allow for certain fields) to modify the details of an existing user. This can include changing their name, email (with caution, as it's often a primary identifier), role, or role-specific profile information. The entire user object or the fields to be changed are sent in the request body.

**Accepted Request Data:**

-   `userId` (path parameter): The ID of the user to update.
    A `PUT` request with a JSON body containing the fields to be updated. For a `PUT`, typically all fields are expected, but often `PATCH` (not explicitly listed but common) is used for partial updates. Assuming `PUT` means full replacement of modifiable fields:
-   `first_name` (string, optional)
-   `last_name` (string, optional)
-   `email` (string, optional)
-   `role` (string, optional)
-   Other updatable fields. (Password changes are usually handled by dedicated endpoints).
    Authentication and authorization are required.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing the updated user details.
    ```json
    {
        "message": "User updated successfully.",
        "user": {
            /* ... updated user details ... */
        }
    }
    ```
-   If user not found: `404 Not Found`.
-   If validation fails: `422 Unprocessable Entity`.
-   If not authorized: `403 Forbidden` or `401 Unauthorized`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Verifies token and permissions.
2.  **User Lookup:** Finds the user by `userId`.
3.  **Input Validation:** Validates the incoming data for the fields being updated.
4.  **Data Update:** Updates the user's record in the `users` table and any associated profile tables with the new information. If email is changed, re-verification might be triggered.
5.  **Response:** Returns a success message and the updated user data.

### 5. Delete User (`DELETE /users/{userId}`)

**Purpose and Functionality:**
The "Delete User" endpoint (`DELETE /users/{userId}`) allows authorized administrators to permanently remove a user account and their associated data from the system. This is a destructive operation and should be used with caution. Depending on data retention policies and system design, this might perform a soft delete (marking as inactive) or a hard delete (physically removing records).

**Accepted Request Data:**

-   `userId` (path parameter): The ID of the user to delete.
    Admin authentication is required.

**Expected Response (Typically `200 OK` or `204 No Content`):**

-   A success message or no content if the deletion was successful.
    ```json
    {
        "message": "User deleted successfully."
    }
    ```
-   If user not found: `404 Not Found`.
-   If not authorized: `403 Forbidden` or `401 Unauthorized`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Ensures the requester is an admin.
2.  **User Lookup:** Finds the user.
3.  **Deletion Logic:**
    a. **Data Archival (Optional):** Before deletion, user data might be archived if required by policy.
    b. **Dependency Handling:** The system checks for and handles any dependencies (e.g., reassigning data owned by the user, or deleting related data according to cascade rules defined in the database schema like `ON DELETE CASCADE` for `patient_profiles`).
    c. **Deletion Execution:** The user's record is removed from the `users` table (and associated profile tables). This could be a soft delete (setting an `is_active` flag to false or `deleted_at` timestamp) or a hard delete (physically removing the row).
4.  **Response:** Returns a success confirmation.

## Product Catalog Endpoints

These endpoints manage information about the SmartRest bed products. They use Laravel's `apiResource` convention, which maps HTTP verbs and URLs to controller actions.

### 1. List Products (`GET /products`)

**Purpose and Functionality:**
The "List Products" endpoint (`GET /products`, mapped to `ProductController@index`) retrieves a list of available SmartRest bed products. This could be used by customers browsing the product catalog on a website or mobile app, or by administrators managing product inventory. The list might include details like product name, model, features, and price. Pagination and filtering (e.g., by category or features) could be supported.

**Accepted Request Data:**
This `GET` request typically doesn't require a body. It might accept optional query parameters for pagination or filtering, such as:

-   `page` (integer): For pagination, specifying the page number to retrieve.
-   `per_page` (integer): For pagination, specifying the number of users per page.
-   `role` (string): To filter users by a specific role (e.g., "patient", "doctor").
-   `search` (string): A search term to find users by name, email, etc.
    Authentication (e.g., admin role) is required via an access token in the `Authorization` header.

**Expected Response (Typically `200 OK`):**

-   A JSON array of product objects. If paginated, it will be structured similarly to the "List Users" response, with product data and pagination metadata.
    ```json
    {
      "data": [
        { "product_id": "SMARTBED-001", "name": "SmartRest Deluxe", "features": [...], "price": "1999.99", ... },
        { "product_id": "SMARTBED-002", "name": "SmartRest Basic", "features": [...], "price": "999.99", ... }
      ],
      "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
      "meta": { "current_page": 1, "from": 1, "last_page": 5, "path": "...", "per_page": 15, "to": 15, "total": 75 }
    }
    ```
-   If not authorized, a `403 Forbidden` or `401 Unauthorized` response.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication/Authorization (If applicable):** Checks if the requester has permission to view products.
2.  **Database Query:** Fetches product records from the `products` table, applying any filters or pagination.
3.  **Data Serialization:** Formats the product data into JSON.
4.  **Response:** Returns the list of products.

### 2. Create Product (`POST /products`)

**Purpose and Functionality:**
The "Create Product" endpoint (`POST /products`, mapped to `ProductController@store`) allows authorized administrators to add new SmartRest bed products to the catalog. This involves providing all the necessary details for the new product, such as its name, model number, description, features, price, and any other relevant attributes.

**Accepted Request Data:**
A `POST` request with a JSON body containing the new product's details:

-   `product_id` (string, if manually assigned, or could be auto-generated)
-   `name` (string): Product name.
-   `description` (text): Detailed description.
-   `features` (array or JSON): List of product features.
-   `price` (decimal): Product price.
-   Other relevant product attributes.
    Admin authentication is required.

**Expected Response (Typically `201 Created`):**

-   A JSON object containing the details of the newly created product.
    ```json
    {
        "message": "Product created successfully.",
        "product": {
            /* ... new product details ... */
        }
    }
    ```
-   If validation fails: `422 Unprocessable Entity`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Ensures the requester is an admin.
2.  **Input Validation:** Validates all provided product data (e.g., required fields, data types).
3.  **Product Creation:** If valid, a new record is inserted into the `products` table.
4.  **Response:** Returns a success message and the created product's data.

### 3. Get Product Details (`GET /products/{product}`)

**Purpose and Functionality:**
The "Get Product Details" endpoint (`GET /products/{product}`, mapped to `ProductController@show`) retrieves detailed information for a specific SmartRest bed product, identified by its `product` ID (e.g., `product_id` like "SMARTBED-001"). This is used to display a product page with all its specifications, images, and pricing to a customer or for an admin to review a specific item.

**Accepted Request Data:**

-   `product` (path parameter): The unique identifier of the product to retrieve.
    Authentication might be required depending on catalog visibility.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing the full details of the specified product.
    ```json
    {
        "product_id": "SMARTBED-001",
        "name": "SmartRest Deluxe"
        // ... all other product attributes
    }
    ```
-   If product not found: `404 Not Found`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication/Authorization (If applicable).**
2.  **Product Lookup:** Queries the `products` table for the product with the given ID.
3.  **Data Serialization:** Formats the retrieved data into JSON.
4.  **Response:** Returns the detailed product information.

### 4. Update Product (`PUT /products/{product}` or `PATCH /products/{product}`)

**Purpose and Functionality:**
The "Update Product" endpoint (`PUT /products/{product}`, mapped to `ProductController@update`) allows authorized administrators to modify the details of an existing product in the catalog. This could involve changing its name, description, price, features, or availability status. A `PUT` request typically expects the complete representation of the resource for update, while a `PATCH` request (if supported) allows for partial updates of specific fields.

**Accepted Request Data:**

-   `product` (path parameter): The ID of the product to update.
    A `PUT` (or `PATCH`) request with a JSON body containing the fields to be updated.
    Admin authentication is required.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing the updated product details.
    ```json
    {
        "message": "Product updated successfully.",
        "product": {
            /* ... updated product details ... */
        }
    }
    ```
-   If product not found: `404 Not Found`.
-   If validation fails: `422 Unprocessable Entity`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Ensures admin privileges.
2.  **Product Lookup:** Finds the product by `productId`.
3.  **Input Validation:** Validates the incoming data for the fields being updated.
4.  **Data Update:** Updates the product's record in the `products` table.
5.  **Response:** Returns a success message and the updated product data.

### 5. Delete Product (`DELETE /products/{product}`)

**Purpose and Functionality:**
The "Delete Product" endpoint (`DELETE /products/{product}`) allows authorized administrators to remove a product from the catalog. This might be used for discontinued items. Similar to deleting users, this could be a soft delete (marking as inactive or not for sale) or a hard delete (removing the record entirely).

**Accepted Request Data:**

-   `product` (path parameter): The ID of the product to delete.
    Admin authentication is required.

**Expected Response (Typically `200 OK` or `204 No Content`):**

-   A success message or no content if the deletion was successful.
    ```json
    {
        "message": "Product deleted successfully."
    }
    ```
-   If product not found: `404 Not Found`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Confirms admin role.
2.  **Product Lookup:** Finds the product.
3.  **Deletion Logic:**
    a. **Data Archival (Optional):** Before deletion, user data might be archived if required by policy.
    b. **Dependency Handling:** The system checks for and handles any dependencies (e.g., reassigning data owned by the user, or deleting related data according to cascade rules defined in the database schema like `ON DELETE CASCADE` for `patient_profiles`).
    c. **Deletion Execution:** The user's record is removed from the `users` table (and associated profile tables). This could be a soft delete (setting an `is_active` flag to false or `deleted_at` timestamp) or a hard delete (physically removing the row).
4.  **Response:** Confirms successful deletion.

## Sensor Data Endpoints

These endpoints are crucial for the "IoT" aspect of SmartRest, handling data collected from the smart mattress sensors.

### 1. Store Sensor Data (`POST /sensors/data`)

**Purpose and Functionality:**
The "Store Sensor Data" endpoint (`POST /sensors/data`, handled by `SensorController@storeData`) is responsible for receiving and persisting sensor readings transmitted from the SmartRest smart mattresses. The mattresses, equipped with various sensors (e.g., for heart rate, breathing patterns, temperature, movement, pressure), send data periodically or based on events to this endpoint. The system then validates this data and stores it in the database (e.g., `sensor_readings` table) for later analysis, display to users/doctors, or triggering alerts. This is a high-traffic endpoint critical for the core functionality of health monitoring.

**Accepted Request Data:**
A `POST` request with a JSON body containing the sensor readings. The exact structure would depend on the types of sensors and data format defined by the IoT device communication protocol. It likely includes:

-   `bed_id` (string): Unique identifier of the smart bed sending the data.
-   `patient_id` (string, optional): Identifier of the patient using the bed, if applicable.
-   `timestamp` (datetime/integer): The time the reading was taken.
-   `readings` (array of objects or key-value pairs): Sensor data, e.g.,
    `json
{
  "bed_id": "BED001",
  "patient_id": "PATIENT007",
  "timestamp": "2025-05-26T10:00:00Z",
  "data": {
    "heart_rate": 75,
    "respiratory_rate": 16,
    "temperature_mattress": 22.5,
    "movement_level": "low",
    "pressure_map": [ /* ... array of pressure values ... */ ]
  },
  "sensor_type": "pressure", // As per db.sql
  "sensor_value": 50 // As per db.sql
  // "additional_metadata": {} // As per db.sql
}
`
    Authentication for this endpoint is critical, likely using a device-specific token or API key to ensure data integrity and prevent unauthorized submissions. The `api.php` doesn't explicitly show middleware here, but it's a common practice.

**Expected Response (Typically `201 Created` or `202 Accepted`):**

-   A success message indicating the data was received and is being processed.
    ```json
    {
        "message": "Sensor data received successfully."
        // Optionally, a reading_id might be returned.
    }
    ```
-   If validation fails (e.g., missing required fields, invalid data types): `422 Unprocessable Entity`.
-   If unauthorized (e.g., invalid device token): `401 Unauthorized`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication (Device/API Key):** Verifies the source of the data.
2.  **Input Validation:** Checks the structure and validity of the incoming sensor data (e.g., data types, ranges, required fields like `bed_id`, `timestamp`). The `db.sql` shows a `CHECK` constraint for pressure sensor values.
3.  **Data Transformation/Processing (Optional):** Raw sensor data might be processed or transformed into a standardized format before storage.
4.  **Database Storage:** The validated sensor readings are inserted into the `sensor_readings` table. Each reading might get a unique `reading_id`.
5.  **Real-time Analysis/Alerting (Optional):** The incoming data might be fed into a real-time analytics engine to detect anomalies or critical conditions (e.g., high heart rate, no breathing detected) and trigger alerts via the messaging system. This could be handled by an event listener or a queued job.
6.  **Response:** A success confirmation is returned. Due to potentially high volume, the response might be sent before all asynchronous processing (like analytics) is complete (`202 Accepted`).

### 2. Get Sensor History (`GET /sensors/history`)

**Purpose and Functionality:**
The "Get Sensor History" endpoint (`GET /sensors/history`, handled by `SensorController@getHistory`) allows authenticated users (like patients for their own data, or doctors for their assigned patients) to retrieve historical sensor data. This is essential for viewing trends, generating reports, and analyzing sleep patterns or health metrics over time. The endpoint would typically support filtering by date range, sensor type, and user/bed ID.

**Accepted Request Data:**
This `GET` request is protected by `auth:sanctum`. It would accept query parameters for filtering:

-   `patient_id` (string, if a doctor is querying for a specific patient). If a patient is querying, their ID is taken from their token.
-   `bed_id` (string, optional): To filter by a specific bed.
-   `start_date` (date/datetime): Start of the desired period.
-   `end_date` (date/datetime): End of the desired period.
-   `sensor_type` (string, optional): To filter for specific sensor types (e.g., "heart_rate", "temperature").
-   Pagination parameters (`page`, `per_page`).

**Expected Response (Typically `200 OK`):**

-   A JSON array of historical sensor readings, matching the filter criteria. Each object in the array would represent a sensor reading at a specific time.
    ```json
    {
      "data": [
        { "timestamp": "2025-05-26T09:00:00Z", "heart_rate": 72, "respiratory_rate": 15, ... },
        { "timestamp": "2025-05-26T08:00:00Z", "heart_rate": 70, "respiratory_rate": 16, ... }
      ],
      // ... pagination links and meta if applicable
    }
    ```
-   If not authorized or no data found for the criteria: Appropriate error or empty response.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Verifies the user and ensures they have permission to access the requested data (e.g., a patient can only see their own data, a doctor only their patients').
2.  **Query Parameter Processing:** Validates and parses filter parameters.
3.  **Database Query:** Constructs and executes a query against the `sensor_readings` table based on the user's identity and the provided filters (date range, sensor types, etc.). The query would likely order results by timestamp.
4.  **Data Serialization:** Formats the retrieved readings into JSON.
5.  **Response:** Returns the historical sensor data.

### 3. Get Latest Sensor Data (`GET /sensors/latest`)

**Purpose and Functionality:**
The "Get Latest Sensor Data" endpoint (`GET /sensors/latest`, handled by `SensorController@getLatest`) provides quick access to the most recent sensor readings for a specific user/bed. This is useful for dashboards or real-time monitoring views where the current status is important. Instead of fetching a whole history, it just returns the latest snapshot of data.

**Accepted Request Data:**
This `GET` request would likely be protected. It might accept query parameters:

-   `patient_id` (string, if a doctor is querying).
-   `bed_id` (string, optional).
    The `routes_and_return.md` implies this exists, though it's not explicitly in `api.php`'s comments but is a common requirement. Authentication would be similar to `/sensors/history`.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing the latest sensor readings for various metrics.
    ```json
    {
        "timestamp": "2025-05-26T10:00:00Z", // Time of the latest reading set
        "heart_rate": 75,
        "respiratory_rate": 16,
        "temperature_mattress": 22.5,
        "movement_level": "low"
        // ... other latest values
    }
    ```
-   If no data is available or not authorized: Error or empty response.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Similar to `/sensors/history`.
2.  **Query Parameter Processing.**
3.  **Database Query:** Queries the `sensor_readings` table, filtering by user/bed ID, and retrieves the most recent record(s) (e.g., using `ORDER BY timestamp DESC LIMIT 1` for each relevant sensor type or a combined latest record).
4.  **Data Aggregation/Serialization:** If multiple sensor types are involved, the latest reading for each might be aggregated into a single JSON object.
5.  **Response:** Returns the latest sensor data snapshot.

## Messaging & Notification Endpoints

These endpoints facilitate communication within the system, such as messages between users (e.g., patient-doctor) or system-generated notifications/alerts.

### 1. Store Message (`POST /messages`)

**Purpose and Functionality:**
The "Store Message" endpoint (`POST /messages`, handled by `MessageController@store`) allows authenticated users to send messages to other users within the SmartRest platform. This could be used for communication between a patient and their assigned doctor, or for other interpersonal messaging features. The system stores these messages, associating them with sender, recipient, and conversation context.

**Accepted Request Data:**
A `POST` request made by an authenticated user, with a JSON body containing:

-   `recipient_id` (string): The `user_id` of the message recipient.
-   `conversation_id` (string, optional): If part of an existing conversation. If not provided, a new conversation might be initiated.
-   `content` (text): The actual text or content of the message.
-   `message_type` (string, optional, e.g., "chat", "alert_manual"): Type of message.

**Expected Response (Typically `201 Created`):**

-   A JSON object containing the details of the newly created message.
    ```json
    {
        "message_id": "new-message-uuid",
        "sender_id": "current-user-uuid",
        "recipient_id": "recipient-user-uuid",
        "content": "Hello Doctor, I have a question.",
        "sent_at": "timestamp"
        // ... other message details
    }
    ```
-   If validation fails or recipient not found: `422 Unprocessable Entity` or `404 Not Found`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Verifies the sender. Checks if the sender is allowed to message the recipient (e.g., patient can message their assigned doctor).
2.  **Input Validation:** Validates `recipient_id`, `content`, etc.
3.  **Message Creation:** A new record is inserted into the `messages` table with sender ID, recipient ID, content, timestamp, and conversation context.
4.  **Notification to Recipient (Optional):** The recipient might be notified of the new message via a real-time mechanism (WebSocket) or a push notification/email. This is often handled by dispatching an event.
5.  **Response:** Returns the created message data.

### 2. Get Messages for a Conversation (`GET /messages/{conversationId}`)

**Purpose and Functionality:**
The "Get Messages for a Conversation" endpoint (`GET /messages/{conversationId}`, handled by `MessageController@getConversation` or similar) retrieves the history of messages within a specific conversation, identified by `conversationId`. This allows users to view their chat history with another user. Pagination is typically supported for long conversations.

**Accepted Request Data:**

-   `conversationId` (path parameter): The unique identifier of the conversation.
    Authenticated `GET` request, possibly with query parameters for pagination (`page`, `per_page`).

**Expected Response (Typically `200 OK`):**

-   A JSON array of message objects belonging to the conversation, usually ordered by time.
    ```json
    {
        "data": [
            {
                "message_id": "msg-uuid-1",
                "sender_id": "user1",
                "content": "Hi",
                "sent_at": "ts1"
            },
            {
                "message_id": "msg-uuid-2",
                "sender_id": "user2",
                "content": "Hello",
                "sent_at": "ts2"
            }
        ]
        // ... pagination links and meta
    }
    ```

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Verifies the user and ensures they are a participant in the requested `conversationId`.
2.  **Database Query:** Fetches messages from the `messages` table that match the `conversationId`, applying pagination and ordering by `sent_at`.
3.  **Data Serialization:** Formats messages into JSON.
4.  **Response:** Returns the conversation's message history.

### 3. Get Notifications (`GET /api/notifications`)

**Purpose and Functionality:**
The "Get Notifications" endpoint (`GET /api/notifications`, handled by `MessageController@getNotifications`) allows an authenticated user to retrieve their notifications. These notifications can be system-generated (e.g., "Low battery on your SmartRest bed", "New health report available") or user-generated (e.g., "You have a new message from Dr. Smith"). The endpoint usually returns a list of unread or all recent notifications for the user.

**Accepted Request Data:**
An authenticated `GET` request. May support query parameters to filter the data, such as:

-   `status` (string, optional, e.g., "unread", "all").
-   `type` (string, optional, e.g., "health_alert").
-   Pagination parameters.

**Expected Response (Typically `200 OK`):**

-   A JSON array of notification objects for the user.
    ```json
    {
        "data": [
            {
                "notification_id": "notif-uuid-1",
                "type": "alert",
                "message": "Heart rate anomaly detected.",
                "created_at": "ts1",
                "read_at": null,
                "link": "/analytics/health-summary"
            },
            {
                "notification_id": "notif-uuid-2",
                "type": "info",
                "message": "Your sleep report for last night is ready.",
                "created_at": "ts2",
                "read_at": "ts3",
                "link": "/analytics/sleep-report"
            }
        ]
        // ... pagination
    }
    ```

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication:** Verifies the user via their access token.
2.  **Database Query:** Fetches records from a `notifications` table (or similar, could be part of `messages` with a specific type) where the `recipient_id` matches the authenticated user's ID. Applies filters for status and pagination.
3.  **Data Serialization:** Formats notification data into JSON.
4.  **Response:** Returns the list of notifications.

### 4. Acknowledge Notification (`POST /notifications/{id}/acknowledge`)

**Purpose and Functionality:**
The "Acknowledge Notification" endpoint (`POST /notifications/{id}/acknowledge`, handled by `MessageController@acknowledgeNotification`) allows a user to mark a specific notification as read or acknowledged. This changes the notification's status in the system (e.g., sets a `read_at` timestamp) and typically removes it from the "unread" notifications list in the UI.

**Accepted Request Data:**

-   `id` (path parameter): The unique identifier of the notification to acknowledge.
    An authenticated `POST` request. No request body is usually needed.

**Expected Response (Typically `200 OK` or `204 No Content`):**

-   A success message or no content, confirming the acknowledgment.
    ```json
    {
        "message": "Notification acknowledged successfully."
    }
    ```
-   If notification not found or user not authorized to acknowledge it: `404 Not Found` or `403 Forbidden`.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Verifies the user and ensures the notification with `id` belongs to them.
2.  **Notification Lookup:** Finds the notification in the database.
3.  **Status Update:** Updates the notification's record, typically by setting a `read_at` timestamp to the current time or an `is_acknowledged` flag to true.
4.  **Response:** Confirms the action.

## Analytics & Reports Endpoints

These endpoints provide processed data and insights derived from the collected sensor readings and other user information.

### 1. Get Health Summary (`GET /analytics/health-summary`)

**Purpose and Functionality:**
The "Get Health Summary" endpoint (`GET /analytics/health-summary`, handled by `AnalyticsController@getHealthSummary`) provides an authenticated user (patient or their authorized doctor) with a summarized overview of their health metrics derived from the SmartRest bed sensor data. This could include average heart rate, sleep duration, sleep quality score, respiratory patterns, and trends over a recent period (e.g., last week, last month). It aims to give a quick, digestible snapshot of overall well-being as monitored by the bed.

**Accepted Request Data:**
An authenticated `GET` request. May accept query parameters:

-   `patient_id` (string, if a doctor is querying for a specific patient).
-   `period` (string, optional, e.g., "daily", "weekly", "monthly") to specify the summary timeframe.
-   `date` (date, optional): A specific date for which to get a daily summary.

**Expected Response (Typically `200 OK`):**

-   A JSON object containing various health summary metrics.
    ```json
    {
        "period": "weekly",
        "start_date": "2025-05-19",
        "end_date": "2025-05-25",
        "average_sleep_duration_hours": 7.5,
        "average_heart_rate_bpm": 65,
        "sleep_quality_score_percent": 85,
        "respiratory_events_count": 5,
        "heart_rate_trend": "stable" // or "improving", "declining"
        // ... other summary data points and possibly charts data
    }
    ```
-   If the requester is not authorized or data is unavailable, an appropriate error is returned.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Verifies the user and their access rights to the health data (self or assigned patient).
2.  **Parameter Processing:** Validates and interprets query parameters like `period`.
3.  **Data Aggregation & Analysis:** This is the core logic. The system queries the historical `sensor_readings` table for the relevant user and period. It then performs calculations and aggregations (averages, totals, trend analysis) on this data to generate the summary metrics. This might involve complex algorithms or pre-calculated aggregate tables for performance.
4.  **Data Serialization:** Formats the summary into JSON.
5.  **Response:** Returns the health summary data.

### 2. Get Sleep Report (`GET /analytics/sleep-report`)

**Purpose and Functionality:**
The "Get Sleep Report" endpoint (`GET /analytics/sleep-report`, handled by `AnalyticsController@getSleepReport`) provides a detailed report focused specifically on a user's sleep patterns and quality for a given night or period. This report might include sleep stages (light, deep, REM), time in bed, time asleep, number of awakenings, sleep efficiency, and other sleep-related metrics. It's designed to help users and doctors understand sleep habits and identify potential issues.

**Accepted Request Data:**
An authenticated `GET` request. Query parameters might include:

-   `patient_id` (string, if a doctor is querying).
-   `date` (date): The specific night for which the sleep report is requested (e.g., "2025-05-25" for the night of May 25th to 26th).
-   `period` (string, optional, e.g., "last_night", "last_7_days_average").

**Expected Response (Typically `200 OK`):**

-   A JSON object containing detailed sleep report data.
    ```json
    {
        "report_date": "2025-05-25",
        "time_in_bed_minutes": 480,
        "time_asleep_minutes": 450,
        "sleep_efficiency_percent": 93.75,
        "awakenings_count": 2,
        "time_to_fall_asleep_minutes": 15,
        "sleep_stages": {
            "deep_sleep_minutes": 120,
            "light_sleep_minutes": 240,
            "rem_sleep_minutes": 90,
            "awake_minutes": 30
        },
        "heart_rate_during_sleep": { "min": 50, "max": 75, "avg": 60 }
        // ... other sleep metrics, possibly data for a hypnogram chart
    }
    ```

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization.**
2.  **Parameter Processing.**
3.  **Sleep Data Analysis:** The system retrieves relevant sensor data (movement, heart rate, respiration) for the specified user and date/period. Sophisticated algorithms are then applied to this raw data to determine sleep stages, awakenings, and other sleep metrics. This is often a computationally intensive process.
4.  **Report Generation & Serialization:** The analyzed sleep data is compiled into a structured JSON report.
5.  **Response:** Returns the detailed sleep report.

## System & Device Management Endpoints

These endpoints are typically for administrative or diagnostic purposes related to the SmartRest beds themselves or the overall system.

### 1. Reboot Device (`POST /system/reboot`)

**Purpose and Functionality:**
The "Reboot Device" endpoint (`POST /system/reboot`, handled by `SystemController@reboot`) allows an authorized administrator or potentially a user (for their own registered bed, if permitted) to remotely trigger a reboot of a specific SmartRest smart mattress. This might be used for troubleshooting connectivity issues, applying firmware updates that require a restart, or resolving minor operational glitches on the device. This is a command-and-control function.

**Accepted Request Data:**
An authenticated `POST` request. The request body should be a JSON object specifying the target device, for example: `{ \"bed_id\": \"unique_bed_identifier_string\" }` or `{ \"device_id\": \"unique_device_identifier_string\" }`.

**Expected Response (Typically `200 OK` or `202 Accepted`):**

-   A success message indicating the reboot command has been sent.
    ```json
    {
        "message": "Reboot command sent to bed [bed_id] successfully."
    }
    ```
-   If bed not found, not online, or user not authorized: Error response (`404 Not Found`, `403 Forbidden`, `503 Service Unavailable`).

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Verifies the user and their permission to reboot the specified bed.
2.  **Device Lookup & Status Check:** The system checks if the `bed_id` is valid and if the bed is currently online and capable of receiving commands (e.g., via an IoT communication channel like MQTT or a direct API).
3.  **Command Dispatch:** If the bed is reachable, a reboot command is sent to it through the established IoT communication protocol. This is an asynchronous operation; the API confirms the command was sent, not necessarily that the reboot was completed.
4.  **Logging:** The reboot attempt is logged for auditing.
5.  **Response:** Returns a confirmation that the command was dispatched.

### 2. Get System Status (`GET /system/status`)

**Purpose and Functionality:**
The "Get System Status" endpoint (`GET /system/status`, handled by `SystemController@getStatus`) provides information about the overall health and operational status of the SmartRest IoT system or specific components, including connected devices (beds). This is primarily for administrators or support personnel to monitor the system, check connectivity of beds, view error rates, database status, queue lengths, etc.

**Accepted Request Data:**
An authenticated `GET` request. May accept query parameters to specify which component's status is desired:

-   `component` (string, optional, e.g., "database", "beds", "queues").
-   `bed_id` (string, optional): To get the status of a specific bed.

**Expected Response (Typically `200 OK`):**

-   A JSON object detailing the status of the requested components.
    ```json
    // Example for overall system status
    {
      "overall_status": "operational",
      "database_status": "connected",
      "message_queue_size": 10,
      "active_beds_count": 150,
      "error_rate_last_hour_percent": 0.1,
      "last_checked_at": "timestamp"
    }
    // Example for a specific bed's status
    {
      "bed_id": "BED001",
      "connection_status": "online",
      "last_seen_at": "timestamp",
      "firmware_version": "1.2.3",
      "sensor_status": { "heart_rate": "ok", "pressure": "ok" }
    }
    ```
-   If the device is not found, or if status information is unavailable, or if the requester is not authorized, an appropriate error (e.g., `404 Not Found`, `403 Forbidden`) is returned.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Authentication & Authorization:** Ensures admin/support role.
2.  **Status Aggregation:** The system gathers status information from various sources:
    -   Pinging the database.
    -   Checking message queue depths.
    -   Querying IoT platform for bed connectivity and last seen times.
    -   Analyzing recent system logs for error rates.
3.  **Data Compilation & Serialization:** The collected status data is compiled into a structured JSON response.
4.  **Response:** Returns the system or component status information.

## API Documentation Endpoints (Swagger/OpenAPI)

These endpoints serve the automatically generated API documentation, typically using a tool like L5-Swagger which integrates Swagger/OpenAPI into Laravel applications.

### 1. Get API Documentation UI (`GET /api/documentation`)

**Purpose and Functionality:**
This endpoint (`GET /api/documentation`, often configured by L5-Swagger) serves the Swagger UI or OpenAPI documentation page. This is an interactive HTML page that displays all the API endpoints, their descriptions, parameters, request/response schemas, and allows developers to try out the API calls directly from the browser. It's an essential tool for developers integrating with the API.

**Accepted Request Data:**
None for this `GET` request.

**Expected Response (Typically `200 OK`):**

-   An HTML page rendering the Swagger UI.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Request Routing:** The request is routed to the L5-Swagger controller.
2.  **Documentation Generation/Loading:** L5-Swagger reads the OpenAPI specification file (often a YAML or JSON file generated from code annotations or a base spec) and uses it to render the Swagger UI HTML page. This page includes JavaScript to make it interactive.
3.  **Response:** The HTML page is returned to the browser.

### 2. OAuth2 Callback (`GET /api/oauth2-callback`)

**Purpose and Functionality:**
This endpoint (`GET /api/oauth2-callback`, also part of L5-Swagger) is used by the Swagger UI if OAuth2 authentication is configured for trying out protected API endpoints directly from the documentation. When a developer clicks "Authorize" in Swagger UI and goes through an OAuth2 flow (e.g., with an Authorization Server), the OAuth2 provider redirects back to this callback URL with an authorization code or token. Swagger UI then uses this to make authenticated API calls.

**Accepted Request Data:**
This `GET` request receives OAuth2 parameters in the query string, such as:

-   `code` (string, if using authorization code flow).
-   `state` (string).
-   `error` (string, if an error occurred).

**Expected Response (Typically `200 OK`):**

-   An HTML page (often a simple one used by Swagger UI's JavaScript) that handles the OAuth2 callback, extracts the token, and allows Swagger UI to use it.

**Underlying Process (What Happens Behind the Scenes):**

1.  **Request Handling:** L5-Swagger's OAuth2 handler receives the callback.
2.  **Token Exchange (if applicable):** If an authorization `code` is received, Swagger UI's helper script might exchange it for an access token with the OAuth2 token endpoint.
3.  **Token Storage (in browser):** Swagger UI's JavaScript stores the obtained access token in the browser's local storage or session storage to be used for subsequent "Try it out" requests to protected API endpoints.
4.  **Response:** Returns a minimal HTML/JS page that completes the OAuth2 flow within the Swagger UI.
