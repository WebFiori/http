# Multiple HTTP Methods

Demonstrates handling different HTTP methods (GET, POST, PUT, DELETE) in a single service.

## What This Example Demonstrates

- Supporting multiple HTTP methods using separate mapping attributes
- Method-specific parameters with `#[RequestParam]`
- Different HTTP status codes per method (e.g., 201 for POST)
- RESTful CRUD operations (Create, Read, Update, Delete)
- Automatic service discovery

## Files

- [`TaskService.php`](TaskService.php) - Service supporting CRUD operations
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# GET - Retrieve tasks
curl "http://localhost:8080"

# POST - Create task
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "title=Buy groceries&description=Get milk and bread" \
  "http://localhost:8080"

# PUT - Update task
curl -X PUT -H "Content-Type: application/x-www-form-urlencoded" \
  -d "id=1&title=Updated task" \
  "http://localhost:8080"

# DELETE - Delete task
curl -X DELETE "http://localhost:8080?id=1"
```

**Expected Responses:**

GET:
```json
{
    "message": "Tasks retrieved",
    "http-code": 200,
    "data": {
        "tasks": [],
        "count": 0
    }
}
```

POST:
```json
{
    "message": "Task created successfully",
    "http-code": 201,
    "data": {
        "id": 1,
        "title": "Buy groceries",
        "description": "Get milk and bread"
    }
}
```

## Code Explanation

- Multiple HTTP methods are supported using separate mapping attributes:
  - `#[GetMapping]` for GET requests
  - `#[PostMapping]` for POST requests  
  - `#[PutMapping]` for PUT requests
  - `#[DeleteMapping]` for DELETE requests
- Each method can have its own parameters defined with `#[RequestParam]`
- **Parameters are automatically injected** into method arguments (e.g., `createTask(string $title, ?string $description)`)
- `#[ResponseBody(status: 201)]` sets custom HTTP status code
- Service is auto-discovered using `autoDiscoverServices()`
