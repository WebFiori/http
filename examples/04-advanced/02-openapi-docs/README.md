# OpenAPI Documentation Generation

Demonstrates generating OpenAPI 3.1 specifications using the standalone `OpenAPIGenerator` class.

## What This Example Demonstrates

- Generating OpenAPI specs without a `WebServicesManager`
- Using `OpenAPIGenerator` directly with service instances
- Setting API description, version, and base path
- Outputting the spec as JSON

## Files

- [`index.php`](index.php) - Generates and outputs an OpenAPI spec

## How to Run

```bash
php -S localhost:8080
# Visit http://localhost:8080 to see the generated OpenAPI JSON
```

## Code Explanation

### Standalone Generator (Recommended)

```php
use WebFiori\Http\OpenAPI\OpenAPIGenerator;

$generator = new OpenAPIGenerator();
$spec = $generator->generate(
    [new UserService(), new TaskService()],  // Services
    'My API',                                 // Description
    '2.0.0',                                  // Version
    '/api/v2'                                 // Base path
);

echo $spec->toJSON();
```

### Legacy Approach (Deprecated)

```php
$manager = new WebServicesManager();
$manager->addService(new UserService());
$manager->setDescription('My API');
$manager->setVersion('2.0.0');
$spec = $manager->toOpenAPI(); // @deprecated
```

### Output

The generated spec follows OpenAPI 3.1.0 format:

```json
{
  "openapi": "3.1.0",
  "info": {
    "title": "My API",
    "version": "2.0.0"
  },
  "paths": {
    "/api/v2/users": {
      "get": { ... },
      "post": { ... }
    }
  }
}
```

Parameters defined with `#[RequestParam]` are automatically included as query parameters (GET) or request body properties (POST/PUT/PATCH).
