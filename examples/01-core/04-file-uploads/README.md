# File Upload Handling

Demonstrates handling file uploads with multipart/form-data requests.

## What This Example Demonstrates

- File upload handling with $_FILES
- File validation (size, type, extension)
- Multiple file uploads
- File storage and metadata

## Files

- [`UploadService.php`](UploadService.php) - File upload handling service
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# Single file upload
curl -X POST "http://localhost:8080" \
  -F "file=@/path/to/your/file.txt" \
  -F "description=Test file upload"

# Upload with metadata
curl -X POST "http://localhost:8080?operation=with-metadata" \
  -F "file=@/path/to/document.pdf" \
  -F "title=Important Document" \
  -F "category=documents"
```

**Expected Response:**
```json
{
    "message": "File uploaded successfully",
    "http-code": 200,
    "data": {
        "filename": "file.txt",
        "size": 1024,
        "type": "text/plain",
        "upload_path": "uploads/file_20240101_120000.txt"
    }
}
```

## Code Explanation

- File uploads are handled through $_FILES superglobal
- Parameters are automatically injected into method arguments
- File validation includes size, type, and extension checks
- Files are moved to a secure upload directory
- Metadata is stored alongside file information
- Content type validation now properly handles multipart/form-data with boundary parameters
- Service is auto-discovered using `autoDiscoverServices()`
