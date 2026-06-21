# OpenAPI Namespace Scanning & Built-in Spec Service

Demonstrates the new OpenAPI features:
- `#[ApiResponse]` for declarative response descriptions
- `#[RestController(path: '...')]` for custom route paths
- `OpenAPIGenerator::discoverServices()` for namespace scanning
- `OpenAPISpecService` for a ready-to-use spec endpoint

## What This Example Demonstrates

- Declaring API responses directly on methods with `#[ApiResponse]`
- Setting a custom multi-segment path via `#[RestController(path: 'shop/products')]`
- Auto-discovering services in a namespace without manual registration
- Serving the OpenAPI spec as a live endpoint with `OpenAPISpecService`

## Files

- [`ProductService.php`](ProductService.php) - Service using `#[ApiResponse]` and custom path
- [`index.php`](index.php) - Generates spec using namespace scanning

## How to Run

```bash
php -S localhost:8080
# Visit http://localhost:8080 to see the generated OpenAPI JSON
```

## Key Features

### #[ApiResponse] — Declarative Response Descriptions

```php
#[GetMapping]
#[ApiResponse(status: '200', description: 'List of products')]
#[ApiResponse(status: '404', description: 'Product not found')]
public function getProducts(?int $id): array { ... }
```

These appear in the OpenAPI spec under `responses` for each operation.

### #[RestController(path: '...')] — Custom Route Path

```php
#[RestController(name: 'products', path: 'shop/products')]
```

- `name` = service identifier (used for lookups)
- `path` = URL mount point (used in OpenAPI spec and routing)

### OpenAPISpecService — Built-in Spec Endpoint

```php
use WebFiori\Http\OpenAPI\OpenAPISpecService;
use WebFiori\Http\RequestProcessor;

$specService = new OpenAPISpecService(
    'App\\Apis',   // Namespace to scan
    '/apis',       // Base path
    'My API',      // Title
    '1.0.0'        // Version
);

$processor = new RequestProcessor();
$processor->process($specService);
```

Point Swagger UI at this endpoint to get live, auto-generated documentation.
