# Object Mapping

Demonstrates all available approaches for mapping HTTP request parameters to PHP objects in WebFiori HTTP.

## What This Example Demonstrates

- **5 Different Object Mapping Approaches**
- Parameter validation and type safety
- Custom setter method mapping
- Clean separation of data and logic
- Modern attribute-based mapping (NEW)

## Files

- [`User.php`](User.php) - Data model class with validation
- [`TraditionalMappingService.php`](TraditionalMappingService.php) - Traditional parameter mapping
- [`ManualMappingService.php`](ManualMappingService.php) - Manual object mapping
- [`GetObjectMappingService.php`](GetObjectMappingService.php) - getObject() mapping
- [`MapEntityMappingService.php`](MapEntityMappingService.php) - MapEntity attribute mapping
- [`MapEntityCustomMappingService.php`](MapEntityCustomMappingService.php) - MapEntity with custom setters
- [`index.php`](index.php) - Main application entry point

## How to Run

```bash
php -S localhost:8080
```


## Object Mapping Approaches

### 1. Traditional Parameter Mapping
**Method signature with individual parameters**
```php
#[RequestParam('name', 'string', false)]
#[RequestParam('email', 'string', false)]
#[RequestParam('age', 'int', false)]
public function create(string $name, string $email, int $age): array
```

### 2. Manual Object Mapping
**Manual parameter extraction and object creation**
```php
public function create(): array {
    $inputs = $this->getInputs();
    $user = new User();
    if ($inputs->hasKey('name')) $user->setName($inputs->get('name'));
    // ... manual mapping
}
```

### 3. getObject() Mapping
**Framework-assisted object mapping**
```php
public function create(): array {
    $user = $this->getObject(User::class);
    // Object automatically mapped from request
}
```

### 4. MapEntity Attribute - Basic
**Clean attribute-based mapping (NEW)**
```php
#[MapEntity(User::class)]
public function create(User $user): array {
    // $user automatically mapped and injected
}
```

### 5. MapEntity Attribute - Custom Setters
**Flexible parameter naming with custom setters (NEW)**
```php
#[MapEntity(User::class, setters: ['full-name' => 'setFullName', 'email-address' => 'setEmailAddress'])]
public function create(User $user): array {
    // Custom parameter mapping handled automatically
}
```

## Testing All Approaches

```bash
# 1. Traditional Parameters
curl -X POST "http://localhost:8080?service=traditional" \
  -H "Content-Type: application/json" \
  -d '{"name": "John Doe", "email": "john@example.com", "age": 30}'

# 2. Manual Mapping
curl -X POST "http://localhost:8080?service=manual" \
  -H "Content-Type: application/json" \
  -d '{"name": "John Doe", "email": "john@example.com", "age": 30}'

# 3. getObject() Mapping
curl -X POST "http://localhost:8080?service=getobject" \
  -H "Content-Type: application/json" \
  -d '{"name": "John Doe", "email": "john@example.com", "age": 30}'

# 4. MapEntity Basic
curl -X POST "http://localhost:8080?service=mapentity" \
  -H "Content-Type: application/json" \
  -d '{"name": "John Doe", "email": "john@example.com", "age": 30}'

# 5. MapEntity Custom Setters
curl -X POST "http://localhost:8080?service=mapentity-custom" \
  -H "Content-Type: application/json" \
  -d '{"full-name": "Jane Smith", "email-address": "jane@example.com", "user-age": 25}'
```

## Comparison of Approaches

| Approach | Pros | Cons | Best For |
|----------|------|------|----------|
| **Traditional Parameters** | ✅ Explicit, IDE support | ❌ Verbose for many params | Simple endpoints |
| **Manual Mapping** | ✅ Full control, flexible | ❌ Boilerplate code | Complex validation |
| **getObject() Mapping** | ✅ Automatic, less code | ❌ Silent error handling | Standard mapping |
| **MapEntity Basic** | ✅ Clean, type-safe | ❌ Less flexible | Modern development |
| **MapEntity Custom** | ✅ Flexible + clean | ❌ Setup complexity | Legacy API integration |

## Expected Results

All approaches produce the same result structure:
```json
{
  "message": "User created with [approach name]",
  "user": {
    "name": "John Doe",
    "email": "john@example.com",
    "age": 30,
    "phone": null,
    "address": null
  },
  "method": "[approach_identifier]"
}
```

## Code Explanation

### Evolution of Object Mapping

**1. Traditional Approach** - Explicit parameter handling
```php
public function create(string $name, string $email, int $age): array {
    $user = new User();
    $user->setName($name);
    $user->setEmail($email);
    $user->setAge($age);
}
```

**2. Manual Mapping** - Direct input processing
```php
public function create(): array {
    $inputs = $this->getInputs();
    $user = new User();
    if ($inputs->hasKey('name')) $user->setName($inputs->get('name'));
}
```

**3. Framework Mapping** - Automated object creation
```php
public function create(): array {
    $user = $this->getObject(User::class);
    // Framework handles mapping automatically
}
```

**4. Modern Attribute Mapping** - Clean and type-safe
```php
#[MapEntity(User::class)]
public function create(User $user): array {
    // $user is automatically mapped and validated
}
```


### When to Use Each Approach

- **Traditional**: Simple endpoints with few parameters
- **Manual**: Complex validation or transformation logic
- **getObject()**: Standard mapping with framework control
- **MapEntity**: Modern development with type safety (RECOMMENDED)
