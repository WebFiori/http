# WebFiori HTTP Examples

This folder contains practical examples demonstrating how to use the WebFiori HTTP library to create RESTful web services. The examples showcase different features including basic services, parameter handling, and authentication.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Setup](#setup)
- [Available Services](#available-services)
  - [1. Hello World Service](#1-hello-world-service-helloworldservicephp)
  - [2. Random Number Service](#2-random-number-service-getrandomservicephp)
  - [3. Hello with Authentication Service](#3-hello-with-authentication-service-hellowithAuthservicephp)
- [Main Application](#main-application-indexphp)
- [Loader Configuration](#loader-configuration-loaderphp)
- [Key Concepts Demonstrated](#key-concepts-demonstrated)
- [Testing All Services](#testing-all-services)
- [Notes](#notes)

## Prerequisites

- PHP 8.1 or higher
- Composer installed
- WebFiori HTTP library dependencies

## Setup

1. **Install dependencies** (run from the project root directory):
   ```bash
   composer install
   ```

2. **Navigate to the examples directory**:
   ```bash
   cd examples
   ```

3. **Start the PHP development server**:
   ```bash
   php -S localhost:8989
   ```

## Available Services

### 1. Hello World Service (`HelloWorldService.php`)

A basic service that demonstrates simple parameter handling.

**Service Name**: `hello`  
**HTTP Methods**: GET  
**Parameters**:
- `my-name` (optional, string): Name to include in greeting

**Code Example**:
```php
<?php

require 'loader.php';

use WebFiori\Http\AbstractWebService;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;

class HelloWorldService extends AbstractWebService {
    public function __construct() {
        parent::__construct('hello');
        $this->setRequestMethods([RequestMethod::GET]);
        
        $this->addParameters([
            'my-name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }
    public function isAuthorized() {
    }

    public function processRequest() {
        $name = $this->getParamVal('my-name');
        
        if ($name !== null) {
            $this->sendResponse("Hello '$name'.");
        }
        $this->sendResponse('Hello World!');
    }
}
```

**Test URLs**:
```bash
# Basic hello
curl "http://localhost:8989?service=hello"
# Response: {"message":"Hello World!","http-code":200}

# Hello with name
curl "http://localhost:8989?service=hello&my-name=ibrahim"
# Response: {"message":"Hello 'ibrahim'.","http-code":200}
```

### 2. Random Number Service (`GetRandomService.php`)

Demonstrates parameter validation and processing with optional integer parameters.

**Service Name**: `get-random-number`  
**HTTP Methods**: GET, POST  
**Parameters**:
- `min` (optional, integer): Minimum value for random number
- `max` (optional, integer): Maximum value for random number

**Code Example**:
```php
<?php

require 'loader.php';

use WebFiori\Http\AbstractWebService;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;

class GetRandomService extends AbstractWebService {
    public function __construct() {
        parent::__construct('get-random-number');
        $this->setRequestMethods([
            RequestMethod::GET, 
            RequestMethod::POST
        ]);
        
        $this->addParameters([
            'min' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true
            ],
            'max' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }

    public function isAuthorized() {
//        $authHeader = $this->getAuthHeader();
//        
//        if ($authHeader === null) {
//            return false;
//        }
//        
//        $scheme = $authHeader->getScheme();
//        $credentials = $authHeader->getCredentials();
        
        //Verify credentials based on auth scheme (e.g. 'Basic', 'Barear'
    }

    public function processRequest() {
        $max = $this->getParamVal('max');
        $min = $this->getParamVal('min');

        if ($max !== null && $min !== null) {
            $random = rand($min, $max);
        } else {
            $random = rand();
        }
        $this->sendResponse($random);
    }
}
```

**Test URLs**:
```bash
# Random number without bounds
curl "http://localhost:8989?service=get-random-number"
# Response: {"message":"1255598581","http-code":200}

# Random number between 1 and 10
curl "http://localhost:8989?service=get-random-number&min=1&max=10"
# Response: {"message":"7","http-code":200}

# Random number between -4 and 0
curl "http://localhost:8989?service=get-random-number&min=-4&max=0"
# Response: {"message":"-1","http-code":200}

# Invalid parameter type (demonstrates validation)
curl "http://localhost:8989?service=get-random-number&min=-4&max=Super"
# Response: {"message":"The following parameter(s) has invalid values: 'max'.","type":"error","http-code":404,"more-info":{"invalid":["max"]}}
```

### 3. Hello with Authentication Service (`HelloWithAuthService.php`)

Demonstrates Bearer token authentication implementation.

**Service Name**: `hello-with-auth`  
**HTTP Methods**: GET  
**Authentication**: Bearer token required (`abc123trX`)  
**Parameters**:
- `my-name` (optional, string): Name to include in greeting

**Code Example**:
```php
<?php

require 'loader.php';

use WebFiori\Http\AbstractWebService;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\ResponseMessage;

class HelloWithAuthService extends AbstractWebService {
    public function __construct() {
        parent::__construct('hello-with-auth');
        $this->setRequestMethods([RequestMethod::GET]);
        
        $this->addParameters([
            'my-name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }
    public function isAuthorized() {
        //Change default response message to custom one
        ResponseMessage::set('401', 'Not authorized to use this API.');
        
        $authHeader = $this->getAuthHeader();
        
        if ($authHeader === null) {
            return false;
        }
        
        $scheme = $authHeader->getScheme();
        $credentials = $authHeader->getCredentials();
        
        if ($scheme != 'bearer') {
            return false;
        }
        
        return $credentials == 'abc123trX';
    }

    public function processRequest() {
        $name = $this->getParamVal('my-name');
        
        if ($name !== null) {
            $this->sendResponse("Hello '$name'.");
        }
        $this->sendResponse('Hello World!');
    }
}
```

**Test URLs**:
```bash
# Without authorization (will fail)
curl "http://localhost:8989?service=hello-with-auth&my-name=ibrahim"
# Response: {"message":"Not authorized to use this API.","type":"error","http-code":401}

# With correct Bearer token
curl -H "Authorization: Bearer abc123trX" "http://localhost:8989?service=hello-with-auth&my-name=ibrahim"
# Response: {"message":"Hello 'ibrahim'.","http-code":200}
```

## Main Application (`index.php`)

The main entry point that registers all services with the WebServicesManager:

```php
<?php

require 'loader.php';
require 'HelloWorldService.php';
require 'GetRandomService.php';
require 'HelloWithAuthService.php';

use HelloWorldService;
use GetRandomService;
use HelloWithAuthService;
use WebFiori\Http\WebServicesManager;

$manager = new WebServicesManager();
$manager->addService(new HelloWorldService());
$manager->addService(new GetRandomService());
$manager->addService(new HelloWithAuthService());
$manager->process();
```

## Loader Configuration (`loader.php`)

Sets up error reporting and autoloading:

```php
<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once '../vendor/autoload.php';
```

## Key Concepts Demonstrated

1. **Service Registration**: How to extend `AbstractWebService` and register services
2. **Parameter Definition**: Using `ParamOption` and `ParamType` for input validation
3. **HTTP Methods**: Supporting different request methods (GET, POST)
4. **Authentication**: Implementing Bearer token authentication with custom error messages
5. **Response Handling**: Using `sendResponse()` to return JSON responses
6. **Parameter Retrieval**: Getting parameter values with `getParamVal()`

## Testing All Services

You can test all the services using the provided test calls:

```bash
# Start the server
php -S localhost:8989

# Test all endpoints
curl "http://localhost:8989?service=hello"
curl "http://localhost:8989?service=hello&my-name=ibrahim"
curl -X POST "http://localhost:8989?service=hello"
curl "http://localhost:8989?service=hello-with-auth&my-name=ibrahim"
curl -H "Authorization: Bearer abc123trX" "http://localhost:8989?service=hello-with-auth&my-name=ibrahim"
curl "http://localhost:8989?service=get-random-number"
curl "http://localhost:8989?service=get-random-number&min=0&max=5"
curl "http://localhost:8989?service=get-random-number&min=-4&max=0"
curl "http://localhost:8989?service=get-random-number&min=-4&max=Super"
```

## Notes

- The POST request to the hello service returns a 415 error because the HelloWorldService only accepts GET requests
- The Bearer token for authentication is hardcoded as `abc123trX` in the HelloWithAuthService
- All responses are returned in JSON format with appropriate HTTP status codes
- Parameter validation is handled automatically by the WebFiori HTTP library based on the parameter definitions
- Invalid parameter types (like passing "Super" for an integer parameter) are automatically caught and return a 404 error with detailed information about which parameters are invalid
