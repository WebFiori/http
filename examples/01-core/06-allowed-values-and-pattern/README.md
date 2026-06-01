# Allowed Values and Pattern Validation

Demonstrates restricting parameter values using `allowedValues` (enum) and `pattern` (regex) constraints.

## What This Example Demonstrates

- Restricting a parameter to a set of allowed values (enum validation)
- Validating parameter format using regex patterns
- Combining both constraints on different parameters
- Using these features with `#[RequestParam]` attributes

## Files

- [`OrderService.php`](OrderService.php) - Service with allowed-values and pattern validation
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Valid status (must be one of: pending, shipped, delivered, cancelled)
curl "http://localhost:8080?service=orders&status=pending"

# Invalid status — returns validation error
curl "http://localhost:8080?service=orders&status=unknown"

# Valid sort parameter (optional, defaults to 'date')
curl "http://localhost:8080?service=orders&status=shipped&sort=total"

# Create order with valid phone (+international format) and postal code (5 digits)
curl -X POST http://localhost:8080 \
  -d "service=orders&customer_name=John&phone=%2B12025551234&postal_code=90210&country=US"

# Invalid phone format — returns validation error
curl -X POST http://localhost:8080 \
  -d "service=orders&customer_name=John&phone=123&postal_code=90210&country=US"

# Invalid postal code — returns validation error
curl -X POST http://localhost:8080 \
  -d "service=orders&customer_name=John&phone=%2B12025551234&postal_code=ABC&country=US"

# Invalid country — returns validation error
curl -X POST http://localhost:8080 \
  -d "service=orders&customer_name=John&phone=%2B12025551234&postal_code=90210&country=JP"
```

## Code Explanation

### Allowed Values (Enum)

Restrict a parameter to a predefined set of values:

```php
#[RequestParam('status', ParamType::STRING, allowedValues: ['pending', 'shipped', 'delivered', 'cancelled'])]
```

If the value is not in the set, it is treated as invalid.

### Pattern (Regex)

Validate a parameter against a regular expression:

```php
#[RequestParam('phone', ParamType::STRING, pattern: '/^\+[0-9]{10,15}$/')]
#[RequestParam('postal_code', ParamType::STRING, pattern: '/^[0-9]{5}$/')]
```

If the value does not match the pattern, it is treated as invalid.

### Traditional Approach

These options also work with the array-based parameter definition:

```php
$this->addParameters([
    'status' => [
        ParamOption::TYPE => ParamType::STRING,
        ParamOption::ALLOWED_VALUES => ['pending', 'shipped', 'delivered', 'cancelled']
    ],
    'phone' => [
        ParamOption::TYPE => ParamType::STRING,
        ParamOption::PATTERN => '/^\+[0-9]{10,15}$/'
    ]
]);
```

### OpenAPI

Both constraints are automatically included in the generated OpenAPI spec:

```json
{
  "type": "string",
  "enum": ["pending", "shipped", "delivered", "cancelled"]
}
```

```json
{
  "type": "string",
  "pattern": "^\\+[0-9]{10,15}$"
}
```
