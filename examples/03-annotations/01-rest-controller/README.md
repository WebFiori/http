# REST Controller with Annotations

Demonstrates the modern annotation-based approach to building REST APIs, including parameter injection, dynamic status codes with `ResponseEntity`, and hyphenated parameter names.

## What This Example Demonstrates

- `#[RestController]` for service naming and description
- `#[GetMapping]`, `#[PostMapping]`, `#[DeleteMapping]` for HTTP method routing
- `#[ResponseBody]` for automatic return value serialization
- `#[RequestParam]` with positional parameter injection
- `ResponseEntity` for dynamic HTTP status codes
- Hyphenated parameter names with arbitrary PHP variable names

## Files

- [`TaskService.php`](TaskService.php) - Complete CRUD service with `ResponseEntity`
- [`index.php`](index.php) - Application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# List all tasks
curl "http://localhost:8080?service=tasks"

# Get a specific task
curl "http://localhost:8080?service=tasks&task-id=1"

# Create a task
curl -X POST "http://localhost:8080?service=tasks" \
  -d "task-name=Buy groceries&task-priority=high"

# Delete a task
curl -X DELETE "http://localhost:8080?service=tasks&task-id=1"

# Try to get a non-existent task (returns 404)
curl "http://localhost:8080?service=tasks&task-id=999"
```

## Code Explanation

### Positional Parameter Injection

Method parameters are matched by position to `#[RequestParam]` attributes, not by name. This allows hyphenated request parameter names (WebFiori convention) with clean PHP variable names:

```php
#[RequestParam('task-id', ParamType::INT)]
#[RequestParam('task-name', ParamType::STRING, true)]
public function getTask(int $id, ?string $name): ResponseEntity {
    // $id ← value of 'task-id' (1st attribute → 1st param)
    // $name ← value of 'task-name' (2nd attribute → 2nd param)
}
```

### Dynamic Status Codes

`ResponseEntity` lets you return different HTTP status codes from the same method:

```php
public function getTask(int $id): ResponseEntity {
    if ($id === 999) {
        return ResponseEntity::notFound(new Json(['message' => 'Not found']));
    }
    return ResponseEntity::ok(new Json(['id' => $id]));
}
```
