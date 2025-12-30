# Error Handling

Demonstrates custom error responses, validation error handling, and proper HTTP status codes.

## What This Example Demonstrates

- Custom error messages and status codes
- Validation error handling
- Exception handling in services
- Structured error responses

## Files

- [`ErrorService.php`](ErrorService.php) - Service with comprehensive error handling
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Valid request
curl "http://localhost:8080?operation=success"

# Validation error
curl "http://localhost:8080?operation=validate&age=15"

# Business logic error
curl "http://localhost:8080?operation=divide&a=10&b=0"

# Not found error
curl "http://localhost:8080?operation=not-found"

# Server error simulation
curl "http://localhost:8080?operation=server-error"
```

## Code Explanation

- Custom error responses with appropriate HTTP status codes
- Exceptions are automatically caught and converted to error responses
- Parameters are automatically injected into method arguments
- Business logic errors handled with try-catch blocks or thrown exceptions
- Consistent error response format across all error types
- Service is auto-discovered using `autoDiscoverServices()`
