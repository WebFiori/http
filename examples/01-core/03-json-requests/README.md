# JSON Request Handling

Demonstrates handling JSON request bodies and automatic parsing.

## What This Example Demonstrates

- Processing JSON request bodies
- Content-Type: application/json handling
- Accessing JSON data in services
- Mixed parameter sources (URL + JSON body)

## Files

- [`JsonService.php`](JsonService.php) - Service that processes JSON requests
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# JSON POST request
curl -X POST "http://localhost:8080" \
  -H "Content-Type: application/json" \
  -d '{"user": {"name": "John", "age": 30}, "preferences": {"theme": "dark", "notifications": true}}'

# JSON with operation parameter
curl -X POST "http://localhost:8080?operation=update" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated Name", "email": "new@example.com"}'

# JSON PUT request
curl -X PUT "http://localhost:8080" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated", "email": "updated@example.com"}'
```

**Expected Response:**
```json
{
    "message": "JSON data processed successfully",
    "http-code": 200,
    "data": {
        "received_json": {...},
        "url_params": {...},
        "processed_at": "2024-01-01 12:00:00"
    }
}
```

## Code Explanation

- JSON requests are automatically parsed when Content-Type is application/json
- `getInputs()` returns the parsed JSON object
- Parameters can be injected into method arguments
- Different methods handle POST and PUT requests separately
- Invalid JSON automatically returns 400 Bad Request
- Service is auto-discovered using `autoDiscoverServices()`
