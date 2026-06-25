# Testing Web Services with `ServiceTestCase`

This example demonstrates how to test web services using the built-in `ServiceTestCase` class, which provides a clean, fluent API for sending requests and asserting responses.

## Files

- `ItemService.php` — A simple CRUD service to test against
- `ItemServiceTest.php` — PHPUnit test class using `ServiceTestCase`

## Key Concepts

### Extending `ServiceTestCase`

```php
use WebFiori\Http\Test\ServiceTestCase;

class ItemServiceTest extends ServiceTestCase {
    public function testGetItems() {
        $this->get(new ItemService())
            ->assertOk()
            ->assertJson()
            ->assertJsonHas('items');
    }
}
```

### Available Request Methods

| Method | Description |
|:-------|:------------|
| `$this->get($service, $params)` | Send a GET request |
| `$this->post($service, $params)` | Send a POST request |
| `$this->put($service, $params)` | Send a PUT request |
| `$this->patch($service, $params)` | Send a PATCH request |
| `$this->delete($service, $params)` | Send a DELETE request |
| `$this->call($method, $service, $params)` | Send any HTTP method |

### Available Assertions (fluent, chainable)

| Assertion | Description |
|:----------|:------------|
| `->assertOk()` | Status 200 |
| `->assertStatus(201)` | Specific status code |
| `->assertUnauthorized()` | Status 401 |
| `->assertNotFound()` | Status 404 |
| `->assertMethodNotAllowed()` | Status 405 |
| `->assertError()` | Status 4xx or 5xx |
| `->assertJson()` | Response is valid JSON |
| `->assertJsonHas('key')` | JSON contains key |
| `->assertJsonEquals('key', $val)` | JSON key equals value |
| `->assertBodyContains('text')` | Raw body contains string |

### Testing with Authentication

Pass a `SecurityPrincipal` as the third argument:

```php
$user = new MyPrincipal('admin', ['ADMIN']);
$this->get(new ProtectedService(), [], $user)
    ->assertOk();
```

## Running

```bash
cd examples/05-testing
../../vendor/bin/phpunit ItemServiceTest.php
```
