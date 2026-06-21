<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\ApiResponse;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

/**
 * Test service with #[ApiResponse] annotations for OpenAPI spec generation.
 */
#[RestController('api-response-service')]
class ApiResponseAnnotatedService extends WebService {
    public function isAuthorized(): bool {
        return true;
    }

    #[GetMapping]
    #[ApiResponse(status: '200', description: 'List of products')]
    #[ApiResponse(status: '404', description: 'Product not found')]
    #[RequestParam(name: 'id', type: ParamType::INT, optional: true)]
    public function getProducts() {
    }

    #[PostMapping]
    #[ApiResponse(status: '201', description: 'Product created successfully')]
    #[ApiResponse(status: '400', description: 'Invalid input data')]
    #[ApiResponse(status: '409', description: 'Product already exists')]
    #[RequestParam(name: 'name', type: ParamType::STRING)]
    public function createProduct() {
    }

    #[DeleteMapping]
    #[RequestParam(name: 'id', type: ParamType::INT)]
    public function deleteProduct() {
        // No #[ApiResponse] — should fall back to default
    }

    public function processRequest() {
    }
}
