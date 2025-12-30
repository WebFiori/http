# Service with Parameters

Demonstrates how to define and handle request parameters with validation.

## What This Example Demonstrates

- Adding parameters to a service
- Parameter types and validation
- Optional vs required parameters
- Retrieving parameter values in service logic

## Files

- `GreetingService.php` - Service with name parameter
- `index.php` - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Without name parameter
curl "http://localhost:8080"

# With name parameter
curl "http://localhost:8080?name=John"

# With empty name (demonstrates validation)
curl "http://localhost:8080?name="
```

**Expected Responses:**

Without name:
```json
{
    "message": "Hello, Guest!",
    "http-code": 200
}
```

With name:
```json
{
    "message": "Hello, John!",
    "http-code": 200
}
```

## Code Explanation

- Parameters are defined using `#[RequestParam]` attribute
- First argument is parameter name, second is type ('string')
- Third argument (`true`) makes it optional
- Fourth argument (`null`) is the default value
- `getParamVal()` retrieves the validated parameter value
- Service is auto-discovered using `autoDiscoverServices()`
