# Basic Authentication

Demonstrates HTTP Basic authentication with username/password credentials.

## What This Example Demonstrates

- HTTP Basic authentication implementation
- Base64 credential decoding
- User credential validation
- Custom authentication error messages

## Files

- [`BasicAuthService.php`](BasicAuthService.php) - Service with Basic auth
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Without authentication (will fail)
curl "http://localhost:8080"

# With Basic authentication
curl -u "admin:password123" "http://localhost:8080"

# With invalid credentials
curl -u "admin:wrongpassword" "http://localhost:8080"
```

**Expected Responses:**

Without auth:
```json
{
    "message": "Authentication required",
    "type": "error",
    "http-code": 401
}
```

With valid auth:
```json
{
    "message": "Access granted to secure resource",
    "http-code": 200,
    "data": {
        "user": "admin",
        "authenticated_at": "2024-01-01 12:00:00"
    }
}
```

## Code Explanation

- `isAuthorized()` method implements custom authentication logic
- `getAuthHeader()` retrieves the Authorization header
- Basic auth credentials are base64 encoded as "username:password"
- Custom user validation logic checks against predefined credentials
- `#[RequiresAuth]` calls `isAuthorized()` first, then checks `#[PreAuthorize]` if present
- **Authorization flow:** `isAuthorized()` → `#[PreAuthorize]` (if exists) → SecurityContext
- Service is auto-discovered using `autoDiscoverServices()`
- Different users get different access levels
