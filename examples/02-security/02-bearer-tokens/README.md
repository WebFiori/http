# Bearer Token Authentication

Demonstrates Bearer token authentication with JWT-like tokens.

## What This Example Demonstrates

- Bearer token authentication
- Token validation and parsing
- Token expiration handling
- User context from tokens

## Files

- [`TokenAuthService.php`](TokenAuthService.php) - Original unified service
- [`LoginService.php`](LoginService.php) - Separate login service
- [`ProfileService.php`](ProfileService.php) - Separate profile service
- [`TokenHelper.php`](TokenHelper.php) - Token generation and validation utilities
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Get a token first
curl -X POST "http://localhost:8080?service=auth&action=login" \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "password123"}'

# Use the token (replace TOKEN with actual token from login)
curl -H "Authorization: Bearer TOKEN" "http://localhost:8080?service=auth&action=profile"

# Invalid token
curl -H "Authorization: Bearer invalid_token" "http://localhost:8080?service=auth&action=profile"

# Expired token simulation
curl -H "Authorization: Bearer expired_token" "http://localhost:8080?service=auth&action=profile"
```

## Code Explanation

**Two approaches demonstrated:**

1. **Separate Services** (Recommended):
   - `LoginService` - Handles authentication, returns token
   - `ProfileService` - Protected resource requiring Bearer token
   - Clean separation of concerns
   - Each service has single responsibility

2. **Unified Service**:
   - `TokenAuthService` - Handles both login and protected operations
   - Uses different HTTP methods (POST for login, GET for profile)

- Bearer token authentication using Authorization header
- Token validation and parsing utilities in TokenHelper
- `#[AllowAnonymous]` for login endpoint
- `#[RequiresAuth]` for protected endpoints
- Service is auto-discovered using `autoDiscoverServices()`
