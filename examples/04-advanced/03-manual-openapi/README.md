# Using OpenAPIObj with WebServicesManager

This example demonstrates how to use the `OpenAPIObj` returned by `WebServicesManager::toOpenAPI()` to generate and serve OpenAPI 3.1.0 documentation for your REST API.

## Files

- `index.php` - Main entry point with WebServicesManager setup
- `UserService.php` - Sample user management service
- `ProductService.php` - Sample product catalog service
- `OpenAPIService.php` - Service that returns OpenAPI specification

## What This Example Shows

- Using `WebServicesManager::toOpenAPI()` to generate OpenAPI specification
- Creating a dedicated service endpoint to serve the OpenAPI documentation
- Accessing and customizing the `OpenAPIObj` and its `InfoObj`
- Automatic documentation generation from registered services

## Running the Example

```bash
# Start PHP built-in server
php -S localhost:8000

# Access the OpenAPI documentation
curl http://localhost:8000?service=openapi

# Or visit in browser
http://localhost:8000?service=openapi
```

## OpenAPIObj Structure

```php
$openApiObj = $manager->toOpenAPI();

// Access components
$info = $openApiObj->getInfo();        // InfoObj with API metadata
$paths = $openApiObj->getPaths();      // PathsObj with all endpoints
$version = $openApiObj->getOpenapi();  // OpenAPI spec version (3.1.0)
```

## Using the Output

### With Swagger UI

1. Copy the JSON output
2. Visit https://editor.swagger.io/
3. Paste the JSON
4. View interactive documentation

### With Postman

1. Save output to `openapi.json`
2. In Postman: Import → Upload Files
3. Select the file
4. All endpoints are imported

## Customizing the Output

```php
// In OpenAPIService.php
$openApiObj = $this->getManager()->toOpenAPI();

// Customize info
$info = $openApiObj->getInfo();
$info->setTermsOfService('https://example.com/terms');
$info->setSummary('My API Summary');
```

## Related Examples

- **02-openapi-docs** - Basic OpenAPI generation setup
- **01-object-mapping** - Request parameter mapping
