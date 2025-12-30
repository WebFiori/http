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
- [Authentication & Authorization](#authentication--authorization)
- [Request & Response Handling](#request--response-handling)
- [Advanced Features](#advanced-features)
  - [Object Mapping](#object-mapping-1)
  - [OpenAPI Documentation](#openapi-documentation)
- [Testing](#testing)
- [Examples](#examples)
- [API Documentation](#api-documentation)

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

### Modern Approach with Attributes

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

The traditional approach using constructor configuration:

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

## Authentication & Authorization

### Basic Authentication Implementation

```php
public function isAuthorized() {
    $authHeader = $this->getAuthHeader();
    
    if ($authHeader === null) {
        return false;
    }
    
    $scheme = $authHeader->getScheme();
    $credentials = $authHeader->getCredentials();
    
    if ($scheme === 'basic') {
        // Decode base64 credentials
        $decoded = base64_decode($credentials);
        list($username, $password) = explode(':', $decoded);
        
        // Validate credentials
        return $this->validateUser($username, $password);
    }
    
    return false;
}
```

### Bearer Token Authentication

```php
public function isAuthorized() {
    $authHeader = $this->getAuthHeader();
    
    if ($authHeader === null) {
        return false;
    }
    
    if ($authHeader->getScheme() === 'bearer') {
        $token = $authHeader->getCredentials();
        return $this->validateToken($token);
    }
    
    return false;
}
```

### Skipping Authentication

```php
public function __construct() {
    parent::__construct('public-service');
    $this->setIsAuthRequired(false); // Skip authentication
}
```

### Custom Error Messages

```php
use WebFiori\Http\ResponseMessage;

public function isAuthorized() {
    ResponseMessage::set('401', 'Custom unauthorized message');
    
    // Your authorization logic
    return false;
}
```

## Request & Response Handling

### Sending JSON Responses

```php
// Simple message response
$this->sendResponse('Operation completed successfully');

// Response with type and status code
$this->sendResponse('User created', 'success', 201);

// Response with additional data
$userData = ['id' => 123, 'name' => 'John Doe'];
$this->sendResponse('User retrieved', 'success', 200, $userData);
```

### Custom Content Type Responses

```php
// Send XML response
$xmlData = '<user><id>123</id><name>John Doe</name></user>';
$this->send('application/xml', $xmlData, 200);

// Send plain text
$this->send('text/plain', 'Hello, World!', 200);

// Send file download
$this->send('application/octet-stream', $fileContent, 200);
```

### Handling Different Request Methods

```php
public function processRequest() {
    $method = $this->getManager()->getRequestMethod();
    
    switch ($method) {
        case RequestMethod::GET:
            $this->handleGet();
            break;
        case RequestMethod::POST:
            $this->handlePost();
            break;
        case RequestMethod::PUT:
            $this->handlePut();
            break;
        case RequestMethod::DELETE:
            $this->handleDelete();
            break;
    }
}
```

### JSON Request Handling

The library automatically handles JSON requests when `Content-Type: application/json`:

```php
public function processRequest() {
    $inputs = $this->getInputs();
    
    if ($inputs instanceof \WebFiori\Json\Json) {
        // Handle JSON input
        $name = $inputs->get('name');
        $email = $inputs->get('email');
    } else {
        // Handle form data
        $name = $inputs['name'] ?? null;
        $email = $inputs['email'] ?? null;
    }
}
```

## Advanced Features

### Object Mapping

Automatically map request parameters to PHP objects:

```php
class User {
    private $name;
    private $email;
    private $age;
    
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setAge($age) { $this->age = $age; }
    
    // Getters...
}

public function processRequest() {
    // Automatic mapping
    $user = $this->getObject(User::class);
    
    // Custom setter mapping
    $user = $this->getObject(User::class, [
        'full-name' => 'setName',
        'email-address' => 'setEmail'
    ]);
}
```

### Services Manager Configuration

```php
$manager = new WebServicesManager();

// Set API version and description
$manager->setVersion('2.1.0');
$manager->setDescription('User Management API');

// Add multiple services
$manager->addService(new CreateUserService());
$manager->addService(new GetUserService());
$manager->addService(new UpdateUserService());
$manager->addService(new DeleteUserService());

// Custom input/output streams
$manager->setInputStream('php://input');
$manager->setOutputStream(fopen('api-log.txt', 'a'));

// Process requests
$manager->process();
```

### Error Handling

```php
public function processRequest() {
    try {
        // Service logic
        $result = $this->performOperation();
        $this->sendResponse('Success', 'success', 200, $result);
    } catch (ValidationException $e) {
        $this->sendResponse($e->getMessage(), 'error', 400);
    } catch (AuthenticationException $e) {
        $this->sendResponse('Unauthorized', 'error', 401);
    } catch (Exception $e) {
        $this->sendResponse('Internal server error', 'error', 500);
    }
}
```

### Custom Filters

```php
use WebFiori\Http\APIFilter;

$customFilter = function($original, $filtered) {
    // Custom validation logic
    if (strlen($filtered) < 3) {
        return APIFilter::INVALID;
    }
    
    // Additional processing
    return strtoupper($filtered);
];

$this->addParameters([
    'code' => [
        ParamOption::TYPE => ParamType::STRING,
        ParamOption::FILTER => $customFilter
    ]
]);
```

### OpenAPI Documentation

Generate OpenAPI 3.1.0 specification for your API:

```php
$manager = new WebServicesManager();
$manager->setVersion('1.0.0');
$manager->setDescription('My REST API');

// Add your services
$manager->addService(new UserService());
$manager->addService(new ProductService());

// Generate OpenAPI specification
$openApiObj = $manager->toOpenAPI();

// Customize if needed
$info = $openApiObj->getInfo();
$info->setTermsOfService('https://example.com/terms');

// Output as JSON
header('Content-Type: application/json');
echo $openApiObj->toJSON();
```

The generated specification can be used with:
- Swagger UI for interactive documentation
- Postman for API testing
- Code generators for client SDKs

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

### Manual Testing

```php
// Set up test environment
$_GET['service'] = 'my-service';
$_GET['param1'] = 'test-value';
$_SERVER['REQUEST_METHOD'] = 'GET';

$manager = new WebServicesManager();
$manager->addService(new MyService());
$manager->process();
```

## Examples

### Complete CRUD Service Example

```php
<?php
class UserService extends AbstractWebService {
    public function __construct() {
        parent::__construct('user');
        $this->setRequestMethods([
            RequestMethod::GET,
            RequestMethod::POST,
            RequestMethod::PUT,
            RequestMethod::DELETE
        ]);
        
        $this->addParameters([
            'id' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true
            ],
            'name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true,
                ParamOption::MIN_LENGTH => 2
            ],
            'email' => [
                ParamOption::TYPE => ParamType::EMAIL,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }
    
    public function isAuthorized() {
        return true; // Implement your auth logic
    }
    
    public function processRequest() {
        $method = $this->getManager()->getRequestMethod();
        
        switch ($method) {
            case RequestMethod::GET:
                $this->getUser();
                break;
            case RequestMethod::POST:
                $this->createUser();
                break;
            case RequestMethod::PUT:
                $this->updateUser();
                break;
            case RequestMethod::DELETE:
                $this->deleteUser();
                break;
        }
    }
    
    private function getUser() {
        $id = $this->getParamVal('id');
        
        if ($id) {
            // Get specific user
            $user = $this->findUserById($id);
            $this->sendResponse('User found', 'success', 200, $user);
        } else {
            // Get all users
            $users = $this->getAllUsers();
            $this->sendResponse('Users retrieved', 'success', 200, $users);
        }
    }
    
    private function createUser() {
        $name = $this->getParamVal('name');
        $email = $this->getParamVal('email');
        
        $user = $this->createNewUser($name, $email);
        $this->sendResponse('User created', 'success', 201, $user);
    }
    
    private function updateUser() {
        $id = $this->getParamVal('id');
        $name = $this->getParamVal('name');
        $email = $this->getParamVal('email');
        
        $user = $this->updateExistingUser($id, $name, $email);
        $this->sendResponse('User updated', 'success', 200, $user);
    }
    
    private function deleteUser() {
        $id = $this->getParamVal('id');
        
        $this->removeUser($id);
        $this->sendResponse('User deleted', 'success', 200);
    }
}
```

### File Upload Service

```php
<?php
class FileUploadService extends AbstractWebService {
    public function __construct() {
        parent::__construct('upload');
        $this->setRequestMethods([RequestMethod::POST]);
        
        $this->addParameters([
            'file' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => false
            ],
            'description' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }
    
    public function isAuthorized() {
        return true;
    }
    
    public function processRequest() {
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $uploadPath = 'uploads/' . basename($file['name']);
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $this->sendResponse('File uploaded successfully', 'success', 200, [
                        'filename' => $file['name'],
                        'size' => $file['size'],
                        'path' => $uploadPath
                    ]);
                } else {
                    $this->sendResponse('Failed to move uploaded file', 'error', 500);
                }
            } else {
                $this->sendResponse('File upload error', 'error', 400);
            }
        } else {
            $this->sendResponse('No file uploaded', 'error', 400);
        }
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
- **Documentation**: [WebFiori Docs](https://webfiori.com/docs/webfiori/http)
- **Examples**: [Examples Directory](examples/)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.
