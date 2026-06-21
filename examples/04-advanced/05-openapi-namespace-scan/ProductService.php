<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\ApiResponse;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

/**
 * Demonstrates #[ApiResponse] and #[RestController(path: ...)] features.
 */
#[RestController(name: 'products', path: 'shop/products', description: 'Product catalog')]
class ProductService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[ApiResponse(status: '200', description: 'List of products or a single product')]
    #[ApiResponse(status: '404', description: 'Product not found')]
    #[RequestParam('id', ParamType::INT, true)]
    public function getProducts(?int $id): array {
        if ($id !== null) {
            return ['id' => $id, 'name' => 'Widget', 'price' => 9.99];
        }
        return ['products' => [['id' => 1, 'name' => 'Widget']]];
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[ApiResponse(status: '201', description: 'Product created')]
    #[ApiResponse(status: '400', description: 'Invalid input')]
    #[RequestParam('name', ParamType::STRING)]
    #[RequestParam('price', ParamType::DOUBLE)]
    public function createProduct(string $name, float $price): array {
        return ['id' => 42, 'name' => $name, 'price' => $price];
    }

    #[DeleteMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[ApiResponse(status: '204', description: 'Product deleted')]
    #[ApiResponse(status: '404', description: 'Product not found')]
    #[RequestParam('id', ParamType::INT)]
    public function deleteProduct(int $id): array {
        return ['deleted' => $id];
    }

    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}
