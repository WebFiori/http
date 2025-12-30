# Response Formats

Demonstrates different response content types and formats beyond JSON.

## What This Example Demonstrates

- Different content types using `#[ResponseBody(contentType: '...')]`
- Separate services for each format (cleaner architecture)
- Unified service with format switching (single endpoint)
- Custom response serialization (XML, Text)
- Automatic content type handling

## Files

- [`ResponseService.php`](ResponseService.php) - Original unified service (demonstrates format switching)
- [`JsonResponseService.php`](JsonResponseService.php) - JSON response service
- [`XmlResponseService.php`](XmlResponseService.php) - XML response service  
- [`TextResponseService.php`](TextResponseService.php) - Plain text response service
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# JSON response
curl "http://localhost:8080?service=json-response"

# XML response  
curl "http://localhost:8080?service=xml-response"

# Plain text response
curl "http://localhost:8080?service=text-response"

# Original unified service with format parameter
curl "http://localhost:8080?service=response&format=json"
curl "http://localhost:8080?service=response&format=xml"
curl "http://localhost:8080?service=response&format=text"
```

## Code Explanation

**Two approaches demonstrated:**

1. **Separate Services** (Recommended for clean architecture):
   - `JsonResponseService` - Returns JSON with default content type
   - `XmlResponseService` - Uses `#[ResponseBody(contentType: 'application/xml')]`
   - `TextResponseService` - Uses `#[ResponseBody(contentType: 'text/plain')]`
   - Each service is focused and simple
   - Auto-discovered and registered automatically

2. **Unified Service** (Single endpoint with format switching):
   - `ResponseService` - Handles multiple formats via parameter
   - Uses `send()` method for custom content types
   - More complex but provides single endpoint

**Benefits of separate services:**
- Cleaner, more maintainable code
- Each service has single responsibility
- Easier to test and modify
- Better separation of concerns
