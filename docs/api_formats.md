# API JSON Formats

## Example Credentials

Here are some test accounts you can use to explore the API:

| Role     | Email                | Password |
| -------- | -------------------- | -------- |
| Admin    | admin@example.com    | admin123 |
| Doctor   | doctor@example.com   | password |
| Patient  | patient@example.com  | password |
| Customer | customer@example.com | password |

## Authentication

### Login

**Endpoint:** `POST /api/login`

**Request:**

```json
{
    "email": "string",
    "password": "string"
}
```

**Response:**

```json
{
    "message": "Login successful",
    "token": "string",
    "user": {
        "user_id": "integer",
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "phone": "string",
        "role": "string",
        "is_email_verified": "boolean",
        "permissions": ["string"]
    }
}
```

### Register

**Endpoint:** `POST /api/register`

**Request:**

```json
{
    "user_id": "integer",
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "phone": "string",
    "password": "string",
    "password_confirmation": "string",
    "role": "string"
}
```

**Response:**

```json
{
    "message": "User registered successfully. Please check your email for verification link.",
    "data": {
        "user_id": "integer",
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "phone": "string",
        "role": "string",
        "is_email_verified": "boolean",
        "permissions": ["string"]
    }
}
```

## User Management

### Get All Users

**Endpoint:** `GET /api/users`

**Response:**

```json
{
    "message": "Users retrieved successfully",
    "data": [
        {
            "user_id": "integer",
            "first_name": "string",
            "last_name": "string",
            "email": "string",
            "phone": "string",
            "role": "string",
            "is_email_verified": "boolean",
            "permissions": ["string"]
        }
    ],
    "count": "integer"
}
```

### Get User by ID

**Endpoint:** `GET /api/users/{id}`

**Response:**

```json
{
    "message": "User details retrieved successfully",
    "data": {
        "user_id": "integer",
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "phone": "string",
        "role": "string",
        "is_email_verified": "boolean",
        "permissions": ["string"]
    }
}
```

### Update User

**Endpoint:** `PUT /api/users/{id}`

**Request:**

```json
{
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "phone": "string",
    "password": "string",
    "password_confirmation": "string",
    "role": "string",
    "is_email_verified": "boolean",
    "permissions": ["string"]
}
```

**Response:**

```json
{
    "message": "User updated successfully",
    "data": {
        "user_id": "integer",
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "phone": "string",
        "role": "string",
        "is_email_verified": "boolean",
        "permissions": ["string"]
    }
}
```

### Delete User

**Endpoint:** `DELETE /api/users/{id}`

**Response:**

```json
{
    "message": "User deleted successfully",
    "status": "success"
}
```

## Permission Management

### Add Permission to User

**Endpoint:** `POST /api/users/{id}/permissions`

**Request:**

```json
{
    "permission": "string"
}
```

**Response:**

```json
{
    "message": "Permission added successfully",
    "data": {
        "user_id": "integer",
        "permissions": ["string"]
    }
}
```

### Remove Permission from User

**Endpoint:** `DELETE /api/users/{id}/permissions`

**Request:**

```json
{
    "permission": "string"
}
```

**Response:**

```json
{
    "message": "Permission removed successfully",
    "data": {
        "user_id": "integer",
        "permissions": ["string"]
    }
}
```

## Error Responses

### Authentication Error

```json
{
    "message": "Access Denied: Invalid email or password",
    "error": "authentication_failed"
}
```

### Permission Error

```json
{
    "message": "Access Denied: You do not have permission to perform this action",
    "error": "insufficient_permissions"
}
```

### Validation Error

```json
{
    "message": "The given data was invalid",
    "errors": {
        "field_name": ["error message"]
    }
}
```

### Not Found Error

```json
{
    "message": "Resource not found",
    "error": "not_found"
}
```

## Notes

1. All endpoints require authentication except login and register
2. Token should be included in the Authorization header as: `Bearer {token}`
3. All timestamps are in ISO 8601 format
4. All IDs are integers
5. Boolean values are represented as true/false
6. Arrays are represented as JSON arrays
7. Optional fields are marked with "string" type
