# Reusable Parameter Sets

Demonstrates grouping related parameters into reusable sets that can be shared across services.

## What This Example Demonstrates

- Implementing the `ParameterSet` interface
- Using `#[UseParameterSet]` attribute on methods
- Combining parameter sets with explicit `#[RequestParam]` attributes
- Validation (pattern, allowed-values) works through parameter sets

## Files

- [`index.php`](index.php) - Defines parameter sets and uses them in a service

## How to Run

```bash
php -S localhost:8080
```

## Testing

```bash
# List orders with pagination (uses PaginationParams set)
curl "http://localhost:8080?page=2&per_page=50"

# List orders with defaults
curl "http://localhost:8080"

# Create order (uses AddressParams set + explicit total param)
curl -X POST http://localhost:8080 \
  -d "street=123+Main+St&city=Springfield&zip=12345&country=US&total=99.99"

# Invalid zip (pattern validation from set)
curl -X POST http://localhost:8080 \
  -d "street=123+Main+St&city=Springfield&zip=ABCDE&country=US&total=99.99"

# Invalid country (allowed-values validation from set)
curl -X POST http://localhost:8080 \
  -d "street=123+Main+St&city=Springfield&zip=12345&country=JP&total=99.99"
```

## Code Explanation

### Define a Parameter Set

```php
class PaginationParams implements ParameterSet {
    public function getParameters(): array {
        return [
            'page' => [ParamOption::TYPE => ParamType::INT, ParamOption::OPTIONAL => true, ParamOption::DEFAULT => 1],
            'per_page' => [ParamOption::TYPE => ParamType::INT, ParamOption::OPTIONAL => true, ParamOption::DEFAULT => 20],
        ];
    }
}
```

### Use with Attributes

```php
#[GetMapping]
#[ResponseBody]
#[UseParameterSet(PaginationParams::class)]
public function listItems(int $page = 1, int $perPage = 20): array { ... }
```

### Use Traditionally

```php
$this->addParameterSet(new PaginationParams());
```

### Combine Sets with Explicit Params

```php
#[UseParameterSet(AddressParams::class)]
#[RequestParam('total', ParamType::DOUBLE)]
public function createOrder(string $street, string $city, string $zip, string $country, float $total): array { ... }
```

Method parameters are matched positionally: set params first, then `#[RequestParam]` params.
