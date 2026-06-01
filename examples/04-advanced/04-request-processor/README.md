# RequestProcessor — Standalone Service Processing

Demonstrates processing a web service directly without a `WebServicesManager`.

## What This Example Demonstrates

- Using `RequestProcessor` to process a single service
- No service registry or manager setup required
- Automatic request creation from globals
- Full pipeline: validation, auth, invocation, serialization

## Files

- [`index.php`](index.php) - Processes a service directly with RequestProcessor

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# GET request
curl "http://localhost:8080?name=Ibrahim"

# GET without param (uses default)
curl "http://localhost:8080"

# POST request
curl -X POST http://localhost:8080 \
  -d "to=Alice&body=Hi there"
```

## Code Explanation

### Before (WebServicesManager)

```php
$manager = new WebServicesManager();
$manager->addService(new GreetService());
$manager->process();
```

### After (RequestProcessor)

```php
$processor = new RequestProcessor();
$processor->process(new GreetService());
```

The `RequestProcessor` is ideal when:
- You have a router that already resolved which service to call
- You want to process a single service without registry overhead
- You're building framework integrations that handle routing externally

### With explicit Request (for testing)

```php
$processor = new RequestProcessor();
$processor->process(new GreetService(), $request, $outputStream);
```
