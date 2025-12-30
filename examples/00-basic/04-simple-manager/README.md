# Simple Service Manager

Demonstrates managing multiple services with a single WebServicesManager instance.

## What This Example Demonstrates

- Auto-discovering and registering multiple services
- Service routing with `?service=` parameter
- Manager configuration (version, description)
- Multiple independent services in one application

## Files

- [`UserService.php`](UserService.php) - User management service
- [`ProductService.php`](ProductService.php) - Product management service
- [`InfoService.php`](InfoService.php) - API information service
- [`index.php`](index.php) - Main application with multiple services

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Get API information
curl "http://localhost:8080?service=info"

# User service
curl "http://localhost:8080?service=users"

# Product service
curl "http://localhost:8080?service=products"
```

**Expected Responses:**

API Info:
```json
{
    "message": "Success",
    "type": "success",
    "http-code": 200,
    "more-info": [...]
}
```

## Code Explanation

- Multiple services are auto-discovered and registered with `autoDiscoverServices()`
- Each service has its own unique name and functionality
- The manager automatically routes requests to the appropriate service:
  - **GET/DELETE**: `?service=` query parameter
  - **POST/PUT/PATCH**: `service` in request body (form data or JSON attribute)
- When multiple services exist, the `service` parameter is required for routing
