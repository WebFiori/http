# Role-Based Access Control (RBAC)

Demonstrates role-based access control using SecurityContext and annotations.

## What This Example Demonstrates

- SecurityContext for user management
- Role-based authorization with `@PreAuthorize`
- Authority-based permissions
- Method-level security annotations

## Files

- [`PublicService.php`](PublicService.php) - Public information (no auth required)
- [`UserService.php`](UserService.php) - User profile service (requires authentication)
- [`AdminService.php`](AdminService.php) - Admin panel service (requires ADMIN role)
- [`UserManagerService.php`](UserManagerService.php) - User management (requires USER_MANAGE authority)
- [`DemoUser.php`](DemoUser.php) - Demo user implementation
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Public endpoint (no auth required)
curl "http://localhost:8080?service=public"

# User endpoint (requires authentication)
curl "http://localhost:8080?service=user"

# Admin endpoint (requires ADMIN role)
curl "http://localhost:8080?service=admin"

# User management endpoint (requires USER_MANAGE authority)
curl "http://localhost:8080?service=user-manager"
```

**Expected Responses:**

Public access:
```json
{
    "message": "This is public information",
    "http-code": 200
}
```

With authentication:
```json
{
    "user": {...},
    "roles": ["USER", "ADMIN"],
    "authorities": ["USER_CREATE", "USER_UPDATE"]
}
```

## Code Explanation

- `isAuthorized()` handles authentication only - validates credentials and sets `SecurityContext::setCurrentUser()`
- `@PreAuthorize` annotations handle method-level authorization based on roles/authorities
- `@AllowAnonymous` bypasses authentication requirements for public endpoints
- `@RequiresAuth` ensures user is authenticated before method execution
- Role and authority checks are evaluated at runtime using security expressions
