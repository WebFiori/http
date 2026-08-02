# Content Type Control with #[Consumes]

Demonstrates the `#[Consumes]` annotation for per-method content type control, allowing services to accept non-standard content types like `application/octet-stream` or `application/xml`.

## What This Example Demonstrates

- `#[Consumes]` to declare accepted request content types per method
- Overriding the default allowed types (form-urlencoded, multipart, JSON)
- Accepting raw binary uploads without parameter filtering
- Accepting multiple content types on a single method
- Reading the raw request body via `php://input`

## Files

- [`FileUploadService.php`](FileUploadService.php) - Service with binary and XML upload endpoints
- [`index.php`](index.php) - Application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Upload a binary file
curl -X POST "http://localhost:8080?service=files" \
  -H "Content-Type: application/octet-stream" \
  --data-binary @somefile.bin

# Upload from stdin
echo "hello binary world" | curl -X POST "http://localhost:8080?service=files" \
  -H "Content-Type: application/octet-stream" \
  --data-binary @-

# Upload XML (application/xml)
curl -X PUT "http://localhost:8080?service=files" \
  -H "Content-Type: application/xml" \
  -d '<root><item>Hello</item></root>'

# Upload XML (text/xml)
curl -X PUT "http://localhost:8080?service=files" \
  -H "Content-Type: text/xml" \
  -d '<config><setting name="debug">true</setting></config>'

# Rejected: form-urlencoded is NOT listed in #[Consumes]
curl -X POST "http://localhost:8080?service=files" \
  -d "name=test"
# Returns 415 Unsupported Media Type
```

## How It Works

### Default Behavior (No #[Consumes])

Without `#[Consumes]`, the framework only allows these content types for POST/PUT:
- `application/x-www-form-urlencoded`
- `multipart/form-data`
- `application/json`

Any other type gets a 415 response.

### With #[Consumes]

The annotation **overrides** the defaults for that specific method:

```php
#[PostMapping]
#[Consumes(MediaType::OCTET_STREAM)]
public function uploadBinary(): ResponseEntity {
    // Only application/octet-stream is accepted
    // form-urlencoded and JSON are REJECTED (not in the list)
    $body = file_get_contents('php://input');
    // ...
}
```

### Parameter Filtering

When the content type is non-standard (not form-encoded, multipart, or JSON), parameter filtering is **automatically skipped**. The raw body is available via `php://input`.

If you include a standard type in `#[Consumes]`, normal filtering applies for that type:

```php
#[Consumes(MediaType::OCTET_STREAM, MediaType::FORM)]
public function flexible(): ResponseEntity {
    // With octet-stream: no filtering, read php://input
    // With form-urlencoded: normal parameter filtering applies
}
```
