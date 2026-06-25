# Allow Empty Strings with `#[RequestParam]`

This example demonstrates using `allowEmpty: true` in the `#[RequestParam]` attribute to accept empty string values without triggering a validation error.

## The Problem

By default, sending an empty string `""` for a string parameter results in a 422 validation error. This is intentional — most string parameters should reject empty input. However, some fields (like optional notes or descriptions) should legitimately accept empty strings.

## The Solution

Use the `allowEmpty` named parameter:

```php
#[RequestParam(name: 'notes', type: ParamType::STRING, optional: true, allowEmpty: true)]
```

## Running

```bash
cd examples/03-annotations/02-allow-empty
php -S localhost:8080 index.php
```

## Testing

```bash
# With a non-empty value
curl -X POST http://localhost:8080 \
  -H "Content-Type: application/json" \
  -d '{"title": "My Note", "notes": "Some content"}'

# With an empty string (accepted because of allowEmpty: true)
curl -X POST http://localhost:8080 \
  -H "Content-Type: application/json" \
  -d '{"title": "My Note", "notes": ""}'

# Without the notes field (accepted because optional: true)
curl -X POST http://localhost:8080 \
  -H "Content-Type: application/json" \
  -d '{"title": "My Note"}'
```
