# WebFiori HTTP

A powerful and flexible PHP library for creating RESTful web APIs with built-in input filtering, data validation, and comprehensive HTTP utilities. The library provides a clean, object-oriented approach to building web services with automatic parameter validation, authentication support, and JSON response handling.

<p align="center">
  <a href="https://github.com/WebFiori/http/actions">
    <img src="https://github.com/WebFiori/http/actions/workflows/php85.yaml/badge.svg?branch=main">
  </a>
  <a href="https://codecov.io/gh/WebFiori/http">
    <img src="https://codecov.io/gh/WebFiori/http/branch/main/graph/badge.svg" />
  </a>
  <a href="https://sonarcloud.io/dashboard?id=WebFiori_http">
      <img src="https://sonarcloud.io/api/project_badges/measure?project=WebFiori_http&metric=alert_status" />
  </a>
  <a href="https://github.com/WebFiori/http/releases">
      <img src="https://img.shields.io/github/release/WebFiori/http.svg?label=latest" />
  </a>
  <a href="https://packagist.org/packages/webfiori/http">
      <img src="https://img.shields.io/packagist/dt/webfiori/http?color=light-green">
  </a>
</p>

## Table of Contents

- [Supported PHP Versions](#supported-php-versions)
- [Key Features](#key-features)
- [Installation](#installation)
- [Quick Start](#quick-start)
  - [Modern Approach with Attributes](#modern-approach-with-attributes)
  - [Traditional Approach](#traditional-approach)
- [Core Concepts](#core-concepts)
- [Creating Web Services](#creating-web-services)
  - [Using Attributes (Recommended)](#using-attributes-recommended)
  - [Traditional Class-Based Approach](#traditional-class-based-approach)
- [Parameter Management](#parameter-management)

- [Testing](#testing)
- [Examples](#examples)


## Supported PHP Versions

|                                                                                        Build Status                                                                                         |
|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php81.yaml"><img src="https://github.com/WebFiori/http/actions/workflows/php81.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php82.yaml"><img src="https://github.com/WebFiori/http/actions/workflows/php82.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php83.yaml"><img src="https://github.com/WebFiori/http/actions/workflows/php83.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php84.yaml"><img src="https://github.com/WebFiori/http/actions/workflows/php84.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/http/actions/workflows/php85.yaml"><img src="https://github.com/WebFiori/http/actions/workflows/php85.yaml/badge.svg?branch=main"></a> |

## Key Features

- **RESTful API Development**: Full support for creating REST services with JSON request/response handling
- **Automatic Input Validation**: Built-in parameter validation with support for multiple data types
- **Custom Filtering**: Ability to create user-defined input filters and validation rules
- **Authentication Support**: Built-in support for various authentication schemes (Basic, Bearer, etc.)
- **HTTP Method Support**: Support for all standard HTTP methods (GET, POST, PUT, DELETE, etc.)
- **Content Type Handling**: Support for `application/json`, `application/x-www-form-urlencoded`, and `multipart/form-data`
- **Object Mapping**: Automatic mapping of request parameters to PHP objects
- **Comprehensive Testing**: Built-in testing utilities with `APITestCase` class
- **Error Handling**: Structured error responses with appropriate HTTP status codes
- **Stream Support**: Custom input/output stream handling for advanced use cases

## Installation

### Using Composer (Recommended)

```bash
composer require webfiori/http
```

### Manual Installation

Download the latest release from [GitHub Releases](https://github.com/WebFiori/http/releases) and include the autoloader:

```php
require_once 'path/to/webfiori-http/vendor/autoload.php';
```

## Quick Start

### Modern Approach with Attributes (Recommended)

PHP 8+ attributes provide a clean, declarative way to define web services:

```php
<?php
use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\ParamType;

#[RestController('hello', 'A simple greeting service')]
class HelloService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', ParamType::STRING, true)]
    public function sayHello(?string $name): string {
        return $name ? "Hello, $name!" : "Hello, World!";
    }
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('message', ParamType::STRING)]
    public function customGreeting(string $message): array {
        return ['greeting' => $message, 'timestamp' => time()];
    }
}
```

### Traditional Approach

For comparison, here's the traditional approach using constructor configuration:

```php
<?php
use WebFiori\Http\AbstractWebService;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\ParamType;
use WebFiori\Http\ParamOption;

class HelloService extends AbstractWebService {
    public function __construct() {
        parent::__construct('hello');
        $this->setRequestMethods([RequestMethod::GET]);
        
        $this->addParameters([
            'name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }
    
    public function isAuthorized() {
        return true;
    }
    
    public function processRequest() {
        $name = $this->getParamVal('name');
        $this->sendResponse($name ? "Hello, $name!" : "Hello, World!");
    }
}
```

Both approaches work with `WebServicesManager`:

```php
$manager = new WebServicesManager();
$manager->addService(new HelloService());
$manager->process();
```

## Core Concepts

### Terminology

| Term | Definition |
|:-----|:-----------|
| **Web Service** | A single endpoint that implements a REST service, represented by `AbstractWebService` |
| **Services Manager** | An entity that manages multiple web services, represented by `WebServicesManager` |
| **Request Parameter** | A way to pass values from client to server, represented by `RequestParameter` |
| **API Filter** | A component that validates and sanitizes request parameters |

### Architecture Overview

The library follows a service-oriented architecture:

1. **AbstractWebService**: Base class for all web services
2. **WebServicesManager**: Manages multiple services and handles request routing
3. **RequestParameter**: Defines and validates individual parameters
4. **APIFilter**: Handles parameter filtering and validation
5. **Request/Response**: Utilities for handling HTTP requests and responses

## Creating Web Services

### Using Attributes (Recommended)

PHP 8+ attributes provide a modern, declarative approach:

```php
<?php
use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\ParamType;

#[RestController('users', 'User management operations')]
#[RequiresAuth]
class UserService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[RequestParam('id', ParamType::INT, true)]
    public function getUser(?int $id): array {
        return ['id' => $id ?? 1, 'name' => 'John Doe'];
    }
    
    #[PostMapping]
    #[ResponseBody]
    #[RequestParam('name', ParamType::STRING)]
    #[RequestParam('email', ParamType::EMAIL)]
    public function createUser(string $name, string $email): array {
        return ['id' => 2, 'name' => $name, 'email' => $email];
    }
    
    #[PutMapping]
    #[ResponseBody]
    #[RequestParam('id', ParamType::INT)]
    #[RequestParam('name', ParamType::STRING)]
    public function updateUser(int $id, string $name): array {
        return ['id' => $id, 'name' => $name];
    }
    
    #[DeleteMapping]
    #[ResponseBody]
    #[RequestParam('id', ParamType::INT)]
    public function deleteUser(int $id): array {
        return ['deleted' => $id];
    }
}
```

### Traditional Class-Based Approach

Every web service must extend `AbstractWebService` and implement the `processRequest()` method:

```php
<?php
use WebFiori\Http\AbstractWebService;
use WebFiori\Http\RequestMethod;

class MyService extends AbstractWebService {
    public function __construct() {
        parent::__construct('my-service');
        $this->setRequestMethods([RequestMethod::GET, RequestMethod::POST]);
        $this->setDescription('A sample web service');
    }
    
    public function isAuthorized() {
        // Implement authorization logic
        return true;
    }
    
    public function processRequest() {
        // Implement service logic
        $this->sendResponse('Service executed successfully');
    }
}
```

### Service Configuration

#### Setting Request Methods

```php
// Single method
$this->addRequestMethod(RequestMethod::POST);

// Multiple methods
$this->setRequestMethods([
    RequestMethod::GET,
    RequestMethod::POST,
    RequestMethod::PUT
]);
```

#### Service Metadata

```php
$this->setDescription('Creates a new user profile');
$this->setSince('1.2.0');
$this->addResponseDescription('Returns user profile data on success');
$this->addResponseDescription('Returns error message on failure');
```

## Parameter Management

### Parameter Types

The library supports various parameter types through `ParamType`:

```php
ParamType::STRING    // String values
ParamType::INT       // Integer values
ParamType::DOUBLE    // Float/double values
ParamType::BOOL      // Boolean values
ParamType::EMAIL     // Email addresses (validated)
ParamType::URL       // URLs (validated)
ParamType::ARR       // Arrays
ParamType::JSON_OBJ  // JSON objects
```

### Adding Parameters

#### Simple Parameter Addition

```php
use WebFiori\Http\RequestParameter;

$param = new RequestParameter('username', ParamType::STRING);
$this->addParameter($param);
```

#### Batch Parameter Addition

```php
$this->addParameters([
    'username' => [
        ParamOption::TYPE => ParamType::STRING,
        ParamOption::OPTIONAL => false
    ],
    'age' => [
        ParamOption::TYPE => ParamType::INT,
        ParamOption::OPTIONAL => true,
        ParamOption::MIN => 18,
        ParamOption::MAX => 120,
        ParamOption::DEFAULT => 25
    ],
    'email' => [
        ParamOption::TYPE => ParamType::EMAIL,
        ParamOption::OPTIONAL => false
    ]
]);
```

### Parameter Options

Available options through `ParamOption`:

```php
ParamOption::TYPE         // Parameter data type
ParamOption::OPTIONAL     // Whether parameter is optional
ParamOption::DEFAULT      // Default value for optional parameters
ParamOption::MIN          // Minimum value (numeric types)
ParamOption::MAX          // Maximum value (numeric types)
ParamOption::MIN_LENGTH   // Minimum length (string types)
ParamOption::MAX_LENGTH   // Maximum length (string types)
ParamOption::EMPTY        // Allow empty strings
ParamOption::FILTER       // Custom filter function
ParamOption::DESCRIPTION  // Parameter description
```

### Custom Validation

```php
$this->addParameters([
    'password' => [
        ParamOption::TYPE => ParamType::STRING,
        ParamOption::MIN_LENGTH => 8,
        ParamOption::FILTER => function($original, $basic) {
            // Custom validation logic
            if (strlen($basic) < 8) {
                return APIFilter::INVALID;
            }
            // Additional password strength checks
            return $basic;
        }
    ]
]);
```

### Retrieving Parameter Values

```php
public function processRequest() {
    $username = $this->getParamVal('username');
    $age = $this->getParamVal('age');
    $email = $this->getParamVal('email');
    
    // Get all inputs as array
    $allInputs = $this->getInputs();
}
```

## Testing

### Using APITestCase

```php
<?php
use WebFiori\Http\APITestCase;

class MyServiceTest extends APITestCase {
    public function testGetRequest() {
        $manager = new WebServicesManager();
        $manager->addService(new MyService());
        
        $response = $this->getRequest($manager, 'my-service', [
            'param1' => 'value1',
            'param2' => 'value2'
        ]);
        
        $this->assertJson($response);
        $this->assertContains('success', $response);
    }
    
    public function testPostRequest() {
        $manager = new WebServicesManager();
        $manager->addService(new MyService());
        
        $response = $this->postRequest($manager, 'my-service', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $this->assertJson($response);
    }
}
```


## Examples

### Complete CRUD Service Example

```php
<?php
use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\ParamType;

#[RestController('tasks', 'Task management service')]
class TaskService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getTasks(): array {
        return [
            'tasks' => [
                ['id' => 1, 'title' => 'Task 1', 'completed' => false],
                ['id' => 2, 'title' => 'Task 2', 'completed' => true]
            ],
            'count' => 2
        ];
    }
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('title', ParamType::STRING)]
    #[RequestParam('description', ParamType::STRING, true)]
    public function createTask(string $title, ?string $description): array {
        
        return [
            'id' => 3,
            'title' => $title,
            'description' => $description ?: '',
            'completed' => false
        ];
    }
    
    #[PutMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', ParamType::INT)]
    #[RequestParam('title', ParamType::STRING, true)]
    public function updateTask(int $id, ?string $title): array {
        
        return [
            'id' => $id,
            'title' => $title,
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
    
    #[DeleteMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', ParamType::INT)]
    public function deleteTask(int $id): array {
        return [
            'id' => $id,
            'deleted_at' => date('Y-m-d H:i:s')
        ];
    }
}
```

For more examples, check the [examples](examples/) directory in this repository.

### Key Classes Documentation

- [`AbstractWebService`](https://webfiori.com/docs/webfiori/http/AbstractWebService) - Base class for web services
- [`WebServicesManager`](https://webfiori.com/docs/webfiori/http/WebServicesManager) - Services management
- [`RequestParameter`](https://webfiori.com/docs/webfiori/http/RequestParameter) - Parameter definition and validation
- [`APIFilter`](https://webfiori.com/docs/webfiori/http/APIFilter) - Input filtering and validation
- [`Request`](https://webfiori.com/docs/webfiori/http/Request) - HTTP request utilities
- [`Response`](https://webfiori.com/docs/webfiori/http/Response) - HTTP response utilities

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- **Issues**: [GitHub Issues](https://github.com/WebFiori/http/issues)
- **Examples**: [Examples Directory](examples/)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.
