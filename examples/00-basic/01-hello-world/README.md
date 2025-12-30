# Hello World Service

The most basic WebFiori HTTP service demonstrating minimal setup and response handling.

## What This Example Demonstrates

- Creating a basic web service by extending `WebService`
- Setting up a service manager
- Handling requests and sending JSON responses
- Basic service registration and processing

## Files

- [`HelloService.php`](HelloService.php) - The service implementation
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Basic hello world response
curl "http://localhost:8080"
```

**Expected Response:**
```json
{
    "message": "Hello World!",
    "http-code": 200
}
```

## Code Explanation

The `HelloService` class uses PHP 8 attributes to define a REST controller:
- `#[RestController]` - Marks the class as a web service with name 'hello'
- `#[GetMapping]` - Maps the method to GET requests
- `#[ResponseBody]` - Indicates the return value should be sent as response
- `#[AllowAnonymous]` - Allows access without authentication

The service is automatically discovered and registered using `autoDiscoverServices()`, which scans the directory for classes extending `WebService`.
