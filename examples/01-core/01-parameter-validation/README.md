# Parameter Validation

Demonstrates comprehensive parameter validation including types, ranges, custom filters, and validation rules.

## What This Example Demonstrates

- Different parameter types (string, int, email, URL)
- Validation rules (min/max length, ranges)
- **Custom validation filters using the `filter` parameter**
- Default values for optional parameters
- Validation error handling
- Automatic parameter injection

## Files

- [`ValidationService.php`](ValidationService.php) - Service with comprehensive validation
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Valid request
curl "http://localhost:8080?name=John&age=25&email=john@example.com&website=https://example.com"

# Valid with custom username validation
curl "http://localhost:8080?name=John&email=john@test.com&username=john123"

# Invalid username (too short)
curl "http://localhost:8080?name=John&email=john@test.com&username=ab"

# Invalid username (special characters)
curl "http://localhost:8080?name=John&email=john@test.com&username=john@123"

# Invalid email format
curl "http://localhost:8080?name=John&age=25&email=invalid-email"

# Missing required parameter
curl "http://localhost:8080?age=25"
```

## Code Explanation

- Different parameter types are defined using `#[RequestParam]` attributes
- Parameters are automatically injected into method arguments with proper types
- **Custom validation** can be added using the `filter` parameter:
  ```php
  #[RequestParam('username', 'string', true, null, 'Description', filter: [ClassName::class, 'methodName'])]
  ```
- Validation happens automatically before method execution:
  - Type validation (string, int, email, url, double)
  - Range validation (min/max for numbers)
  - Length validation (min/max for strings)
  - Format validation (email, URL)
  - Custom validation via filter functions
- Invalid parameters automatically return 400 errors with details
- Service is auto-discovered using `autoDiscoverServices()`
